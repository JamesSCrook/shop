<?php

namespace JamesSCrook\Shop;

use PDOException;

/*
 * shop - Copyright (C) 2017-2021 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */
class User extends DBConnection {

	public function __construct() {
		$this->dbConnect();
	}

	public function isUserValid($userName, $password) {
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
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not read users.<p>" . PHP_EOL;
		}
		return FALSE;
	}

	public function getSortOrder($userName) {
		try {
			$getSortOrderPrepStmt = $this->dbConn->prepare("SELECT sortorder FROM user WHERE username=:username");
			$getSortOrderPrepStmt->execute(array(
				'username' => $userName
			));
			$userProfileRow = $getSortOrderPrepStmt->fetch();
			$this->sortorder = $userProfileRow['sortorder'];
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not read user profile data for user '" . htmlspecialchars($userName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
		}
		return $this->sortorder;
	}

	public function getDisplayUpdates($userName) {
		try {
			$getDisplayUpdatesPrepStmt = $this->dbConn->prepare("SELECT displayUpdates FROM user WHERE username=:username");
			$getDisplayUpdatesPrepStmt->execute(array(
				'username' => $userName
			));
			$userProfileRow = $getDisplayUpdatesPrepStmt->fetch();
			$this->displayUpdates = $userProfileRow['displayUpdates'];
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not read user profile data for user '" . htmlspecialchars($userName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
		}
		return $this->displayUpdates;
	}

	public function setSortOrder($userName, $newSortOrder) {
		try {
			$sortOrderPrepStmt = $this->dbConn->prepare("UPDATE user SET sortorder=:sortorder WHERE username=:username");
			$sortOrderPrepStmt->execute(array(
				'sortorder' => $newSortOrder,
				'username' => $userName
			));
			echo "<br>" . Utils::successSymbol() . "Sort order changed successfully<br>" . PHP_EOL;
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not update sort order.<p>" . PHP_EOL;
		}
	}

	public function setDisplayUpdates($userName, $displayUpdates) {
		try {
			$displayUpdatesPrepStmt = $this->dbConn->prepare("UPDATE user SET displayUpdates=:displayUpdates WHERE username=:username");
			$displayUpdatesPrepStmt->execute(array(
				'displayUpdates' => $displayUpdates,
				'username' => $userName
			));
			echo "<br>" . Utils::successSymbol() . "Display updates changed successfully<br>" . PHP_EOL;
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not update display updates.<p>" . PHP_EOL;
		}
	}

	public function setPassword($userName, $newPassword1, $newPassword2) {
		if ($newPassword1 == $newPassword2) {
			try {
				$passwordHash = password_hash($newPassword1, PASSWORD_DEFAULT);
				$passwordPrepStmt = $this->dbConn->prepare("UPDATE user SET password=:password WHERE username=:username");
				$passwordPrepStmt->execute(array(
					'username' => $userName,
					'password' => $passwordHash
				));
				echo "<br>" . Utils::successSymbol() . "Password changed successfully<br>" . PHP_EOL;
			} catch(PDOException $exception) {
				echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
				echo "Could not update password.<p>" . PHP_EOL;
			}
		} else {
			echo "<br>" . Utils::failureSymbol() . "Passwords do not match, please try again<br>" . PHP_EOL;
		}
	}

	public function displayUserNameDropDownList() {
		try {
			$getUsernamesPrepStmt = $this->dbConn->prepare("SELECT username FROM user ORDER BY username");
			$getUsernamesPrepStmt->execute();
			while ($userRow = $getUsernamesPrepStmt->fetch()) {
				echo " <option value='" . htmlspecialchars($userRow['username'], ENT_QUOTES) . "'>" . htmlspecialchars($userRow['username'], ENT_QUOTES) . " </option>" . PHP_EOL;
			}
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not read any users.<p>" . PHP_EOL;
			exit();
		}
	}

	private function userNameExists($userName) {
		try {
			$userNameExistsPrepStmt = $this->dbConn->prepare("SELECT username FROM user WHERE username=:username");
			$userNameExistsPrepStmt->execute(array(
				'username' => $userName
			));
			return $userNameExistsPrepStmt->fetch();
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not check:<br>'" . htmlspecialchars($userName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
		}
		return FALSE;
	}

	public function addUserName($newUserName, $newPassword) {
		if ($this->userNameExists($newUserName)) {
			echo "<br>" . Utils::failureSymbol() . "Duplicate entry: '" . htmlspecialchars($newUserName, ENT_QUOTES) . "' - User NOT added!" . PHP_EOL;
		} else {
			try {
				$passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
				$addUsernamePrepStmt = $this->dbConn->prepare("INSERT INTO user (username, password) VALUES (:username, :password)");
				$addUsernamePrepStmt->execute(array(
					'username' => $newUserName,
					'password' => $passwordHash
				));
				// ConfirmChange::confirmSuccess("User '$newUserName' successfully added");
				echo "<br>" . Utils::successSymbol() . htmlspecialchars("User '$newUserName' successfully added", ENT_QUOTES) . "<p>" . PHP_EOL;
			} catch(PDOException $exception) {
				echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
				echo "Could not add user '" . htmlspecialchars($newUserName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
			}
		}
	}

	public function deleteUserName($deleteUserName) {
		try {
			$deleteUserNamePrepStmt = $this->dbConn->prepare("DELETE FROM user WHERE username = :username");
			$deleteUserNamePrepStmt->execute(array(
				'username' => $deleteUserName
			));
			// ConfirmChange::confirmSuccess("User '$deleteUserName' successfully deleted");
			echo "<br>" . Utils::successSymbol() . htmlspecialchars("User '$deleteUserName' successfully deleted", ENT_QUOTES) . "<p>" . PHP_EOL;
		} catch(PDOException $exception) {
			echo "ERROR in file: " . __FILE__ . ", function: " . __FUNCTION__ . ", line: " . __LINE__ . "<p>" . $exception->getMessage() . "<p>" . PHP_EOL;
			echo "Could not delete username '" . htmlspecialchars($deleteUserName, ENT_QUOTES) . "'.<p>" . PHP_EOL;
		}
	}
}
?>
