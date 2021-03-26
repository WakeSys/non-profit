<?php
$sql = "DELETE FROM members_lock WHERE loginID = '" . $_SESSION["user"]["id"] . "' AND session_id='" . session_id() . "'";
$db->execute($sql);
$_SESSION["user"]["id"] = null;
//todo reset Fehler!!!
unset ($_SESSION["user"]["id"]);
setUserCookie(null);
include ('../pages_iphone_admin/login.inc.php');


?>

