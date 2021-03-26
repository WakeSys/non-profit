<?php
//echo "<pre>";print_r($_POST);echo "</pre>";
//echo "<pre>";print_r($_GET);echo "</pre>";

// Edit bei Payed nonMembers nicht möglich!!!
if (isset($_GET["edit"])){
	$sql = "SELECT invoiceID from credits WHERE rideID = '" . $_GET["edit"] . "'";
	//if invoiceID: Payed NonMember -> kein Edit!!!
	$invoiceID = $db->querySingleItem($sql);
	if (isset($invoiceID) && is_numeric($invoiceID)){
		die ("ERROR, Add Time for payed nonMember Rides impossible!");
	}
}

if (isset($_GET["delRideTime"]) && is_numeric($_GET["delRideTime"]) && isset($_GET["edit"])){
	$sql = "SELECT * from rideTimes WHERE rideID = '" . $_GET["edit"] . "'";
	if (count($db->queryArray($sql)) > 1 ){
		$sql = "UPDATE rideTimes set status = '4' WHERE ID = '" . $_GET["delRideTime"] . "'";
		$db->execute($sql);
		
		$sql = "SELECT boatID, sportID,riderID FROM rides WHERE ID = '" . $_GET["edit"] . "'";
		$rideData_del = $db->queryArray($sql);
		//echo "<pre>";print_r($rideData);echo "</pre>";die();
		$_POST["sportID"] = $rideData_del[0]["sportID"];
		$_POST["boatID"] = $rideData_del[0]["boatID"];
		$_POST["riderID"] = $rideData_del[0]["riderID"]; 
	}
}
elseif (isset($_GET["edit"])
	&& isset($_POST["add_time_sent"])
	//Prüfe StartTime
	&& is_numeric($_POST["start_h"]) && $_POST["start_h"] >= 0  && $_POST["start_h"] <= 24
	//Prüfe StartTime m
	&& is_numeric($_POST["start_m"]) && $_POST["start_m"] >= 0  && $_POST["start_m"] < 60
	//Prüfe StartTime s
	&& is_numeric($_POST["start_s"]) && $_POST["start_s"] >= 0  && $_POST["start_s"] < 60
	//Prüfe StopTime
	&& is_numeric($_POST["stop_h"]) && $_POST["stop_h"] >= 0  && $_POST["stop_h"] <= 24
	//Prüfe StopTime
	&& is_numeric($_POST["stop_m"]) && $_POST["stop_m"] >= 0  && $_POST["stop_m"] < 60
	//Prüfe StopTime
	&& is_numeric($_POST["stop_s"]) && $_POST["stop_s"] >= 0  && $_POST["stop_s"] < 60
) {
	
	
	// Date aus altem RIde holen!
	$sql = "SELECT start from rideTimes WHERE rideID = '" . $_GET["edit"] . "' LIMIT 0,1";
	$startTime = $db->querySingleItem($sql);
	
	//insert into ride_times status 3
	$sql = "INSERT INTO rideTimes (rideID, start,stop,status) VALUES ('" . $_GET["edit"] . "',";
		$sql .= " '" . mktime($_POST["start_h"], $_POST["start_m"], $_POST["start_s"], date("m",$startTime), date("d",$startTime),date("Y",$startTime)) . "', ";
		$sql .= " '" . mktime($_POST["stop_h"], $_POST["stop_m"], $_POST["stop_s"], date("m",$startTime), date("d",$startTime),date("Y",$startTime)) . "', 3";
	$sql .= ")";
	
	$db->execute($sql);
	
	
	// Zeiten zurücksetzen:
	$_POST["start_h"] = "hh";
	$_POST["stop_h"] = "hh";
	$_POST["start_m"] = "mm";
	$_POST["stop_m"] = "mm";
	$_POST["start_s"] = "ss";
	$_POST["stop_s"] = "ss";
	
}
elseif (
	isset($_GET["add"])
	&& isset($_POST["add_sent"])
	//Prüfe StartTime
	&& is_numeric($_POST["start_h"]) && $_POST["start_h"] >= 0  && $_POST["start_h"] <= 24
	//Prüfe StartTime m
	&& is_numeric($_POST["start_m"]) && $_POST["start_m"] >= 0  && $_POST["start_m"] < 60
	//Prüfe StartTime s
	&& is_numeric($_POST["start_s"]) && $_POST["start_s"] >= 0  && $_POST["start_s"] < 60
	//Prüfe StopTime
	&& is_numeric($_POST["stop_h"]) && $_POST["stop_h"] >= 0  && $_POST["stop_h"] <= 24
	//Prüfe StopTime
	&& is_numeric($_POST["stop_m"]) && $_POST["stop_m"] >= 0  && $_POST["stop_m"] < 60
	//Prüfe StopTime
	&& is_numeric($_POST["stop_s"]) && $_POST["stop_s"] >= 0  && $_POST["stop_s"] < 60
	//Prüfe Date
	&& preg_match("/^([0-3]?[0-9])\.([01]?[0-9])\.([12][09][0-9]{2})$/", $_POST["date"], $match_date)
) {
	
	if (!isset($_POST["categoryID"])){
		$sql = "SELECT categoryID FROM members WHERE ID = '" . $_POST["riderID"] . "'";
		$_POST["categoryID"] = $db->querySingleItem($sql);
	}
	
	//Insert into rides / credits! => rideID
	if (isset($_POST["riderID"]) && is_numeric($_POST["riderID"])){
		$sql = "INSERT INTO rides (sportID, categoryID, boatID, driverID, status, riderID,ballast,rounding) VALUES ('" . $_POST["sportID"]. "', '" . $_POST["categoryID"]. "', '" . $_POST["boatID"]. "', '" . $_POST["driverID"]. "', '5', '" . $_POST["riderID"]. "', '" . $_POST["ballast"]. "', '" . $preferences[0]["round"] . "')";	
	}
	else {
		$sql = "INSERT INTO rides (sportID, categoryID, boatID, driverID, status, riderName,ballast,rounding) VALUES ('" . $_POST["sportID"]. "', '" . $_POST["categoryID"]. "', '" . $_POST["boatID"]. "', '" . $_POST["driverID"]. "', '5', '" . $_POST["riderName"]. "', '" . $_POST["ballast"]. "', '" . $preferences[0]["round"] . "')";	
	}

	$db->execute($sql);
	
	$_GET["edit"] = $db->insertId();
	$type = "edit";
	
	
	
	
	
	if (isset($_POST["riderID"]) && is_numeric($_POST["riderID"])){
		$sql = "INSERT INTO credits (rideID,memberID) VALUES ('" . $_GET["edit"] . "', '" . $_POST["riderID"] . "')";
	}
	else {
		$sql = "INSERT INTO credits (rideID,memberID) VALUES ('" . $_GET["edit"] . "', null)";
	}
	$db->execute($sql);
	
	//insert into ride_times status 3
	$sql = "INSERT INTO rideTimes (rideID, start,stop,status) VALUES ('" . $_GET["edit"] . "',";
		$sql .= " '" . mktime($_POST["start_h"], $_POST["start_m"], $_POST["start_s"], $match_date[2], $match_date[1],$match_date[3]) . "', ";
		$sql .= " '" . mktime($_POST["stop_h"], $_POST["stop_m"], $_POST["stop_s"], $match_date[2], $match_date[1],$match_date[3]) . "', 3";
	$sql .= ")";
	
	$db->execute($sql);
	
	// Zeiten zurücksetzen:
	$_POST["start_h"] = "hh";
	$_POST["stop_h"] = "hh";
	$_POST["start_m"] = "mm";
	$_POST["stop_m"] = "mm";
	$_POST["start_s"] = "ss";
	$_POST["stop_s"] = "ss";
	// => Edit!
}
	
