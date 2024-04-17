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
class History {

    private $dbConn;

    public function __construct($dbConnection) {
	$this->dbConn = $dbConnection->pdo;
    }

    public function trimHistory() {
	try {
	    $trimHistoryPrepStmt = $this->dbConn->prepare("DELETE FROM history WHERE DATE_SUB(CURDATE(),INTERVAL 180 DAY) > time");
	    $trimHistoryPrepStmt->execute();
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not trim the history data.<p>" . PHP_EOL;
	}
    }

    public function displayHistory() {
	try {
	    $displayHistoryPrepStmt = $this->dbConn->prepare("SELECT time, username, itemname, unitname, oldQuantity, newQuantity FROM history ORDER BY time DESC, itemname LIMIT 512");
	    $displayHistoryPrepStmt->execute();

	    echo "<table>" . PHP_EOL;
	    echo "<tr><th>Time</th><th>Who</th><th>Item</th><th>Change</th></tr>" . PHP_EOL;
	    while ($row = $displayHistoryPrepStmt->fetch()) {
		echo "<tr><td>" . htmlspecialchars($row['time'], ENT_QUOTES) . "</td><td>" . htmlspecialchars($row['username'], ENT_QUOTES) . "</td><td>" . htmlspecialchars($row['itemname'], ENT_QUOTES) . Utils::separatorSymbol() . $row['unitname'] . "</td><td>" . htmlspecialchars($row['oldQuantity'], ENT_QUOTES) . Utils::changeValueSymbol() . htmlspecialchars($row['newQuantity'], ENT_QUOTES) . "</td></tr>" . PHP_EOL;
	    }
	    echo "</table>" . PHP_EOL;
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not read history.<p>" . PHP_EOL;
	}
    }
}
?>
