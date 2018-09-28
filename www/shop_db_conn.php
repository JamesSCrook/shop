<?php

namespace JamesSCrook\Shop;

use PDO;
use PDOException;

/*
 * shop - Copyright (C) 2017-2018 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */
class DBConnection {
	private static $dbHost = "localhost";
	private static $dbName = "shop";
	private static $dbUser = "shop_username";
	private static $dbPassword = "shop_password";
	protected $dbConn;

	protected function dbConnect() {
		try {
			$this->dbConn = new PDO("mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName . ";charset=utf8mb4", self::$dbUser, self::$dbPassword, array(
				PDO::ATTR_EMULATE_PREPARES => false,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			));
		} catch(PDOException $ex) {
			echo "Cannot connect to the DB - " . $ex->getMessage();
		}
	}
}
?>