//echo "<pre>";print_r($_GET);echo "</pre>";
if (isset($_GET["edit"])){
	$sql = "SELECT invoiceID from credits WHERE rideID = '" . $_GET["edit"] . "'";
	//if invoiceID: Payed NonMember -> kein Edit!!!
	$invoiceID = $db->querySingleItem($sql);
	$type = "edit";
	$_GET["rideID"] = $_GET["edit"];
	if ( (isset($_POST["edit_sent"]) && $_POST["edit_sent"] == "yes") || ( isset($_POST["add_sent"]) && $_POST["add_sent"] == "yes") || ( isset($_POST["add_time_sent"]) && $_POST["add_time_sent"] == "yes") || isset($_GET["delRideTime"])){
		
		if (isset($invoiceID) && is_numeric($invoiceID)){
			echo "<h1>ERROR, Edit from payed nonMember Rides impossible!</h1>";
		}
		else {
			//echo "UPDATE RIDE!!!!";
			
			$sql = "SELECT * FROM rideTimes WHERE rideID = '" . $_GET["rideID"] . "' and status = '3' ORDER BY ID DESC";
			$rideTimes = $db->queryArray($sql);
			
			if (is_array($rideTimes)){
				$rideTimeData = "";
				$time = 0;	
				foreach ($rideTimes as $ride){
					$time = $time + ($ride["stop"]-$ride["start"]);
				}
			}	
			
//			echo "Time: " . $time;
			
			// Berechnung!
			if (!isset($_POST["categoryID"])){
				$sql = "SELECT categoryID FROM rides WHERE ID = '" . $_GET["edit"]. "'";
				$categoryID = $db->querySingleItem($sql);
				//echo $sql;
				//echo "TEST";
				if ($categoryID == "-1"){
					error_die("CategoryID " . $sql);
				}
				$_POST["categoryID"] = $categoryID;
				
			}
			
			$sql = "SELECT riderID FROM rides WHERE ID = '" . $_GET["edit"]. "'";
			$riderID = $db->querySingleItem($sql);
			
			// Hole prices!
			$sql = "SELECT price FROM prices WHERE sportsID='999' AND categoryID='" . $_POST["categoryID"] . "' AND boatID='" . $_POST["boatID"]. "' AND ";
			
			if (isset($riderID) && $riderID > 0){
				$sql .= "member = 1";
			}
			else {
				$sql .= "member = 2";
			}
			$ballastPrice = $db->querySingleItem($sql);
			if ($ballastPrice == "-1"){
				error_die("SQL Error: " . $sql);
			}
			//echo "Ballast : --> " . $sql . " ==> ".$ballastPrice."<br>";
			$sql = "SELECT price FROM prices WHERE sportsID='" . $_POST["sportID"]. "' AND categoryID='" . $_POST["categoryID"]. "' AND boatID='" . $_POST["boatID"]. "' AND ";
			if (isset($riderID) && $riderID > 0){
				$sql .= "member = 1";
			}
			else {
				$sql .= "member = 2";
			}
			//echo $sql;
			$price = $db->querySingleItem($sql);
			if ($price == "-1"){
				die ("SQL Error: " . $sql);
			}
			//echo "Price : --> " . $sql . " ==> ".$price."<br>";
			
			//Berechnung je nach eingestellter Rundung!!!
			if ($preferences[0]["round"] == "round"){
				$roundedTime = round (($time / 60));
			}
			elseif ($preferences[0]["round"] == "second"){
				$roundedTime = ceil (($time / 60),2);
			}
			elseif ($preferences[0]["round"] == "up"){
				$roundedTime = ceil (($time / 60));
			}
			elseif ($preferences[0]["round"] == "down"){
				$roundedTime = floor (($time / 60));
			}
			$totalRidePrice = $roundedTime * $price;
			if (!isset($_POST["ballast"])){
				$sql = "SELECT ballast FROM rides WHERE ID = '" . $_GET["edit"]. "'";
				$ballast = $db->querySingleItem($sql);	
			}
			else {
				$ballast = $_POST["ballast"];
			}
			
			//echo "<br>Ballast--->".$ballast;
			//echo "<pre>";print_r($_POST);echo "</pre>";
			
			if ($ballast == "no"){
				$totalBallastPrice = 0;
			}
			else {
				$totalBallastPrice = $roundedTime * $ballastPrice;	
			}
			
			$totalPrice = $totalRidePrice + $totalBallastPrice;
			//echo "TotalRidePRice:" . $totalRidePrice . "----".
			
			$sql = "UPDATE rides SET ";
			$i = 0;
			//todo bei rideTimes + add manually ausfiltern!!!
			if (!isset($_POST["add_sent"]) && !isset($_POST["add_time_sent"]) ){
				foreach ($_POST as $key=>$elem){
					if ($key != "edit_sent"){
						if ($i != 0){
							$sql .= ", ";
						}
						$sql .= $key . " = '" . $elem . "'";	
						$i ++;
					}
				}
			}
			if ($i != 0){
				$sql .= ", ";
			}
			$sql .= "rounding='" . $preferences[0]["round"] . "', price = '" . $price . "', priceBallast = '" . $ballastPrice . "', timeTotal = '" . $time . "', priceTotal = '" . $totalPrice . "'";
			$sql .= " WHERE ID = '" . $_GET["rideID"] . "'";
//			echo "<br>".$sql."<br>";
			$db->execute($sql);
			
			//PayDriver beachten!!!
			if ($preferences[0]["payDriver"] > 0){
				$sql = "UPDATE credits SET payDriver = '" . $preferences[0]["payDriver"] * $roundedTime . "' WHERE rideID = '" . $_GET["rideID"] . "'";
				$db->execute($sql);
				$sql = "UPDATE rides SET payDriver = '" . $preferences[0]["payDriver"] * $roundedTime . "' WHERE ID = '" . $_GET["rideID"] . "'";
				$db->execute($sql);
			}
			else {
				$sql = "UPDATE credits SET payDriver = 0 WHERE rideID = '" . $_GET["rideID"] . "'";
				$db->execute($sql);
				$sql = "UPDATE rides SET payDriver = 0 WHERE ID = '" . $_GET["rideID"] . "'";
				$db->execute($sql);
			}
			$db->execute($sql);
			//payDriver in rides einfügen!!!
			$sql = "UPDATE rides SET payDriver = (SELECT COALESCE(SUM(payDriver),0) FROM  credits WHERE rideID = '" . $_GET["rideID"] . "') WHERE ID = '" . $_GET["rideID"] . "'";
			$db->execute($sql);
			
			// Credits abändern!!!
			if (isset($_POST["riderID"])){
				//Members
				$sql = "UPDATE credits SET value = '" . $totalPrice . "', memberID = '" . $_POST["riderID"] . "' WHERE rideID = '" . $_GET["rideID"] . "'";
//				echo $sql;
			}
			elseif (!isset($_POST["riderID"])){
				//NonMembers
				$sql = "UPDATE credits SET value = '" . $totalPrice . "', memberID = null WHERE rideID = '" . $_GET["rideID"] . "'";
//				echo $sql;
			}
			$db->execute($sql);
		}
	}
	
}
elseif(isset($_GET["add"])) {
	$type = "add";
}
else {
	$type = "view";
}
	

