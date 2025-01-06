<?php

namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2025 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 *
 * Login to shop.
 */
session_start();
require_once "Classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");
$pageSubtitle = "Login";
Utils::topOfPageHTML(": $pageSubtitle");

Menu::displayMenus(FALSE);

echo "<h3>$pageSubtitle to " . Constant::WEBSITEDESCRIPTION . "</h3>" . PHP_EOL;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<form id=login_form method='POST'>" . PHP_EOL;
    echo "  Username<br>" . PHP_EOL;
    echo "  <input type='text' class='enter-input-text input-color' name='username' placeholder='Username'><br><br>" . PHP_EOL;
    echo "  Password<br>" . PHP_EOL;
    echo "  <div class='pw-show-hide-input'>" . PHP_EOL;
    echo "   <input type='password' class='enter-input-text input-color' name='password' id='password' placeholder='Password'>" . PHP_EOL;
    echo "   <img src='Images/eye-icon.png' class='pw-show-hide-icon' id='pwtoggleshowhide'>" . PHP_EOL;
    echo "  </div><p>" . PHP_EOL;
    echo "  <button class='bttn query-color' name='login'>Login</button><br>" . PHP_EOL;
    echo "</form>" . PHP_EOL;
    Utils::passwordToggleShowHide('pwtoggleshowhide', 'password');
} else {
    $dbConnection = new DBConnection();
    $user = new User($dbConnection);
    if ($user->isUserValid($_POST['username'], $_POST['password'])) {
	/* Trim the history every time any user logs in successfully */
	$history = new History($dbConnection);
	$history->trimHistory();
	$_SESSION['username'] = $_POST['username'];
	header("Location: index");
	exit();
    } else {
	echo "Login unsuccessful - Please try again.<br>" . PHP_EOL;
	unset($_SESSION['username']);
    }
}
?>

</body>
</html>
