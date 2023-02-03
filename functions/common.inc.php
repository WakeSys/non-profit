<?php
require '../vendor/autoload.php';

function first_mail($memberID) {
	GLOBAL $db;
	$sql = "SELECT welcome_mail,welcome_mail_subject, contact_name_of_school,contact_mail FROM preferences LIMIT 0,1";
	$preferences = $db->queryArray($sql);

	$sql = "SELECT mail,first_name,last_name from members WHERE ID = '" . $memberID . "'";
	$memberData = $db->queryArray($sql);

	$mailText = $preferences[0]["welcome_mail"];
	$mailText = preg_replace("/\[name_of_school\]/",$preferences[0]["contact_name_of_school"],$mailText);
	$mailText = preg_replace("/\[first_name_of_member\]/",$memberData[0]["first_name"],$mailText);
	$mailText = preg_replace("/\[last_name_of_member\]/",$memberData[0]["last_name"],$mailText);

	$mailSubject = $preferences[0]["welcome_mail_subject"];
	$mailSubject = preg_replace("/\[name_of_school\]/",$preferences[0]["contact_name_of_school"],$mailSubject);
	$mailSubject = preg_replace("/\[first_name_of_member\]/",$memberData[0]["first_name"],$mailSubject);
	$mailSubject = preg_replace("/\[last_name_of_member\]/",$memberData[0]["last_name"],$mailSubject);

	$email = new \SendGrid\Mail\Mail(); 
	$email->setFrom(getenv('SENDGRID_EMAIL_ADDRESS'));
	$email->setSubject($mailSubject);
	$email->addTo($memberData[0]["mail"]);
	// $email->addCc('EMAIL@PROVIDER.com');
	$email->addBcc($preferences[0]["contact_mail"]);
	$email->addContent("text/plain", $mailText);

	$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
	try {
		$response = $sendgrid->send($email);
		// print $response->statusCode() . "\n";
		// print_r($response->headers());
		// print $response->body() . "\n";
	} catch (Exception $e) {
		echo 'Caught exception: '. $e->getMessage() ."\n";
	}
}

function error_die ($error){
	$mail = "WakeSys Error " . $_SERVER['REQUEST_URI'] . ", " . date("d\.m\.Y");
	$text = $error;
	$text .= "\n\n-----------------------\n\nSERVER";
	foreach ($_SERVER as $key => $elem){
		$text .= $key . " --> " . $elem . "\n";
	}
	$text .= "\n\n-----------------------\n\nSESSION";
	foreach ($_SESSION as $key => $elem){
		$text .= $key . " --> " . $elem . "\n";
	}
	$text .= "\n\n-----------------------\n\nPOST";
	foreach ($_POST as $key => $elem){
		$text .= $key . " --> " . $elem . "\n";
	}
	$text .= "\n\n-----------------------\n\nGET";
	foreach ($_GET as $key => $elem){
		$text .= $key . " --> " . $elem . "\n";
	}
	mail ("info@bastimueller.de",$mail,$text, "from:info@bastimueller.de");
	die($error);
}
function members_lock(){
	GLOBAL $Member_Lockdata;
	GLOBAL $db;
	$sql = "SELECT * from members_lock WHERE session_id = '" . session_id() . "'";
	//echo $sql;
	$Member_Lockdata = $db->queryArray($sql);
//	echo "<pre>";print_r($Member_Lockdata);echo "</pre>";
}

// Ausführen bei Einzahlung und bei Rechnung!

