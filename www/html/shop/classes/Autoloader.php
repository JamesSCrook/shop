<?php

namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2018 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */

/*
 * Set up to auto-load classes iff they are required.
 */
class Autoloader {

	public static function loader($className) {
		$classNameComponents = explode('\\', $className);
		$fileName = "classes/" . end($classNameComponents) . ".php";

		if (file_exists($fileName)) {
			require_once $fileName;
			if (class_exists($className)) {
				return TRUE;
			}
		}
		return FALSE;
	}
}
