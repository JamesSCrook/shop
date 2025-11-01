<?php

declare(strict_types=1);
namespace JamesSCrook\Shop;

use PDOException;

/*
 * shop - Copyright (C) 2017-2026 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */
class Item {

    private $dbConn;

    public function __construct($dbConnection) {
	$this->dbConn = $dbConnection->pdo;
    }

    public function displayLinks() : void {
	try {
	    $firstCharSubstring = "SUBSTR(itemname,1,1)";
	    $getItemFirstCharsPrepStmt = $this->dbConn->prepare("SELECT DISTINCT $firstCharSubstring FROM item");
	    $getItemFirstCharsPrepStmt->execute();
	    echo "<div>" . PHP_EOL;
	    while ($itemRow = $getItemFirstCharsPrepStmt->fetch()) {
		$first_char = $itemRow[$firstCharSubstring];
		echo " <a class='first-char' href='first_char?first_char=" . htmlspecialchars($first_char, ENT_QUOTES) . "'>" . htmlspecialchars($first_char, ENT_QUOTES) . "</a>" . PHP_EOL;
	    }
	    echo "</div>" . PHP_EOL;
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not display any item first characters.<p>" . PHP_EOL;
	}
    }

    public function getItemRow(int $itemId) : mixed {
	try {
	    $getItemPrepStmt = $this->dbConn->prepare("SELECT * FROM item WHERE itemid = :itemid");
	    $getItemPrepStmt->execute(array(
		'itemid' => $itemId
	    ));
	    if ($itemRow = $getItemPrepStmt->fetch()) {
		return $itemRow;
	    }
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not read item details.<p>" . PHP_EOL;
	}
	return -1;
    }

    private function getItemId(string $itemName, string $unitName) : int {
	try {
	    $getItemPrepStmt = $this->dbConn->prepare("SELECT itemid FROM item WHERE itemname=:itemname AND unitid=(SELECT unitid FROM unit WHERE unitname=:unitname)");
	    $getItemPrepStmt->execute(array(
		'itemname' => $itemName,
		'unitname' => $unitName
	    ));
	    if ($itemRow = $getItemPrepStmt->fetch()) {
		return intval($itemRow['itemid']);
	    }
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not check:<br>'" . htmlspecialchars($itemName, ENT_QUOTES) . "', '" . htmlspecialchars($unitName, ENT_QUOTES) . "'<p>" . PHP_EOL;
	}
	return -1;
    }

