<?php
namespace JamesSCrook\Shop;

use \PDOException;

/*
 * shop - Copyright (C) 2017-2018 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */
class Item extends DBConnection
{

    public function __construct()
    {
        $this->dbConnect();
    }

    public function displayLinks()
    {
        try {
            $firstCharSubstring = "SUBSTR(itemname,1,1)";
            $getItemFirstCharsPrepStmt = $this->dbConn->prepare("SELECT DISTINCT $firstCharSubstring FROM item");
            $getItemFirstCharsPrepStmt->execute();
            while ($itemRow = $getItemFirstCharsPrepStmt->fetch()) {
                $first_char = $itemRow[$firstCharSubstring];
                echo "<font size='5'><a href='first_char.php?first_char=" . htmlspecialchars($first_char, ENT_QUOTES) . "'>" . htmlspecialchars($first_char, ENT_QUOTES) . "</a>&nbsp;</font>\n";
            }
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not display any item first characters<br>\n";
        }
        echo "<p>";
    }

    public function getItemRow($itemId)
    {
        try {
            $getItemPrepStmt = $this->dbConn->prepare("SELECT * FROM item WHERE itemid = :itemid");
            $getItemPrepStmt->execute(array(
                'itemid' => $itemId
            ));
            if ($itemRow = $getItemPrepStmt->fetch()) {
                return $itemRow;
            }
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not read item details.<br>\n";
        }
        return NULL;
    }

    private function itemExists($itemName, $unitName)
    {
        try {
            $itemExistsPrepStmt = $this->dbConn->prepare("SELECT unitid FROM item WHERE itemname=:itemname AND unitid=(SELECT unitid FROM unit WHERE unitname=:unitname)");
            $itemExistsPrepStmt->execute(array(
                'itemname' => $itemName,
                'unitname' => $unitName
            ));
            return $itemExistsPrepStmt->fetch();
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not check:<br>'" . htmlspecialchars($itemName, ENT_QUOTES) . "', '" . htmlspecialchars($unitName, ENT_QUOTES) . "'<p>\n";
        }
        return FALSE;
    }

    public function addItem($newItemName, $newUnitName, $newCategoryName, $newNotes, $newUserName)
    {
        if ($this->itemExists($newItemName, $newUnitName)) {
            echo "<span class=failure_symbol>&#x2718; </span>";
            echo "Cannot add this item:<p>\n";
            echo "<table class='table_error'>\n";
            echo "<tr><td>Description</td><td>" . htmlspecialchars($newItemName, ENT_QUOTES) . "</td></tr>\n";
            echo "<tr><td>Unit</td><td>" . htmlspecialchars($newUnitName, ENT_QUOTES) . "</td></tr>\n";
            echo "</table><p>\n";
            echo "because it already exists.\n";
        } else {
            try {
                $addItemPrepStmt = $this->dbConn->prepare("INSERT INTO item (itemname, unitid, categoryid, notes, quantity, addusername, addtime) VALUES (:itemname, (SELECT unitid FROM unit WHERE unitname=:unitname), (SELECT categoryid FROM category WHERE categoryname=:categoryname), :notes, 0, :addusername, NOW())");
                $addItemPrepStmt->execute(array(
                    'itemname' => $newItemName,
                    'unitname' => $newUnitName,
                    'categoryname' => $newCategoryName,
                    'notes' => $newNotes,
                    'addusername' => $newUserName
                ));
                
                echo "<span class=success_symbol>&#x2714; </span>";
                echo "New item added<p>\n";
                echo "<table>\n";
                echo "<tr><td>Description</td><td>" . htmlspecialchars($newItemName, ENT_QUOTES) . "</td></tr>\n";
                echo "<tr><td>Unit</td><td>" . htmlspecialchars($newUnitName, ENT_QUOTES) . "</td></tr>\n";
                echo "<tr><td>Category</td><td>" . htmlspecialchars($newCategoryName, ENT_QUOTES) . "</td></tr>\n";
                echo "<tr><td>Notes</td><td>" . htmlspecialchars($newNotes, ENT_QUOTES) . "</td></tr>\n";
                echo "</table><p>\n";
                
                echo "<form id=ack_new_item method='POST'>\n";
                echo " <button class='bttn' style=background-color:aqua; name='ack_new_item_bttn'>&#x25C0; Back</button><br>\n";
                echo "</form>\n";
            } catch (PDOException $exception) {
                echo "ERROR(" . __FILE__ . "): Could not add item:<br>'" . htmlspecialchars($newItemName, ENT_QUOTES) . "', '" . htmlspecialchars($newUnitName, ENT_QUOTES) . "', '" . htmlspecialchars($newCategoryName, ENT_QUOTES) . "'<p>\n";
            }
        }
    }

