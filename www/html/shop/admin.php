<?php
namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2018 James S. Crook
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
require_once "classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");

if (! isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

Menu::displayMenus(FALSE);
$unit = new Unit();
$category = new Category();
$user = new User();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<h3><div class='section_separator'>Add a New Item</div></h3>\n";
    
    echo "<form method='POST'>\n";
    
    echo "Description(*)<br><input type='text' name='itemname' placeholder='1UC+0-29*' pattern='.{1,30}'><p>\n";
    echo "Unit(*)<br><select name='unitname'><option></option>\n";
    $unit->displayUnitDropDownList(NULL);
    echo "</select><p>\n";
    echo "Category(*)<br><select name='categoryname'><option></option>\n";
    $category->displayCategoryDropDownList(NULL);
    echo "</select><p>\n";
    echo "Notes<br><input type='text' name='notes'><p>\n";
    echo "<button class='bttn' style=background-color:forestgreen; name='add_item_bttn'>&#x271A; Add Item</button><br>\n";
    echo "<button class='bttn' style=background-color:aqua; name='back_to_items_bttn'>&#x25C0; Back to Items</button><br>\n";
    
    echo "<h3><div class='section_separator'>Manage Items' Category Assignments</div></h3>\n";
    echo "<a href='items_by_category.php'>Manage Items' Category Assignments</a>\n";
    
    echo "<h3><div class='section_separator'>Edit User Profile</div></h3>\n";
    echo "<a href='profile.php'>Edit User Profile</a>\n";
    
    echo "<h3><div class='section_separator'>Manage Units</div></h3>\n";
    echo "Unit (add or rename as this)<br><input type='text' name='add_rename_unit' placeholder='1-12*' pattern='.{1,12}'><br>\n";
    echo "<button class='bttn' style=background-color:forestgreen; name='add_unit_bttn'>&#x271A; Add Unit</button><p>\n";
    echo "Unit to rename or delete<br><select name='rename_delete_unit'><option></option>\n";
    $unit->displayUnitDropDownList(NULL);
    echo "</select><br>\n";
    echo "<button class='bttn' style=background-color:lightgreen; name='rename_unit_bttn'>&#x270E; Rename Unit</button><p>\n";
    echo "<button class='bttn' style=background-color:salmon; name='delete_unit_bttn'>&#x1F5D1 Delete Unit</button>(!)\n";
    
    echo "<h3><div class='section_separator'>Manage Categories</div></h3>\n";
    echo "Category (add or rename as this)<br><input type='text' name='add_rename_category' placeholder='1-64*' pattern='.{1,64}'><br>\n";
    echo "<button class='bttn' style=background-color:forestgreen; name='add_category_bttn'>&#x271A; Add Category</button><p>\n";
    echo "Category to rename or delete<br><select name='rename_delete_category'><option></option>\n";
    $category->displayCategoryDropDownList(NULL);
    echo "</select><br>\n";
    echo "<button class='bttn' style=background-color:lightgreen; name='rename_category_bttn'>&#x270E; Rename Category</button><p>\n";
    echo "<button class='bttn' style=background-color:salmon; name='delete_category_bttn'>&#x1F5D1 Delete Category</button>(!)\n";
    
    echo "<h3><div class='section_separator'>Manage Users</div></h3>\n";
    echo "New User<br><input type='text' name='add_username' pattern='.{1,}'><br>\n";
    echo "New password<br><input type='password' name='newpw1' size='20' pattern='.{6,}' placeholder='min 6 chars'><br>\n";
    echo "Repeat password<br><input type='password' name='newpw2' size='20' pattern='.{6,}'><br>\n";
    echo "<button class='bttn' style=background-color:forestgreen; name='add_user_bttn'>&#x271A; Add User</button><p>\n";
    echo "User to delete<br>\n";
    echo "<select name='delete_username'><option></option>\n";
    $user->displayUsernameDropDownList();
    echo "</select><p>\n";
    echo "<button class='bttn' style=background-color:salmon; name='delete_user_bttn'>&#x1F5D1 Delete User</button>(!)\n";
    
    echo "</form>\n";
} else { /* POST - a button has been pressed */
    
    if (isset($_POST['add_item_bttn'])) {
        if ($_POST['itemname'] != "" && $_POST['unitname'] != "" && $_POST['categoryname'] != "") {
            $item = new Item();
            $item->addItem(mb_strtoupper(mb_substr($_POST['itemname'], 0, 1)) . mb_substr($_POST['itemname'], 1), $_POST['unitname'], $_POST['categoryname'], $_POST['notes'], $_SESSION['username']);
        } else {
            echo "<span class=failure_symbol>&#x2718; </span>";
            echo "Description, unit and category are required!<p>\n";
        }
    } else if (isset($_POST['back_to_items_bttn'])) {
        header("Location: index.php");
        exit();
    } else if (isset($_POST['ack_new_item_bttn'])) {
        header("Location: admin.php");
        exit();
    } else if (isset($_POST['add_unit_bttn'])) {
        if ($_POST['add_rename_unit'] != "") {
            $unit->addUnit($_POST['add_rename_unit']);
        } else {
            echo "<span class=failure_symbol>&#x2718; </span>";
            echo "Unit is required!<p>\n";
        }
    } else if (isset($_POST['rename_unit_bttn'])) {
        if ($_POST['add_rename_unit'] != "" && $_POST['rename_delete_unit'] != "") {
            $unit->renameUnit($_POST['rename_delete_unit'], $_POST['add_rename_unit']);
        } else {
            echo "<span class=failure_symbol>&#x2718; </span>";
            echo "Both old and new unit names are required!<p>\n";
        }
    } else if (isset($_POST['delete_unit_bttn'])) {
        if ($_POST['rename_delete_unit'] != "") {
            $unit->deleteUnit($_POST['rename_delete_unit']);
        } else {
            echo "<span class=failure_symbol>&#x2718; </span>";
            echo "Unit is required!<p>\n";
        }
    } else if (isset($_POST['add_category_bttn'])) {
        if ($_POST['add_rename_category'] != "") {
            $category->addCategory($_POST['add_rename_category']);
        } else {
            echo "<span class=failure_symbol>&#x2718; </span>";
            echo "Category is required!<p>\n";
        }
    } else if (isset($_POST['rename_category_bttn'])) {
        if ($_POST['add_rename_category'] != "" && $_POST['rename_delete_category'] != "") {
            $category->renameCategory($_POST['rename_delete_category'], $_POST['add_rename_category']);
        } else {
            echo "<span class=failure_symbol>&#x2718; </span>";
            echo "Both old and new category names are required!<p>\n";
        }
    } else if (isset($_POST['delete_category_bttn'])) {
        if ($_POST['rename_delete_category'] != "") {
            $category->deleteCategory($_POST['rename_delete_category']);
        } else {
            echo "<span class=failure_symbol>&#x2718; </span>";
            echo "Category is required!<p>\n";
        }
    } else if (isset($_POST['add_user_bttn']) && $_POST['add_username'] != "" && $_POST['newpw1'] != "") {
        if ($_POST['newpw1'] == $_POST['newpw2']) {
            $user->addUserName($_POST['add_username'], $_POST['newpw1']);
        } else {
            echo "Passwords do not match, please try again<br>\n";
        }
    } else if (isset($_POST['delete_user_bttn'])) {
        if ($_POST['delete_username'] != "") {
            $user->deleteUserName($_POST['delete_username']);
        } else {
            echo "<span class=failure_symbol>&#x2718; </span>";
            echo "User is required!<p>\n";
        }
    } else if (isset($_POST['ack_manage_bttn'])) {
        header('Location: admin.php');
        exit();
    } else {
        echo "SNARK!<br>";
    }
}
?>

</body>
</html>
<!-- shop - Copyright (C) 2017-2018 James S. Crook - GPL3+ -->
