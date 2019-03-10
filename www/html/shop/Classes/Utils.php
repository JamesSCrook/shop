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
class Utils {

	public static function successSymbol() {
		return "<br><font style='color: green;'>&#x2714;</font> ";		// tick
	}

	public static function failureSymbol() {
		return "<br><font style='color: red;'>&#x2718;</font> ";		// cross
	}
}
?>
