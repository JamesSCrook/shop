<?php

namespace JamesSCrook\Shop;

use PDO;
use PDOException;

// require_once dirname(dirname(dirname(dirname(__FILE__)))) . dirname($_SERVER["PHP_SELF"]) . "_db_conn.php";

/*
 * shop - Copyright (C) 2017-2024 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */
class DBConnection {
    private static $dbName;
    private static $dbHost;
    private static $dbUser;
    private static $dbPassword;
    public $pdo;

    public function __construct() {
	// (self::<varname> for $dbName, $dbHost, $dbUser and $dbPassword should be defined in this include file.
	include dirname(dirname(dirname(dirname(__FILE__)))) . dirname($_SERVER["PHP_SELF"]) . "_db_conn.php";

	try {

	    $this->pdo = new PDO("mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName . ";charset=utf8mb4", self::$dbUser, self::$dbPassword, array(
		PDO::ATTR_EMULATE_PREPARES => false,
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	    ));
	} catch(PDOException $ex) {
	    echo "Cannot connect to the DB - " . $ex->getMessage();
	}
    }
}
?>
