<?php

namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2020 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */
class Utils {

	public static function successSymbol() {
		return "<span style='color: green;'>&#x2714;</span> "; // tick
	}

	public static function failureSymbol() {
		return "<span style='color: red;'>&#x2718;</span> "; // cross
	}
}
?>
