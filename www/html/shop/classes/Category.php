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
class Category extends DBConnection
{

    public function __construct()
    {
        $this->dbConnect();
    }

    private function categoryExists($categoryName)
    {
        try {
            $categoryExistsPrepStmt = $this->dbConn->prepare("SELECT categoryname FROM category WHERE categoryname=:categoryname");
            $categoryExistsPrepStmt->execute(array(
                'categoryname' => $categoryName
            ));
            return $categoryExistsPrepStmt->fetch();
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not check:<br>'" . htmlspecialchars($categoryName, ENT_QUOTES) . "'<p>\n";
        }
        return FALSE;
    }

    public function addCategory($newCategoryName)
    {
        if ($this->categoryExists($newCategoryName)) {
            echo "Duplicate entry: '" . htmlspecialchars($newCategoryName, ENT_QUOTES) . "'<p>Category not added.\n";
        } else {
            try {
                $addCategoryPrepStmt = $this->dbConn->prepare("INSERT INTO category (categoryname) VALUES (:categoryname)");
                $addCategoryPrepStmt->execute(array(
                    'categoryname' => $newCategoryName
                ));
                ConfirmChange::confirmSuccess("Category '$newCategoryName' successfully added");
            } catch (PDOException $exception) {
                echo "ERROR(" . __FILE__ . "): Could not add category '" . htmlspecialchars($newCategoryName, ENT_QUOTES) . "'<p>\n";
            }
        }
    }

    public function renameCategory($categoryName, $newCategoryName)
    {
        try {
            $renameCategoryPrepStmt = $this->dbConn->prepare("UPDATE category SET categoryname=:newcategoryname WHERE categoryname=:categoryname");
            $renameCategoryPrepStmt->execute(array(
                'categoryname' => $categoryName,
                'newcategoryname' => $newCategoryName
            ));
            ConfirmChange::confirmSuccess("Category '$categoryName' successfully changed to '$newCategoryName'");
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not change category '" . htmlspecialchars($categoryName, ENT_QUOTES) . "'<p>\n";
        }
    }

    public function deleteCategory($deleteCategoryName)
    {
        try {
            $deleteCategoryPrepStmt = $this->dbConn->prepare("DELETE FROM category WHERE categoryname = :categoryname");
            $deleteCategoryPrepStmt->execute(array(
                'categoryname' => $deleteCategoryName
            ));
            ConfirmChange::confirmSuccess("Category '$deleteCategoryName' successfully deleted");
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not delete category '" . htmlspecialchars($deleteCategoryName, ENT_QUOTES) . "'<p>\n";
        }
    }

    public function displayCategoryDropDownList($categoryId)
    {
        try {
            $getCategoriesPrepStmt = $this->dbConn->prepare("SELECT categoryid, categoryname FROM category ORDER BY categoryname");
            $getCategoriesPrepStmt->execute();
            while ($categoryRow = $getCategoriesPrepStmt->fetch()) {
                if ($categoryRow['categoryid'] == $categoryId) {
                    echo " <option value='" . htmlspecialchars($categoryRow['categoryname'], ENT_QUOTES) . "' selected>" . htmlspecialchars($categoryRow['categoryname'], ENT_QUOTES) . "</option>\n";
                } else {
                    echo " <option value='" . htmlspecialchars($categoryRow['categoryname'], ENT_QUOTES) . "'>" . htmlspecialchars($categoryRow['categoryname'], ENT_QUOTES) . "</option>\n";
                }
            }
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not read any categories<br>\n";
            exit();
        }
    }

    /*
     * The "active" CATEGORIES are the one(s) that have at least one ITEM that
     * has a non-zero QUANITY.
     */
    public function getActiveCategories()
    {
        $activeCategoriesTbl = [];
        try {
            $categoryPrepStmt = $this->dbConn->prepare("SELECT DISTINCT categoryname from category INNER JOIN item where category.categoryid = item.categoryid and item.quantity != 0 ORDER BY categoryname");
            $categoryPrepStmt->execute(array());
            while ($categoryRow = $categoryPrepStmt->fetch()) {
                $activeCategoriesTbl[] = $categoryRow['categoryname'];
            }
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not get category count<br>\n";
        }
        return $activeCategoriesTbl;
    }
}
?>
