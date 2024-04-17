<?php

namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2024 James S. Crook
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
<title>Shop: Logout</title>
<link rel='stylesheet' media='screen' href='shop.css'>
</head>
<body>

<?php
/*
 * Logout of shop.
 */
session_start();
require_once "Classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");

Menu::displayMenus(FALSE);

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    echo "<h3>See you next time ($username)...</h3>";
    setcookie(session_name(), '', 100);
    session_unset();
    session_destroy();
} else {
    echo "<h3>You weren't logged in, bye!</h3>";
}
?>

</body>
</html>
