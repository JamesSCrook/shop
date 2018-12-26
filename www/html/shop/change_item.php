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
<title>Shop: Change an Item</title>
<link rel='stylesheet' media='screen' href='shop.css'>
</head>
<body>

	<h3>Change an Item</h3>

<?php
/*
 * Change an item's details: itemname, unitname, categoryname and notes.
 * The user and timestamp of when this item was created and last changed are also shown.
 */
session_start();
require_once dirname(dirname(dirname(__FILE__))) . dirname($_SERVER["PHP_SELF"]) . "_db_conn.php";
require_once "classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");

if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
}

Menu::displayMenus(FALSE);
$item = new Item();
$unit = new Unit();
$category = new Category();

if (isset($_SESSION['previous_page'])) {
	$previousPage = "Location: " . $_SESSION['previous_page'];
} else {
	$previousPage = "Location: index.php"; // This should never happen!
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

	$itemRow = $item->getItemRow($_GET['itemid']);
	if ($itemRow != NULL) {
		echo "<form method='POST'>" . PHP_EOL;
		echo "Description (required)<br><input type='text' name='itemname' placeholder='1UC+0-29*' pattern='.{1,30}' value='" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . "'><p>" . PHP_EOL;

		echo "Unit (required)<br><select name='unitname'>" . PHP_EOL;
		$unit->displayUnitDropDownList($itemRow['unitid']);
		echo "</select><p>" . PHP_EOL;

		echo "Category (required)<br><select name='categoryname'>" . PHP_EOL;
		$category->displayCategoryDropDownList($itemRow['categoryid']);
		echo "</select><p>" . PHP_EOL;
		echo "Notes<br><input type='text' name='notes' value='" . htmlspecialchars($itemRow['notes'], ENT_QUOTES) . "'><p>" . PHP_EOL;
		echo "<button class='bttn' style=background-color:lightgreen; name='change_item_bttn'>&#x270E; Change Item</button><br>" . PHP_EOL;
		echo "<button class='bttn' style=background-color:salmon; name='delete_item_bttn'>&#x1F5D1; Delete Item</button>(!)" . PHP_EOL;
		echo "</form>" . PHP_EOL;

		$item->displayItemMetaData($itemRow);

		$_SESSION['itemid'] = $itemRow['itemid'];
	} else {
		header($previousPage);
		exit();
	}
} else { /* POST - a button has been pressed */
	if (isset($_POST['change_item_bttn'])) {
		if ($_POST['itemname'] != "" && $_POST['unitname'] != "" && $_POST['categoryname'] != "") {
			$itemName = preg_replace('/\s+/', ' ', trim($_POST['itemname']));
			$notes = preg_replace('/\s+/', ' ', trim($_POST['notes']));
			if ($item->updateItem(mb_strtoupper(mb_substr($itemName, 0, 1)) . mb_substr($itemName, 1), $_POST['unitname'], $_POST['categoryname'], $notes, $_SESSION['username'], $_SESSION['itemid'])) {
				header($previousPage);
				exit();
			}
		} else {
			echo "Description, unit and category are all required!<p>" . PHP_EOL;
		}
	} else if (isset($_POST['delete_item_bttn'])) {
		$item->deleteItem($_SESSION['itemid']);
		header($previousPage);
		exit();
	} else {
		echo "Unexpected error in " . $_SERVER["PHP_SELF"] . "<br>";
	}
}
?>

</body>
</html>
<!-- shop - Copyright (C) 2017-2018 James S. Crook - GPL3+ -->