if ($type == "edit" && (!isset($_GET["edit"]) || !is_numeric($_GET["edit"])) && (!isset($_GET["rideID"]) || !is_numeric($_GET["rideID"])) ){
	die ("ERROR No RideID!");
}
elseif ($type == "edit" || $type == "view" ) {
	$sql = "SELECT A.timeTotal, A.price, A.ballast, A.categoryID, A.boatID, A.driverID, A.sportID, A.ID AS rideID, 
		C.campRider, A.priceTotal,A.priceBallast, B.name AS sportsName, A.riderID, A.riderName AS riderName_nonMember,
		CONCAT(C.first_name, ' ', C.last_name) AS riderName, CONCAT(D.first_name, ' ', D.last_name) AS driverName,
		E.name AS boatName, F.name AS categoryName,
		A.autostop, A.counter
		FROM rides AS A
		LEFT JOIN sports AS B ON A.sportID = B.ID
		LEFT JOIN members AS C ON A.riderID = C.ID
		LEFT JOIN members AS D ON A.driverID = D.ID
		LEFT JOIN boats AS E ON A.boatID = E.ID
		LEFT JOIN categories AS F ON A.categoryID = F.ID

		WHERE A.ID = '" .  $_GET["rideID"] . "' LIMIT 0,1";
	$rideData = $db->queryArray($sql);
	
	if (!is_array($rideData)){
		die ("ERROR ride not in Database!");
	}
}
	


