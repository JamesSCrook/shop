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
<title>Shop: First Characters</title>
<link rel='stylesheet' media='screen' href='shop.css'>
</head>
<body>

<?php
/*
 * Display all the items for a the specified first character. At this point, the sort
 * order for this page is always alphabetical on itemname (first) and unitname (second).
 */
session_start();
require_once dirname(dirname(dirname(__FILE__))) . dirname($_SERVER["PHP_SELF"]) . "_db_conn.php";
require_once "classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");

if (! isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$item = new Item();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    Menu::displayMenus(TRUE);
    echo "<h3>All Items Beginning with '" . htmlspecialchars($_GET['first_char'], ENT_QUOTES) . "'</h3>\n";
    $item->displayLinks();
    echo "<form id=items_form method='POST'>\n";
    echo "<div class='grid-container'>\n";
    $item->displayItems("AND SUBSTR(itemname,1,1)='" . htmlspecialchars($_GET['first_char'], ENT_QUOTES) . "'");
    echo "</div>\n";
    echo "</form>\n";
} else { /* POST - a button has been pressed */
    $item->updateItemQuantities();
    
    if (isset($_POST['update_items_bttn'])) {
        $user = new User();
        if ($user->getDisplayUpdates($_SESSION['username']) == "No") {
            header("Location: first_char.php?first_char=" . htmlspecialchars($_GET['first_char'], ENT_QUOTES));
            exit();
        } else {
            Menu::displayMenus(FALSE);
            echo "<form id=ack_changes method='POST'>\n";
            echo " <button class='bttn' style=background-color:aqua; name='ack_changes_bttn'>&#x25C0; Back</button>\n";
            echo "</form>\n";
        }
    } else if (isset($_POST['ack_changes_bttn'])) {
        header("Location: first_char.php?first_char=" . htmlspecialchars($_GET['first_char'], ENT_QUOTES));
        exit();
    } else {
        echo "Unexpected error in " . $_SERVER["PHP_SELF"] . "<br>";
    }
}
?>

</body>
</html>
<!-- shop - Copyright (C) 2017-2018 James S. Crook - GPL3+ -->
