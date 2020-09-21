<?php

namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2020 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Shop: Administration</title>
<link rel='stylesheet' media='screen' href='shop.css'>
</head>
<body>

<?php
/*
 * Administer items, units, categories and users. Also, links to the manage category
 * assignment page and the edit user profile page
 */
session_start();
require_once dirname(dirname(dirname(__FILE__))) . dirname($_SERVER["PHP_SELF"]) . "_db_conn.php";
require_once "Classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");

if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
} else {
	$username = $_SESSION['username'];
}

Menu::displayMenus(FALSE);
$unit = new Unit();
$category = new Category();
$user = new User();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	echo "<h3>Administration (" . htmlspecialchars($username, ENT_QUOTES) . ")</h3>" . PHP_EOL;
	echo "<h3><div class='section_separator'>Add a New Item</div></h3>" . PHP_EOL;

	echo "<form method='POST'>" . PHP_EOL;

	echo "<input type='text' class='enter_input_text' name='itemname' placeholder='Description (required)' pattern='.{1,30}'>";
	echo "<select class='enter_select' name='unitname'><option value='' disabled selected>Unit (required)</option>";
	$unit->displayUnitDropDownList(NULL);
	echo "</select>";
	echo "<select class='enter_select' name='categoryname'><option value='' disabled selected>Category (required)</option>";
	$category->displayCategoryDropDownList(NULL);
	echo "</select>";
	echo "<input type='text' class='enter_input_text' placeholder='Notes (optional)' name='notes'>";
	echo "<input type='number' class='enter_input_number' name='quantity' placeholder='Quanitity (optional)' min='-9999' max='9999' step='any'>";
	echo "<button class='bttn' style=background-color:forestgreen; name='add_item_bttn'>&#x271A; Add Item</button><br>" . PHP_EOL;

	echo "<h3><div class='section_separator'>Miscellaneous</div></h3>" . PHP_EOL;
	$dirName = dirname($_SERVER['PHP_SELF']);
	echo "<input type='button' value='Edit User Profile' class='bttn' onclick=\"document.location.href='$dirName/user_profile.php';\">";
	echo "<input type='button' value='Display Item Details' class='bttn' onclick=\"document.location.href='$dirName/display_item_details.php';\">";
	echo "<input type='button' value='Display Items Sorted' class='bttn' onclick=\"document.location.href='$dirName/display_items_sorted.php';\">";

	echo "<h3><div class='section_separator'>Manage Units</div></h3>" . PHP_EOL;
	echo "<input type='text' class='enter_input_text' name='add_rename_unit' placeholder='Unit (add or rename unit as this)' pattern='.{1,12}'>";
	echo "<button class='bttn' style=background-color:forestgreen; name='add_unit_bttn'>&#x271A; Add Unit</button>";
	echo "<select class='enter_select' name='rename_delete_unit'><option value='' disabled selected>Unit to rename or delete</option>";
	$unit->displayUnitDropDownList(NULL);
	echo "</select>";
	echo "<button class='bttn' style=background-color:lightblue; name='rename_unit_bttn'>&#x270E; Rename Unit</button>";
	echo "<button class='bttn' style=background-color:salmon; name='delete_unit_bttn'>&#x1F5D1; Delete Unit</button>" . PHP_EOL;

	echo "<h3><div class='section_separator'>Manage Categories</div></h3>" . PHP_EOL;
	echo "<input type='text' class='enter_input_text' name='add_rename_category' placeholder='Category (add or rename as this)' pattern='.{1,64}'>";
	echo "<button class='bttn' style=background-color:forestgreen; name='add_category_bttn'>&#x271A; Add Category</button>";
	echo "<select class='enter_select' name='rename_delete_category'><option value='' disabled selected>Category to rename or delete</option>";
	$category->displayCategoryDropDownList(NULL);
	echo "</select>";
	echo "<button class='bttn' style=background-color:lightblue; name='rename_category_bttn'>&#x270E; Rename Category</button>";
	echo "<button class='bttn' style=background-color:salmon; name='delete_category_bttn'>&#x1F5D1; Delete Category</button>" . PHP_EOL;

	echo "<h3><div class='section_separator'>Manage Users</div></h3>" . PHP_EOL;
	echo "<input type='text' class='enter_input_text' name='add_username' placeholder='Username' pattern='.{1,}'>";
	echo "<input type='password' class='enter_input_text' name='newpw1' size='20' pattern='.{6,}' placeholder='password'>";
	echo "<input type='password' class='enter_input_text' name='newpw2' size='20' pattern='.{6,}' placeholder='repeat password'>";
	echo "<button class='bttn' style=background-color:forestgreen; name='add_user_bttn'>&#x271A; Add User</button>";
	echo "<select class='enter_select' name='delete_username'><option value='' disabled selected>User to delete</option>";
	$user->displayUsernameDropDownList();
	echo "</select>";
	echo "<button class='bttn' style=background-color:salmon; name='delete_user_bttn'>&#x1F5D1; Delete User</button>" . PHP_EOL;

	echo "</form>" . PHP_EOL;
} else { /* POST - a button has been pressed */

	if (isset($_POST['add_item_bttn'])) {
		if ($_POST['itemname'] != "" && $_POST['unitname'] != "" && $_POST['categoryname'] != "") {
			$item = new Item();
			if ($_POST['quantity'] != "") {
				$quantity = $_POST['quantity'];
			} else {
				$quantity = 0;
			}
			$itemName = preg_replace('/\s+/', ' ', trim($_POST['itemname']));
			$notes = preg_replace('/\s+/', ' ', trim($_POST['notes']));
			$item->addItem(mb_strtoupper(mb_substr($itemName, 0, 1)) . mb_substr($itemName, 1), $_POST['unitname'], $_POST['categoryname'], $notes, $quantity, $_SESSION['username']);
		} else {
			echo "<br>" . Utils::failureSymbol() . "Description, unit and category are required!<p>" . PHP_EOL;
		}
	} else if (isset($_POST['ack_new_item_bttn'])) {
		header("Location: admin.php");
		exit();
	} else if (isset($_POST['add_unit_bttn'])) {
		if ($_POST['add_rename_unit'] != "") {
			$unitName = preg_replace('/\s+/', ' ', trim($_POST['add_rename_unit']));
			$unit->addUnit($unitName);
		} else {
			echo "<br>" . Utils::failureSymbol() . "Unit is required!<p>" . PHP_EOL;
		}
	} else if (isset($_POST['rename_unit_bttn'])) {
		if ($_POST['add_rename_unit'] != "" && $_POST['rename_delete_unit'] != "") {
			$unitName = preg_replace('/\s+/', ' ', trim($_POST['add_rename_unit']));
			$unit->renameUnit($_POST['rename_delete_unit'], $unitName);
		} else {
			echo "<br>" . Utils::failureSymbol() . "Both old and new unit names are required!<p>" . PHP_EOL;
		}
	} else if (isset($_POST['delete_unit_bttn'])) {
		if ($_POST['rename_delete_unit'] != "") {
			$unit->deleteUnit($_POST['rename_delete_unit']);
		} else {
			echo "<br>" . Utils::failureSymbol() . "Unit is required!<p>" . PHP_EOL;
		}
	} else if (isset($_POST['add_category_bttn'])) {
		if ($_POST['add_rename_category'] != "") {
			$categoryName = preg_replace('/\s+/', ' ', trim($_POST['add_rename_category']));
			$category->addCategory($categoryName);
		} else {
			echo "<br>" . Utils::failureSymbol() . "Category is required!<p>" . PHP_EOL;
		}
	} else if (isset($_POST['rename_category_bttn'])) {
		if ($_POST['add_rename_category'] != "" && $_POST['rename_delete_category'] != "") {
			$categoryName = preg_replace('/\s+/', ' ', trim($_POST['add_rename_category']));
			$category->renameCategory($_POST['rename_delete_category'], $categoryName);
		} else {
			echo "<br>" . Utils::failureSymbol() . "Both old and new category names are required!<p>" . PHP_EOL;
		}
	} else if (isset($_POST['delete_category_bttn'])) {
		if ($_POST['rename_delete_category'] != "") {
			$category->deleteCategory($_POST['rename_delete_category']);
		} else {
			echo "<br>" . Utils::failureSymbol() . "Category is required!<p>" . PHP_EOL;
		}
	} else if (isset($_POST['add_user_bttn'])) {
		if ($_POST['add_username'] != "" && $_POST['newpw1'] != "") {
			if ($_POST['newpw1'] == $_POST['newpw2']) {
				$userName = preg_replace('/\s+/', ' ', trim($_POST['add_username']));
				$user->addUserName($userName, $_POST['newpw1']);
			} else {
				echo "<br>" . Utils::failureSymbol() . "Passwords do not match, please try again<p>" . PHP_EOL;
			}
		} else {
			echo "<br>" . Utils::failureSymbol() . "User is required!<p>" . PHP_EOL;
		}
	} else if (isset($_POST['delete_user_bttn'])) {
		if ($_POST['delete_username'] != "") {
			$user->deleteUserName($_POST['delete_username']);
		} else {
			echo "<br>" . Utils::failureSymbol() . "User is required!<p>" . PHP_EOL;
		}
	} else if (isset($_POST['ack_manage_bttn'])) {
		header('Location: admin.php');
		exit();
	} else {
		echo "UNEXPECTED ERROR: in file: " . basename(__FILE__) . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . PHP_EOL;
	}
}
?>

</body>
</html>
