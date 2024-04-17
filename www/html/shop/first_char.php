<?php

namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2024 James S. Crook
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
require_once "Classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");

if (!isset($_SESSION['username'])) {
    header("Location: login");
    exit();
} else {
    $username = $_SESSION['username'];
}

$dbConnection = new DBConnection();
$item = new Item($dbConnection);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    Menu::displayMenus(TRUE);
    echo "<h3>Items starting with '" . htmlspecialchars($_GET['first_char'], ENT_QUOTES) . "'</h3>" . PHP_EOL;
    $item->displayLinks();
    echo "<form id=items_form method='POST'>" . PHP_EOL;
    echo "<div class='grid-container'>" . PHP_EOL;
    $item->displayItems("AND SUBSTR(itemname,1,1)='" . htmlspecialchars($_GET['first_char'], ENT_QUOTES) . "'");
    echo "</div>" . PHP_EOL;
    echo "</form>" . PHP_EOL;
} else { /* POST - a button has been pressed */
    $item->updateItemQuantities($_POST);
    if (isset($_POST['update_items_bttn'])) {
	$user = new User($dbConnection);
	if ($user->getDisplayUpdates($username) == "No") {
	    header("Location: first_char?first_char=" . htmlspecialchars($_GET['first_char'], ENT_QUOTES));
	    exit();
	}
    }
}
?>

</body>
</html>
