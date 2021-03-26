<?php 
$preferences = getPreferences();
//Todo
/*
	ToDo Insert Lock und Release Lock
*/
/*
 * Ride Status
 * 1	Angelegt
 * 2	enthält Daten
 * 3	gestartet!
 * 4	Pausiert
 * 5	gestoppt
 */
//Lade Member_Lockdata

//echo "<pre>";print_r($_POST);echo"</pre>";
//echo "<hr><pre>";print_r($_SESSION);echo"</pre>";
if (!isset ($Member_Lockdata)){
	$Member_Lockdata = $db->queryArray("SELECT * FROM members_lock WHERE session_id = '" . session_id() . "'");	
}

$rideData = getRideData($Member_Lockdata[0]["rideID"]);
//echo "<hr><pre>";print_r($rideData);echo"</pre>";	

//data for oilchange....
if (isset($_POST["engine_time"]) && $_POST["engine_time"] != "" && $_POST["engine_time"] != "enter engine time" && is_numeric($_POST["engine_time"] )){
	$sql = "SELECT engineTime FROM maintenance WHERE boatID = '" . $rideData[0]["boatID"] . "' AND (OIL = 1) ORDER BY engineTime DESC LIMIT 0,1";
	$lastOil = $db->querySingleItem($sql);
	

	$sql = "SELECT oilchange FROM preferences LIMIT 0,1";
	$oildiff = $db->querySingleItem($sql);
	
	$sql = "INSERT INTO maintenance (engineTime,boatID,loginID,rideID,ts) VALUES ('" . $_POST["engine_time"] . "', '" . $rideData[0]["boatID"] . "', '" . $_SESSION["user"]["id"] . "' , '" . $rideData[0]["ID"] . "', '" . date("Y-m-d H:i:s") . "')";
	$db->execute($sql);
	//Oil Warning!!!
	if(isset($_POST["engine_time"]) && ($lastOil + $oildiff) < $_POST["engine_time"]){
			$oilwarning = "alert";
	}
	unset($_POST["engine_time"]);	
}
	
if (!isset($_SESSION["ridesettings"])){
	if ($Member_Lockdata[0]["riderID"] == 0){
		$_SESSION["ridesettings"]["group"] = "nonmember";
	}
	else {
		$sql = "SELECT A.ID,campRider FROM rides AS A LEFT JOIN members AS B ON A.riderID=B.ID WHERE A.ID = '" . $Member_Lockdata[0]["rideID"] . "'";
		$camprider = $db->querySingleItem($sql);
		if ($camprider == 'no'){
			$_SESSION["ridesettings"]["group"] = "member";
		}
		else {
			$_SESSION["ridesettings"]["group"] = "camprider";
		}
		
	}
	$_SESSION["ridesettings"]["boatID"] = $Member_Lockdata[0]["boatID"];
	$_SESSION["ridesettings"]["driverID"] = $Member_Lockdata[0]["driverID"];
	$_SESSION["ridesettings"]["riderID"] = $Member_Lockdata[0]["riderID"];
	$_SESSION["ridesettings"]["catID"] = $Member_Lockdata[0]["catID"];
}

if (isset($_POST["start"]) && $_POST["start"] && 
	(
		(isset($_POST["non_member_rider_name"]) && ($_POST["non_member_rider_name"] == "" || $_POST["non_member_rider_name"] == "Enter Name") ) 
	||	
		(isset($_POST["engine_time"]) && ($_POST["engine_time"] == "" || ($_POST["engine_time"] == "enter engine time") || !is_numeric($_POST["engine_time"])) )
	||
		isset($oilwarning) && $oilwarning == "alert"
	)
	){
		if (isset($oilwarning) && $oilwarning == "alert"){
			echo "--OIL--";
		}
		if (isset($_POST["timepreset"])){
			$_SESSION["ridesettings"]["timePreset"] = $_POST["timepreset"];
		}

		if (isset($_POST["autostop"])){
			$_SESSION["ridesettings"]["autostop"] = $_POST["autostop"];
		}

		unset($_POST["start"]);
		include ("../pages_iphone_admin/riderunning.inc.php");
}

