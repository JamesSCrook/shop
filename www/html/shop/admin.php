<?php

namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2023 James S. Crook
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
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<title>Shop: Administration</title>
<link rel='stylesheet' media='screen' href='shop.css'>
</head>
<body>

<script>
    function visitPage(page) {
	window.location.href = page;
    }
</script>

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
    header("Location: login");
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
    echo "<h3><div class='section-separator'>Add a New Item</div></h3>" . PHP_EOL;

    echo "<form method='POST'>" . PHP_EOL;

    echo "<input type='text' class='enter-input-text input-color' name='itemname' placeholder='Description (required)' pattern='.{1,30}'>" . PHP_EOL;
    echo "<select class='enter-select input-color' name='unitname'><option value='' disabled selected>Unit (required)</option>" . PHP_EOL;
    $unit->displayUnitDropDownList(NULL);
    echo "</select>" . PHP_EOL;
    echo "<select class='enter-select input-color' name='categoryname'><option value='' disabled selected>Category (required)</option>" . PHP_EOL;
    $category->displayCategoryDropDownList(NULL);
    echo "</select>" . PHP_EOL;
    echo "<input type='text' class='enter-input-text input-color' placeholder='Notes (optional)' name='notes'>" . PHP_EOL;
    echo "<input type='number' class='enter-input-number input-color' name='quantity' placeholder='Quanitity (optional)' min='-9999' max='9999' step='any'>" . PHP_EOL;
    echo "<button class='bttn add-color' name='add_item_bttn'>" . Utils::addSymbol() . " Add Item</button><br>" . PHP_EOL;

    echo "<h3><div class='section-separator'>Miscellaneous</div></h3>" . PHP_EOL;
    $dirName = dirname($_SERVER['PHP_SELF']);
    echo "<button type='button' onclick='visitPage(\"$dirName/user_profile\");' class='bttn change-color'>" . Utils::changeSymbol() . "Edit User Profile</button>" . PHP_EOL;
    echo "<button type='button' onclick='visitPage(\"$dirName/display_item_details\");' class='bttn query-color'>Display Item Details</button>" . PHP_EOL;
    echo "<button type='button' onclick='visitPage(\"$dirName/display_items_sorted\");' class='bttn query-color'>Display Items Sorted</button>" . PHP_EOL;

    echo "<h3><div class='section-separator'>Manage Units</div></h3>" . PHP_EOL;
    echo "<input type='text' class='enter-input-text input-color' name='add_rename_unit' placeholder='Unit (add or rename unit as this)' pattern='.{1,12}'>" . PHP_EOL;
    echo "<button class='bttn add-color' name='add_unit_bttn'>" . Utils::addSymbol() . " Add Unit</button>" . PHP_EOL;
    echo "<select class='enter-select input-color' name='rename_delete_unit'><option value='' disabled selected>Unit to rename or delete</option>" . PHP_EOL;
    $unit->displayUnitDropDownList(NULL);
    echo "</select>" . PHP_EOL;
    echo "<button class='bttn change-color' name='rename_unit_bttn'>" . Utils::changeSymbol() . " Rename Unit</button>" . PHP_EOL;
    echo "<button class='bttn delete-color' name='delete_unit_bttn'>" . Utils::deleteSymbol() . " Delete Unit</button>" . PHP_EOL;

    echo "<h3><div class='section-separator'>Manage Categories</div></h3>" . PHP_EOL;
    echo "<input type='text' class='enter-input-text input-color' name='add_rename_category' placeholder='Category (add or rename as this)' pattern='.{1,64}'>" . PHP_EOL;
    echo "<button class='bttn add-color' name='add_category_bttn'>" . Utils::addSymbol() . " Add Category</button>" . PHP_EOL;
    echo "<select class='enter-select input-color' name='rename_delete_category'><option value='' disabled selected>Category to rename or delete</option>" . PHP_EOL;
    $category->displayCategoryDropDownList(NULL);
    echo "</select>" . PHP_EOL;
    echo "<button class='bttn change-color' name='rename_category_bttn'>" . Utils::changeSymbol() . " Rename Category</button>" . PHP_EOL;
    echo "<button class='bttn delete-color' name='delete_category_bttn'>" . Utils::deleteSymbol() . " Delete Category</button>" . PHP_EOL;

    echo "<h3><div class='section-separator'>Manage Users</div></h3>" . PHP_EOL;
    echo "<input type='text' class='enter-input-text input-color' name='add_username' placeholder='Username' pattern='.{1,}'>" . PHP_EOL;
    echo "<input type='password' class='enter-input-text input-color' name='newpw1' size='20' pattern='.{6,}' placeholder='password'>" . PHP_EOL;
    echo "<input type='password' class='enter-input-text input-color' name='newpw2' size='20' pattern='.{6,}' placeholder='repeat password'>" . PHP_EOL;
    echo "<button class='bttn add-color' name='add_user_bttn'>" . Utils::addSymbol() . " Add User</button>" . PHP_EOL;
    echo "<select class='enter-select input-color' name='delete_username'><option value='' disabled selected>User to delete</option>" . PHP_EOL;
    $user->displayUsernameDropDownList();
    echo "</select>" . PHP_EOL;
    echo "<button class='bttn delete-color' name='delete_user_bttn'>" . Utils::deleteSymbol() . " Delete User</button>" . PHP_EOL;

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
	header("Location: admin");
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
	    $newUnitName = preg_replace('/\s+/', ' ', trim($_POST['add_rename_unit']));
	    $unit->renameUnit($_POST['rename_delete_unit'], $newUnitName);
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
	    $newCategoryName = preg_replace('/\s+/', ' ', trim($_POST['add_rename_category']));
	    $category->renameCategory($_POST['rename_delete_category'], $newCategoryName);
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
	header('Location: admin');
	exit();
    } else {
	echo "<p>UNEXPECTED ERROR: in file: " . basename(__FILE__) . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . PHP_EOL;
    }
}
?>

</body>
</html>