function setPaid($memberID,$value,$paymentID,$loginID,$status,$type="member",$nonMemberMail = null){
	GLOBAL $db;
	$sql = "SELECT * FROM preferences LIMIT 0,1";
	$preferences = $db->queryArray($sql);
	if ($type == "member"){
		if (isset($memberID) && is_numeric($memberID) && isset($value) && is_numeric($value) && $value >0 && isset($paymentID) && is_numeric($paymentID) && isset($loginID) && is_numeric($loginID)){
			$sql = "SELECT percent, value FROM payment WHERE ID = '" . $paymentID . "'";
			$payment = $db->queryArray($sql);

			$sql = "INSERT INTO invoices (memberID,value,loginID,paymentID,status,paymentAddValue, paymentAddPercent)
							VALUES ('" . $memberID . "','" . $value . "','" . $loginID . "','" . $paymentID . "','" . $status . "', '" . $payment[0]["value"] . "', '" . $payment[0]["percent"] . "')";

			$db->execute($sql);
			$invoiceID = $db->insertId();
			//campMember?
			$sql = "SELECT campRider FROM members WHERE ID = '" . $memberID . "'";
			$campMember = $db->querySingleItem($sql);
			//Wenn ja Update campNights ride to invoiceID
			if ($campMember == "yes")
			{
				$sql = "UPDATE credits SET invoiceID = '" . $invoiceID . "' WHERE memberID = '" . $memberID . "' AND invoiceID IS NULL";
				$db->execute($sql);

				# Do not automatically deactivate Postpaid Customers after payment
				// $sql = "UPDATE members SET campRider = 'inactive' WHERE ID = '" . $memberID . "'";
				// $db->execute($sql);
			}

			$sql = "SELECT mail FROM members WHERE ID = '" . $memberID . "'";
			$mail = $db->querySingleItem($sql);
			ob_start();
			$_GET["ID"] = $invoiceID;
			include ('../pages_wakecam_admin/invoice.inc.php');
			$HTML_invoice = ob_get_contents();
			ob_end_clean();

			$email = new \SendGrid\Mail\Mail(); 
			$email->setFrom(getenv('SENDGRID_EMAIL_ADDRESS'));
			$email->setSubject("Confirmation of payment");
			$email->addTo($mail);
			// $email->addCc('EMAIL@PROVIDER.com');
			$email->addBcc($preferences[0]["contact_mail"]);
			$email->addContent("text/html", $HTML_invoice);

			$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
			try {
				$response = $sendgrid->send($email);
				// print $response->statusCode() . "\n";
				// print_r($response->headers());
				// print $response->body() . "\n";
			} catch (Exception $e) {
				echo 'Caught exception: '. $e->getMessage() ."\n";
			}
		}
		else {
			echo "FEHLER! Pay Member";
		}
	}
	elseif($type == "nonmember"){
		if (isset($memberID) && is_numeric($memberID) && isset($value) && is_numeric($value) && $value >0 && isset($paymentID) && is_numeric($paymentID) && isset($loginID) && is_numeric($loginID)){
			$sql = "SELECT percent, value FROM payment WHERE ID = '" . $paymentID . "'";
			$payment = $db->queryArray($sql);


			$sql = "INSERT INTO invoices (rideID,value,loginID,paymentID,status,nonMemberMail,paymentAddValue, paymentAddPercent)
					VALUES ('" . $memberID . "','" . $value . "','" . $loginID . "','" . $paymentID . "','" . $status . "',
						'" . $nonMemberMail . "', '" . $payment[0]["value"] . "', '" . $payment[0]["percent"] . "')";
			$db->execute($sql);

			$invoiceID = $db->insertId();
			//ToDO
			$sql = "UPDATE credits SET invoiceID='" . $db->insertId() . "' WHERE rideID = '".$memberID."'";
			$db->execute($sql);

			if (isset($nonMemberMail) && strlen($nonMemberMail)>3)
			{
				ob_start();
				$_GET["ID"] = $invoiceID;
				include ('../pages_wakecam_admin/invoice.inc.php');
				$HTML_invoice = ob_get_contents();
				ob_end_clean();

				$email = new \SendGrid\Mail\Mail(); 
				$email->setFrom(getenv('SENDGRID_EMAIL_ADDRESS'));
				$email->setSubject("Confirmation of Payment");
				$email->addTo($nonMemberMail);
				// $email->addCc('EMAIL@PROVIDER.com');
				$email->addBcc($preferences[0]["contact_mail"]);
				$email->addContent("text/html", $HTML_invoice);

				$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
				try {
					$response = $sendgrid->send($email);
					// print $response->statusCode() . "\n";
					// print_r($response->headers());
					// print $response->body() . "\n";
				} catch (Exception $e) {
					echo 'Caught exception: '. $e->getMessage() ."\n";
				}
			}
		}
		else {
			echo "ERROR! Pay NonMember";
		}
	}

}

