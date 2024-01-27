<?php

namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2023 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 */
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<title>Shop: Edit User Profile</title>
<link rel='stylesheet' media='screen' href='shop.css'>
</head>
<body>

<?php
/*
 * Edit a user's profile: password, (Item page) sort order and the "display updates"
 * behavior (either do or don't display a page showing the item quantity change(s) just made).
 */
session_start();
require_once dirname(dirname(dirname(__FILE__))) . dirname($_SERVER["PHP_SELF"]) . "_db_conn.php";
require_once "Classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");

if (!isset($_SESSION['username'])) {
    header("Location: login");
    exit();
} else {
    $username = $_SESSION['username'];
}

echo "<h3>Edit User Profile (" . htmlspecialchars($username, ENT_QUOTES) . ")</h3>" . PHP_EOL;

Menu::displayMenus(FALSE);
$user = new User();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<form id=profile_form method='POST'>" . PHP_EOL;

    echo "New password<br>" . PHP_EOL;
    echo "<div class='pw-show-hide-input'>" . PHP_EOL;
    echo "<input type='password' class='enter-input-text input-color' name='newpassword1' id='newpassword1' size='20' pattern='.{6,}' placeholder='min 6 chars'>" . PHP_EOL;
    echo "<img src='Images/eye-icon.png' class='pw-show-hide-icon' id='pwtoggleshowhideA'>" . PHP_EOL;
    echo "</div>" . PHP_EOL;

    echo "<br><br>Repeat password<br>" . PHP_EOL;
    echo "<div class='pw-show-hide-input'>" . PHP_EOL;
    echo "<input type='password' class='enter-input-text input-color' name='newpassword2' id='newpassword2' size='20' pattern='.{6,}'>" . PHP_EOL;
    echo "<img src='Images/eye-icon.png' class='pw-show-hide-icon' id='pwtoggleshowhideB'>" . PHP_EOL;
    echo "</div><p>" . PHP_EOL;

    $sortOrder = $user->getSortOrder($username);
    echo "Sort Order<br>" . PHP_EOL;
    echo "<select class='enter-select input-color' name='sortOrder'>" . PHP_EOL;
    if ($sortOrder == "cq") {
	echo " <option value='q'>Quantity</option>" . PHP_EOL;
	echo " <option value='cq' selected>Category, quantity</option>" . PHP_EOL;
	echo " <option value='a'>Alphabetical</option>" . PHP_EOL;
    } else if ($sortOrder == "a") {
	echo " <option value='q'>Quantity</option>" . PHP_EOL;
	echo " <option value='cq'>Category, quantity</option>" . PHP_EOL;
	echo " <option value='a' selected>Alphabetical</option>" . PHP_EOL;
    } else {
	echo " <option value='q' selected>Quantity</option>" . PHP_EOL;
	echo " <option value='cq'>Category, quantity</option>" . PHP_EOL;
	echo " <option value='a'>Alphabetical</option>" . PHP_EOL;
    }
    echo "</select><p>" . PHP_EOL;

    echo "Display Update Confirmations<br>" . PHP_EOL;
    echo "<select class='enter-select input-color' name='displayUpdates'>" . PHP_EOL;
    if ($user->getDisplayUpdates($username) == "No") {
	echo " <option value='Yes'>Yes</option>" . PHP_EOL;
	echo " <option value='No' selected>No</option>" . PHP_EOL;
    } else {
	echo " <option value='Yes' selected>Yes</option>" . PHP_EOL;
	echo " <option value='No'>No</option>" . PHP_EOL;
    }
    echo "</select><p>" . PHP_EOL;
    echo " <button class='bttn change-color' name='updateprofile'>" . Utils::changeSymbol() . " Update Profile</button><p>" . PHP_EOL;
    echo "</form>" . PHP_EOL;
    Utils::passwordToggleShowHide('pwtoggleshowhideA', 'newpassword1');
    Utils::passwordToggleShowHide('pwtoggleshowhideB', 'newpassword2');
} else { /* POST - a button has been pressed */
    if (isset($_POST['updateprofile'])) {
	if ($_POST['newpassword1'] != "") {
	    $user->setPassword($username, $_POST['newpassword1'], $_POST['newpassword2']);
	}

	if ($_POST['sortOrder'] != "" && $_POST['sortOrder'] != $user->getSortOrder($username)) {
	    $user->setSortOrder($username, $_POST['sortOrder']);
	}

	if ($_POST['displayUpdates'] != "" && $_POST['displayUpdates'] != $user->getDisplayUpdates($username)) {
	    $user->setDisplayUpdates($username, $_POST['displayUpdates']);
	}
    }
}
?>

</body>
</html>
