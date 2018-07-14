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
class Unit extends DBConnection
{

    public function __construct()
    {
        $this->dbConnect();
    }

    private function unitExists($unitName)
    {
        try {
            $unitExistsPrepStmt = $this->dbConn->prepare("SELECT unitname FROM unit WHERE unitname=:unitname");
            $unitExistsPrepStmt->execute(array(
                'unitname' => $unitName
            ));
            return $unitExistsPrepStmt->fetch();
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not check:<br>'" . htmlspecialchars($unitName, ENT_QUOTES) . "'<p>\n";
        }
        return FALSE;
    }

    public function addUnit($newUnitName)
    {
        if ($this->unitExists($newUnitName)) {
            echo "Duplicate entry: '" . htmlspecialchars($newUnitName, ENT_QUOTES) . "'<p>Unit not added.\n";
        } else {
            try {
                $addUnitPrepStmt = $this->dbConn->prepare("INSERT INTO unit (unitname) VALUES (:unitname)");
                $addUnitPrepStmt->execute(array(
                    'unitname' => $newUnitName
                ));
                ConfirmChange::confirmSuccess("Unit '$newUnitName' successfully added");
            } catch (PDOException $exception) {
                echo "ERROR(" . __FILE__ . "): Could not add unit '" . htmlspecialchars($newUnitName, ENT_QUOTES) . "'<p>\n";
            }
        }
    }

    public function renameUnit($unitName, $newUnitName)
    {
        try {
            $renameUnitPrepStmt = $this->dbConn->prepare("UPDATE unit SET unitname=:newunitname WHERE unitname=:unitname");
            $renameUnitPrepStmt->execute(array(
                'unitname' => $unitName,
                'newunitname' => $newUnitName
            ));
            ConfirmChange::confirmSuccess("Unit '$unitName' successfully changed to '$newUnitName'");
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not change unit '" . htmlspecialchars($unitName, ENT_QUOTES) . "'<p>\n";
        }
    }

    public function deleteUnit($deleteUnitName)
    {
        try {
            $deleteUnitPrepStmt = $this->dbConn->prepare("DELETE FROM unit WHERE unitname = :unitname");
            $deleteUnitPrepStmt->execute(array(
                'unitname' => $deleteUnitName
            ));
            ConfirmChange::confirmSuccess("Unit '$deleteUnitName' successfully deleted");
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not delete unit '" . htmlspecialchars($deleteUnitName, ENT_QUOTES) . "'<p>\n";
        }
    }

    public function displayUnitDropDownList($unitId)
    {
        try {
            $getUnitsPrepStmt = $this->dbConn->prepare("SELECT unitid, unitname FROM unit ORDER BY unitname");
            $getUnitsPrepStmt->execute();
            while ($unitRow = $getUnitsPrepStmt->fetch()) {
                if ($unitRow['unitid'] == $unitId) {
                    echo " <option value='" . htmlspecialchars($unitRow['unitname'], ENT_QUOTES) . "' selected>" . htmlspecialchars($unitRow['unitname'], ENT_QUOTES) . "</option>\n";
                } else {
                    echo " <option value='" . htmlspecialchars($unitRow['unitname'], ENT_QUOTES) . "'>" . htmlspecialchars($unitRow['unitname'], ENT_QUOTES) . "</option>\n";
                }
            }
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not read any units<br>\n";
            exit();
        }
    }
}
?>
