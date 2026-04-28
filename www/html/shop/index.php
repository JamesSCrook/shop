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
 * Item (main) page. Display all the items and their current quantity values.
 * There are 3 sort orders:
 * q (quantity), cq (categoryname, quantity), and a (alphabetical).
 * See below for details.
 */
session_start();
require_once "Classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");

if (!isset($_SESSION['username'])) {
    header('Location: login');
    exit();
}

$dbConnection = new DBConnection();
$user = new User($dbConnection);
$item = new Item($dbConnection);

$pageSubtitle = "Items";
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    Utils::topOfPageHTML(": $pageSubtitle");
    $username = $_SESSION['username'];

    Menu::displayMenus(TRUE);
    echo "<h3>" . Constant::WEBSITEDESCRIPTION . ": $pageSubtitle (" . htmlspecialchars($username, ENT_QUOTES) . ")</h3>" . PHP_EOL;
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

    $_SESSION['previous_page'] = htmlspecialchars(basename($_SERVER['PHP_SELF'], '.php'), ENT_QUOTES);
} else { /* POST - a button has been pressed */
    $changedItemSummaryTable = [];
    $item->updateItemQuantities($_POST, $changedItemSummaryTable);
    if ($user->getDisplayUpdates($_SESSION['username']) == "No") {
	Utils::reloadSamePage(htmlspecialchars(basename($_SERVER['PHP_SELF'], '.php'), ENT_QUOTES));
	exit();
    } else {
	Utils::topOfPageHTML(": $pageSubtitle");
	Menu::displayMenus(FALSE);
	if (!empty($changedItemSummaryTable)) {
	    $item->displayChangedItemSummary($changedItemSummaryTable);
	}
    }
}
?>

</body>
</html>
