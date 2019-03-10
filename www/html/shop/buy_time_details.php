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
<title>Shop: Buy Time Details</title>
<link rel='stylesheet' media='screen' href='shop.css'>
</head>
<body>

<?php
/*
 *
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

echo "<h3>Buy Time Details (" . htmlspecialchars($username, ENT_QUOTES) . ")</h3>" . PHP_EOL;

$itemList = new ItemList();
if (isset($_GET['sortby'])) {
	$itemList->displayBuyTimeDetails($_GET['sortby']);
} else {
	$itemList->displayBuyTimeDetails("last_buy_time_asc");
}

?>

</body>
</html>
<!-- shop - Copyright (C) 2017-2018 James S. Crook - GPL3+ -->
