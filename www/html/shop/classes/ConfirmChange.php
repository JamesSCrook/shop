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
class ConfirmChange
{

    public static function confirmSuccess($message)
    {
        echo "<span class=success_symbol>&#x2714; </span>";
        echo htmlspecialchars($message, ENT_QUOTES) . "<p>\n";
        echo "<form id=ack_manage method='POST'>\n";
        echo "<button class='bttn' style=background-color:aqua; name='ack_manage_bttn'>&#x25C0; Back to Admin</button>\n";
        echo "</form>\n";
    }
}
?>