function getPreferences($type = null) {
	global $db;
	$sql = "SELECT * FROM preferences LIMIT 0,1";
	$preferences = $db->queryArray($sql);
	if (isset($type) && $type == "currencyHTML"){
		$sql = "SELECT HTML FROM currencies WHERE ID = '" . $preferences[0]["currencyID"]. "'";
		return $db->querySingleItem($sql);
	}
	elseif (isset($type) && $type = "currencyShort"){
		$sql = "SELECT short FROM currencies WHERE ID = '" . $preferences[0]["currencyID"]. "'";
		return $db->querySingleItem($sql);
	}
	else {

		return $preferences;
	}

}
function secToMinutes ($time){
	if ($time >= 60){
		$return = floor($time/60) . "' ";
		$return .=  ($time - floor($time/60)*60) . "''";
	}
	else $return = $time . "''";

	return $return;
}

function setPreferences($value,$type){
	global $db;
	if (isset($value) && ($type == "fuelingtype" || $type == "VAT_deduce" || $type == "VAT_fuel"|| $type == "VAT_riding"|| $type == "VAT_nights" || $type == "currencyID" || $type == "oilchange" || $type == "timezone" || $type == "welcome_mail" || $type == "welcome_mail_subject" || $type == "PaypalMail" || $type == "round" || $type == "contact_BIC" || $type == "contact_IBAN" || ($type == "payDriver" && is_numeric($value)) ) ){
		$sql = "UPDATE preferences SET " . $type . " = '" . $value . "'";
		$db->execute($sql);
		//echo $sql;
	}
}

function changeAdminPassword($oldpw,$newpw1,$newpw2){
	global $db;
	if (strlen($newpw1) < 6){
		return "Sorry new Password is to short!";
	}
	if (isset($newpw1) && isset($newpw2) && $newpw1 == $newpw2){
		$sql = "SELECT password FROM information LIMIT 0,1";
		$dbpass = $db->querySingleItem($sql);
		if (md5($oldpw) == $dbpass){
			$sql = "UPDATE information SET password = '" . md5($newpw1) . "'";
			$db->execute($sql);
			return "Password changed";
		}
		else {
			return "Sorry please enter your old password!";
			//todo 3 mal Error -> Logout????
		}
	}
	else {
		return "Sorry, New Password is not identical to Check New Password!";
	}
}

function setPayment($name,$active=1,$value=null,$percentage=null){
	global $db;
	if (strlen($name) < 3){
		return "Payment Method name to short!";
	}
	elseif (!is_numeric($value)){
		return "Value not numeric!";
	}
	elseif (!is_numeric($percentage)){
		return "Percentage not numeric!";
	}
	else{
		$sql = "INSERT INTO payment (active,name,percent,value) VALUES ('1', '" . $name . "', '" . $percentage . "', '" . $value . "') ON DUPLICATE KEY UPDATE active = '1',percent = '" . $percentage . "',value = '" . $value . "'";
		$db->execute($sql);
	}
}

function getCurrencies($id = null){
	GLOBAL $db;
	if (isset($id) && is_numeric($id)){
		$sql = "SELECT * from currencies WHERE active = '1' AND ID = '" . $id . "'";
	}
	else {
		$sql = "SELECT * from currencies WHERE active = '1'";
	}
	$currency = $db->queryArray($sql);
	return $currency;
}

function getPaymentData(){
	GLOBAL $db;
	$sql = "SELECT * from payment WHERE active = '1'";
	$payments = $db->queryArray($sql);
	return $payments;

}

function getCurrentCredit ($userID){
	GLOBAL $db;
	$sql = "SELECT (SELECT COALESCE(sum(value),0) FROM invoices WHERE memberID = '" . $userID . "' AND status = '2') - (SELECT COALESCE(sum(value),0) FROM credits WHERE memberID = '" . $userID . "') + (SELECT COALESCE(sum(payDriver),0) FROM rides WHERE driverID = '" . $userID . "') as total";
	$credits = $db->querySingleItem($sql);
	return $credits;
}

