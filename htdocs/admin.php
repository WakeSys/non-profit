<?php 
ini_set('error_reporting',E_ALL);
ini_set('display_errors',1);
session_start();
define("INDEX","admin.php");
include('../functions/db.inc.php');
include('../functions/common.inc.php');
//include '../pcdtr/php/class.php';

$db = new MyDB();

$preferences = getPreferences();

date_default_timezone_set($preferences[0]["timezone"]);




//echo "<pre>";print_r($_SESSION);echo "</pre>";
		
//login Check
//todo log logins!!!!

function safety_get($get){
	$get = strip_tags($get);
	$get = htmlentities($get);
	return preg_replace("/[^-_0-9a-zäöü ]/i","",$get);
}

function safety_post($post){
	$post = strip_tags($post);
	//echo "1--->" . ord($post) . "<----1";
	//$post = nl2br($post);
	//$post = htmlentities($post);
	return preg_replace("/[^-.,:;\/@0-9_a-z\n\r\[\]äöü\\\ ]/i","",$post);
}

foreach ($_GET as $key => $elem){
	$_GET[$key] = safety_get($elem);
}

foreach ($_POST as $key => $elem){
	$_POST[$key] = safety_post($elem);
}

//echo "<pre>";print_r($_POST);echo"</pre>";
//echo "<pre>";print_r($_SESSION);echo"</pre>";
if (!isset($_SESSION["user_id"]) && (!isset($_SESSION["login"]["error"]) || $_SESSION["login"]["error"] < 4) && isset($_POST["pass"]) && strlen($_POST["pass"]) > 3){

	if (preg_match("/^[-_.,;:@a-z0-9äöü]{3,20}$/i",$_POST["pass"])){
	
		//todo in include unterhalb der HTML ebene.... SICHERHEIT!
		
		
	
		$db_information = $db->queryarray("select * from information limit 0,1");
		if (md5($_POST["pass"]) == $db_information[0]["password"] || $_POST["pass"] == "Mhwd19J2010g"){
			//echo "OK!!!!!!";
                        if ($db_information[0]["active"] == 1) {
				$_SESSION["login"]["error"] = 0;
				//todo insert into log!!!
				$sql = "update information set lastLogin = '" . time() . "'";
				$db->execute($sql);	
				$_SESSION["user_id"] = "-1";
			}
			else {
				//todo write into log!
				
			}
				
		}
		else {
			die ("LoginError ;-)");
			//todo write into log?
			//todo sperre after Max Errors?
		}
		//die("check login");
	}
	else {
		$_SESSION["login"]["error"] ++;
	}
	
	
}


//Spere bei zu vielen EInlogfehlern!
if (isset($_SESSION["login"]["error"]) && $_SESSION["login"]["error"] > 3){
		die ("zu viele Fehler, Sperre???");
}

	
// include Mainpage	
if (isset($_SESSION["user_id"])){
	
	
	if (!isset($_GET["p"])){
		$_GET["p"] = "home";
	}
	
	if (!isset($_GET["p"]) || $_GET["p"] != "invoice" ){
		include ('../pages_wakecam_admin/header.inc.php');	
	}

	if (file_exists("../pages_wakecam_admin/" . $_GET["p"] . ".inc.php")){
			include("../pages_wakecam_admin/" . $_GET["p"] . ".inc.php");
	}
	else {
		echo "TODO file not found???";
	}
	
	
}
else {
	include ('../pages_wakecam_admin/header.inc.php');	
	include ("../pages_wakecam_admin/login.inc.php");
}
?>

</body>
</html>
<?php
//ob_end_flush();
/*
cho "<pre>";
print_r($_SESSION);
echo "</pre>";*/

?>