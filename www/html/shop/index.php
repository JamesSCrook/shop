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
 * Item (main) page. Display all the items and their current quantity values.
 * There are 3 sort orders:
 * q (quantity), cq (categoryname, quantity), and a (alphabetical).
 * See below for details.
 */
session_start();
require_once "Classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");
$pageSubtitle = "Items";
Utils::topOfPageHTML(": $pageSubtitle");

if (!isset($_SESSION['username'])) {
    header("Location: login");
    exit();
} else {
    $username = $_SESSION['username'];
}

$dbConnection = new DBConnection();
$user = new User($dbConnection);
$item = new Item($dbConnection);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    Menu::displayMenus(TRUE);
    echo "<h3>$pageSubtitle (" . htmlspecialchars($username, ENT_QUOTES) . ")</h3>" . PHP_EOL;
    $item->displayLinks();
    echo "<form id=items_form method='POST'>" . PHP_EOL;

    $category = new Category($dbConnection);
    $activeCategories = $category->getActiveCategories();

    $sortOrder = $user->getSortOrder($username);
    switch ($sortOrder) {
	case "cq": // sort "by category, then by quantity"
	    foreach ($activeCategories as $activeCategory) { // For each active category
		echo "<div class='section-separator'>" . htmlspecialchars($activeCategory, ENT_QUOTES) . "</div>" . PHP_EOL;
		echo "<div class='grid-container'>" . PHP_EOL;
		$item->displayItems("AND category.categoryid=(select categoryid from category where categoryname='$activeCategory') AND quantity > 0");
		$item->displayItems("AND category.categoryid=(select categoryid from category where categoryname='$activeCategory') AND quantity < 0");
		echo "</div>" . PHP_EOL;
	    }
	    echo "<div class='section-separator'>Zero Quantities</div>" . PHP_EOL;
	    echo "<div class='grid-container'>" . PHP_EOL;
	    $item->displayItems("AND quantity = 0");
	    echo "</div>" . PHP_EOL;
	    break;
	case "a": // sort alphabetically
	    echo "<div class='grid-container'>" . PHP_EOL;
	    $item->displayItems("");
	    echo "</div>" . PHP_EOL;
	    break;
	default: // sort "by quantity" - the default
	    echo "<div class='grid-container'>" . PHP_EOL;
	    $item->displayItems("AND quantity > 0");
	    $item->displayItems("AND quantity < 0");
	    $item->displayItems("AND quantity = 0");
	    echo "</div>" . PHP_EOL;
	    break;
    }

    echo "</form>" . PHP_EOL;
} else { /* POST - a button has been pressed */
    $item->updateItemQuantities($_POST);
    if ($user->getDisplayUpdates($_SESSION['username']) == "No") {
	header("Location: index");
	exit();
    } else {
	Menu::displayMenus(FALSE);
    }
}
?>

</body>
</html>
