<?php

namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2026 James S. Crook
 * This program comes with ABSOLUTELY NO WARRANTY.
 * This is free software, and you are welcome to redistribute it under certain conditions.
 * This program is licensed under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version (see <http://www.gnu.org/licenses/>).
 *
 * This page shows all the items sorted by categoryname, item.
 * It's useful for finding items with no categoryname assigned and correcting
 * ones with an incorrect categoryname.
 */
session_start();
require_once "Classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");
$pageSubtitle = "Display Item Details";
Utils::topOfPageHTML(": $pageSubtitle");

if (!isset($_SESSION['username'])) {
    header("Location: login");
    exit();
} else {
    $username = $_SESSION['username'];
}

Menu::displayMenus(FALSE);

echo "<h3>" . Constant::WEBSITEDESCRIPTION . ": $pageSubtitle (" . htmlspecialchars($username, ENT_QUOTES) . ")</h3>" . PHP_EOL;
$itemList = new ItemList(new DBConnection());
$itemList->displayItemsByCategory();
?>

</body>
</html>
