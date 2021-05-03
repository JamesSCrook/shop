<?php

namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2021 James S. Crook
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

	public static function addSymbol() {
		return "&#x271A;"; // plus sign
	}

	public static function changeSymbol() {
		return "&#x270E;"; // pencil
	}

	public static function deleteSymbol() {
		return "&#x1F5D1;"; // trash bin
	}

	public static function separatorSymbol() {
		return "&#x25CF;"; // dot
	}

	public static function changeValueSymbol() {
		return "&rarr;"; // right arrow
	}

	public static function separatorWithTipSymbol() {
		return "&rarr;"; // right arrow 
	}

	public static function sortAscendingSymbol() {
		return "<span style='color:green; font-size:180%;'>&#x25B2;</span>"; // triangle pointing up
	}

	public static function sortDescendingSymbol() {
		return "<span style='color:red; font-size:180%;'>&#x25BC;</span>"; // triangle pointing down
	}
}
?>
