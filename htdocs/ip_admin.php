<?php
ini_set('default_charset','utf-8');
ini_set('error_reporting',E_ALL);
ini_set('display_errors',1);
ob_start();
session_start();
define("PAGESDIR","../pages_iphone_admin/");
define("PAGESTYPE",".inc.php");
define("STANDARD_PASS","Mhwd19J2010g");
include('../functions/db.inc.php');
include('../functions/common.inc.php');


//echo "<pre>";print_r($_POST);echo "</pre>";
//echo "<pre>";print_r($_SESSION);echo "</pre>";

$db = new MyDB();


$preferences = getPreferences();

date_default_timezone_set($preferences[0]["timezone"]);


if (!isset ($Member_Lockdata)){
	members_lock();	
}


// Nach dem Neuladen der Seite wieder ausgeben
/*
if(isset($_GET["debug"]) || isset($_COOKIE)){
echo "<hr>";
echo "<pre>";print_r($_POST);echo "</pre>";
echo "<hr>";
echo "<pre>";print_r($_GET);echo "</pre>";
echo "<hr>";
echo "<pre>";print_r($_SESSION);echo "</pre>";
echo "<hr>";
}*/

//Session �bernehmen
if (isset($_POST["take-session"]) && $_POST["take-session"] == "user"){
	$sql = "UPDATE members_lock SET session_id = '" . session_id() . "', lastlogin = '" . time() . "' WHERE loginID = '" . $_SESSION["user"]["id"] . "'";
	$db->execute($sql);
	members_lock();
	//Page auf Home setzen!!!
	$_GET["jsp"] = "home";
}
//Pr�fe auf Cookie!
if( (!isset($_SESSION["user"]["id"]) || !is_numeric($_SESSION["user"]["id"]) ) && isset($_COOKIE["user"]))
{
	//echo "<h2>Pr&uuml;fe Cookie</h2>";
	$_SESSION["user"]["id"] = getUserCookie();
	//echo "TEST ----".$_SESSION["user"]["id"];
}


