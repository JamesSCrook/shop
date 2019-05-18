<?php

namespace JamesSCrook\Shop;

use PDOException;

/*
 * shop - Copyright (C) 2017-2019 James S. Crook
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
			echo "<tr><th>Category</th><th>Item&#x25CF;Unit</th><th>Notes</th></tr>";
			while ($itemRow = $getItemsPrepStmt->fetch()) {
				echo "<tr><td>" . htmlspecialchars($itemRow['categoryname'], ENT_QUOTES) . "</td><td>" . "<a href='change_item.php?itemid=" . htmlspecialchars($itemRow['itemid'], ENT_QUOTES) . "'>" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . "</a>&#x25CF;" . htmlspecialchars($itemRow['unitname'], ENT_QUOTES) . "</td><td>" . htmlspecialchars($itemRow['notes']) . "</td></tr>" . PHP_EOL;
			}
			echo "</table>" . PHP_EOL;
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not display any itmes by category.<p>" . PHP_EOL;
		}
	}

	public function displayBuyTimeDetails($sortby) {
		$_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
		switch ($sortby) {
			case "category_asc":
				$orderBySQLargs = "categoryname ASC, itemname, unitname";
				break;
			case "category_desc":
				$orderBySQLargs = "categoryname DESC, itemname, unitname";
				break;
			case "buy_count_asc":
				$orderBySQLargs = "buycount ASC, itemname, unitname ASC";
				break;
			case "buy_count_desc":
				$orderBySQLargs = "buycount DESC, itemname, unitname";
				break;
			case "last_buy_time_asc":
				$orderBySQLargs = "lastbuytime ASC, itemname, unitname";
				break;
			case "last_buy_time_desc":
				$orderBySQLargs = "lastbuytime DESC, itemname, unitname";
				break;
			case "item_unit_desc":
				$orderBySQLargs = "itemname DESC, unitname DESC";
				break;
			default:
				$orderBySQLargs = "itemname ASC, unitname ASC";
				break; // also handles item_unit_asc
		}
		try {
			$getItemsPrepStmt = $this->dbConn->prepare("SELECT itemname, itemid, unitname, categoryname, buycount, lastbuytime FROM item INNER JOIN unit ON item.unitid = unit.unitid INNER JOIN category ON item.categoryid = category.categoryid ORDER BY " . $orderBySQLargs);
			$getItemsPrepStmt->execute();

			echo "<table>" . PHP_EOL;
			echo "<tr>
				<th><a href='buy_time_details.php?sortby=item_unit_asc'>&#x25b2;</a>Item&#x25CF;Unit<a href='buy_time_details.php?sortby=item_unit_desc'>&#x25bc</a></th>
				<th><a href='buy_time_details.php?sortby=category_asc'>&#x25b2;</a>Category<a href='buy_time_details.php?sortby=category_desc'>&#x25bc</a></th>
				<th><a href='buy_time_details.php?sortby=buy_count_asc'>&#x25b2;</a>Buy Count<a href='buy_time_details.php?sortby=buy_count_desc'>&#x25bc</a></th>
				<th><a href='buy_time_details.php?sortby=last_buy_time_asc'>&#x25b2;</a>Last Buy Time<a href='buy_time_details.php?sortby=last_buy_time_desc'>&#x25bc</a></th>
			</tr>";

			while ($itemRow = $getItemsPrepStmt->fetch()) {
				echo "<tr><td>" . "<a href='change_item.php?itemid=" . htmlspecialchars($itemRow['itemid'], ENT_QUOTES) . "'>" . htmlspecialchars($itemRow['itemname'], ENT_QUOTES) . "</a>&#x25CF;" . htmlspecialchars($itemRow['unitname'], ENT_QUOTES) . "</td><td>" . htmlspecialchars($itemRow['categoryname'], ENT_QUOTES) . "</td><td>" . htmlspecialchars($itemRow['buycount'], ENT_QUOTES) . "</td><td>" . htmlspecialchars($itemRow['lastbuytime'], ENT_QUOTES) . "</td></tr>" . PHP_EOL;
			}
			echo "</table>" . PHP_EOL;
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not display any itmes by category.<p>" . PHP_EOL;
		}
	}
}
?>