    public function addItem(string $newItemName, string $newUnitName, string $newCategoryName, string $newNotes, float $quantity, string $newUserName) : void {
	if ($this->getItemId($newItemName, $newUnitName) != -1) {
	    echo "<br>" . Utils::failureSymbol() . "Cannot add this item:<p>" . PHP_EOL;
	    echo "<table class='table-error'>" . PHP_EOL;
	    echo "<tr><td>Description</td><td>" . htmlspecialchars($newItemName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
	    echo "<tr><td>" . Constant::UNITDESCRIPTION . "</td><td>" . htmlspecialchars($newUnitName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
	    echo "</table><p>" . PHP_EOL;
	    echo "because it already exists." . PHP_EOL;
	} else {
	    try {
		if ($quantity == 0.0) {
		    $addItemPrepStmt = $this->dbConn->prepare("INSERT INTO item (itemname, unitid, categoryid, notes, quantity, addusername, addtime, updatecount) VALUES (:itemname, (SELECT unitid FROM unit WHERE unitname=:unitname), (SELECT categoryid FROM category WHERE categoryname=:categoryname), :notes, :quantity, :addusername, NOW(), 0)");
		} else {
		    $addItemPrepStmt = $this->dbConn->prepare("INSERT INTO item (itemname, unitid, categoryid, notes, quantity, addusername, addtime, updatecount, lastupdatetime) VALUES (:itemname, (SELECT unitid FROM unit WHERE unitname=:unitname), (SELECT categoryid FROM category WHERE categoryname=:categoryname), :notes, :quantity, :addusername, NOW(), 1, NOW())");
		}
		$addItemPrepStmt->execute(array(
		    'itemname' => $newItemName,
		    'unitname' => $newUnitName,
		    'categoryname' => $newCategoryName,
		    'notes' => $newNotes,
		    'quantity' => $quantity,
		    'addusername' => $newUserName
		));

		echo "<br>" . Utils::successSymbol() . "New item added<p>" . PHP_EOL;
		echo "<table>" . PHP_EOL;
		echo "<tr><td>Description</td><td>" . htmlspecialchars($newItemName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
		echo "<tr><td>" . Constant::UNITDESCRIPTION . "</td><td>" . htmlspecialchars($newUnitName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
		echo "<tr><td>" . Constant::CATEGORYDESCRIPTION . "</td><td>" . htmlspecialchars($newCategoryName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
		echo "<tr><td>Notes</td><td>" . htmlspecialchars($newNotes, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
		echo "<tr><td>Quantity</td><td>$quantity</td></tr>" . PHP_EOL;
		echo "</table><p>" . PHP_EOL;
	    } catch(PDOException $exception) {
		echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
		echo "Could not add item:<br>'" . htmlspecialchars($newItemName, ENT_QUOTES) . "', '" . htmlspecialchars($newUnitName, ENT_QUOTES) . "', '" . htmlspecialchars($newCategoryName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
	    }
	}
    }

    public function updateItemHistory(string $userName, string $itemName, string $unitName, float $oldQuantity, float $newQuantity) : void {
	try {
	    $updateHistoryPrepStmt = $this->dbConn->prepare("INSERT INTO history (time, username, itemname, unitname, oldquantity, newquantity)
		VALUES(NOW(), :username, :itemname, :unitname, :oldquantity, :newquantity)");
	    $updateHistoryPrepStmt->execute(array(
		'username' => $userName,
		'itemname' => $itemName,
		'unitname' => $unitName,
		'oldquantity' => $oldQuantity,
		'newquantity' => $newQuantity
	    ));
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not display any itmes.<p>" . PHP_EOL;
	}
    }

    public function displayItems(string $sqlPredicate) : int {
	$itemCount = 0;
	try {
	    $_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
	    $getItemsPrepStmt = $this->dbConn->prepare("SELECT itemid, itemname, unitname, categoryname, notes, quantity FROM item INNER JOIN unit ON unit.unitid = item.unitid INNER JOIN category ON category.categoryid = item.categoryid " . $sqlPredicate . " ORDER BY itemname, unitname");
	    $getItemsPrepStmt->execute();
	    while ($itemRow = $getItemsPrepStmt->fetch()) {
		echo " <div class='grid-item'>" . PHP_EOL;
		echo "  <div class='sub-grid-container'>" . PHP_EOL;
		echo "   <input type='number' class='item-quantity input-color' name='i_" . $itemRow['itemid'] . "' min='-9999' max='9999' step='any'";
		echo " value='" . (floatval($itemRow['quantity']) != 0.0 ? $itemRow['quantity'] : "") . "'>" . PHP_EOL;
		echo "   <a href='change_item?itemid=" . $itemRow['itemid'] . "' class='grid-item-link'><abbr title='" . htmlspecialchars($itemRow['categoryname'], ENT_QUOTES) . "'>" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . "</abbr>";
		if ($itemRow['notes'] != "") {
		    echo Utils::separatorWithTipSymbol() . "<abbr title='" . $itemRow['notes'] . "'>" . htmlspecialchars($itemRow['unitname'], ENT_QUOTES) . "</abbr></a>" . PHP_EOL;
		} else {
		    echo Utils::separatorSymbol() . $itemRow['unitname'] . "</a>" . PHP_EOL;
		}
		echo "  </div>" . PHP_EOL;
		echo " </div>" . PHP_EOL;
		$itemCount++;
	    }
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not display any itmes.<p>" . PHP_EOL;
	}
	return $itemCount;
    }

    public function displayItemMetaData($itemRow) : void {
	echo "<table>" . PHP_EOL;
	echo "<tr><td>Current quantity</td><td>" . $itemRow['quantity'] . "</td></tr>" . PHP_EOL;
	echo "<tr><td>Added by</td><td>" . $itemRow['addusername'] . "</td></tr>" . PHP_EOL;
	echo "<tr><td>Added</td><td>" . $itemRow['addtime'] . "</td></tr>" . PHP_EOL;
	echo "<tr><td>Last changed by</td><td>" . $itemRow['changeusername'] . "</td></tr>" . PHP_EOL;
	echo "<tr><td>Last changed</td><td>" . $itemRow['changetime'] . "</td></tr>" . PHP_EOL;
	echo "<tr><td>Times updated</td><td>" . $itemRow['updatecount'] . "</td></tr>" . PHP_EOL;
	echo "<tr><td>Last update</td><td>" . $itemRow['lastupdatetime'] . "</td></tr>" . PHP_EOL;
	echo "</table>" . PHP_EOL;
    }

    /* updateItemQuantities is called with $_POST from index and first_char. Here is an example when 2 (500 and 89) items have been entered:
     * array(3) { ["update_items_bttn"]=> string(0) "" ["i_500"]=> string(1) "1" ["i_89"]=> string(1) "3" }
    */
    public function updateItemQuantities(&$itemIdTable) : void {
	try {
	    $getItemsPrepStmt = $this->dbConn->prepare("SELECT itemid, itemname, item.unitid, unitname, quantity FROM item INNER JOIN unit ON item.unitid = unit.unitid ORDER BY itemname, unitname");
	    $getItemsPrepStmt->execute();

	    // Update details (the table below) is always sent to the browser, but the user will only see it if "display update notifications" is enabled.
	    Menu::displayMenus(FALSE);
	    echo "<h3>Quantities Just Updated</h3>" . PHP_EOL;
	    echo "<table>" . PHP_EOL;
	    echo "<tr><th>Item</th><th>Change</th></tr>" . PHP_EOL;
	    while ($itemRow = $getItemsPrepStmt->fetch()) {
		$itemKey = "i_" . $itemRow['itemid'];
		if (isset($itemIdTable[$itemKey])) {	// If this item has been updated

		    if ($itemIdTable[$itemKey] == "") {	// Force blanking the field
			$itemIdTable[$itemKey] = 0;	// to behave the same as explicitly setting it to 0
		    }

		    // $itemIdTable is an array of the POSTed quantity value(s) where the index is the itemKey.
		    if ($itemIdTable[$itemKey] != $itemRow['quantity']) {	// If the POSTed quantity has changed, update the DB.
			echo "<tr><td>" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . Utils::separatorSymbol() . $itemRow['unitname'] . "</td><td>" . $itemRow['quantity'] . Utils::changeValueSymbol() . $itemIdTable[$itemKey] . "</td></tr>" . PHP_EOL;
			$updateCountClause = floatval($itemIdTable[$itemKey]) != floatval($itemRow['quantity']) ? "updatecount=updatecount+1," : "";
			$updateItemPrepStmt = $this->dbConn->prepare("UPDATE item SET quantity=:quantity, $updateCountClause lastupdatetime=NOW() WHERE itemid=:itemid");

			$updateItemPrepStmt->execute(array(
			    'quantity' => $itemIdTable[$itemKey],
			    'itemid' => $itemRow['itemid']
			));
			$this->updateItemHistory($_SESSION['username'], $itemRow['itemname'], $itemRow['unitname'], floatval($itemRow['quantity']), floatval($itemIdTable[$itemKey]));
		    }
		}
	    }
	    echo "</table><p>" . PHP_EOL;
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not update any itmes.<p>" . PHP_EOL;
	}
    }

    private function itemExistsWithAnotherItemid(string $itemName, string $unitName, int $itemId) : mixed {
	try {
	    $itemExistsWithAnotherItemidPrepStmt = $this->dbConn->prepare("SELECT unitname FROM unit INNER JOIN item WHERE unit.unitid = item.unitid AND itemname=:itemname AND unitname=:unitname AND itemid!=:itemid");
	    $itemExistsWithAnotherItemidPrepStmt->execute(array(
		'itemname' => $itemName,
		'unitname' => $unitName,
		'itemid' => $itemId
	    ));
	    return $itemExistsWithAnotherItemidPrepStmt->fetch();
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not check if this is a duplicate item:<br>'" . htmlspecialchars($itemName, ENT_QUOTES) . "', '" . htmlspecialchars($unitName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
	}
	return FALSE;
    }

    public function changeItemQuantity(int $itemId, string $userName, string $itemName, string $unitName, string $categoryName, float $currentQuantity, float $newQuantity) : void {
	try {
	    $updateCountClause = $newQuantity != $currentQuantity ? ", updatecount=updatecount+1" : "";
	    $updateItemQuantityPrepStmt = $this->dbConn->prepare("UPDATE item SET quantity=:quantity, lastupdatetime=NOW() $updateCountClause WHERE itemid=:itemid");
	    $updateItemQuantityPrepStmt->execute(array(
		'quantity' => $newQuantity,
		'itemid' => $itemId
	    ));
	    $this->updateItemHistory($userName, $itemName, $unitName, $currentQuantity, $newQuantity);
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not change the item details.<p>" . PHP_EOL;
	}
    }

    public function changeItem(string $itemName, string $unitName, string $categoryName, string $notes, string $userName, int $itemId) : bool {
	if ($this->itemExistsWithAnotherItemid($itemName, $unitName, $itemId)) {
	    echo "This item already exists:<p>" . PHP_EOL;
	    echo "<table class='table-error'>" . PHP_EOL;
	    echo "<tr><td>Description</td><td>" . htmlspecialchars($itemName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
	    echo "<tr><td>" . Constant::UNITDESCRIPTION . "</td><td>" . htmlspecialchars($unitName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
	    echo "</table><p>" . PHP_EOL;
	    echo "so it cannot be saved with these values." . PHP_EOL;
	} else {
	    try {
		$updateItemPrepStmt = $this->dbConn->prepare("UPDATE item SET itemname=:itemname, unitid=(SELECT unitid FROM unit WHERE unitname=:unitname), categoryid=(SELECT categoryid FROM category WHERE categoryname=:categoryname), notes=:notes, changeusername=:changeusername, changetime = NOW() WHERE itemid=:itemid");
		$updateItemPrepStmt->execute(array(
		    'itemname' => $itemName,
		    'unitname' => $unitName,
		    'categoryname' => $categoryName,
		    'notes' => $notes,
		    'changeusername' => $userName,
		    'itemid' => $itemId
		));
		return TRUE;
	    } catch(PDOException $exception) {
		echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
		echo "Could not change the item details.<p>" . PHP_EOL;
	    }
	}
	return FALSE;
    }

    public function deleteItem(int $itemId) : void {
	try {
	    $deleteItemPrepStmt = $this->dbConn->prepare("DELETE FROM item WHERE itemid=:itemid");
	    $deleteItemPrepStmt->execute(array(
		'itemid' => $itemId
	    ));
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not delete item details.<p>" . PHP_EOL;
	}
    }
}
?>
