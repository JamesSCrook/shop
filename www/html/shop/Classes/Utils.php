<?php

namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2024 James S. Crook
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

    public static function changeValueSymbol() {
	return "&rarr;"; // right arrow
    }

    public static function deleteSymbol() {
	return "&#x1F5D1;"; // trash bin
    }

    public static function separatorSymbol() {
	return "&#x25CF;"; // dot
    }

    public static function separatorWithTipSymbol() {
	return "&rarr;"; // right arrow 
    }

    public static function sortAscendingSymbol() {
	return "&#x25B2;"; // triangle pointing up
    }

    public static function sortDescendingSymbol() {
	return "&#x25BC;"; // triangle pointing down
    }

    public static function passwordToggleShowHide($querySelector, $id) {
	echo "
	<script>
	    const $querySelector = document.querySelector('#$querySelector');
	    const $id = document.querySelector('#$id');

	    // When the show/hide icon is clicked, toggle from: (password hidden / eye icon) <-> (pw visible / eye-slash icon)
	    $querySelector.addEventListener('click', function(e) {
		// Toggle the icon: eye <-> eye-slash
		if ($querySelector.src.match('Images/eye-slash-icon.png')) {
		    $querySelector.src = 'Images/eye-icon.png';
		} else {
		    $querySelector.src = 'Images/eye-slash-icon.png';
		}

		// toggle the password field type attribute from text <-> password
		const type = $id.getAttribute('type') === 'password' ? 'text' : 'password';
		$id.setAttribute('type', type);
	    });
	</script>";
    }
}
?>