    public function displayItems($sqlPredicate)
    {
        try {
            $_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
            $getItemsPrepStmt = $this->dbConn->prepare("SELECT itemid, itemname, unitname, categoryname, notes, quantity FROM item INNER JOIN unit ON unit.unitid = item.unitid INNER JOIN category ON category.categoryid = item.categoryid " . $sqlPredicate . " ORDER BY itemname, unitname");
            $getItemsPrepStmt->execute();
            while ($itemRow = $getItemsPrepStmt->fetch()) {
                echo " <input type='number' name='i_" . htmlspecialchars($itemRow['itemid'], ENT_QUOTES) . "' min='-9999' max='9999'";
                echo " value='" . htmlspecialchars($itemRow['quantity'], ENT_QUOTES) . "'>";
                echo "<a href='change_item.php?itemid=" . htmlspecialchars($itemRow['itemid'], ENT_QUOTES) . "'><abbr title='" . htmlspecialchars($itemRow['categoryname'], ENT_QUOTES) . "'>" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . "</abbr></a>";
                if ($itemRow['notes'] != "") {
                    echo "<abbr title='" . htmlspecialchars($itemRow['notes'], ENT_QUOTES) . "'>&rarr;" . htmlspecialchars($itemRow['unitname'], ENT_QUOTES) . "</abbr><br>\n";
                } else {
                    echo "&#x25CF;" . htmlspecialchars($itemRow['unitname'], ENT_QUOTES) . "<br>\n";
                }
            }
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not display any itmes<br>\n";
        }
    }

    public function displayItemMetaData($itemRow)
    {
        echo "<table>\n";
        echo "<tr><td>Current quantity</td><td>" . $itemRow['quantity'] . "</td></tr>\n";
        echo "<tr><td>Added by</td><td>" . $itemRow['addusername'] . "</td></tr>\n";
        echo "<tr><td>Added</td><td>" . $itemRow['addtime'] . "</td></tr>\n";
        echo "<tr><td>Last changed by</td><td>" . $itemRow['changeusername'] . "</td></tr>\n";
        echo "<tr><td>Last changed</td><td>" . $itemRow['changetime'] . "</td></tr>\n";
        echo "</table>\n";
    }

