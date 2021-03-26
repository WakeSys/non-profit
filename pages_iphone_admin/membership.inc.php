<?php
if (!isset($_POST["memberID"]) || !is_numeric($_POST["memberID"])){
	die ("Error no Member selected");
}
echo "--TITLE--Membership--";

$sql = "SELECT * from membership WHERE memberID = '" . $_POST["memberID"]. '";
$membershipData = $db->queryArray($sql);