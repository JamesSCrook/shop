<?php

namespace JamesSCrook\Shop;

use PDOException;

/*
 * shop - Copyright (C) 2017-2023 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */
class ItemList extends DBConnection {

	public function __construct() {
		$this->dbConnect();
	}

	public function displayItemsByCategory() {
		$_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
		try {
			$getItemsPrepStmt = $this->dbConn->prepare("SELECT itemid, itemname, unitname, categoryname, notes FROM item INNER JOIN unit ON item.unitid = unit.unitid INNER JOIN category ON item.categoryid = category.categoryid ORDER BY categoryname, itemname, unitname");
			$getItemsPrepStmt->execute();
			echo "<table>" . PHP_EOL;
			echo " <tr><th>Category</th><th>Item" . Utils::separatorSymbol() . "Unit</th><th>Notes</th></tr>" . PHP_EOL;

			while ($itemRow = $getItemsPrepStmt->fetch()) {
				echo "<tr><td>" . htmlspecialchars($itemRow['categoryname'], ENT_QUOTES) . "</td>";
				echo "<td><a class='clickablecell' href='change_item?itemid=" . htmlspecialchars($itemRow['itemid'], ENT_QUOTES) . "'>" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . Utils::separatorSymbol() . htmlspecialchars($itemRow['unitname'], ENT_QUOTES) . "</a></td>";
				echo "<td>" . htmlspecialchars($itemRow['notes'], ENT_QUOTES) . "</td><tr>" . PHP_EOL;
			}

			echo "</table>" . PHP_EOL;
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not display any itmes by category.<p>" . PHP_EOL;
		}
	}

	public function displayItemsSorted($columnName, $ascendingFlag) {
		$_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];

		$sortDirectionSymbolTable['itemname'] = "";
		$sortDirectionSymbolTable['categoryname'] = "";
		$sortDirectionSymbolTable['buycount'] = "";
		$sortDirectionSymbolTable['lastbuytime'] = "";
		$sortDirectionSymbolTable[$columnName] = $ascendingFlag ? Utils::sortAscendingSymbol() : Utils::sortDescendingSymbol();

		switch ($columnName) {
			case 'itemname':
				$orderBySQLargs = $ascendingFlag ? "itemname ASC, unitname ASC" : "itemname DESC, unitname DESC";
				break;
			case 'categoryname':
				$orderBySQLargs = $ascendingFlag ? "categoryname ASC, itemname, unitname" : "categoryname DESC, itemname, unitname";
				break;
			case 'buycount':
				$orderBySQLargs = $ascendingFlag ? "buycount ASC, itemname, unitname ASC" : "buycount DESC, itemname, unitname";
				break;
			case 'lastbuytime':
				$orderBySQLargs = $ascendingFlag ? "lastbuytime ASC, itemname, unitname" : "lastbuytime DESC, itemname, unitname";
				break;
			default:
				$orderBySQLargs = "itemname ASC, unitname ASC";
				break;
		}
		try {
			$getItemsPrepStmt = $this->dbConn->prepare("SELECT itemname, itemid, unitname, categoryname, buycount, lastbuytime FROM item INNER JOIN unit ON item.unitid = unit.unitid INNER JOIN category ON item.categoryid = category.categoryid ORDER BY " . $orderBySQLargs);
			$getItemsPrepStmt->execute();

			echo "<table>" . PHP_EOL;
			echo " <tr>" . PHP_EOL;
			echo "  <th><a class='clickablecell' href='display_items_sorted?sortby=itemname'>"     . "Item" . Utils::separatorSymbol() . "Unit" . $sortDirectionSymbolTable['itemname'] . "</a></th>" . PHP_EOL;
			echo "  <th><a class='clickablecell' href='display_items_sorted?sortby=categoryname'>" . "Category"  . $sortDirectionSymbolTable['categoryname'] . "</a></th>" . PHP_EOL;
			echo "  <th><a class='clickablecell' href='display_items_sorted?sortby=buycount'>"     . "Buy count" . $sortDirectionSymbolTable['buycount'] . "</a></th>" . PHP_EOL;
			echo "  <th><a class='clickablecell' href='display_items_sorted?sortby=lastbuytime'>"  . "Last Buy Time" . $sortDirectionSymbolTable['lastbuytime'] . "</a></th>" . PHP_EOL;
			echo " </tr>" . PHP_EOL;

			while ($itemRow = $getItemsPrepStmt->fetch()) {
				echo " <tr><td>" . "<a class='clickablecell' href='change_item?itemid=" . htmlspecialchars($itemRow['itemid'], ENT_QUOTES) . "'>" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . Utils::separatorSymbol() . htmlspecialchars($itemRow['unitname'], ENT_QUOTES) . "</a></td><td>" . htmlspecialchars($itemRow['categoryname'], ENT_QUOTES) . "</td><td>" . htmlspecialchars($itemRow['buycount'], ENT_QUOTES) . "</td><td>" . htmlspecialchars($itemRow['lastbuytime'], ENT_QUOTES) . "</td></tr>" . PHP_EOL;
			}
			echo "</table>" . PHP_EOL;
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not display any itmes by category.<p>" . PHP_EOL;
		}
	}
}
?>
