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
 * Change an item's details: itemname, unitname, categoryname and notes.
 * The user and timestamp of when this item was created and last changed are also shown.
 */
session_start();
require_once "Classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");
$pageSubtitle = "Change an Item";
Utils::topOfPageHTML(": $pageSubtitle");

if (!isset($_SESSION['username'])) {
    header("Location: login");
    exit();
} else {
    $username = $_SESSION['username'];
}

Menu::displayMenus(FALSE);

echo "<h3>$pageSubtitle (" . htmlspecialchars($username, ENT_QUOTES) . ")</h3>" . PHP_EOL;

$dbConnection = new DBConnection();
$item = new Item($dbConnection);
$unit = new Unit($dbConnection);
$category = new Category($dbConnection);

if (isset($_SESSION['previous_page'])) {
    $previousPage = "Location: " . $_SESSION['previous_page'];
} else {
    $previousPage = "Location: index"; // This should never happen!
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    $itemRow = $item->getItemRow($_GET['itemid']);
    if ($itemRow != -1) {
	echo "<form method='POST'>" . PHP_EOL;
	echo "Changing '" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . "'<p>" . PHP_EOL;
	echo "<input type='text' class='enter-input-text input-color' name='itemname' placeholder='Description (required)' pattern='.{1,30}' value='" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . "'>";
	echo "<select class='enter-select input-color' name='unitname'><option value='' disabled>" . Constant::UNITDESCRIPTION . " (required)</option>";
	$unit->displayUnitDropDownList($itemRow['unitid']);
	echo "</select>";
	echo "<select class='enter-select input-color' name='categoryname'><option value='' disabled>" . Constant::CATEGORYDESCRIPTION . " (required)</option>";
	$category->displayCategoryDropDownList($itemRow['categoryid']);
	echo "</select>";
	echo "<input type='text' class='enter-input-text input-color' name='notes' placeholder='Notes (optional)' value='" . htmlspecialchars($itemRow['notes'], ENT_QUOTES) . "'>";
	echo "<input type='number' class='enter-input-number input-color' name='quantity' placeholder='Quanitity (optional)' min='-9999' max='9999' step='any'";
	echo " value='" . (floatval($itemRow['quantity']) != 0.0 ? $itemRow['quantity'] : "") . "'>" . PHP_EOL;
	echo "<button class='bttn change-color' name='change_item_bttn'>" . Utils::changeSymbol() . " Change Item</button>";
	$item->displayItemMetaData($itemRow);
	echo "<button class='bttn delete-color' name='delete_item_bttn'>" . Utils::deleteSymbol() . " Delete Item</button>";
	echo "</form>" . PHP_EOL;

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
	    $quantity = isset($_POST['quantity']) && floatval($_POST['quantity']) != 0.0  ? floatval($_POST['quantity']) : 0.0;
	    if ($item->updateItem(mb_strtoupper(mb_substr($itemName, 0, 1)) . mb_substr($itemName, 1), $_POST['unitname'], $_POST['categoryname'], $notes, $quantity, $username, $_SESSION['itemid'])) {
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
	echo "<p>UNEXPECTED ERROR: in file: " . basename(__FILE__) . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . PHP_EOL;
    }
}
?>

</body>
</html>
