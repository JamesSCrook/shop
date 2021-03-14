<?php

namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2021 James S. Crook
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
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Shop: Login</title>
<link rel='stylesheet' media='screen' href='shop.css'>
</head>
<body>

<?php
/*
 * Login to shop.
 */
session_start();
require_once dirname(dirname(dirname(__FILE__))) . dirname($_SERVER["PHP_SELF"]) . "_db_conn.php";
require_once "Classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");

Menu::displayMenus(FALSE);

echo "<h3>Login to Shop</h3>" . PHP_EOL;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	echo "<form id=login_form method='POST'>" . PHP_EOL;
	echo "  Username<br>" . PHP_EOL;
	echo "  <input type='text' class='enter_input_text' name='username' size='20'><p>" . PHP_EOL;
	echo "  Password<br>" . PHP_EOL;
	echo "  <input type='password' class='enter_input_text' name='password' size='20'><p>" . PHP_EOL;
	echo "  <button class='bttn' name='login'>Login</button><br>" . PHP_EOL;
	echo "</form>" . PHP_EOL;
} else {
	$user = new User();
	if ($user->isUserValid($_POST['username'], $_POST['password'])) {
		/* Trim the history every time any user logs in successfully */
		$history = new History();
		$history->trimHistory();
		$_SESSION['username'] = $_POST['username'];
		header("Location: index.php");
		exit();
	} else {
		echo "Login unsuccessful - Please try again.<br>" . PHP_EOL;
		unset($_SESSION['username']);
	}
}
?>

</body>
</html>