    /*
     * Note: do NOT replace $_POST[*] in this method - e.g., with parameter(s)!!!
     * There can be several/many of POST values set, so they cannot be passed as parameters.
     */
    public function updateItemQuantities()
    {
        try {
            $getItemsPrepStmt = $this->dbConn->prepare("SELECT itemid, itemname, item.unitid, unitname, quantity FROM item INNER JOIN unit ON item.unitid = unit.unitid ORDER BY itemname, unitname");
            $updateItemPrepStmt = $this->dbConn->prepare("UPDATE item SET quantity=:quantity WHERE itemid=:itemid");
            $updateHistoryPrepStmt = $this->dbConn->prepare("INSERT INTO history (time, username, itemname, unitname, oldquantity, newquantity)
		VALUES(NOW(), :username, :itemname, (SELECT unitname FROM unit WHERE unitid=:unitid), :oldquantity, :newquantity)");
            $getItemsPrepStmt->execute();
            
            echo "<h3>Quantities Just Updated</h3>\n"; // Header and table are not displayed if display
            echo "<table>\n"; // updates are disabled
            echo "<tr><th>Item</th><th>Change</th></tr>\n";
            while ($itemRow = $getItemsPrepStmt->fetch()) {
                $itemKey = "i_" . $itemRow['itemid'];
                if (isset($_POST[$itemKey]) && $itemRow['quantity'] != $_POST[$itemKey]) {
                    if ($_POST[$itemKey] == "") {
                        $_POST[$itemKey] = 0;
                    }
                    echo "<tr><td>" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . "&#x25CF;" . $itemRow['unitname'] . "</td><td>" . htmlspecialchars($itemRow['quantity'], ENT_QUOTES) . "&rarr;" . htmlspecialchars($_POST[$itemKey], ENT_QUOTES) . "</td></tr>\n";
                    $updateItemPrepStmt->execute(array(
                        'quantity' => $_POST[$itemKey],
                        'itemid' => $itemRow['itemid']
                    ));
                    $updateHistoryPrepStmt->execute(array(
                        'username' => $_SESSION['username'],
                        'itemname' => $itemRow['itemname'],
                        'unitid' => $itemRow['unitid'],
                        'oldquantity' => $itemRow['quantity'],
                        'newquantity' => $_POST[$itemKey]
                    ));
                }
            }
            echo "</table><p>\n";
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not update any itmes<br>\n";
        }
    }

    public function displayItemsByCategory()
    {
        $_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
        try {
            $getItemsPrepStmt = $this->dbConn->prepare("SELECT itemid, itemname, unitname, categoryname FROM item INNER JOIN unit ON item.unitid = unit.unitid INNER JOIN category ON item.categoryid = category.categoryid ORDER BY categoryname, itemname, unitname");
            $getItemsPrepStmt->execute();
            echo "<table>\n";
            echo "<tr><th>Category</th><th>Item</th></tr>";
            while ($itemRow = $getItemsPrepStmt->fetch()) {
                echo "<tr><td>" . htmlspecialchars($itemRow['categoryname'], ENT_QUOTES) . "</td><td>" . "<a href='change_item.php?itemid=" . htmlspecialchars($itemRow['itemid'], ENT_QUOTES) . "'>" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . "</a>&#x25CF;" . htmlspecialchars($itemRow['unitname'], ENT_QUOTES) . "</td></tr>\n";
            }
            echo "</table>\n";
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not display any itmes by category<br>\n";
        }
    }

    private function itemExistsWithAnotherItemid($itemName, $unitName, $itemId)
    {
        try {
            $itemExistsWithAnotherItemidPrepStmt = $this->dbConn->prepare("SELECT unitname FROM unit INNER JOIN item WHERE unit.unitid = item.unitid AND itemname=:itemname AND unitname=:unitname AND itemid!=:itemid");
            $itemExistsWithAnotherItemidPrepStmt->execute(array(
                'itemname' => $itemName,
                'unitname' => $unitName,
                'itemid' => $itemId
            ));
            return $itemExistsWithAnotherItemidPrepStmt->fetch();
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not check if this is a duplicate item:<br>'" . htmlspecialchars($itemName, ENT_QUOTES) . "', '" . htmlspecialchars($unitName, ENT_QUOTES) . "'<p>\n";
        }
        return FALSE;
    }

    public function updateItem($itemName, $unitName, $categoryName, $notes, $userName, $itemId)
    {
        if ($this->itemExistsWithAnotherItemid($itemName, $unitName, $itemId)) {
            echo "This item already exists:<p>\n";
            echo "<table class='table_error'>\n";
            echo "<tr><td>Description</td><td>" . htmlspecialchars($itemName, ENT_QUOTES) . "</td></tr>\n";
            echo "<tr><td>Unit</td><td>" . htmlspecialchars($unitName, ENT_QUOTES) . "</td></tr>\n";
            echo "</table><p>\n";
            echo "so it cannot be saved with these values.\n";
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
            } catch (PDOException $exception) {
                echo "ERROR(" . __FILE__ . "): Could not change the item details.<br>\n";
            }
        }
        return FALSE;
    }

    public function deleteItem($itemId)
    {
        try {
            $deleteItemPrepStmt = $this->dbConn->prepare("DELETE FROM item WHERE itemid=:itemid");
            $deleteItemPrepStmt->execute(array(
                'itemid' => $itemId
            ));
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not delete item details.<br>\n";
        }
    }
}
?>
