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
 * Display all the items for a the specified first character. At this point, the sort
 * order for this page is always alphabetical on itemname (first) and unitname (second).
 */
session_start();
require_once "Classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");

if (!isset($_SESSION['username'])) {
    header('Location: login');
    exit();
}

$dbConnection = new DBConnection();
$item = new Item($dbConnection);
$username = $_SESSION['username'];
$pageSubtitle = "First Character";
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    Utils::topOfPageHTML(": $pageSubtitle");

    Menu::displayMenus(TRUE);
    echo "<h3>" . Constant::WEBSITEDESCRIPTION . ": Items starting with '" . htmlspecialchars($_GET['first_char'], ENT_QUOTES) . "'</h3>" . PHP_EOL;
    $item->displayLinks();
    echo "<form id=items_form method='POST'>" . PHP_EOL;
    echo "<div class='grid-container'>" . PHP_EOL;
    $itemCount = $item->displayItems("AND SUBSTR(itemname,1,1)='" . htmlspecialchars($_GET['first_char'], ENT_QUOTES) . "'");
    echo "</div>" . PHP_EOL;
    echo "</form>" . PHP_EOL;

    $_SESSION['previous_page'] = htmlspecialchars(basename($_SERVER['PHP_SELF'], '.php'), ENT_QUOTES);

    if ($itemCount == 0) {
	echo "Invalid first character '" . htmlspecialchars($_GET['first_char'], ENT_QUOTES) . "' specified in the URL." . PHP_EOL;
    }
} else { /* POST - a button has been pressed */
    if (isset($_POST['update_items_bttn'])) {
	$user = new User($dbConnection);
	$changedItemSummaryTable = [];
	$item->updateItemQuantities($_POST, $changedItemSummaryTable);
	if ($user->getDisplayUpdates($username) == "No") {
	    Utils::reloadSamePage(htmlspecialchars(basename($_SERVER['PHP_SELF'], '.php') . '?first_char=' . $_GET['first_char'], ENT_QUOTES));
	    exit();
	} else {
	    Utils::topOfPageHTML(": $pageSubtitle");
	    Menu::displayMenus(FALSE);
	    if (!empty($changedItemSummaryTable)) {
		$item->displayChangedItemSummary($changedItemSummaryTable);
	    }
	}
    }
}
?>

</body>
</html>