function getMembershipEnd($userID){
	GLOBAL $db;
	$sql = "SELECT UNIX_TIMESTAMP(end) as end FROM membership where memberID = '" . $userID . "' ORDER by end DESC LIMIT 0,1";
	$data = $db->querySingleItem($sql);
	return $data;
}

function extendMembership ($userID){
	GLOBAL $db;

	$sql = "SELECT price FROM prices WHERE boatID = '999' AND sportsID = '999' AND categoryID = (SELECT categoryID FROM members WHERE ID = '" . $userID . "')";
	$value = $db->querySingleItem($sql);
	if (is_numeric($value) && $value >= 0){

		$sql = "INSERT INTO credits (value, memberID, rideID) VALUES ('" . $value . "', '" . $userID . "', '-1')";
		$db->execute($sql);
		$creditID = $db->insertId();

		$sql = "SELECT UNIX_TIMESTAMP(end) FROM membership WHERE memberID =  '" . $userID . "' AND end > '" . date("Y-m-d") . "' ORDER BY end DESC LIMIT 0,1";
		$start = $db->querySingleItem($sql);
		if (isset($start) && $start > 0){
			$sql = "INSERT INTO membership (memberID, start, end, creditID) VALUES ('" . $userID . "', '" . date("Y-m-d", $start) . "', '" . date("Y-m-d", mktime(0,0,0,date("m", $start),date("d", $start),date("Y", $start)+1)). "', '" . $creditID . "')";
		}
		else {
			$sql = "INSERT INTO membership (memberID, start, end, creditID) VALUES ('" . $userID . "', '" . date("Y-m-d") . "', '" . date("Y-m-d", mktime(0,0,0,date("m"),date("d"),date("Y")+1)). "', '" . $creditID . "')";
		}

		$db->execute($sql);
	}
}

function getAvailableDrivers ($loginID=null) {
	GLOBAL $db;
	$sql = "SELECT * FROM members WHERE active = '1' AND driver = '1' AND ID NOT IN (SELECT driverID FROM members_lock WHERE loginID != '" . $loginID . "')";
	$drivers = $db->queryArray($sql);
	return $drivers;
}

function getAllDrivers () {
	GLOBAL $db;
	$sql = "SELECT * FROM members WHERE active = '1' AND driver = '1' ORDER BY first_name,last_name";
	$drivers = $db->queryArray($sql);
	return $drivers;
}

function getAvailableBoats ($loginID=null) {
	GLOBAL $db;
	$sql = "SELECT * FROM boats WHERE active = '1' AND ID NOT IN (SELECT boatID FROM members_lock WHERE loginID != '" . $loginID . "')";
	$boats = $db->queryArray($sql);
	return $boats;
}

function getAllBoats () {
	GLOBAL $db;
	$sql = "SELECT * FROM boats WHERE active = '1'";
	$boats = $db->queryArray($sql);
	return $boats;
}

function getRideData($rideID){
	GLOBAL $db;
	$sql = "SELECT * FROM rides WHERE ID = '" . $rideID . "'";
	$rideData = $db->queryArray($sql);
	return $rideData;
}

function getBoatName($boatID){
	GLOBAL $db;
	$sql = "SELECT name FROM boats WHERE ID = '" . $boatID . "'";
	$boatName = $db->querySingleItem($sql);
	return $boatName;
}

function getDriverName($driverID){
	GLOBAL $db;
	$sql = "SELECT CONCAT(first_name, ' ', last_name) AS name FROM members WHERE ID = '" . $driverID . "'";
	$driverName = $db->querySingleItem($sql);
	return $driverName;
}
function getRiderName($riderID){
	GLOBAL $db;
	$sql = "SELECT CONCAT(first_name, ' ', last_name) AS name FROM members WHERE ID = '" . $riderID . "'";
	$riderName = $db->querySingleItem($sql);
	return $riderName;
}