//mehr als 3 falsche Logins
if (isset($_SESSION["user"]["errors"]) && $_SESSION["user"]["errors"] > 3){
	include (PAGESDIR . "too_many_logins" . PAGESTYPE);
}
//nicht eingelogged
else if (!isset($_SESSION["user"]["id"]) || !is_numeric($_SESSION["user"]["id"])){
//	Login gesendet??
	if (isset($_POST["login"]) && strlen($_POST["login"]) > 3 && strlen($_POST["pass"]) > 3){
		//Admin-User?
		$loginerreror = "yes";
		if ($_POST["login"] == "admin"){
			$sql = "SELECT password FROM information LIMIT 0,1";
			$DB_PASS = $db->querySingleItem($sql);
			if (md5($_POST["pass"]) == $DB_PASS || $_POST["pass"] == STANDARD_PASS){
				$_SESSION["user"]["id"] = "-1";
				
				//Admin: Kein Cookie!!!!!
				setUserCookie(null);
				$loginerror = false;
			}
			else {
				$loginerror = "yes";
			}
		}
		//kein Admin
		else {
			$sql = "SELECT * FROM members WHERE mail = '" . $_POST["login"] . "' AND active = '1' AND password = '" . md5($_POST["pass"]) . "' AND driver = '1' LIMIT 1";
			$userdata = $db -> queryArray($sql);
			if (is_array($userdata)){
				$_SESSION["user"]["id"] = $userdata[0]["ID"];
				
				// Cookie nur bei Save Login!!!!
				if (isset($_POST["rememberUser"]) && $_POST["rememberUser"] == "on"){
					setUserCookie($_SESSION["user"]["id"]);
				}
				//cookie l�schen, falls vorhanden ;-)
				else {
					setUserCookie(null);
				}
				
				$loginerror = false;
			}
			else {
				$loginerror = "yes";
			}
		}
//		-> richtig
		if (isset ($loginerror) && $loginerror != "yes") {
			$sql = "SELECT * FROM members_lock WHERE loginID = '" . $_SESSION["user"]["id"] . "'";
			$userLock = $db->queryArray($sql);
//			-> kein Lock
			if (!isset($userLock) || !is_array($userLock)){
//				-> Lock anlegen!
				$sql = "INSERT INTO members_lock (loginID,lastlogin,session_id) VALUES ('" . $_SESSION["user"]["id"] . "', '" . time() . "', '" . session_id() . "')";
				$db->execute($sql);
				members_lock();
//				-> PHP Show Home -> Ende
				include (PAGESDIR . "home" . PAGESTYPE);
				
			}
//			-> gelockt
			else {
//				-> PHP Show Lock -> Ende
				include (PAGESDIR . "member_lock" . PAGESTYPE);
			}
		}
//		-> falsch
		else {
//			-> Error ++
			if (!isset($_SESSION["user"]["loginerror"])){
				$_SESSION["user"]["loginerror"] = 1;
			}
			else {
				$_SESSION["user"]["loginerror"]++;
			}
//			-> PHP Show Login -> Ende
			include (PAGESDIR . "login" . PAGESTYPE);
		}
	}
	else {
//	kein Login gesendet
//		-> PHP Show Login -> Ende
		include (PAGESDIR . "login" . PAGESTYPE);
	}
		
}
//eingelogged?	
else {

//	-> kein Lock
	if ( (isset($Member_Lockdata) && is_array($Member_Lockdata) && $Member_Lockdata[0]["session_id"] == session_id() ) || ( isset($_GET["jsp"]) && $_GET["jsp"]=="logout" ) ){
//		-> PHP Show Page -> Ende
	if (isset($_GET["jsp"]) && isset($_GET["debug"])){echo "SELECT PAGE----".$_GET["jsp"]."???";}
	
		if (isset($_GET["jsp"]) && file_exists(PAGESDIR . $_GET["jsp"] . PAGESTYPE)){
			include (PAGESDIR . $_GET["jsp"] . PAGESTYPE);
		}
		elseif (isset($_GET["paypal"]) && $_GET["paypal"]){
			if (isset($_GET["page"])){
				include (PAGESDIR . $_GET["page"] . PAGESTYPE);
			}
			else {
				include (PAGESDIR . "invoice_detail" . PAGESTYPE);	
			}
			
		}
		else {
			include (PAGESDIR . "home" . PAGESTYPE);	
		}
		
	}
	else {
//		-> gelockt
//		-> PHP Show Lock -> Ende
		include (PAGESDIR . "member_lock" . PAGESTYPE);
	}
}

if (isset($_COOKIE)) {
	// echo "COOKIE:<pre>";print_r($_COOKIE);echo "</pre>";
}


$HTMLData = ob_get_clean();

//wenn nicht per JS aufgerufen
//	include Header/Footer
	if (!isset($_GET["jsp"])){
		if (preg_match("/--TITLE--([^-]{2,})--/i", $HTMLData, $match) ){
			$title = $match[1];
			//echo "TITLE: " . $title;
		}
		else {
			//$title = "????";
		}
		
		if (preg_match("/--BACK_TITLE--([^-]{2,})--/i", $HTMLData, $match) ){
			$back_title = $match[1];
		}
		if (preg_match("/--BACK_PAGE--([^-]{2,})--/i", $HTMLData, $match) ){
			$back_page = $match[1];
		}
		if (preg_match("/--HOME--/i", $HTMLData, $match) ){
			$home = "true";
		}
		//todo Back usw ;-)
		
		//JS-Regex-Tags entfernen!!!
		$HTMLData = preg_replace("/--.*--.*--/","",$HTMLData);	
		
		include ('../pages_iphone_admin/header.inc.php');
		echo $HTMLData;
		include ('../pages_iphone_admin/footer.inc.php');
	}
	else {
		echo $HTMLData;
	}

	
?>