<?php
$preferences = getPreferences();
//echo "GET:<pre>";print_r($_GET);echo "</pre>";
//echo "POST:<pre>";print_r($_POST);echo "</pre>";

if (isset($_POST["nights"]) && is_numeric($_POST["nights"]) && $_POST["nights"] > 0 &&isset($_POST["nightprice"]) && is_numeric($_POST["nightprice"]) && $_POST["nightprice"] > 0){
	if (!isset($_POST["creditID"]) || !is_numeric($_POST["creditID"])){
		//print_r($_POST);
		$sql = "INSERT INTO credits (memberID, campNights, value) VALUES ('" . $_POST["memberID"] . "', '" . $_POST["nights"] . "', '" . ($_POST["nights"] * $_POST["nightprice"]) . "')";	
		$db->execute($sql);
		//echo $sql;
	}
	else {
		$sql = "UPDATE credits SET campNights = '" . $_POST["nights"] . "', value = '" . ($_POST["nights"] * $_POST["nightprice"]) . "' WHERE ID = '" . $_POST["creditID"] . "'";
		$db->execute($sql);
		//echo $sql;
	}
	
}

if (isset($_GET["paypal"])){
	//echo "POST:<pre>";print_r($_POST);echo "</pre>";
	//echo "GET:<pre>";print_r($_GET);echo "</pre>";
	
	foreach ($_GET as $key => $elem){
			$_POST[$key] = $elem;	
	}
	
	
	if ($_GET["paypal"] == "error"){
		$paypal = "true";
	}
	elseif ($_GET["paypal"] == "true"){
		$payed = "true";
	}
	
	unset ($_GET);
	//echo "POST_new:<pre>";print_r($_POST);echo "</pre>";
	//echo "GET_new:<pre>";print_r($_GET);echo "</pre>";
	
}
elseif(isset($_POST["add"]) && !isset($_POST["paypal"])){
	if (!is_numeric($_POST["add"])){
		echo "Not numeric!!!";
	}
	else {
		if(isset($_POST["pass"])){
			if ($_SESSION["user"]["id"] == -1){
				$sql = "SELECT password FROM information LIMIT 0,1";
			}
			else {
				$sql = "SELECT password FROM members WHERE driver = '1' AND ID = '" . $_SESSION["user"]["id"] . "' LIMIT 0,1";
			}
			$DB_PASS = $db->querySingleItem($sql);
			if (md5($_POST["pass"]) == $DB_PASS || $_POST["pass"] == STANDARD_PASS){
				if($_POST["paymentID"] == 1){
					$paypal = "true";
				}
				else {
					$payed = "true";
				}
			}
			else {
				//todo prüfe => mehr als 3mal falsch => logout
				if(isset($_SESSION["user"]["error"]) && $_SESSION["user"]["error"] > 3){
					$logout = "true";	
				}
				else {
					if(isset($_SESSION["user"]["error"])){
						$_SESSION["user"]["error"]++;	
					}
					else {
						$_SESSION["user"]["error"] = 1;
					}
					
				}
				
			}
			
		}
	}
}

