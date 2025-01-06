<?php

namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2025 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 *
 * Administer items, units, categories and users. Also, links to the manage category
 * assignment page and the edit user profile page
 */
session_start();
require_once "Classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");
$pageSubtitle = "Administration";
Utils::topOfPageHTML(": $pageSubtitle");

if (!isset($_SESSION['username'])) {
    header("Location: login");
    exit();
} else {
    $username = $_SESSION['username'];
}

Menu::displayMenus(FALSE);
$dbConnection = new DBConnection();
$unit = new Unit($dbConnection);
$category = new Category($dbConnection);
$user = new User($dbConnection);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<h3>$pageSubtitle (" . htmlspecialchars($username, ENT_QUOTES) . ")</h3>" . PHP_EOL;
    echo "<h3><div class='section-separator'>Add a New Item</div></h3>" . PHP_EOL;

    echo "<form method='POST'>" . PHP_EOL;

    echo "<input type='text' class='enter-input-text input-color' name='itemname' placeholder='Description (required)' pattern='.{1,30}'>" . PHP_EOL;
    echo "<select class='enter-select input-color' name='unitname'><option value='' disabled selected>" . Constant::UNITDESCRIPTION . " (required)</option>" . PHP_EOL;
    $unit->displayUnitDropDownList(NULL);
    echo "</select>" . PHP_EOL;
    echo "<select class='enter-select input-color' name='categoryname'><option value='' disabled selected>" . Constant::CATEGORYDESCRIPTION . " (required)</option>" . PHP_EOL;
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

    echo "<h3><div class='section-separator'>Manage a " . Constant::UNITDESCRIPTION . "</div></h3>" . PHP_EOL;
    echo "<input type='text' class='enter-input-text input-color' name='add_rename_unit' placeholder='" . Constant::UNITDESCRIPTION . " (add or rename as this)' pattern='.{1,12}'>" . PHP_EOL;
    echo "<button class='bttn add-color' name='add_unit_bttn'>" . Utils::addSymbol() . " Add a " . Constant::UNITDESCRIPTION . "</button>" . PHP_EOL;
    echo "<select class='enter-select input-color' name='rename_delete_unit'><option value='' disabled selected>" . Constant::UNITDESCRIPTION . " to rename or delete</option>" . PHP_EOL;
    $unit->displayUnitDropDownList(NULL);
    echo "</select>" . PHP_EOL;
    echo "<button class='bttn change-color' name='rename_unit_bttn'>" . Utils::changeSymbol() . " Rename a " . Constant::UNITDESCRIPTION . "</button>" . PHP_EOL;
    echo "<button class='bttn delete-color' name='delete_unit_bttn'>" . Utils::deleteSymbol() . " Delete a " . Constant::UNITDESCRIPTION . "</button>" . PHP_EOL;

    echo "<h3><div class='section-separator'>Manage a " . Constant::CATEGORYDESCRIPTION . "</div></h3>" . PHP_EOL;
    echo "<input type='text' class='enter-input-text input-color' name='add_rename_category' placeholder='" . Constant::CATEGORYDESCRIPTION . " (add or rename as this)' pattern='.{1,64}'>" . PHP_EOL;
    echo "<button class='bttn add-color' name='add_category_bttn'>" . Utils::addSymbol() . " Add " . Constant::CATEGORYDESCRIPTION . "</button>" . PHP_EOL;
    echo "<select class='enter-select input-color' name='rename_delete_category'><option value='' disabled selected>" . Constant::CATEGORYDESCRIPTION . " to rename or delete</option>" . PHP_EOL;
    $category->displayCategoryDropDownList(NULL);
    echo "</select>" . PHP_EOL;
    echo "<button class='bttn change-color' name='rename_category_bttn'>" . Utils::changeSymbol() . " Rename " . Constant::CATEGORYDESCRIPTION . "</button>" . PHP_EOL;
    echo "<button class='bttn delete-color' name='delete_category_bttn'>" . Utils::deleteSymbol() . " Delete " . Constant::CATEGORYDESCRIPTION . "</button>" . PHP_EOL;

    echo "<h3><div class='section-separator'>Manage a User</div></h3>" . PHP_EOL;
    echo "<input type='text' class='enter-input-text input-color' name='username' placeholder='Username' pattern='.{1,}'>" . PHP_EOL;
    echo "<div class='pw-show-hide-input'>" . PHP_EOL;
    echo " <input type='password' class='enter-input-text input-color' name='newpassword1' id='newpassword1' size='20' pattern='.{6,}' placeholder='password'>" . PHP_EOL;
    echo " <img src='Images/eye-icon.png' class='pw-show-hide-icon' id='pwtoggleshowhideA'>" . PHP_EOL;
    echo "</div>" . PHP_EOL;
    echo "<div class='pw-show-hide-input'>" . PHP_EOL;
    echo " <input type='password' class='enter-input-text input-color' name='newpassword2' id='newpassword2' size='20' pattern='.{6,}' placeholder='repeat password'>" . PHP_EOL;
    echo " <img src='Images/eye-icon.png' class='pw-show-hide-icon' id='pwtoggleshowhideB'>" . PHP_EOL;
    echo "</div>" . PHP_EOL;
    echo "<button class='bttn add-color' name='add_user_bttn'>" . Utils::addSymbol() . " Add User</button>" . PHP_EOL;
    echo "<select class='enter-select input-color' name='delete_username'><option value='' disabled selected>User to delete</option>" . PHP_EOL;
    $user->displayUsernameDropDownList();
    echo "</select>" . PHP_EOL;
    echo "<button class='bttn delete-color' name='delete_user_bttn'>" . Utils::deleteSymbol() . " Delete User</button>" . PHP_EOL;

    echo "</form>" . PHP_EOL;
    Utils::passwordToggleShowHide('pwtoggleshowhideA', 'newpassword1');
    Utils::passwordToggleShowHide('pwtoggleshowhideB', 'newpassword2');
} else { /* POST - a button has been pressed */

    if (isset($_POST['add_item_bttn'])) {
	if ($_POST['itemname'] != "" && $_POST['unitname'] != "" && $_POST['categoryname'] != "") {
	    $item = new Item($dbConnection);
	    if ($_POST['quantity'] != "") {
		$quantity = floatval($_POST['quantity']);
	    } else {
		$quantity = 0.0;
	    }
	    $itemName = preg_replace('/\s+/', ' ', trim($_POST['itemname']));
	    $notes = preg_replace('/\s+/', ' ', trim($_POST['notes']));
	    $item->addItem(mb_strtoupper(mb_substr($itemName, 0, 1)) . mb_substr($itemName, 1), $_POST['unitname'], $_POST['categoryname'], $notes, $quantity, $_SESSION['username']);
	} else {
	    echo "<br>" . Utils::failureSymbol() . "Description, unit and category are required!<p>" . PHP_EOL;
	}
    } else if (isset($_POST['add_unit_bttn'])) {
	if ($_POST['add_rename_unit'] != "") {
	    $unitName = preg_replace('/\s+/', ' ', trim($_POST['add_rename_unit']));
	    $unit->addUnit($unitName);
	} else {
	    echo "<br>" . Utils::failureSymbol() . Constant::UNITDESCRIPTION . " is required!<p>" . PHP_EOL;
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
	    echo "<br>" . Utils::failureSymbol() . Constant::UNITDESCRIPTION . " is required!<p>" . PHP_EOL;
	}
    } else if (isset($_POST['add_category_bttn'])) {
	if ($_POST['add_rename_category'] != "") {
	    $categoryName = preg_replace('/\s+/', ' ', trim($_POST['add_rename_category']));
	    $category->addCategory($categoryName);
	} else {
	    echo "<br>" . Utils::failureSymbol() . Constant::CATEGORYDESCRIPTION . " is required!<p>" . PHP_EOL;
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
	    echo "<br>" . Utils::failureSymbol() . Constant::CATEGORYDESCRIPTION . " is required!<p>" . PHP_EOL;
	}
    } else if (isset($_POST['add_user_bttn'])) {
	if ($_POST['username'] != "" && $_POST['newpassword1'] != "") {
	    if ($_POST['newpassword1'] == $_POST['newpassword2']) {
		$userName = preg_replace('/\s+/', ' ', trim($_POST['username']));
		$user->addUserName($userName, $_POST['newpassword1']);
	    } else {
		echo "<br>" . Utils::failureSymbol() . "Passwords do not match, please try again<p>" . PHP_EOL;
	    }
	} else {
	    echo "<br>" . Utils::failureSymbol() . "User is required!<p>" . PHP_EOL;
	}
    } else if (isset($_POST['delete_user_bttn'])) {
	if ($_POST['delete_username'] != ""  && $_POST['username'] != "" && $_POST['delete_username'] == $_POST['username']) {
	    $user->deleteUserName($_POST['delete_username']);
	} else {
	    echo "<br>" . Utils::failureSymbol() . "Please enter the name of the user to delete and select the same user from the drop-down list.<p>" . PHP_EOL;
	}
    } else {
	echo "<p>UNEXPECTED ERROR: in file: " . basename(__FILE__) . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . PHP_EOL;
    }
}
?>

</body>
</html>

<script>
    function visitPage(page) {
	window.location.href = page;
    }
</script>