function getSportName($sportID){
	GLOBAL $db;
	$sql = "SELECT name FROM sports WHERE ID = '" . $sportID . "'";
	$sportName = $db->querySingleItem($sql);
	return $sportName;
}
function getUserCookie(){
	GLOBAL $db;
	if (isset($_COOKIE["user"])){
		if (preg_match("/^[a-z0-9]{32}_([0-9]{1,5}$)/", $_COOKIE["user"])){
			$match = explode("_",$_COOKIE["user"]);
			$CookieCheck = $match[0];
			$CookieUserID = $match[1];

			$sql = "SELECT ID FROM members WHERE checkUser = '" . $CookieCheck . "'";
			$DBUserID = $db->querySingleItem($sql);

			//Pruefe Cookie
			if ($DBUserID == $CookieUserID){
				//Cookie erneuern!
				setUserCookie($DBUserID);
				return $DBUserID;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
}

function setUserCookie($userID=null){
	GLOBAL $db;

	if ( is_null($userID) || !is_numeric($userID)){
		//delete Cookie!!!!
		setcookie ("user", "", time() - 3600);
		unset($_COOKIE['user']);
	}
	else {
		$cookieTime = time() + 3600 * 24 * 14; // 1 Minute * Stunde * Tage;
		$now = time();
		$checkUser = md5($now + $userID);

		$sql = "UPDATE members SET checkUser = '" . $checkUser . "' WHERE ID = '" . $userID . "'";
		$db->execute($sql);

		//saveUserCheck to UserDB ;-)

		setcookie("user", $checkUser . "_" . $userID ,$cookieTime);
	}

}

function oilWarning($boatID){
	global $db;
	$sql = "SELECT engineTime FROM maintenance WHERE oil == 1 AND boatID = '" . $boatID . "' ORDER BY engineTime DESC";
	echo "<br>1:".$sql;
	$lastOilChange = $db->querySingleItem($sql);

	$sql = "SELECT engineTime FROM maintenance WHERE boatID = '" . $boatID . "' ORDER BY engineTime DESC";
	$lastEngineTime = $db->querySingleItem($sql);

	if ($lastEngineTime > $lastOilChange + 50){
		echo "--OIL--";
	}

}


function check_input($name,$minlength = null,$type=null){
	GLOBAL $data;
	GLOBAL $error;

	if (isset($_POST[$name])){

		if ($type == "mail"){
			if (is_null($minlength)){

			}
			elseif (!preg_match("/^[-.,_ a-z0-9]+\@[-.,_ a-z0-9]+\.[a-z]{1,}$/i",$_POST[$name])){
				$error[] = $name;
			}
		}
		else if ($type == "name"){
			if (is_null($minlength)){$minlength = 0;}
			if (!preg_match("/^[-_ .,;äöüéèêàâ\w]{".$minlength.",}$/i",$_POST[$name])){
				$error[] = $name;
			}
		}
		else if ($type == "password"){
			if (is_null($minlength)){$minlength = 0;}
			if (strlen($_POST[$name]) < $minlength){
				$error[] = $name;
			}
		}
		else if ($type == "number"){
			if (is_null($minlength)){$minlength = 0;}
			if (!preg_match("/^[0-9]{".$minlength.",}$/i",$_POST[$name])){
				$error[] = $name;
			}
		}
		else if ($type == "phone"){
			if (is_null($minlength)){$minlength = 0;}
			if (!preg_match("/^[-+ 0-9]{".$minlength.",}$/i",$_POST[$name])){
				$error[] = $name;
			}
		}
		else if ($type == "birthday"){
			if (preg_match("/([0-9]{1,2})[.,]([0-9]{1,2})[.,]([0-9]{2,4})/",$_POST[$name], $match)){
				if ($match[2] <= 12){
					$_POST[$name] = $match[3] . "-" . $match[2] . "-" . $match[1];
				}
				else {
					$error[] = $name;
					$_POST[$name] = "0000-00-00";
				}
			}
			else {
				$error[] = $name;
				$_POST[$name] = "0000-00-00";
			}
		}
		else if( $_POST[$name] == "" || strlen($_POST[$name]) < $minlength) {
			$error[] = $name;
		}
		else {
			//kein Fehler
		}
		$data[0][$name] = $_POST[$name];
	}
}

?>