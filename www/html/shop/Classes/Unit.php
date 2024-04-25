<?php

declare(strict_types=1);
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
class Unit {

    private $dbConn;

    public function __construct($dbConnection) {
	$this->dbConn = $dbConnection->pdo;
    }

    private function unitExists(string $unitName) : mixed {
	try {
	    $unitExistsPrepStmt = $this->dbConn->prepare("SELECT unitname FROM unit WHERE unitname=:unitname");
	    $unitExistsPrepStmt->execute(array(
		'unitname' => $unitName
	    ));
	    return $unitExistsPrepStmt->fetch();
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not check:<br>'" . htmlspecialchars($unitName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
	}
	return FALSE;
    }

    public function addUnit(string $newUnitName) : void {
	if ($this->unitExists($newUnitName)) {
	    echo "<p>" . Utils::failureSymbol() . "Duplicate entry: '" . htmlspecialchars($newUnitName, ENT_QUOTES) . "'<p>Unit not added." . PHP_EOL;
	} else {
	    try {
		$addUnitPrepStmt = $this->dbConn->prepare("INSERT INTO unit (unitname) VALUES (:unitname)");
		$addUnitPrepStmt->execute(array(
		    'unitname' => $newUnitName
		));
		echo "<br>" . Utils::successSymbol() . htmlspecialchars("Unit '$newUnitName' successfully added", ENT_QUOTES) . "<p>" . PHP_EOL;
	    } catch(PDOException $exception) {
		echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
		echo "Could not add unit '" . htmlspecialchars($newUnitName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
	    }
	}
    }

    public function renameUnit(string $unitName, string $newUnitName) : void {
	if ($this->unitExists($newUnitName)) {
	    echo "<p>" . Utils::failureSymbol() . "Unit '" . htmlspecialchars($unitName, ENT_QUOTES) . "' cannot be renamed to existing unit '" . htmlspecialchars($newUnitName, ENT_QUOTES) . "'<p>Unit not renamed." . PHP_EOL;
	    return;
	}
	try {
	    $renameUnitPrepStmt = $this->dbConn->prepare("UPDATE unit SET unitname=:newunitname WHERE unitname=:unitname");
	    $renameUnitPrepStmt->execute(array(
		'unitname' => $unitName,
		'newunitname' => $newUnitName
	    ));
	    echo "<br>" . Utils::successSymbol() . htmlspecialchars("Unit '$unitName' successfully renamed to '$newUnitName'", ENT_QUOTES) . "<p>" . PHP_EOL;
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not change unit '" . htmlspecialchars($unitName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
	}
    }

    private function countItemsWithThisUnit(string $unitName) : int {
	try {
	    $checkUnitPrepStmt = $this->dbConn->prepare("select count(*) FROM unit, item WHERE unitname=:unitname AND unit.unitid=item.unitid");
	    $checkUnitPrepStmt->execute(array(
		'unitname' => $unitName
	    ));
	    if ($unitRow = $checkUnitPrepStmt->fetch()) {
		return intval($unitRow['count(*)']);
	    } else {
		echo "Inconsistency with unit '$unitName'! Ack!<p>";
	    }
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	}
	exit();
    }

    public function deleteUnit(string $deleteUnitName) : void {
	$unitCount = $this->countItemsWithThisUnit($deleteUnitName);
	if ($this->countItemsWithThisUnit($deleteUnitName) == 0) {
	    try {
		$deleteUnitPrepStmt = $this->dbConn->prepare("DELETE FROM unit WHERE unitname = :unitname");
		$deleteUnitPrepStmt->execute(array(
		    'unitname' => $deleteUnitName
		));
		echo "<br>" . Utils::successSymbol() . htmlspecialchars("Unit '$deleteUnitName' successfully deleted", ENT_QUOTES) . "<p>" . PHP_EOL;
		return;
	    } catch(PDOException $exception) {
		echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    }
	} else {
	    echo "<br>There are $unitCount item(s) that use this unit.<p>";
	}
	echo "Could not delete unit '" . htmlspecialchars($deleteUnitName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
    }

    public function displayUnitDropDownList(?int $unitId) : void {
	try {
	    $getUnitsPrepStmt = $this->dbConn->prepare("SELECT unitid, unitname FROM unit ORDER BY unitname");
	    $getUnitsPrepStmt->execute();
	    while ($unitRow = $getUnitsPrepStmt->fetch()) {
		if (intval($unitRow['unitid']) == $unitId) {
		    echo " <option value='" . htmlspecialchars($unitRow['unitname'], ENT_QUOTES) . "' selected>" . htmlspecialchars($unitRow['unitname'], ENT_QUOTES) . "</option>" . PHP_EOL;
		} else {
		    echo " <option value='" . htmlspecialchars($unitRow['unitname'], ENT_QUOTES) . "'>" . htmlspecialchars($unitRow['unitname'], ENT_QUOTES) . "</option>" . PHP_EOL;
		}
	    }
	} catch(PDOException $exception) {
	    echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
	    echo "Could not read any units.<p>" . PHP_EOL;
	    exit();
	}
    }
}
?>
