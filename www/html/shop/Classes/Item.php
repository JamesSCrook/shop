<?php

namespace JamesSCrook\Shop;

use PDOException;

/*
 * shop - Copyright (C) 2017-2024 James S. Crook
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

    public function displayLinks() {
	try {
	    $firstCharSubstring = "SUBSTR(itemname,1,1)";
	    $getItemFirstCharsPrepStmt = $this->dbConn->prepare("SELECT DISTINCT $firstCharSubstring FROM item");
	    $getItemFirstCharsPrepStmt->execute();
	    echo "<div>" . PHP_EOL;
	    while ($itemRow = $getItemFirstCharsPrepStmt->fetch()) {
		$first_char = $itemRow[$firstCharSubstring];
		echo "<a class='first-char' href='first_char?first_char=" . htmlspecialchars($first_char, ENT_QUOTES) . "'>" . htmlspecialchars($first_char, ENT_QUOTES) . "</a>";
	    }
	    echo "</div>" . PHP_EOL;
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not display any item first characters.<p>" . PHP_EOL;
	}
    }

    public function getItemRow($itemId) {
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
	return NULL;
    }

    private function getItemId($itemName, $unitName) {
	try {
	    $getItemPrepStmt = $this->dbConn->prepare("SELECT itemid FROM item WHERE itemname=:itemname AND unitid=(SELECT unitid FROM unit WHERE unitname=:unitname)");
	    $getItemPrepStmt->execute(array(
		'itemname' => $itemName,
		'unitname' => $unitName
	    ));
	    if ($itemRow = $getItemPrepStmt->fetch()) {
		return $itemRow['itemid'];
	    }
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not check:<br>'" . htmlspecialchars($itemName, ENT_QUOTES) . "', '" . htmlspecialchars($unitName, ENT_QUOTES) . "'<p>" . PHP_EOL;
	}
	return NULL;
    }

    public function addItem($newItemName, $newUnitName, $newCategoryName, $newNotes, $quantity, $newUserName) {
	if ($this->getItemId($newItemName, $newUnitName) != NULL) {
	    echo "<br>" . Utils::failureSymbol() . "Cannot add this item:<p>" . PHP_EOL;
	    echo "<table class='table-error'>" . PHP_EOL;
	    echo "<tr><td>Description</td><td>" . htmlspecialchars($newItemName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
	    echo "<tr><td>Unit</td><td>" . htmlspecialchars($newUnitName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
	    echo "</table><p>" . PHP_EOL;
	    echo "because it already exists." . PHP_EOL;
	} else {
	    try {
		$addItemPrepStmt = $this->dbConn->prepare("INSERT INTO item (itemname, unitid, categoryid, notes, quantity, addusername, addtime, buycount) VALUES (:itemname, (SELECT unitid FROM unit WHERE unitname=:unitname), (SELECT categoryid FROM category WHERE categoryname=:categoryname), :notes, :quantity, :addusername, NOW(), 0)");
		$addItemPrepStmt->execute(array(
		    'itemname' => $newItemName,
		    'unitname' => $newUnitName,
		    'categoryname' => $newCategoryName,
		    'notes' => $newNotes,
		    'quantity' => $quantity,
		    'addusername' => $newUserName
		));

		$this->updateNewItem($newItemName, $newUnitName, $newCategoryName, $quantity, $newUserName);

		echo "<br>" . Utils::successSymbol() . "New item added<p>" . PHP_EOL;
		echo "<table>" . PHP_EOL;
		echo "<tr><td>Description</td><td>" . htmlspecialchars($newItemName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
		echo "<tr><td>Unit</td><td>" . htmlspecialchars($newUnitName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
		echo "<tr><td>Category</td><td>" . htmlspecialchars($newCategoryName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
		echo "<tr><td>Notes</td><td>" . htmlspecialchars($newNotes, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
		echo "<tr><td>Quantity</td><td>$quantity</td></tr>" . PHP_EOL;
		echo "</table><p>" . PHP_EOL;
	    } catch(PDOException $exception) {
		echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
		echo "Could not add item:<br>'" . htmlspecialchars($newItemName, ENT_QUOTES) . "', '" . htmlspecialchars($newUnitName, ENT_QUOTES) . "', '" . htmlspecialchars($newCategoryName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
	    }
	}
    }

    private function updateNewItem($itemName, $unitName, $categoryName, $quantity, $userName) {
	try {
	    $itemId = $this->getItemId($itemName, $unitName);
	    $updateItemPrepStmt = $this->dbConn->prepare("UPDATE item SET quantity=:quantity, buycount=buycount+1, lastbuytime=NOW() WHERE itemid=:itemid");
	    $updateItemPrepStmt->execute(array(
		'quantity' => $quantity,
		'itemid' => $itemId
	    ));

	    if ($quantity != 0) { // Only update the history if the new item has been added with a non-zero quantity.
		$updateHistoryPrepStmt = $this->dbConn->prepare("INSERT INTO history (time, username, itemname, unitname, oldquantity, newquantity)
		    VALUES(NOW(), :username, :itemname, :unitname, 0, :quantity)");
		$updateHistoryPrepStmt->execute(array(
		    'username' => $userName,
		    'itemname' => $itemName,
		    'unitname' => $unitName,
		    'quantity' => $quantity
		));
	    }
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not update a new item:<br>'" . htmlspecialchars($itemName, ENT_QUOTES) . "', '" . htmlspecialchars($unitName, ENT_QUOTES) . "', '" . htmlspecialchars($categoryName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
	}
    }

    public function displayItems($sqlPredicate) {
	try {
	    $_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
	    $getItemsPrepStmt = $this->dbConn->prepare("SELECT itemid, itemname, unitname, categoryname, notes, quantity FROM item INNER JOIN unit ON unit.unitid = item.unitid INNER JOIN category ON category.categoryid = item.categoryid " . $sqlPredicate . " ORDER BY itemname, unitname");
	    $getItemsPrepStmt->execute();
	    while ($itemRow = $getItemsPrepStmt->fetch()) {
		echo " <div class='grid-item'>" . PHP_EOL;
		echo "  <div class='sub-grid-container'>" . PHP_EOL;
		echo "   <input type='number' class='item-quantity input-color' name='i_" . htmlspecialchars($itemRow['itemid'], ENT_QUOTES) . "' min='-9999' max='9999' step='any'";
		echo " value='" . ($itemRow['quantity'] != 0 ? htmlspecialchars($itemRow['quantity'], ENT_QUOTES) : "") . "'>" . PHP_EOL;
		echo "   <a href='change_item?itemid=" . htmlspecialchars($itemRow['itemid'], ENT_QUOTES) . "' class='grid-item-link'><abbr title='" . htmlspecialchars($itemRow['categoryname'], ENT_QUOTES) . "'>" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . "</abbr>";
		if ($itemRow['notes'] != "") {
		    echo Utils::separatorWithTipSymbol() . "<abbr title='" . htmlspecialchars($itemRow['notes'], ENT_QUOTES) . "'>" . htmlspecialchars($itemRow['unitname'], ENT_QUOTES) . "</abbr></a>" . PHP_EOL;
		} else {
		    echo Utils::separatorSymbol() . htmlspecialchars($itemRow['unitname'], ENT_QUOTES) . "</a>" . PHP_EOL;
		}
		echo "  </div>" . PHP_EOL;
		echo " </div>" . PHP_EOL;
	    }
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not display any itmes.<p>" . PHP_EOL;
	}
    }

    public function displayItemMetaData($itemRow) {
	echo "<table>" . PHP_EOL;
	echo "<tr><td>Current quantity</td><td>" . $itemRow['quantity'] . "</td></tr>" . PHP_EOL;
	echo "<tr><td>Added by</td><td>" . $itemRow['addusername'] . "</td></tr>" . PHP_EOL;
	echo "<tr><td>Added</td><td>" . $itemRow['addtime'] . "</td></tr>" . PHP_EOL;
	echo "<tr><td>Last changed by</td><td>" . $itemRow['changeusername'] . "</td></tr>" . PHP_EOL;
	echo "<tr><td>Last changed</td><td>" . $itemRow['changetime'] . "</td></tr>" . PHP_EOL;
	echo "<tr><td>Times bought</td><td>" . $itemRow['buycount'] . "</td></tr>" . PHP_EOL;
	echo "<tr><td>Last time bought</td><td>" . $itemRow['lastbuytime'] . "</td></tr>" . PHP_EOL;
	echo "</table>" . PHP_EOL;
    }

    /* This is called with $_POST from index and first_char */
    public function updateItemQuantities(&$itemIdTable) {
	try {
	    $getItemsPrepStmt = $this->dbConn->prepare("SELECT itemid, itemname, item.unitid, unitname, quantity FROM item INNER JOIN unit ON item.unitid = unit.unitid ORDER BY itemname, unitname");
	    $updateHistoryPrepStmt = $this->dbConn->prepare("INSERT INTO history (time, username, itemname, unitname, oldquantity, newquantity)
		VALUES(NOW(), :username, :itemname, (SELECT unitname FROM unit WHERE unitid=:unitid), :oldquantity, :newquantity)");
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
			$itemIdTable[$itemKey] = 0;		// to behave the same as explicitly setting it to 0
		    }

		    if ($itemRow['quantity'] != $itemIdTable[$itemKey]) {	// If the quantity has changed, update the DB.
			echo "<tr><td>" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . Utils::separatorSymbol() . $itemRow['unitname'] . "</td><td>" . htmlspecialchars($itemRow['quantity'], ENT_QUOTES) . Utils::changeValueSymbol() . htmlspecialchars($itemIdTable[$itemKey], ENT_QUOTES) . "</td></tr>" . PHP_EOL;

			if (abs($itemIdTable[$itemKey]) < abs($itemRow['quantity'])) {
			    $updateItemPrepStmt = $this->dbConn->prepare("UPDATE item SET quantity=:quantity, buycount=buycount+1, lastbuytime=NOW() WHERE itemid=:itemid");
			} else {
			    $updateItemPrepStmt = $this->dbConn->prepare("UPDATE item SET quantity=:quantity WHERE itemid=:itemid");
			}
			$updateItemPrepStmt->execute(array(
			    'quantity' => $itemIdTable[$itemKey],
			    'itemid' => $itemRow['itemid']
			));

			$updateHistoryPrepStmt->execute(array(
			    'username' => $_SESSION['username'],
			    'itemname' => $itemRow['itemname'],
			    'unitid' => $itemRow['unitid'],
			    'oldquantity' => $itemRow['quantity'],
			    'newquantity' => $itemIdTable[$itemKey]
			));
		    }
		}
	    }
	    echo "</table><p>" . PHP_EOL;
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not update any itmes.<p>" . PHP_EOL;
	}
    }

    private function itemExistsWithAnotherItemid($itemName, $unitName, $itemId) {
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

    public function updateItem($itemName, $unitName, $categoryName, $notes, $userName, $itemId) {
	if ($this->itemExistsWithAnotherItemid($itemName, $unitName, $itemId)) {
	    echo "This item already exists:<p>" . PHP_EOL;
	    echo "<table class='table-error'>" . PHP_EOL;
	    echo "<tr><td>Description</td><td>" . htmlspecialchars($itemName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
	    echo "<tr><td>Unit</td><td>" . htmlspecialchars($unitName, ENT_QUOTES) . "</td></tr>" . PHP_EOL;
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

    public function deleteItem($itemId) {
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
