<?php
namespace JamesSCrook\Shop;

use PDOException;

/*
 * shop - Copyright (C) 2017-2018 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */
class History extends DBConnection
{

    public function __construct()
    {
        $this->dbConnect();
    }

    public function trimHistory()
    {
        try {
            $trimHistoryPrepStmt = $this->dbConn->prepare("DELETE FROM history WHERE DATE_SUB(CURDATE(),INTERVAL 31 DAY) > time");
            $trimHistoryPrepStmt->execute();
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not trim the history data<br>\n";
        }
    }

    public function displayHistory()
    {
        try {
            $displayHistoryPrepStmt = $this->dbConn->prepare("SELECT time, username, itemname, unitname, oldQuantity, newQuantity FROM history ORDER BY time DESC, itemname LIMIT 512");
            $displayHistoryPrepStmt->execute();
            
            echo "<table>\n";
            echo "<tr><th>Time</th><th>Who</th><th>Item</th><th>Change</th></tr>";
            while ($row = $displayHistoryPrepStmt->fetch()) {
                echo "</tr><td>" . htmlspecialchars($row['time'], ENT_QUOTES) . "</td><td>" . htmlspecialchars($row['username'], ENT_QUOTES) . "</td><td>" . htmlspecialchars($row['itemname'], ENT_QUOTES) . "&#x25CF;" . $row['unitname'] . "</td><td>" . htmlspecialchars($row['oldQuantity'], ENT_QUOTES) . "&rarr;" . htmlspecialchars($row['newQuantity'], ENT_QUOTES) . "</td></tr>\n";
            }
            echo "</table>\n";
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not read history<br>\n";
        }
    }
}
?>
