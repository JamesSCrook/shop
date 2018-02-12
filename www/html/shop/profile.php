<?php
namespace JamesSCrook\Shop;

/*
 * shop - Copyright (C) 2017-2018 James S. Crook
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
<title>Shop: User Profile</title>
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
require_once "classes/Autoloader.php";
spl_autoload_register(__NAMESPACE__ . "\Autoloader::loader");

if (! isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

echo "<h3>Edit Profile: " . htmlspecialchars($_SESSION['username'], ENT_QUOTES) . "</h3>\n";

Menu::displayMenus(FALSE);
$user = new User();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    
    echo "<form id=profile_form method='POST'>\n";
    echo "New password<br><input type='password' name='newPassword1' size='20' pattern='.{6,}' placeholder='min 6 chars'><br>\n";
    echo "Repeat password<br><input type='password' name='newPassword2' size='20' pattern='.{6,}'><p>\n";
    
    $sortOrder = $user->getSortOrder($_SESSION['username']);
    echo "Sort Order<br>\n";
    echo "<select name='sortOrder'>\n";
    if ($sortOrder == "cq") {
        echo " <option value='q'>Quantity</option>\n";
        echo " <option value='cq' selected>Category, quantity</option>\n";
        echo " <option value='a'>Alphabetical</option>\n";
    } else if ($sortOrder == "a") {
        echo " <option value='q'>Quantity</option>\n";
        echo " <option value='cq'>Category, quantity</option>\n";
        echo " <option value='a' selected>Alphabetical</option>\n";
    } else {
        echo " <option value='q' selected>Quantity</option>\n";
        echo " <option value='cq'>Category, quantity</option>\n";
        echo " <option value='a'>Alphabetical</option>\n";
    }
    echo "</select><p>\n";
    
    echo "Display Updates<br>\n";
    echo "<select name='displayUpdates'>\n";
    if ($user->getDisplayUpdates($_SESSION['username']) == "No") {
        echo " <option value='Yes'>Yes</option>\n";
        echo " <option value='No' selected>No</option>\n";
    } else {
        echo " <option value='Yes' selected>Yes</option>\n";
        echo " <option value='No'>No</option>\n";
    }
    echo "</select><p>\n";
    echo " <button class='bttn' style=background-color:lightgreen; name='updateprofile'>&#x270E; Update Profile</button><p>\n";
    echo "</form>\n";
} else { /* POST - a button has been pressed */
    if (isset($_POST['updateprofile'])) {
        if ($_POST['newPassword1'] != "") {
            $user->setPassword($_SESSION['username'], $_POST['newPassword1'], $_POST['newPassword2']);
        }
        
        if ($_POST['sortOrder'] != "" && $_POST['sortOrder'] != $user->getSortOrder($_SESSION['username'])) {
            $user->setSortOrder($_SESSION['username'], $_POST['sortOrder']);
        }
        
        if ($_POST['displayUpdates'] != "" && $_POST['displayUpdates'] != $user->getDisplayUpdates($_SESSION['username'])) {
            $user->setDisplayUpdates($_SESSION['username'], $_POST['displayUpdates']);
        }
    }
}
?>

</body>
</html>
<!-- shop - Copyright (C) 2017-2018 James S. Crook - GPL3+ -->
