<?php
namespace JamesSCrook\Shop;

use \PDOException;

/*
 * shop - Copyright (C) 2017-2018 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */
class User extends DBConnection
{

    public function __construct()
    {
        $this->dbConnect();
    }

    public function isUserValid($userName, $password)
    {
        try {
            $getUserPrepStmt = $this->dbConn->prepare("SELECT password FROM user WHERE username=:username");
            $getUserPrepStmt->execute(array(
                'username' => $userName
            ));
            while ($userRow = $getUserPrepStmt->fetch()) {
                if (password_verify($password, $userRow['password'])) {
                    return TRUE;
                }
            }
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not read users<br>\n";
        }
        return FALSE;
    }

    public function getSortOrder($userName)
    {
        try {
            $getSortOrderPrepStmt = $this->dbConn->prepare("SELECT sortorder FROM user WHERE username=:username");
            $getSortOrderPrepStmt->execute(array(
                'username' => $userName
            ));
            $userProfileRow = $getSortOrderPrepStmt->fetch();
            $this->sortorder = $userProfileRow['sortorder'];
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not read user profile data for user '" . htmlspecialchars($userName, ENT_QUOTES) . "'<br>\n";
        }
        return $this->sortorder;
    }

    public function getDisplayUpdates($userName)
    {
        try {
            $getDisplayUpdatesPrepStmt = $this->dbConn->prepare("SELECT displayUpdates FROM user WHERE username=:username");
            $getDisplayUpdatesPrepStmt->execute(array(
                'username' => $userName
            ));
            $userProfileRow = $getDisplayUpdatesPrepStmt->fetch();
            $this->displayUpdates = $userProfileRow['displayUpdates'];
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not read user profile data for user '" . htmlspecialchars($userName, ENT_QUOTES) . "'<br>\n";
        }
        return $this->displayUpdates;
    }

    public function setSortOrder($userName, $newSortOrder)
    {
        try {
            $sortOrderPrepStmt = $this->dbConn->prepare("UPDATE user SET sortorder=:sortorder WHERE username=:username");
            $sortOrderPrepStmt->execute(array(
                'sortorder' => $newSortOrder,
                'username' => $userName
            ));
            echo "<span class=success_symbol>&#x2714; </span>";
            echo "Sort order changed successfully<br>\n";
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not update sort order<br>\n";
        }
    }

    public function setDisplayUpdates($userName, $displayUpdates)
    {
        try {
            $displayUpdatesPrepStmt = $this->dbConn->prepare("UPDATE user SET displayUpdates=:displayUpdates WHERE username=:username");
            $displayUpdatesPrepStmt->execute(array(
                'displayUpdates' => $displayUpdates,
                'username' => $userName
            ));
            echo "<span class=success_symbol>&#x2714; </span>";
            echo "Display updates changed successfully<br>\n";
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not update display updates<br>\n";
        }
    }

    public function setPassword($userName, $newPassword1, $newPassword2)
    {
        if ($newPassword1 == $newPassword2) {
            try {
                $passwordHash = password_hash($newPassword1, PASSWORD_DEFAULT);
                $passwordPrepStmt = $this->dbConn->prepare("UPDATE user SET password=:password WHERE username=:username");
                $passwordPrepStmt->execute(array(
                    'username' => $userName,
                    'password' => $passwordHash
                ));
                echo "<span class=success_symbol>&#x2714; </span>";
                echo "Password changed successfully<br>\n";
            } catch (PDOException $exception) {
                echo "ERROR(" . __FILE__ . "): Could not update password<br>\n";
            }
        } else {
            echo "<span class=failure_symbol>&#x2718; </span>";
            echo "Passwords do not match, please try again<br>\n";
        }
    }

    public function displayUserNameDropDownList()
    {
        try {
            $getUsernamesPrepStmt = $this->dbConn->prepare("SELECT username FROM user ORDER BY username");
            $getUsernamesPrepStmt->execute();
            while ($userRow = $getUsernamesPrepStmt->fetch()) {
                echo " <option value='" . htmlspecialchars($userRow['username'], ENT_QUOTES) . "'>" . htmlspecialchars($userRow['username'], ENT_QUOTES) . " </option>\n";
            }
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not read any users<br>\n";
            exit();
        }
    }

    private function userNameExists($userName)
    {
        try {
            $userNameExistsPrepStmt = $this->dbConn->prepare("SELECT username FROM user WHERE username=:username");
            $userNameExistsPrepStmt->execute(array(
                'username' => $userName
            ));
            return $userNameExistsPrepStmt->fetch();
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not check:<br>'" . htmlspecialchars($userName, ENT_QUOTES) . "'<p>\n";
        }
        return FALSE;
    }

    public function addUserName($newUserName, $newPassword)
    {
        if ($this->userNameExists($newUserName)) {
            echo "<span class=failure_symbol>&#x2718; </span>";
            echo "Duplicate entry: '" . htmlspecialchars($newUserName, ENT_QUOTES) . "' - User NOT added!\n";
        } else {
            try {
                $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $addUsernamePrepStmt = $this->dbConn->prepare("INSERT INTO user (username, password) VALUES (:username, :password)");
                $addUsernamePrepStmt->execute(array(
                    'username' => $newUserName,
                    'password' => $passwordHash
                ));
                ConfirmChange::confirmSuccess("User '$newUserName' successfully added");
            } catch (PDOException $exception) {
                echo "ERROR(" . __FILE__ . "): Could not add user '" . htmlspecialchars($newUserName, ENT_QUOTES) . "'<p>\n";
            }
        }
    }

    public function deleteUserName($deleteUserName)
    {
        try {
            $deleteUserNamePrepStmt = $this->dbConn->prepare("DELETE FROM user WHERE username = :username");
            $deleteUserNamePrepStmt->execute(array(
                'username' => $deleteUserName
            ));
            ConfirmChange::confirmSuccess("User '$deleteUserName' successfully deleted");
        } catch (PDOException $exception) {
            echo "ERROR(" . __FILE__ . "): Could not delete username '" . htmlspecialchars($deleteUserName, ENT_QUOTES) . "'<p>\n";
        }
    }
}
?>
