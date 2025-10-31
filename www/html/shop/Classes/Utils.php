<?php

declare(strict_types=1);
namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2026 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */
class Utils {

    public static function topOfPageHTML(string $pageName) : void {
    echo "
<!doctype html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'>
  <title>" . Constant::WEBSITEDESCRIPTION . "$pageName</title>
  <link rel='stylesheet' media='screen' href='shop.css'>
</head>
<body>";
    }

    public static function successSymbol() : string {
	return "<span style='color: green;'>&#x2714;</span>"; // tick
    }

    public static function failureSymbol() : string {
	return "<span style='color: red;'>&#x2718;</span>"; // cross
    }

    public static function addSymbol() : string {
	return "&#x271A;"; // plus sign
    }

    public static function changeSymbol() : string {
	return "&#x270E;"; // pencil
    }

    public static function changeValueSymbol() : string {
	return "&rarr;"; // right arrow
    }

    public static function deleteSymbol() : string {
	return "&#x1F5D1;"; // trash bin
    }

    public static function separatorSymbol() : string {
	return "&#x25CF;"; // dot
    }

    public static function separatorWithTipSymbol() : string {
	return "&rarr;"; // right arrow 
    }

    public static function sortAscendingSymbol() : string {
	return "&#x25B2;"; // triangle pointing up
    }

    public static function sortDescendingSymbol() : string {
	return "&#x25BC;"; // triangle pointing down
    }

    public static function passwordToggleShowHide(string $querySelector, string $id) : void {
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