//Stop Ride!
elseif (isset($_POST["stop"]) || $rideData[0]["status"] == 5) {
//	echo "<pre>";print_r($_POST);echo "</pre>";
	$sql = "SELECT * FROM rideTimes WHERE rideID = '" . $Member_Lockdata[0]["rideID"] . "' AND status = '1'";
	$times = $db->queryArray($sql);
	if (is_array($times)){
		$sql = "UPDATE rideTimes SET status = '2', stop='" . time() . "' WHERE status = '1' AND rideID='" . $Member_Lockdata[0]["rideID"] . "'";
		$db->execute($sql);
	}
	if ($rideData[0]["status"] != 5){
		$sql = "UPDATE rides SET status='5' WHERE ID = '" . $Member_Lockdata[0]["rideID"] . "'";	
		$db->execute($sql);
		
	}
	$sql = "UPDATE rideTimes set status = '3' WHERE status ='2' AND rideID = '" . $Member_Lockdata[0]["rideID"] . "'";
	$db->execute($sql);
		
	$sql = "UPDATE members_lock SET riderID = '0', boatID = '0', rideID = '0',driverID = '0' WHERE session_id = '" . session_id() . "'";
	$db->execute($sql);
	
	echo "--TITLE--Checkout--";
	echo "--HOME--";
	echo "&nbsp;";
	
	if ( $rideData[0]["riderID"] > 0){
		$sql = "SELECT CONCAT(first_name , ' ', last_name) AS name FROM members WHERE ID = '" . $rideData[0]["riderID"] . "'";
		$riderName = $db->querySingleItem($sql);	
	}
	else {
		$riderName = $rideData[0]["riderName"];
	}
	
	echo "<ul>";
		echo "<li>Ridername<span class=\"secondaryWLink\">";
		echo $riderName;
		echo "</span></li>";
	echo "</ul>";
	
	$sql = "SELECT * FROM rideTimes WHERE rideID = '" . $Member_Lockdata[0]["rideID"] . "' AND status = 3";
	$rideTimes = $db->queryArray($sql);
	
	$totalTime = 0;
	if (is_array($rideTimes)){
		foreach ($rideTimes as $time){
			$totalTime = $totalTime + ($time["stop"] - $time["start"]);
		}
	}
		
	
	echo "<ul>";
                
		echo "<li>Total Time<span class=\"secondaryWLink\">" . secToMinutes($totalTime);
		
		
		//FACEBOOK MAil
		$sql = "SELECT facebookON, facebookmail FROM members WHERE ID = '" . $rideData[0]["riderID"] . "'";
			$FaceBookData = $db->queryArray($sql);
			if ($FaceBookData[0]["facebookON"] == 1){
				$subject = " has just " . strtolower(getSportName($rideData[0]["sportID"])) . "ed for ";
				$subject .= secToMinutes($totalTime);
				$subject .=	" at " . $preferences[0]["contact_name_of_school"] . " - pushed by WakeSys";
		
				mail($FaceBookData[0]["facebookmail"], $subject,$subject,"FROM:" . $preferences[0]["contact_mail"]);
			}

		 
		 echo "</span></li>";

                 $sql = "SELECT autostop, counter FROM rides WHERE ID = '" . $Member_Lockdata[0]["rideID"] . "'";
                $autostop = $db->queryArray($sql);
                if ($autostop[0]["autostop"] == "yes"){
                    echo "<li>Autostop<span class=\"secondaryWLink\">" . secToMinutes($autostop[0]["counter"]) . "</span></li>";
                }

                
			$roundedTime = $totalTime / 60;
                        if ($autostop[0]["counter"] > 0 && $autostop[0]["autostop"] == "yes"){
                            $roundedTimeCounter = $autostop[0]["counter"] / 60;
                        }

		 	if ($preferences[0]["round"] == "round"){
				$roundedTime = round ($roundedTime);
			}
			
			elseif ($preferences[0]["round"] == "second"){
				$roundedTime = round ($roundedTime,2);
			}
			
			elseif ($preferences[0]["round"] == "up"){
				$roundedTime = ceil ($roundedTime);
			}
			
			elseif ($preferences[0]["round"] == "down"){
				$roundedTime = floor ($roundedTime);
			}

                        // Falls Autostop => Grenze!!
                        if (isset($roundedTimeCounter) && $roundedTime > $roundedTimeCounter && $roundedTimeCounter > 0){
                            $roundedTime = $roundedTimeCounter;
                        }
                        //immer Timer Preset berechnen, auch wenn weniger gefahren wurde!
                        if (isset($roundedTimeCounter) && $roundedTime < $roundedTimeCounter && $roundedTimeCounter > 0){
                            $roundedTime = $roundedTimeCounter;
                        }
			$totalRidePrice = $roundedTime * $rideData[0]["price"];
			$totalBallastPrice = $roundedTime * $rideData[0]["priceBallast"];
			$totalPrice = $totalRidePrice;
			
		echo "<li>Price (" . $rideData[0]["price"] . " " . getPreferences("currencyHTML") . " * " . secToMinutes($roundedTime*60) . ")<span class=\"secondaryWLink\">" . $totalPrice . " " . getPreferences("currencyHTML") . "</span></li>";
		
		if ($rideData[0]["ballast"] == "yes") {
			echo "<li>Ballast (" . $rideData[0]["priceBallast"] . " " . getPreferences("currencyHTML") . " * " . secToMinutes($roundedTime*60) . ")<span class=\"secondaryWLink\">" . $totalBallastPrice . " " . getPreferences("currencyHTML") . "</span></li>";
			$totalPrice = $totalRidePrice + $totalBallastPrice;
		} 
		echo "<li><b>Total</b><span class=\"secondaryWLink\"><b>" . $totalPrice . " " . getPreferences("currencyHTML") . "</b></span></li>";
		
		$sql = "UPDATE rides SET rounding = '" . $preferences[0]["round"] . "', timeTotal = '" . $totalTime . "', priceTotal = '" . $totalPrice . "' WHERE ID = '" . $rideData[0]["ID"] . "'";
		$db->execute($sql); 
		
	echo "</ul>";
	
	if (isset($rideData[0]["riderID"]) && $rideData[0]["riderID"] > 0 ){
		echo "<h2>Payment - Member</h2>";
		echo "<ul>";
			$credits = getCurrentCredit($rideData[0]["riderID"]);
			echo "<li>Credits before<span class=\"secondaryWLink\">" . $credits . " " . getPreferences("currencyHTML") . "</span></li>";
			echo "<li>Credits after<span class=\"secondaryWLink\">" . ($credits-$totalPrice) . getPreferences("currencyHTML") . "</span></li>";
		echo "</ul>";
	}
		
	//insert into Credits!!
	if (isset($rideData[0]["riderID"]) && $rideData[0]["riderID"] > 0 ){
		
		$preferences = getPreferences();
		if ($preferences[0]["payDriver"] > 0){
			$sql = "INSERT INTO credits (value,rideID,memberID,payDriver) VALUES ('" . $totalPrice . "', '" . $rideData[0]["ID"] . "', '" . $rideData[0]["riderID"] . "', '" . $preferences[0]["payDriver"] * $roundedTime . "')";
			$db->execute($sql);
			
			$sql = "UPDATE rides SET payDriver = (SELECT COALESCE(SUM(payDriver),0) FROM credits WHERE rideID = '" . $rideData[0]["ID"] . "') WHERE ID = '" . $rideData[0]["ID"]  . "'";
			$db->execute($sql);
		}
		else {
			$sql = "INSERT INTO credits (value,rideID,memberID) VALUES ('" . $totalPrice . "', '" . $rideData[0]["ID"] . "', '" . $rideData[0]["riderID"] . "')";
			$db->execute($sql);
		}
	}
	else {
		$preferences = getPreferences();
		if ($preferences[0]["payDriver"] > 0){
			$sql = "INSERT INTO credits (value,rideID,payDriver) VALUES ('" . $totalPrice . "', '" . $rideData[0]["ID"] . "', '" . $preferences[0]["payDriver"] * $roundedTime . "')";
			$db->execute($sql);
			$sql = "UPDATE rides SET payDriver = (SELECT COALESCE(SUM(payDriver),0) FROM credits WHERE rideID = '" . $rideData[0]["ID"] . "') WHERE ID = '" . $rideData[0]["ID"] . "'";
			$db->execute($sql);
		}
		else {
			$sql = "INSERT INTO credits (value,rideID) VALUES ('" . $totalPrice . "', '" . $rideData[0]["ID"] . "')";
			$db->execute($sql);
		}
		
	}
	
	
	echo "<div class=\"start\">";	
		echo "<li><a href=\"javascript: LoadPage('ride','closeRide=" . $rideData[0]["ID"] . "');\">Close this ride</a></li>";
	echo "</div>";
	unset($_SESSION["ridesettings"]["group"]);
	unset($_SESSION["ridesettings"]["riderID"]);
	unset($_SESSION["ridesettings"]["catID"]);
	unset($_SESSION["ridesettings"]["ballast"]);
	unset($_SESSION["ridesettings"]["autostop"]);
	unset($_SESSION["ridesettings"]["timePreset"]);

}
elseif (isset($_POST["start"]) || isset($_POST["pause"]) || $rideData[0]["status"] == 3 || $rideData[0]["status"] == 4){
	if ($rideData[0]["status"] == 2){
		$sql = "UPDATE rides SET status='3' WHERE ID = '" . $rideData[0]["ID"] . "'";	
		$db->execute($sql);
	}
	
	$sql = "SELECT * FROM rideTimes WHERE rideID = '" . $rideData[0]["ID"] . "' AND status = '1'";
	$times = $db->queryArray($sql);
	if (isset($_POST["pause"]) || ($rideData[0]["status"] == 4 && !isset($_POST["start"])) ) {
		$sql = "UPDATE rides SET status='4' WHERE ID = '" . $rideData[0]["ID"] . "'";	
		$db->execute($sql);
		
		$sql = "UPDATE rideTimes SET status = '2', stop='" . time() . "' WHERE status = '1' AND rideID='" . $rideData[0]["ID"] . "'";
		$db->execute($sql);
	}
	elseif (is_array($times) || isset($_POST["start"])){
		if ($rideData[0]["status"] != 3){
			$sql = "UPDATE rides SET status='3' WHERE ID = '" . $rideData[0]["ID"] . "'";	
			$db->execute($sql);
		}
		if (isset($_POST["start"])) {
			$sql = "INSERT INTO rideTimes (rideID,start,status) VALUES ('" . $rideData[0]["ID"] . "', '" . time() . "',1)";
			$db->execute($sql);
			$timeID = $db->insertId();
		}
		else {
			$sql = "SELECT ID FROM rideTimes WHERE status = '1' and rideID = '" . $rideData[0]["ID"] . "'";
			$timeID = $db->querySingleItem($sql);
		}
		
		// berechne Zeit bis JETZT!!!!
		$time = 0;
		$sql = "SELECT * FROM rideTimes WHERE rideID = '" . $rideData[0]["ID"] . "' and status = '2' ORDER BY ID DESC";
	$rideTimes = $db->queryArray($sql);
		if (is_array($rideTimes)){
			foreach ($rideTimes as $ride){
				$time = $time + ($ride["stop"]-$ride["start"]);
			}
		}
		
		if (isset($timeID)){
			$sql = "SELECT start FROM rideTimes WHERE ID = '" . $timeID . "'";
			$oldStart = $db->querySingleItem($sql);
			$time = $time + (time() - $oldStart);
		}
			
		
		//todo if TimePreset: Count Down + Value Countstart!!!
		if (!isset($_POST["timepreset"]) && $rideData[0]["counter"] > 0){
			$_POST["timepreset"] = $rideData[0]["counter"];
		}
		if (isset($_POST["timepreset"]) && $_POST["timepreset"] > 0){

                        //Autostop in DB übernehmen!!!
                        if (isset($_POST["autostop"]) && $_POST["autostop"] == "yes"){
                            $sql = "UPDATE rides SET autostop = 'yes' WHERE ID = '" . $rideData[0]["ID"] . "'";
                            $db->execute($sql);
                            echo "--autostop--";
                        }


                        //Insert into Ride!
			$sql = "UPDATE rides SET Counter = '" . $_POST["timepreset"] . "' WHERE ID = '" . $rideData[0]["ID"] . "'";
			$db->execute($sql);
			echo "--COUNTDOWN--";
			// Abzüglich DB-Ergebnis!
			$time = $_POST["timepreset"] - $time;

                        
		}
		//Falls COunter bereits in DB!
		elseif (isset($rideData[0]["counter"])){
			$time = $rideData[0]["counter"] - $time;
			echo "--COUNTDOWN--";
                        //Autostop aus DB holen!
                        if (isset($rideData[0]["autostop"])){
                                echo "--autostop--";
                        }
		}
		
		


		// Countstart aus DB holen
		/*
		bei Up +
		bei down preset - abgelaufen!
		*/
		
		echo "--COUNTSTART--".$time."--";
	}
	
	if (isset($_POST["pause"]) || ($rideData[0]["status"] == 4 && !isset($_POST["start"])) ) {
		echo "--TITLE--paused Ride--";
		echo "--HOME--";
		echo "&nbsp;<ul>";
			echo "<li><a href=\"javascript:LoadPage('riderunning','start=" . $rideData[0]["ID"] . "')\">Resume Ride!<span class=\"showArrow secondaryArrow\">&nbsp;</span></a></li></ul>";
			echo "<ul><li class=\"listop\"><a href=\"javascript:LoadPage('riderunning','stop=" . $rideData[0]["ID"] . "')\">Stop Ride!<span class=\"showArrow secondaryArrow\">&nbsp;</span></a></li>";
		echo "</ul>";
	}
	else {
		$sql = "SELECT start FROM rideTimes WHERE ID = '" . $timeID . "'";
		$startTime = $db->querySingleItem($sql);
		// todo if Ballast und Rider name => insert into ride.....!!!
//		echo "<pre>";print_r($_POST);echo "</pre>";
		if (isset($_POST["ballast"]) && $_POST["ballast"] == "yes" && isset($_POST["ballastValue"]) && $_POST["ballastValue"]>0){ 
			$sql = "UPDATE rides SET ballast = 'yes', priceBallast = '" . $_POST["ballastValue"]. "' WHERE ID = '" . $rideData[0]["ID"] . "'";
			$db->execute($sql);
		}
		elseif(isset($_POST["ballastValue"])) {
			$sql = "UPDATE rides SET ballast = 'no', priceBallast = '" . $_POST["ballastValue"]. "' WHERE ID = '" . $rideData[0]["ID"] . "'";
			$db->execute($sql);
		}
		
		
		if (isset($_POST["non_member_rider_name"])){
			$sql = "UPDATE rides SET riderName = '" . $_POST["non_member_rider_name"]. "' WHERE ID = '" . $rideData[0]["ID"] . "'";
			$db->execute($sql);
		}
		
		echo "<h2>--TITLE--Start: " . date("H:i:s",$startTime) . "--</h2>";
		echo "--HOME--";
		echo "<center><a class=\"minutes\" id=\"timer\" href=\"javascript:LoadPage('riderunning','refresh')\">.</a>";
                echo "</center>";
		echo "<ul>";
			echo "<li><a href=\"javascript:LoadPage('riderunning','pause=" . $rideData[0]["ID"] . "')\">Pause Ride!<span class=\"showArrow secondaryArrow\">.</span></a></li>";
			//echo "<li><a href=\"javascript:LoadPage('riderunning','refresh')\">Refresh TIme!<span class=\"showArrow secondaryArrow\">.</span></a></li></ul>";
			echo "</ul>";
			echo "<ul><li class=\"listop\"><a href=\"javascript:LoadPage('riderunning','stop=" . $rideData[0]["ID"] . "')\">Stop Ride!</a></li>";
		echo "</ul>";
	}
		
	
	$sql = "SELECT * FROM rideTimes WHERE rideID = '" . $rideData[0]["ID"] . "' and status = '2' ORDER BY ID DESC";
	$rideTimes = $db->queryArray($sql);
	
	if (is_array($rideTimes)){
		$rideTimeData = "";
		$time = 0;	
		foreach ($rideTimes as $ride){
			$time = $time + ($ride["stop"]-$ride["start"]);
			$rideTimeData .= "<ul>";
				$rideTimeData .= "<li>Start<span class=\"secondaryWLink\">" ;
					$rideTimeData .= (date("H:i:s", ($ride["start"])) );
				$rideTimeData .= "</span></li>";
				$rideTimeData .= "<li>Stop<span class=\"secondaryWLink\">";
					$rideTimeData .= date("H:i:s", ($ride["stop"]) );
				$rideTimeData .= "</span></li>";
			$rideTimeData .= "</ul>";
		}
		if ($time > 0)
		{
			echo "<h2>Time Before: " . $time . " Sek</h2>";
			
		}
		echo $rideTimeData;		
	}	
    
}
//noch nichts ausgewählt!
else {
	
	?>
	--TITLE--Edit Ride--
	--BACK_TITLE--Back--
	--BACK_PAGE--ride_UnRide--
	--HOME--
	
	<form method="POST" action="javascript: LoadPage('riderunningStart',document.getElementById('form_start'));" id="form_start">
	<h2>Check Previous Information</h2>
	<ul>
	<?php 

	$boatName = getBoatName($rideData[0]["boatID"]);
		
	$driverName = getDriverName($rideData[0]["driverID"]);
		
	$riderName = getRiderName($rideData[0]["riderID"]);
	
	$sportName = getSportName($rideData[0]["sportID"])
	
	
	?>
		<li>Boat<span class="secondaryWLink"><?php echo $boatName; ?></span></li>
		<li>Driver<span class="secondaryWLink"><?php echo $driverName; ?></span></li>
		
		<?php
		if ($riderName != "-1"){
			echo "<li>Ridername<span class=\"secondaryWLink\">" . $riderName . "</span></li>";
		}
		?>
		
		<li>Sport<span class="secondaryWLink"><?php echo $sportName; ?></span></li>
		
	</ul>
	<h2>Special settings</h2>
	<ul>
		<?php
		if ($riderName == "-1"){
			echo "<li";
			if (isset($_POST["non_member_rider_name"]) && ($_POST["non_member_rider_name"] == "" || $_POST["non_member_rider_name"] == "Enter Name")){
				echo " class=\"listop\"";	
			}
			echo ">NonMember<span class=\"showArrow secondaryWArrow\"><label>";
			echo "<input name=\"non_member_rider_name\" class=\"secondaryInput\" type=\"text\" size=\"12\" maxlength=\"25\"";
			if (isset($_POST["non_member_rider_name"]) && $_POST["non_member_rider_name"] != ""){
				echo " value=\"" . $_POST["non_member_rider_name"] . "\"";
			}
			else {
				echo "value=\"Enter Name\"";
			}
			echo " onfocus=\"this.value='';\"";
			echo ">";
			echo "</label></span></li>";
		}
		
		$sql = "SELECT price FROM prices WHERE sportsID='999' AND categoryID='" . $rideData[0]["categoryID"]. "' AND boatID='" . $rideData[0]["boatID"]. "' AND ";
		if ($rideData[0]["riderID"] > 0){
			$sql .= "member = 1";
		}
		else {
			$sql .= "member = 2";
		}
		$ballastPrice = $db->querySingleItem($sql);
		
		$sql = "SELECT price FROM prices WHERE sportsID='" . $rideData[0]["sportID"]. "' AND categoryID='" . $rideData[0]["categoryID"]. "' AND boatID='" . $rideData[0]["boatID"]. "' AND ";
		if ($rideData[0]["riderID"] > 0){
			$sql .= "member = 1";
		}
		else {
			$sql .= "member = 2";
		}
		$price = $db->querySingleItem($sql);
		
		?>
		<li>Ballast (<?php echo $ballastPrice . " " . getPreferences("currencyHTML");?>)<span class="secondaryWLink">
			<?php
			if (isset($_POST["ballast"])){
				$_SESSION["ridesettings"]["ballast"] = $_POST["ballast"];
				$ballast = $_SESSION["ridesettings"]["ballast"];
			}
			if (isset($_SESSION["ridesettings"]["ballast"])){
				$ballast =$_SESSION["ridesettings"]["ballast"];
			}
			
			if ($rideData[0]["riderID"] > 0){
				$sql = "SELECT ballast FROM members WHERE ID = '" . $rideData[0]["riderID"] . "'";
				$ballast = $db->querySingleItem($sql);
			}
			else {
				$ballast = "no";
			}
			 
			
			if ($ballast == "no"){?>
				<a href="javascript:button('ballast','yes_no','<?php echo $price . "','" . number_format($price + $ballastPrice,2);?>');" id="href_ballast"><img src="iphone_admin/images/no.png"></a>
				<input type="hidden" name="ballast" value="no" clas="test1" id="input_ballast">
			<?php }
			else {?>
				<a href="javascript:button('ballast','yes_no','<?php echo $price . "','" . number_format($price + $ballastPrice,2);?>');" id="href_ballast"><img src="iphone_admin/images/yes.png"></a>
				<input type="hidden" name="ballast"  clas="test2" value="yes" id="input_ballast">
			<?php }
			
			?>
			
			<input type="hidden" name="ballastValue" value="<?php echo $ballastPrice;?>">
		</span></li>


</span></li>

	<?php
	if (!isset($_SESSION["ridesettings"]["timePreset"]) && !isset($_SESSION["ridesettings"]["autostop"])){
		$sql = "SELECT preset, autostop FROM prices WHERE ";
		if ($rideData[0]["riderID"] > 0){
			$sql .= "member = 1 ";
		}
		else {
			$sql .= "member = 2 ";
		}
		$sql .= "AND sportsID = '998' AND categoryID = '" . $_SESSION["ridesettings"]["catID"] . "' AND boatID = '" . $_SESSION["ridesettings"]["boatID"] . "'";

		$autostopData = $db->queryArray($sql);
		$_SESSION["ridesettings"]["autostop"] = $autostopData[0]["autostop"];
		$_SESSION["ridesettings"]["timePreset"] = $autostopData[0]["preset"];
	}
	?>
	<li>Minutes Preset<span class="secondaryWLink"><select name="timepreset" onchange="showautostop(this.value);">
	<?php

		

		$selectArr[0]="none";
		$selectArr[300]="5'";
		$selectArr[600]="10'";
		$selectArr[900]="15'";
		$selectArr[1200]="20'";
		$selectArr[1500]="25'";
		$selectArr[1800]="30'";
		$selectArr[2100]="35'";
		$selectArr[2400]="40'";
		$selectArr[2700]="45'";
		$selectArr[3000]="50'";
		$selectArr[3300]="55'";
		$selectArr[3600]="60'";
		
		foreach ($selectArr AS $key => $elem){
			echo "<option value=\"" . $key . "\"";
			if (isset($_SESSION["ridesettings"]["timePreset"])){
				if ($_SESSION["ridesettings"]["timePreset"] == $key){
					echo "selected";
				}
			}
			//selected
			echo ">" . $elem . "</option>";
		}
		
	?>
	</select></span></li>
    
      
	<li id="autostop" <?php
            if (!isset($_SESSION["ridesettings"]["timePreset"]) || $_SESSION["ridesettings"]["timePreset"] <= 0){
                echo "style=\"display:none;\"";
            }
            

        ?>>Auto-Stop?<span class="secondaryWLink">
	
        <?php
        if (!isset($_SESSION["ridesettings"]["autostop"]) || $_SESSION["ridesettings"]["autostop"] == "no"){
            ?>
                <a href="javascript:button('autostop','yes_no','STOPPPP');" id="href_autostop"><img src="iphone_admin/images/no.png"></a>
                <input type="hidden" name="autostop" value="no" clas="test1" id="input_autostop">
        <?php }
        else {?>
                <a href="javascript:button('autostop','yes_no','STOPPPP222');" id="href_autostop"><img src="iphone_admin/images/yes.png"></a>
                <input type="hidden" name="autostop"  clas="test2" value="yes" id="input_autostop">
        <?php }

        ?>
                </span></li>
<?php
	$sql = "SELECT enginetime FROM maintenance WHERE ts LIKE '" . date("Y-m-d") . "%' AND boatID = '" . $rideData[0]["boatID"] . "' AND oil IS NULL AND filter IS NULL AND fuel_liters IS NULL LIMIT 0,1";
	$engineTime = $db->querySingleItem($sql);
	if (!isset($engineTime) || $engineTime == ""|| $engineTime == "-1"){
		echo "<li";
		if (isset($_POST["engine_time"]) && ($_POST["engine_time"] == "" || $_POST["engine_time"] == "enter engine time") ){
				echo " class=\"listop\"";	
		}
		echo ">EngineTime<span class=\"showArrow secondaryWArrow\"><label><input class=\"secondaryInput\" name=\"engine_time\" type=\"text\" size=\"12\" maxlength=\"30\"";
		if (isset($_POST["engine_time"]) && $_POST["engine_time"] != ""){
			echo "value=\"" . $_POST["engine_time"] . "\"";
		}
		else {
			echo "value=\"enter engine time\"";
		}
		
		echo " onfocus=\"this.value='';\"/></label></span></li>";
	}
	else {
	}
    
	echo "</ul>";
	
	if ($price > 0){
		$sql = "UPDATE rides SET price = '" . $price . "' WHERE ID = '" . $rideData[0]["ID"] . "'";
		$db->execute($sql); 
		?>
		<ul>
			<li>Price<span class="secondaryWLink"><?php echo "<span id=\"totalprice\">";
			if ($ballast == "no"){
				echo $price; 
			}
			else {
				echo number_format($price + $ballastPrice,2);
			}
			echo "</span>	 " . getPreferences("currencyHTML");?></span></li>
		</ul>
		<?php 
		echo "<input type=\"hidden\" name=\"start\" value=\"" . $rideData[0]["ID"] . "\">";
		echo "<div class=\"start\"><li><a href=\"javascript:LoadPage('riderunningStart',document.getElementById('form_start'));\">Start Ride</a></li></div>";
	}
	else {
		echo "<ul>";	
			echo "<li>ERROR! no price to calculate</li>";
		echo "</ul>";	
	}
		?>
	</form>	
	
<?php 
}
?>