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
<title>Shop</title>
<link rel='stylesheet' media='screen' href='shop.css'>
</head>
<body>

<?php
/*
 * Item (main) page. Display all the items and their current quantity values.
 * There are 3 sort orders:
 * q (quantity), cq (categoryname, quantity), and a (alphabetical).
 * See below for details.
 */
session_start();
require_once dirname(dirname(dirname(__FILE__))) . dirname($_SERVER["PHP_SELF"]) . "_db_conn.php";
require_once "classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");

if (! isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user = new User();
$item = new Item();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    Menu::displayMenus(TRUE);
    echo "<h3>Shopping List</h3>\n";
    $item->displayLinks();
    echo "<form id=items_form method='POST'>\n";
    
    $category = new Category();
    $activeCategories = $category->getActiveCategories();
    
    $sortOrder = $user->getSortOrder($_SESSION['username']);
    switch ($sortOrder) {
        case "cq": // sort "by category, then by quantity"
            foreach ($activeCategories as $activeCategory) { // For each active category
                echo "<div class='section_separator'>" . htmlspecialchars($activeCategory, ENT_QUOTES) . "</div>\n";
		echo "<div class='grid-container'>\n";
                $item->displayItems("AND category.categoryid=(select categoryid from category where categoryname='$activeCategory') AND quantity > 0");
                $item->displayItems("AND category.categoryid=(select categoryid from category where categoryname='$activeCategory') AND quantity < 0");
		echo "</div>\n";
            }
            echo "<div class='section_separator'>Zero Quantities</div>\n";
	    echo "<div class='grid-container'>\n";
            $item->displayItems("AND quantity = 0");
	    echo "</div>\n";
            break;
        case "a": // sort alphabetically
	    echo "<div class='grid-container'>\n";
            $item->displayItems("");
	    echo "</div>\n";
            break;
        default: // sort "by quantity" - the default
	    echo "<div class='grid-container'>\n";
            $item->displayItems("AND quantity > 0");
            $item->displayItems("AND quantity < 0");
            $item->displayItems("AND quantity = 0");
	    echo "</div>\n";
            break;
    }
    
    echo "</form>\n";
} else { /* POST - a button has been pressed */
    if (isset($_POST['ack_changes_bttn'])) {
        header("Location: index.php");
        exit();
    } else {
        $item->updateItemQuantities();
        if ($user->getDisplayUpdates($_SESSION['username']) == "No") {
            header("Location: index.php");
            exit();
        } else {
            Menu::displayMenus(FALSE);
            echo "<form id=ack_changes method='POST'>\n";
            echo " <button class='bttn' style=background-color:aqua; name='ack_changes_bttn'>&#x25C0; Back to Items</button>\n";
            echo "</form>\n";
        }
    }
}
?>

</body>
</html>
<!--
Version 0.1.0 - Fri Feb 16 10:34:09 AEDT 2018
shop - Copyright (C) 2017-2018 James S. Crook - GPL3+
-->
