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
class UserData {

    private $displayItemsSortByColumnName = 'itemname';
    private $displayItemsSortByAscendingFlag = TRUE;
    
    public function getDisplayItemsSortByColumnName() : string {
	return $this->displayItemsSortByColumnName;
    }
    
    public function getDisplayItemsSortByAscendingFlag() : bool {
	return $this->displayItemsSortByAscendingFlag;
    }

    public function setDisplayItemsSortByColumnName(string $displayItemsSortByColumnName) : void {
	$this->displayItemsSortByColumnName = $displayItemsSortByColumnName;
    }

    public function setDisplayItemsSortByAscendingFlag(bool $displayItemsSortByAscendingFlag) : void {
	$this->displayItemsSortByAscendingFlag = $displayItemsSortByAscendingFlag;
    }
}
?>