if (isset($paypal) && $paypal == "true"){
	echo "--BACK_PAGE--invoice_detail--";	
	echo "--BACK_TITLE--back--";
	$i=0;
	$backoptions = "";
	foreach ($_POST as $key => $elem){
		if ($key != "add" && $key != "pass"){
			if ($i>0){ $backoptions .= "&";}
			$backoptions .= $key . "=" . $elem;
			$i++;
		}
	}
	echo "--BACK_OPTIONS--" . $backoptions . "--";
	//echo "Back:--->".$backoptions;
	
	
	?>
	<form action="https://www.paypal.com/uk/cgi-bin/webscr" method="post" id="PayPalForm">
	<h2>Pay with Paypal</h2>
	<ul>
		<?php
			echo "--TITLE--Paypal--";
			if (isset($_POST["paypal"])){
				echo "<li>Error! Not Paid!</li>";
				echo "<li><a href=\"javascript:document.getElementById('PayPalForm').submit();\">Go back to Paypal<span class=\"showArrow secondaryArrow\">.</span></a></li>";
			}
			else {
				echo "<li><a href=\"javascript:document.getElementById('PayPalForm').submit();\">Go to Paypal<span class=\"showArrow secondaryArrow\">.</span></a></li>";
			}
		?>
		
	</ul>
	<h3>In order to mark the invoice as paid you have to click on "Return to Shop", on the PayPal site, after the payment has been carried out</h3>
    
   <input type="hidden" name="cmd" value="_xclick" />
   <input type="hidden" name="business" value="<?php echo $preferences[0]["PaypalMail"];?>" />
   <input type="hidden" name="item_name" value="<?php  echo $preferences[0]["contact_name_of_school"]; ?>" />
   <input type="hidden" name="cancel_return" value="http://<?php echo $_SERVER["HTTP_HOST"] . "/index.php?paypal=error";
	foreach ($_POST as $key => $elem){
		if ($key != "pass"){
			echo "&" . $key . "=" . $elem;
		}
	}
   ?>" />
   <input type="hidden" name="return" value="http://<?php echo $_SERVER["HTTP_HOST"] . "/index.php?paypal=true";
	foreach ($_POST as $key => $elem){
		if ($key != "pass"){
			echo "&" . $key . "=" . $elem;
		}
	}
	if (isset($_GET["jsp"])){
		echo "&page=" . $_GET["jsp"];
	}
   ?>" />
   <input type="hidden" name="currency_code" value="<?php echo getPreferences("currencyShort"); ?>" />
   <input type="hidden" name="amount" value="<?php echo $_POST["add"]; ?>" />
   <input type="hidden" name="notify_url" value="http://chris.wakesystem.werbungmueller.de/paypal.php" />
   
</form>
	<?
	
}
elseif(isset($payed) && $payed == "true"){
	//member, add, paymentID, loginID
	if (isset($_POST["paypal"])){
		//echo "SET PAYPAL AS PAYD!!!!";
	}
	
	
	if (isset($_POST["riderID"])){
            if (!isset($_POST["invoice_mail"])){$_POST["invoice_mail"] = null;}
            setPaid($_POST["riderID"],$_POST["add"],$_POST["paymentID"],$_SESSION["user"]["id"],2,"member");
	}
	elseif (isset($_POST["memberID"])){
            if (!isset($_POST["invoice_mail"])){$_POST["invoice_mail"] = null;}
            setPaid($_POST["memberID"],$_POST["add"],$_POST["paymentID"],$_SESSION["user"]["id"],2,"member");
	}
	elseif (isset($_POST["rideID"])){
            if (!isset($_POST["invoice_mail"])){$_POST["invoice_mail"] = null;}
            setPaid($_POST["rideID"],$_POST["add"],$_POST["paymentID"],$_SESSION["user"]["id"],2,"nonmember",$_POST["invoice_mail"]);
	}

	
	if (isset($_POST["invoice_detail"])){
		if ($_POST["invoice_detail"] == "camp_all"){
			$_POST["camp"] = "all";
			unset ($_POST["memberID"]);
		}
		elseif ($_POST["invoice_detail"] == "nonMember_all" ){
			$_POST["nonMember"] = "all";
			unset ($_POST["rideID"]);
		}
		elseif ($_POST["invoice_detail"] == "nonMember_today"){
			$_POST["nonMember"] = "today";
			unset ($_POST["rideID"]);
		}
		unset ($_POST["add"]);
		unset ($payed);
		
		include ('../pages_iphone_admin/invoice_detail.inc.php');
	}
	elseif (isset($_POST["showmember"])){
		$_POST["UserID"] = $_POST["memberID"];
		include ('../pages_iphone_admin/member_show.inc.php');
	}
	else {
		include ('../pages_iphone_admin/ride.inc.php');
	}
	
	
}
elseif (isset($logout) && $logout == "true"){
	include ('../pages_iphone_admin/logout.inc.php');
}
elseif ( (isset($_POST["rideID"]) && is_numeric($_POST["rideID"])) ||  (isset($_POST["riderID"]) && is_numeric($_POST["riderID"])) || (isset($_POST["memberID"]) && is_numeric($_POST["memberID"])))
	{
	echo "<form method=\"POST\" name=\"invoice_detail\" action=\"javascript: LoadPage('addcredit',document.getElementById('form_invoice_detail'));\" id=\"form_invoice_detail\">";
	foreach ($_POST as $key => $elem){
            if ($key != "nightprice" && $key != "nights" && $key != "creditID"){
                echo "<input type=\"hidden\" name=\"" . $key . "\" value=\"" . $elem . "\">";
            }
	}
	
	//echo "<pre>";print_r($_POST);echo"</pre>";
	
	if (isset($_POST["invoice_detail"]) && $_POST["invoice_detail"] == "camp_all"){
		echo "--BACK_PAGE--invoice_detail--";
		echo "--BACK_TITLE--back--";
		echo "--BACK_OPTIONS--camp=all--";
		//todo Options	
	}
	elseif (isset($_POST["invoice_detail"]) && ($_POST["invoice_detail"] == "member_today" || $_POST["invoice_detail"] == "nonMember_all" || $_POST["invoice_detail"] == "nonMember_today") ){
		echo "--BACK_PAGE--invoice_detail--";
		echo "--BACK_TITLE--back--";
		if ($_POST["invoice_detail"] == "member_today"){
			echo "--BACK_OPTIONS--Member=today--";
		}
		elseif ($_POST["invoice_detail"] == "nonMember_today"){
			echo "--BACK_OPTIONS--nonMember=today--";
		}
		else {
			echo "--BACK_OPTIONS--nonMember=all--";	
		}
	}
	elseif (isset($_POST["showmember"])){
		echo "--BACK_PAGE--member_show--";	
		echo "--BACK_TITLE--back--";
		echo "--BACK_OPTIONS--" . $_POST["showmember"] . "--";
	}
	else {
		echo "--BACK_PAGE--ride--";	
		echo "--BACK_TITLE--back--";
		$i=0;
		$backoptions = "";
		if (isset($_POST["showmember"])){
			
		}
		else {
			foreach ($_POST as $key => $elem){
				if ($key != "riderID" && $key != "rideID"){
					if ($i>0){ $backoptions .= "&";}
					$backoptions .= $key . "=" . $elem;
					$i++;
				}
			}
		}
		echo "--BACK_OPTIONS--" . $backoptions . "--";
	}
	
	
	echo "&nbsp;";
	
	
	//echo "<pre>";print_r($_POST);echo"</pre>";
	
	if (isset($_POST["input"]) && is_numeric($_POST["input"])){
		echo "achtung Zahlungsabfrage TODO";
	}	
		
	if (isset($_POST["riderID"]) && is_numeric($_POST["riderID"])){
		$sql = "SELECT *, CONCAT(first_name , ' ', last_name) as name FROM members WHERE ID = '" . $_POST["riderID"] . "'";
		$memberData= $db->queryArray($sql);
	}
	elseif ( isset($_POST["memberID"]) && is_numeric($_POST["memberID"])){
		$sql = "SELECT *, CONCAT(first_name , ' ', last_name) as name FROM members WHERE ID = '" . $_POST["memberID"] . "'";
		$memberData= $db->queryArray($sql);
	}
	elseif (isset($_POST["rideID"]) && is_numeric($_POST["rideID"])){
		$sql = "SELECT *,riderName as name FROM rides WHERE ID = '" . $_POST["rideID"] . "'";
		$memberData= $db->queryArray($sql);
	}
		
	echo "--TITLE--Credits--";
	echo "--HOME--";
	echo "<h2>" . $memberData[0]["name"] . "</h2>";
	echo "<ul>";
	//echo "<pre>";print_r($_POST);echo"</pre>";
		if ( isset($_POST["rideID"]) && is_numeric($_POST["rideID"])){
			echo "<li>To Pay: " ;
			echo "<span class=\"secondaryWArrow\"";
					echo " style=\"color:red;\"";
			echo ">" . $memberData[0]["priceTotal"] . " " . getPreferences("currencyHTML") . "";
			echo "</span></a></li>";
			echo "<input class=\"secondaryInput\" name=\"add\" type=\"hidden\" value = '" . $memberData[0]["priceTotal"] . "' size=\"12\" maxlength=\"30\" />";
		
		}
		else {
			echo "<li>Current: " ;
			$credits = getCurrentCredit($memberData[0]["ID"]);
			if(!isset($credits) || $credits == null){
				$credits = 0;
			}
			echo "<span class=\"secondaryWArrow\"";
			if ($credits < 0) {
					echo " style=\"color:red;\"";
			}
			echo ">" . $credits . " " . getPreferences("currencyHTML") . "";
			echo "</span></a></li>";
			
			if (isset($memberData[0]["campRider"]) && $memberData[0]["campRider"] == "yes"){
				echo "<input type=\"hidden\" name=\"add\" value = \"" . abs($credits) . "\">";
			}
			else {
				echo "<li>Add Prepaid<span class=\"showArrow secondaryWArrow\"><label><input class=\"secondaryInput\" name=\"add\" type=\"text\" size=\"12\" maxlength=\"30\" /></label>" . getPreferences("currencyHTML") . "</span></li>";
			}
		}
		
		
		
	echo "</ul>";
	
	if (isset($memberData[0]["campRider"]) && $memberData[0]["campRider"] == "yes"){
		echo "<h2>Camp Nights</h2>";
		echo "<ul>";
			$sql = "SELECT * FROM credits WHERE campNights > 0 AND memberID = '" . $memberData[0]["ID"] . "' AND (invoiceID IS NULL OR invoiceID = 0)";
			$campNights = $db->queryArray($sql);
			if (!is_array($campNights) || $campNights == "-1"){
				echo "<li><a href=\"javascript: LoadPage('campNights','rideID=" . $memberData[0]["ID"] . "&memberID=" . $memberData[0]["ID"] . "&invoice_detail=" . $_POST["invoice_detail"] . "');\">Add Nights<span class=\"showArrow secondaryWArrow\"> </span></a></li>";	
			}
			else {
				echo "<li><a href=\"javascript: LoadPage('campNights','rideID=" . $memberData[0]["ID"] . "&memberID=" . $memberData[0]["ID"] . "&invoice_detail=" . $_POST["invoice_detail"] . "&creditID=" . $campNights[0]["ID"] . "');\">Edit Nights<span class=\"showArrow secondaryWArrow\">(" . $campNights[0]["campNights"] . " night";
				if ($campNights[0]["campNights"] > 1){
					echo "s";
				}
				echo " / " . $campNights[0]["value"] . " " . getPreferences("currencyHTML") . ")</span></a></li>";
			}
		echo "</ul>";
	}
		
	
	?>
	<h2>Safety</h2>
	<ul>
	<li>Driver Password<span class="showArrow secondaryWArrow">
		        <label><input class="secondaryInput" name="pass" type="password" size="12" maxlength="30" /></label></span></li>
	</ul>
        <?php
        if ( isset($_POST["rideID"]) && is_numeric($_POST["rideID"])){
            ?>
            <h2>Mail Invoice to Non-Member</h2>
            <ul>
            <li>Email<span class="showArrow secondaryWArrow">
                            <label><input class="secondaryInput" name="invoice_mail" type="email" size="12" maxlength="100" /></label></span></li>
            </ul>
	<?php
        }
        ?>
	<h2>Mode of Payment</h2>
	 <ul>
	<?php
		$payments = getPaymentData();
		foreach ($payments as $payment){
			echo "<li>";
			echo "<a href=\"javascript: LoadPage('addcredit','" . $payment["ID"] . "');\">";
			echo $payment["name"];
			if ($payment["value"] > 0 || $payment["value"] > 0){
				
				/*
				echo " (";
				if ($payment["value"] > 0){
					echo " + " . $payment["value"] . getPreferences("currencyHTML");
				}
				if ($payment["percent"] > 0){
					echo " + " . $payment["percent"] . "%";
				}
				echo ")";
				*/
			}
			echo "<span class=\"showArrow secondaryArrow\">.</span></a></li>";
		}
	echo "</ul>";
	echo "</form>";
}
else {
	echo "--BACK_PAGE--invoice_management--";	
	echo "--BACK_TITLE--back--";
	echo "--HOME--";
	//echo "<pre>";print_r($_POST);echo "</pre>";
	if (isset($_POST["camp"]) && $_POST["camp"] == "all"){
		echo "--TITLE--Select Rider--";
		$sql = "SELECT B.ID, CONCAT(B.first_name , ' ' , B.last_name) as name, sum(A.value) AS value FROM credits AS A LEFT JOIN members AS B ON A.memberID = B.ID WHERE A.invoiceID IS NULL AND B.campRider = 'yes' GROUP BY A.memberID";
		$members = $db->queryArray($sql);
	}
	elseif (isset($_POST["nonMember"]) && $_POST["nonMember"] == "all"){
		echo "--TITLE--Non Member--";
		$sql = "SELECT B.riderName as name, A.value, rideID FROM credits AS A LEFT JOIN rides AS B ON A.rideID = B.ID WHERE A.invoiceID IS NULL AND A.memberID IS NULL AND A.value > 0";
		$members = $db->queryArray($sql);
		//echo $sql;
	}
	elseif (isset($_POST["nonMember"]) && $_POST["nonMember"] == "today"){
		echo "--TITLE--Non Member--";
		//todo TimeFilter!
		$sql = "SELECT B.riderName as name, A.value, A.rideID FROM credits AS A LEFT JOIN rides AS B ON A.rideID = B.ID WHERE A.invoiceID IS NULL AND (A.memberID IS NULL OR A.memberID = 0) AND A.rideID IN (SELECT rideID FROM rideTimes WHERE start BETWEEN '" . mktime(0,0,0,date("m"),date("d"),date("Y")) . "' AND '" . mktime(23,59,59,date("m"),date("d"),date("Y")) ."' GROUP BY rideID)";
		//echo $sql;
		
		
		$members = $db->queryArray($sql);
	}
	
	echo "&nbsp;<ul>";
		if (isset($members) && is_array($members)){
			foreach ($members as $member){
				if (isset($_POST["camp"]) && $_POST["camp"] == "all"){
					echo "<li>";
					echo "<a href=\"javascript: LoadPage('invoice_detail','memberID=" .  $member["ID"]. "&invoice_detail=camp_all');\">";
					echo $member["name"]. " ";
					$credits = getCurrentCredit($member["ID"]);
					if(!isset($credits) || $credits == null){
						$credits = 0;
					}
					echo "<span class=\"showArrow secondaryWArrow\"";
					if ($credits < 0) {
						echo " style=\"color:red;\"";
					}
					echo ">" . $credits . " " . getPreferences("currencyHTML") . "";
					echo "</span></a></li>";
					
				
				}
				elseif (isset($_POST["nonMember"]) && ($_POST["nonMember"] == "all" || $_POST["nonMember"] == "today") ) {
					echo "<li>";
					echo "<a href=\"javascript: LoadPage('invoice_detail','rideID=" .  $member["rideID"]. "&invoice_detail=nonMember_" . $_POST["nonMember"] . "');\">";
					echo $member["name"]. " ";
					echo "<span class=\"showArrow secondaryWArrow\" style=\"color:red;\"";
					echo ">" . $member["value"] . " " . getPreferences("currencyHTML") . "";
					echo "</span></a></li>";
				}
				
				
			}	
		}
		else {
			echo "<li>no open invoices</li>";
		}
	
	
		
	echo "</ul>";
}
?>
	