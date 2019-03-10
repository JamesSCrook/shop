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
class Category extends DBConnection {

	public function __construct() {
		$this->dbConnect();
	}

	private function categoryExists($categoryName) {
		try {
			$categoryExistsPrepStmt = $this->dbConn->prepare("SELECT categoryname FROM category WHERE categoryname=:categoryname");
			$categoryExistsPrepStmt->execute(array(
				'categoryname' => $categoryName
			));
			return $categoryExistsPrepStmt->fetch();
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not check:<br>'" . htmlspecialchars($categoryName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
		}
		return FALSE;
	}

	public function addCategory($newCategoryName) {
		if ($this->categoryExists($newCategoryName)) {
			echo "Duplicate entry: '" . htmlspecialchars($newCategoryName, ENT_QUOTES) . "'<p>Category not added." . PHP_EOL;
		} else {
			try {
				$addCategoryPrepStmt = $this->dbConn->prepare("INSERT INTO category (categoryname) VALUES (:categoryname)");
				$addCategoryPrepStmt->execute(array(
					'categoryname' => $newCategoryName
				));
				//ConfirmChange::confirmSuccess("Category '$newCategoryName' successfully added");
				echo Utils::successSymbol() . htmlspecialchars("Category '$newCategoryName' successfully added", ENT_QUOTES) . "<p>" . PHP_EOL;
			} catch(PDOException $exception) {
				echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
				echo "Could not add category '" . htmlspecialchars($newCategoryName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
			}
		}
	}

	public function renameCategory($categoryName, $newCategoryName) {
		try {
			$renameCategoryPrepStmt = $this->dbConn->prepare("UPDATE category SET categoryname=:newcategoryname WHERE categoryname=:categoryname");
			$renameCategoryPrepStmt->execute(array(
				'categoryname' => $categoryName,
				'newcategoryname' => $newCategoryName
			));
			//ConfirmChange::confirmSuccess("Category '$categoryName' successfully renamed to '$newCategoryName'");
			echo Utils::successSymbol() . htmlspecialchars("Category '$categoryName' successfully renamed to '$newCategoryName'", ENT_QUOTES) . "<p>" . PHP_EOL;
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not change category '" . htmlspecialchars($categoryName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
		}
	}

	private function countItemsWithThisCategory($categoryName) {
		try {
			$checkCategoryPrepStmt = $this->dbConn->prepare("select count(*) FROM category, item WHERE categoryname=:categoryname AND category.categoryid=item.categoryid");
			$checkCategoryPrepStmt->execute(array(
				'categoryname' => $categoryName
			));
			if ($categoryRow = $checkCategoryPrepStmt->fetch()) {
				return $categoryRow['count(*)'];
			} else {
				echo "Inconsistency with category '$categoryName'! Ack!<p>";
			}
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
		}
		exit();
	}

	public function deleteCategory($deleteCategoryName) {
		$categoryCount = $this->countItemsWithThisCategory($deleteCategoryName);
		if ($this->countItemsWithThisCategory($deleteCategoryName) == 0) {
			try {
				$deleteCategoryPrepStmt = $this->dbConn->prepare("DELETE FROM category WHERE categoryname = :categoryname");
				$deleteCategoryPrepStmt->execute(array(
					'categoryname' => $deleteCategoryName
				));
				//ConfirmChange::confirmSuccess("Category '$deleteCategoryName' successfully deleted");
				echo Utils::successSymbol() . htmlspecialchars("Category '$deleteCategoryName' successfully deleted", ENT_QUOTES) . "<p>" . PHP_EOL;
				return;
			} catch(PDOException $exception) {
				echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			}
		} else {
			echo "There are $categoryCount item(s) that use this category.<p>";
		}
		echo "Could not delete category '" . htmlspecialchars($deleteCategoryName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
	}

	public function displayCategoryDropDownList($categoryId) {
		try {
			$getCategoriesPrepStmt = $this->dbConn->prepare("SELECT categoryid, categoryname FROM category ORDER BY categoryname");
			$getCategoriesPrepStmt->execute();
			while ($categoryRow = $getCategoriesPrepStmt->fetch()) {
				if ($categoryRow['categoryid'] == $categoryId) {
					echo " <option value='" . htmlspecialchars($categoryRow['categoryname'], ENT_QUOTES) . "' selected>" . htmlspecialchars($categoryRow['categoryname'], ENT_QUOTES) . "</option>" . PHP_EOL;
				} else {
					echo " <option value='" . htmlspecialchars($categoryRow['categoryname'], ENT_QUOTES) . "'>" . htmlspecialchars($categoryRow['categoryname'], ENT_QUOTES) . "</option>" . PHP_EOL;
				}
			}
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not read any categories.<p>" . PHP_EOL;
			exit();
		}
	}

	/*
	 * The "active" CATEGORIES are the one(s) that have at least one ITEM that
	 * has a non-zero QUANITY.
	 */
	public function getActiveCategories() {
		$activeCategoriesTbl = [];
		try {
			$categoryPrepStmt = $this->dbConn->prepare("SELECT DISTINCT categoryname from category INNER JOIN item where category.categoryid = item.categoryid and item.quantity != 0 ORDER BY categoryname");
			$categoryPrepStmt->execute(array());
			while ($categoryRow = $categoryPrepStmt->fetch()) {
				$activeCategoriesTbl[] = $categoryRow['categoryname'];
			}
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not get category count.<p>" . PHP_EOL;
		}
		return $activeCategoriesTbl;
	}
}
?>
