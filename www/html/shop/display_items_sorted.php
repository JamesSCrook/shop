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
<title>Shop: Display Items Sorted</title>
<link rel='stylesheet' media='screen' href='shop.css'>
</head>
<body>

<?php
/*
 * Display items sorted by any of the 4 columns - both ascending and descending.
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

echo "<h3>Display Items Sorted (" . htmlspecialchars($username, ENT_QUOTES) . ")</h3>" . PHP_EOL;

if (isset($_SESSION['userdata'])) {
	$userData = unserialize($_SESSION['userdata']);
} else {
	$userData = new UserData();
}

$itemList = new ItemList();
if (isset($_GET['sortby'])) {
	$sortByColumnName = $_GET['sortby'];
	if ($sortByColumnName == $userData->getDisplayItemsSortByColumnName()) {
		$sortAscendingFlag = $userData->getDisplayItemsSortByAscendingFlag();
		$sortAscendingFlag = !$sortAscendingFlag;
		$userData->setDisplayItemsSortByAscendingFlag($sortAscendingFlag);
	} else {
		$userData->setDisplayItemsSortByColumnName($sortByColumnName);
		$sortAscendingFlag = TRUE;
		$userData->setDisplayItemsSortByAscendingFlag($sortAscendingFlag);
	}
} else {
	$sortByColumnName = 'itemname';
	$sortAscendingFlag = TRUE;
	$userData->setDisplayItemsSortByColumnName($sortByColumnName);
	$userData->setDisplayItemsSortByAscendingFlag($sortAscendingFlag);
}
$itemList->displayItemsSorted($sortByColumnName, $sortAscendingFlag);
$_SESSION['userdata'] = serialize($userData);
?>

</body>
</html>