//echo "<pre>";print_r($rideData);echo "</pre>";
?>

<div class="main">
	<?php
	if ($type == "edit"){
		echo "<form action=\"\" method=\"POST\">";
		echo "<input type=\"hidden\" name=\"edit_sent\" value=\"yes\">";
	}
	elseif ($type == "add"){
		echo "<form action=\"\" method=\"POST\">";
		echo "<input type=\"hidden\" name=\"add_sent\" value=\"yes\">";
	}
	
	?>
	
	
	<div class="main_text">
	<?php
	if ($type == "view"){
		echo "<div class=\"main_text_title\">View ";
		if (!is_numeric($rideData[0]["riderID"]) || $rideData[0]["riderID"] == 0){
			echo "NonMember";
		}
		elseif ($rideData[0]["campRider"] == "yes"){
			echo "CampRider";
		}
		else {
			echo "Member";
		}
		echo " - Ride / Logbook</div>";
	}
	else {
		if ($type == "edit"){
			echo "<div class=\"main_text_title\">Edit ";
			if (!is_numeric($rideData[0]["riderID"]) || $rideData[0]["riderID"] == 0){
				echo "NonMember";
			}
			elseif ($rideData[0]["campRider"] == "yes"){
				echo "CampRider";
			}
			else {
				echo "Member";
			}
			echo " Ride to Logbook</div>";	
		}
		elseif ($type == "add"){
			echo "<div class=\"main_text_title\">Add ";
			if ($_GET["add"] == "member"){
				echo "Member";
			}
			elseif ($_GET["add"] == "campmember"){
				echo "CampMember";
			}
			elseif ($_GET["add"] == "nonmember"){
				echo "NonMember";
			}
			echo " Ride to Logbook</div>";	
		}
		
	}
	?>
	
	<table width="600" border="0" cellspacing="0" cellpadding="0" class="text_invoice_boxes_white">
	  <?php
	    if ($type == "view" || $type == "edit"){
	    	?>
	    	<tr>
		    <td width="194">Date</td>
		    <?php
	    	$sql = "SELECT start FROM rideTimes WHERE rideID = '" . $rideData[0]["rideID"] . "' AND status = 3 LIMIT 0,1";
	    	echo "<td>" . date("d\.m\.Y",$db->querySingleItem($sql)) . "</td>";
	    	?>
	      </tr>
	      <?php
	    }?>
		  
	  <tr>
	    <td>Boat</td>
	    <?php
	    if ($type == "view"){
	    	echo "<td>" . $rideData[0]["boatName"] . "</td>";
	    }
	    else {
			$sql = "SELECT * FROM boats WHERE active = 1";
			$boats = $db->queryArray($sql);
			echo "<td><select name=\"boatID\">";
			foreach ($boats as $boat){
				echo "<option value=\"" . $boat["ID"] . "\"";
				if ($type == "edit"){
					if ($rideData[0]["boatID"] == $boat["ID"]){
						echo " selected";
					}
				}
				echo ">";
					echo $boat["name"];
				echo "</option>";
			}
			echo "</select></td>";
	    }
	    ?>
      </tr>
	  <tr>
	    <td>Name of Driver</td>
	    <?php
	    if ($type == "view"){
	    	echo "<td>" . $rideData[0]["driverName"] . "</td>";
	    }
	    else {
			$sql = "SELECT * FROM members WHERE driver = 1 AND active = 1 ORDER by first_name, last_name";
			$drivers = $db->queryArray($sql);
			echo "<td><select name=\"driverID\">";
			foreach ($drivers as $driver){
				echo "<option value=\"" . $driver["ID"] . "\"";
				if ($type == "edit"){
					if ($rideData[0]["driverID"] == $driver["ID"]){
						echo " selected";
					}
				}
				echo ">";
					echo $driver["first_name"] . " " . $driver["last_name"] ;
				echo "</option>";
			}
			echo "</select></td>";
	    }
	    ?>
      </tr>
	  <tr>
	    <td>Sport</td>
	    <?php
	    if ($type == "view"){
	    	echo "<td>" . $rideData[0]["sportsName"] . "</td>";
	    }
	    else {
			$sql = "SELECT * FROM sports WHERE active = 1";
			$sports = $db->queryArray($sql);
			echo "<td><select name=\"sportID\">";
			foreach ($sports as $sport){
				echo "<option value=\"" . $sport["ID"] . "\"";
				if ($type == "edit"){
					if ($rideData[0]["sportID"] == $sport["ID"]){
						echo " selected";
					}
				}
				echo ">";
					echo $sport["name"];
				echo "</option>";
			}
			echo "</select></td>";
	    }
	    ?>
      </tr>
      <tr>
	    <td>Name of Rider</td>
	    <?php
	    if ($type == "view"){
	    	if (isset($rideData[0]["riderID"]) && $rideData[0]["riderID"] > 0){
	    		echo "<td>" . $rideData[0]["riderName"] . "</td>";	
	    	}
	    	else {
	    		echo "<td>" . $rideData[0]["riderName_nonMember"] . "</td>";
	    	}
	    	
	    }
	    else {
	    	if ( (isset($_GET["add"]) && ($_GET["add"] == "campmember" || $_GET["add"] == "member") ) || (isset($rideData[0]["riderID"]) && $rideData[0]["riderID"] > 0) ){
	    		if (isset($_GET["add"]) && $_GET["add"] == "member"){
	    			$sql = "SELECT * FROM members WHERE campRider = 'no' AND active = 1 ORDER by first_name, last_name";
	    		}
	    		elseif (isset($_GET["add"]) && $_GET["add"] == "campmember"){
	    			$sql = "SELECT * FROM members WHERE campRider = 'yes' AND active = 1 ORDER by first_name, last_name";
	    		}
	    		else {
	    			$sql = "SELECT * FROM members WHERE campRider = '" . $rideData[0]["campRider"] . "' AND active = 1 ORDER by first_name, last_name";	
	    		}
	    		
	    		$members = $db->queryArray($sql);
	    		echo "<td>";
				

				if (count($members) == 1 && is_numeric($members[0]["ID"])){
					echo $members[0]["first_name"] . " " . $members[0]["last_name"];
					echo "<input type=\"hidden\" name =\"riderID\" value = \"" . $members[0]["ID"] . "\">";
				}
				elseif (count($members) > 1){
					echo "<select name = \"riderID\">";
					foreach ($members as $member){
						$creditValue = getCurrentCredit($member["ID"]);

						echo "<option value=\"" . $member["ID"] . "\"";
						if (isset($rideData[0]["riderID"]) && $member["ID"] == $rideData[0]["riderID"]){
							echo " selected";
						}
						if ($creditValue <= 0 && $members[0]["campRider"] == "no"){
							echo " disabled style=\"color:red;\"";
						}
						echo ">" . $member["first_name"] . " " . $member["last_name"]. " (" ;
						echo $creditValue;
						echo ")";
						/*
						if (isset($_GET["add"]) && ($_GET["add"] == "campmember" || $_GET["add"] == "campmember" ) ){
							$value = getCurrentCredit($member["ID"]);
							if ($value >= 0){
								echo "(" . $value . ")";
							}
							else {
								echo "<span style=\"color:red;\">" . $value . "</span>";
							}
						}*/
						echo "</option>";
					}
					echo "</select>";
				}
				else {
					
					echo "<span style=\"color:red;\">Sorry no Riders available!</span>";
					echo "</td></tr></table></div><div class=\"bottom\"></div></center></body>";
					die ("");
					$noRiders = true;
				}
				
	    		echo "</td>";
	    	}
	    	else {
	    		echo "<td><input name=\"riderName\" ";
	    		if (isset($rideData[0]["riderName_nonMember"])){
	    			echo "value=\"" . $rideData[0]["riderName_nonMember"] . "\"";
	    		}
	    		echo "></td>";
	    	}
	    }
	    ?>
      </tr>
      <?php
	    if ($type == "view"){
	  		echo "<tr>";
	    		echo "<td>Price Rate</td>";
	    		echo "<td>" . $rideData[0]["categoryName"] . "</td>";
	    }
	    else {
	    	if ( (isset($rideData[0]["riderID"]) && $rideData[0]["riderID"] <= 0) || (isset($_GET["add"]) && $_GET["add"] == "nonmember") ){
	    		echo "<td>Price Rate</td>";
	    		echo "<td>";
	    		echo "<select name = \"categoryID\">";
	    		$sql = "SELECT * FROM categories WHERE active = 1";
	    		$categories = $db->queryArray($sql);
	    		foreach ($categories as $category){
	    			echo "<option value=\"" . $category["ID"] . "\"";
	    			if (isset($rideData[0]["categoryID"]) && $category["ID"] == $rideData[0]["categoryID"]){
	    				echo " selected";
	    			}
	    			echo ">";
	    				echo $category["name"];
	    			echo "</option>";
	    		}
	    		echo "</select>";
	    		echo "</td>";
	    	}
	    	else {
	    		//echo "<input type=\"hidden\" name=\"categoryID\" value=\"" . $rideData[0]["categoryID"] . "\">";
	    	}
	    	echo "</tr>";
	    }
	    ?>

	  <tr>
	    <td>Ballast</td>
	    <?php
	    if ($type == "view"){
	    	if ($rideData[0]["ballast"] == "yes"){
	    		echo "<td><label>Yes</label></td>";
	    	}
	    	else {
	    		echo "<td><label>No</label></td>";
	    	}
	    }
	    else {
	    	echo "<td>";
	    		echo "<input type=\"radio\" value = \"yes\" name = \"ballast\"";
	    			if ( (isset($rideData[0]["ballast"]) && $rideData[0]["ballast"] == "yes" && !isset($_POST["ballast"])) || (isset($_POST["ballast"]) && $_POST["ballast"] == "yes") ){
	    				echo " checked";
	    			}
	    		echo ">yes";
	    		echo "<input type=\"radio\" value = \"no\" name = \"ballast\"";
	    			if (isset($_GET["add"]) || (isset($rideData[0]["ballast"]) && !isset($_POST["ballast"]) && $rideData[0]["ballast"] != "yes") || (isset($_POST["ballast"]) && $_POST["ballast"] != "yes") ){
	    				echo " checked";
	    			}
	    		echo ">no";
	    	echo "</td>";
	    }
	    ?>
      </tr>
      <tr><td>&nbsp;</td></tr>	
      <?php
      if ($type == "edit"){
      	echo "<tr><td>&nbsp;</td></tr>";
      	echo "<tr><td colspan=\"2\"><input type = \"submit\"></td></tr>";
      	echo "</table>";
      	echo "</form>";
      	echo "<form method=\"POST\" action=\"" . INDEX . "?p=logbook&sub=view_detail&edit=" . $_GET["edit"]. "\">";
      	echo "<table>";
      }
      
      if ($type == "add" || $type == "edit") {
      	
      	echo "<tr><td>Add Ride Time:</td></tr>";
      	if ($type == "add"){
	      	echo "<tr><td>Ride Date: (dd.mm.YY)</td><td>";
	      		echo "<input type=\"text\" name=\"date\"";
	      			if (isset($_POST["date"])){
	      				echo " value = \"" . $_POST["date"] . "\"";
	      				if (!preg_match("/^([0-3]?[0-9])\.([01]?[0-9])\.([12][09][0-9]{2})$/", $_POST["date"])){
	      					echo " style=\"background-color:red;\"";
	      				}
	      			}
	      		echo ">";
	  		echo "</td></tr>";	
		}
		else {
			echo "<input type=\"hidden\" name=\"add_time_sent\" value = \"yes\">";
			echo "<input type=\"hidden\" name=\"sportID\" value = \"" . $rideData[0]["sportID"] . "\">";
			echo "<input type=\"hidden\" name=\"boatID\" value = \"" . $rideData[0]["boatID"] . "\">";
			echo "<input type=\"hidden\" name=\"categoryID\" value = \"" . $rideData[0]["categoryID"] . "\">";
			echo "<input type=\"hidden\" name=\"riderID\" value = \"" . $rideData[0]["riderID"] . "\">";
		}
      	echo "<tr><td>Start time:</td><td>";
      		echo "<input type=\"text\" name =\"start_h\" ";
      		if (isset($_POST["start_h"]) && $_POST["start_h"] != "hh" && ( !is_numeric($_POST["start_h"]) || $_POST["start_h"] >= 24 || $_POST["start_h"] < 0 )){
      			echo "style = \"background-color:red;\"";
      			echo " value=\"hh\"";
      		}
      		elseif (isset($_POST["start_h"])){
      			echo "value=\"" . $_POST["start_h"] . "\"";
      		}
      		else {
      			echo "value=\"hh\"";	
      		}
      		echo ">";
      		
      		echo "<input type=\"text\" name =\"start_m\" ";
      		if (isset($_POST["start_m"]) && $_POST["start_m"] != "mm" && ( !is_numeric($_POST["start_m"]) || $_POST["start_m"] >= 60 || $_POST["start_m"] < 0 )){
      			echo "style = \"background-color:red;\"";
      			echo " value=\"mm\"";
      		}
      		elseif (isset($_POST["start_m"])){
      			echo "value=\"" . $_POST["start_m"] . "\"";
      		}
      		else {
      			echo "value=\"mm\"";	
      		}
      		echo ">";
      		
      		
      		echo "<input type=\"text\" name =\"start_s\" ";
      		if (isset($_POST["start_s"]) && $_POST["start_s"] != "ss" && ( !is_numeric($_POST["start_s"]) || $_POST["start_s"] >= 60 || $_POST["start_s"] < 0 )){
      			echo "style = \"background-color:red;\"";
      			echo " value=\"ss\"";
      		}
      		elseif (isset($_POST["start_s"])){
      			echo "value=\"" . $_POST["start_s"] . "\"";
      		}
      		else {
      			echo "value=\"ss\"";	
      		}
      		echo ">";
      		
      		
      		echo "</td></tr>";
      		
      		
      		echo "<tr><td>Stop time:</td><td>";
      		echo "<input type=\"text\" name =\"stop_h\" ";
      		if (isset($_POST["stop_h"]) && $_POST["stop_h"] != "hh" && ( !is_numeric($_POST["stop_h"]) || $_POST["stop_h"] >= 24 || $_POST["stop_h"] < 0 )){
      			echo "style = \"background-color:red;\"";
      			echo " value=\"hh\"";
      		}
      		elseif (isset($_POST["stop_h"])){
      			echo "value=\"" . $_POST["stop_h"] . "\"";
      		}
      		else {
      			echo "value=\"hh\"";	
      		}
      		echo ">";
      		
      		echo "<input type=\"text\" name =\"stop_m\" ";
      		if (isset($_POST["stop_m"]) && $_POST["stop_m"] != "mm" && ( !is_numeric($_POST["stop_m"]) || $_POST["stop_m"] >= 60 || $_POST["stop_m"] < 0 )){
      			echo "style = \"background-color:red;\"";
      			echo " value=\"mm\"";
      		}
      		elseif (isset($_POST["stop_m"])){
      			echo "value=\"" . $_POST["stop_m"] . "\"";
      		}
      		else {
      			echo "value=\"mm\"";	
      		}
      		echo ">";
      		
      		
      		echo "<input type=\"text\" name =\"stop_s\" ";
      		if (isset($_POST["stop_s"]) && $_POST["stop_s"] != "ss" && ( !is_numeric($_POST["stop_s"]) || $_POST["stop_s"] >= 60 || $_POST["stop_s"] < 0 )){
      			echo "style = \"background-color:red;\"";
      			echo " value=\"ss\"";
      		}
      		elseif (isset($_POST["stop_s"])){
      			echo "value=\"" . $_POST["stop_s"] . "\"";
      		}
      		else {
      			echo "value=\"ss\"";	
      		}
      		echo ">";
      		
      		
      		echo "</td></tr>";
      		
      	echo "<tr><td>&nbsp;</td></tr>";
      }
      
      if ($type == "edit"){
      	echo "<tr><td>&nbsp;</td></tr>";
      	echo "<tr><td colspan=\"2\"><input type = \"submit\" value =\"add ride time\"></td></tr>";
      	echo "</table>";
      }
      elseif ($type == "add" && !isset($noRiders)){
      	echo "<tr><td colspan=\"2\"><input type = \"submit\"></td></tr>";
      }
      ?>
      
      </form> 
      </table>
      <?php
      if ($type != "add"){
	      ?>
	      <table>
		  <tr>
		    <td><b>Ride Times</b></td>
		    <td></td>
	      </tr>
	      <?php
	      	$sql = "SELECT * FROM rideTimes WHERE rideID = '" . $rideData[0]["rideID"] . "' AND status = 3";
			$rideTimes = $db->queryArray($sql);
			
			$totalTime = 0;
			if (is_array($rideTimes)){
				foreach ($rideTimes as $time){
					echo "<tr>";
						echo "<td>Ride Time:</td>";
						echo "<td>from " . date("H:i:s",$time["start"]) . " to " . date("H:i:s",$time["stop"]) . " ==&gt; " ;
						$difftime = ($time["stop"] - $time["start"]);
						echo secToMinutes($difftime);
						echo "</td>";
						if (count($rideTimes) > 1){
							echo "<td><a href = \"" . INDEX . "?p=logbook&sub=view_detail&edit=" . $rideData[0]["rideID"] . "&delRideTime=" . $time["ID"] . "\">XXXX</a></td>";
						}
					echo "</tr>";
					$totalTime = $totalTime + $difftime;
				}
			}
			//Berechnung je nach eingestellter Rundung!!!
			if ($preferences[0]["round"] == "round"){
				$roundedTime = round (($totalTime / 60));
			}
			elseif ($preferences[0]["round"] == "second"){
				$roundedTime = round (($totalTime / 60),2);
			}
			elseif ($preferences[0]["round"] == "up"){
				$roundedTime = ceil (($totalTime / 60));
			}
			elseif ($preferences[0]["round"] == "down"){
				$roundedTime = floor (($totalTime / 60));
			}
			
			
			echo "<tr>";
			  	echo "<td>Total Time:</td>";
			  	echo "<td>";
			  		echo secToMinutes($totalTime);
				echo "</td>";
		  	echo "</tr>";
		  	if ($rideData[0]["autostop"] == "yes"){
				echo "<tr>";
					echo "<td>Autostop Counter:</td>";
					echo "<td>";
						echo secToMinutes($rideData[0]["counter"]);
					echo "</td>";
				echo "</tr>";
			}
	      	
	      ?>
			<tr><td>&nbsp;</td></tr>	 
	      <tr>
	      	<td><b>Prices:</b></td>
	      </tr>
	      <?php
		  if ($rideData[0]["autostop"] == "yes"){
			  $rideData[0]["timeTotal"] = $rideData[0]["counter"];
		  }
      	$min = floor($rideData[0]["timeTotal"] / 60);
		$sek = $rideData[0]["timeTotal"] - ($min*60);
	      if ($rideData[0]["ballast"] && $rideData[0]["ballast"] == "yes"){
	      	  echo "<tr>";
		      	echo "<td><b>Price Ballast:</b></td>";
		      	echo "<td>" . $rideData[0]["priceBallast"] . " " . getPreferences("currencyHTML") . " * " . $min . "' "  . $sek . "'";
				if ($roundedTime != $rideData[0]["timeTotal"] && $rideData[0]["autostop"] != "yes"){
					echo " (rounded: " . $roundedTime . ")";
				}
				echo "=> " . $roundedTime * $rideData[0]["priceBallast"] . " " . getPreferences("currencyHTML") . "</td>";
		      echo "</tr>";
		      
		      echo "<tr>";
		      	echo "<td><b>Price Ride:</b></td>";
		      	echo "<td>" . $rideData[0]["price"] . " " . getPreferences("currencyHTML") . " * " . $min . "' "  . $sek . "'";
				if ($roundedTime != $rideData[0]["timeTotal"] && $rideData[0]["autostop"] != "yes"){
					echo " (rounded: " . $roundedTime . ")";
					echo "=> " . $roundedTime * $rideData[0]["price"] . " " . getPreferences("currencyHTML") . "</td>";
				}
				elseif($rideData[0]["autostop"] == "yes"){
					echo "=> " . $rideData[0]["counter"]/60 * $rideData[0]["price"] . " " . getPreferences("currencyHTML") . "</td>";
				}
				else {
					echo "=> " . $rideData[0]["timeTotal"] * $rideData[0]["price"] . " " . getPreferences("currencyHTML") . "</td>";
				}
				
	      	echo "</tr>";
	      }
	      else {
	      	
		      echo "<tr>";
		      	echo "<td><b>Price Ride:</b></td>";
		      	echo "<td>" . $rideData[0]["price"] . " " . getPreferences("currencyHTML") . " * " . $min . "' "  . $sek . "'";
				if ($roundedTime != $rideData[0]["timeTotal"] && $rideData[0]["autostop"] != "yes"){
					echo " (rounded: " . $roundedTime . ")";
					echo "=> " . $roundedTime * $rideData[0]["price"] . " " . getPreferences("currencyHTML") . "</td>";
				}
				elseif($rideData[0]["autostop"] == "yes"){
					echo "=> " . $rideData[0]["counter"]/60 * $rideData[0]["price"] . " " . getPreferences("currencyHTML") . "</td>";
				}
				else {
					echo "=> " . $rideData[0]["timeTotal"] * $rideData[0]["price"] . " " . getPreferences("currencyHTML") . "</td>";
				}
	      	echo "</tr>";
	      }
	      
	      echo "<tr>";
	      	echo "<td><b>Price Total:</b></td>";
	      	echo "<td>" . $rideData[0]["priceTotal"] . " " . getPreferences("currencyHTML") . "</td>";
	      echo "</tr>";
	      
	      if ($type == "view"){
			$sql = "SELECT invoiceID from credits WHERE rideID = '" . $rideData[0]["rideID"] . "'";
			//if invoiceID: Payed NonMember -> kein Edit!!!
			$invoiceID = $db->querySingleItem($sql);
			if (!isset($invoiceID) || !is_numeric($invoiceID)){
				echo "<tr><td>&nbsp;</td></tr>";
	      		echo "<tr><td colspan = \"2\"><a href = \"" . INDEX . "?p=logbook&sub=view_detail&edit=" . $rideData[0]["rideID"] . "\">Edit this ride</a></td></tr>";
			}
			else {
				echo "<tr><td>&nbsp;</td></tr>";
	      		echo "<tr><td colspan = \"2\">Edit payed nonMember ride impossible!</td></tr>";
			}
	      	
	      }
	      echo "</table>";
      }
      
      ?>
      
      
      
	  
	</div>
</div> 
<div class="bottom"></div>
</center>
</body>
<!--Bottom End -->