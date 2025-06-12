<?php

declare(strict_types=1);
namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2025 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */
class Menu {
    /*
     * Multiple heredocs just looked to horrible, so, multi-line echo it is...
     */
    public static function displayMenus(bool $itemPageFlag) : void {
	echo "
<nav class='nav-bar'>
  <label for='toggle' class='nav-bar__label'>
    <div class='menu__icon'></div>
    <div class='menu__icon'></div>
    <div class='menu__icon'></div>
  </label>
  <input type='checkbox' id='toggle'>
  <ul class='nav-bar__list'>
    <li class='nav-bar__list-item'>
      <a href='./' class='nav-bar__link'>Items</a>
    </li>
    <li class='nav-bar__list-item'>
      <a href='admin' class='nav-bar__link'>Admin</a>
    </li>
    <li class='nav-bar__list-item'>
      <a href='history' class='nav-bar__link'>History</a>
    </li>
    <li class='nav-bar__list-item'>
      <a href='logout' class='nav-bar__link'>Logout</a>
    </li>
  ";

	  if ($itemPageFlag) {
	      echo "
    <li class='nav-bar__list-item'>
	<button class='nav-bar__bttn-item' form='items_form' name='update_items_bttn'>Update</button>
    </li>
  ";
	  }

	  echo "
  </ul>
</nav>
";
    }
}
?>

<!-- Version 2.2.3 Thu 12 Jun 2025 10:56:03 AEST -->
