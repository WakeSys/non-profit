<?php


if (isset($_POST["boatID"])){
	$_SESSION["ridesettings"]["boatID"] = $_POST["boatID"];
}
if (isset($_POST["driverID"])){
	$_SESSION["ridesettings"]["driverID"] = $_POST["driverID"];
}
if (isset($_POST["group"])){
	$_SESSION["ridesettings"]["group"] = $_POST["group"];
}
if (isset($_POST["riderID"])){
	$_SESSION["ridesettings"]["riderID"] = $_POST["riderID"];
}
if (isset($_POST["catID"])){
	$_SESSION["ridesettings"]["catID"] = $_POST["catID"];
}
if (!isset ($Member_Lockdata)){
	members_lock();	
}
//				-> Select price
//					-> Check Ride
//						-> Start => Ende/Reload	


//
//echo "<pre>";print_r($_POST);echo "</pre><hr>";
//RIDE
//Abfragen:

//	-> lösche settings
if (isset($_POST["unset"])){
//	-> Boat
	if ($_POST["unset"] == "boat"){
		$_SESSION["ridesettings"]["boatID"] = null;
		unset($_SESSION["ridesettings"]["boatID"]);
	}
//	-> Driver
	if ($_POST["unset"] == "driver"){
		$_SESSION["ridesettings"]["driverID"] = null;
		unset($_SESSION["ridesettings"]["driverID"]);
	}
//	-> Driver
	if ($_POST["unset"] == "group"){
		$_SESSION["ridesettings"]["group"] = null;
		unset($_SESSION["ridesettings"]["group"]);
	}
	//catID && riderID
	if ($_POST["unset"] == "catID"){
		$_SESSION["ridesettings"]["catID"] = null;
		unset($_SESSION["ridesettings"]["catID"]);
		$_SESSION["ridesettings"]["riderID"] = null;
		unset($_SESSION["ridesettings"]["riderID"]);
	}
	
	
//	-> Ride
	if ($_POST["unset"] == "ride"){
		$rideID = $Member_Lockdata[0]["rideID"];
		
		if (is_numeric($rideID) && $rideID > 0)
		{
			$_SESSION["ridesettings"]["autostop"] = null;
			unset($_SESSION["ridesettings"]["autostop"]);

			$_SESSION["ridesettings"]["timePreset"] = null;
			unset($_SESSION["ridesettings"]["timePreset"]);

			$sql = "DELETE FROM rides WHERE ID ='" . $rideID . "'";
			$db->execute($sql);
			
			$sql = "UPDATE members_lock SET rideID = '0', boatID = '0', driverID = '0', riderID ='0' WHERE session_id = '" . session_id() . "'";
			$db->execute($sql);
			members_lock();
		}
		
		
		
	}
}
// Set Sport
// Erstelle Ride!!!!
elseif (isset($_POST["sportID"]) && is_numeric($_POST["sportID"])){
	$sql = "INSERT INTO rides (status) VALUES (1)";
	$db->execute($sql);
	$sql = "UPDATE members_lock SET rideID = '" . $db->insertId() . " ', riderID = '" . $_SESSION["ridesettings"]["riderID"] . "', boatID = '" . $_SESSION["ridesettings"]["boatID"] . "', driverID = '" . $_SESSION["ridesettings"]["driverID"] . "' WHERE session_id = '" . session_id() . "'";
	$db->execute($sql);
	members_lock();
}


//Lade Member_Lockdata
//echo "<pre>";print_r($Member_Lockdata);echo "</pre><hr>";


//Ride läuft????
//	-> ja
if ($Member_Lockdata[0]["rideID"] > 0){
	if(isset($_POST["sportID"])){
		$sql = "UPDATE rides SET riderID='" . $_SESSION["ridesettings"]["riderID"] . "', sportID='" . $_POST["sportID"] . "', categoryID = '" . $_SESSION["ridesettings"]["catID"] . "',boatID = '" . $_SESSION["ridesettings"]["boatID"] . "',driverID = '" . $_SESSION["ridesettings"]["driverID"] . "', status='2' WHERE ID = '" . $Member_Lockdata[0]["rideID"] . "'";
		$db->execute($sql);	
	}

	include ("../pages_iphone_admin/riderunning.inc.php");
	
//		-> Stop / Check Out
}			
//	nein => kein Ride
else{
	
	$sql = "SELECT A.*, B.loginID as LockMemberID FROM members AS A  LEFT JOIN members_lock AS B ON A.ID = B.driverID WHERE A.driver = '1' ORDER BY A.first_name, last_name";
	$drivers = $db->queryArray($sql);
	//falls nur 1 Driver wählbar und noch keiner gewählt...
	if (is_array($drivers) && count($drivers) == 1 && (!isset($_SESSION["ridesettings"]["driverID"]) || $_SESSION["ridesettings"]["driverID"] == 0)){
		$_SESSION["ridesettings"]["driverID"] = $drivers[0]["ID"];
	}
	
	$sql = "SELECT  A.*, B.loginID as LockMemberID FROM boats AS A LEFT JOIN members_lock AS B ON A.ID = B.boatID WHERE A.active = '1'";
	//echo $sql;
	$boats = $db->queryArray($sql);
	//falls nur 1 Driver wählbar und noch keiner gewählt...
	if (is_array($boats) && count($boats) == 1 && (!isset($_SESSION["ridesettings"]["boatID"]) || $_SESSION["ridesettings"]["boatID"] == 0)){
		$_SESSION["ridesettings"]["boatID"] = $boats[0]["ID"];
	}

	
	
//	-> Boat&Driver gewählt?
//	-> Select Customer / Guest usw....
	if (isset($_SESSION["ridesettings"]["driverID"]) && $_SESSION["ridesettings"]["driverID"] > 0 && isset($_SESSION["ridesettings"]["boatID"]) && $_SESSION["ridesettings"]["boatID"] > 0){
	
		if (isset($_SESSION["ridesettings"]["group"])){

//			Group / MemberID / Category selected	
//			-> Select Sport
			if (($_SESSION["ridesettings"]["group"] == "member" || $_SESSION["ridesettings"]["group"] == "camprider") && isset($_SESSION["ridesettings"]["riderID"]) && is_numeric($_SESSION["ridesettings"]["riderID"]) || ( $_SESSION["ridesettings"]["group"] == "nonmember" && isset($_SESSION["ridesettings"]["catID"]) && is_numeric($_SESSION["ridesettings"]["catID"]) ) ){
				if($_SESSION["ridesettings"]["group"] == "nonmember"){
					$_SESSION["ridesettings"]["riderID"] = 0;
				}
				
				
				if ($_SESSION["ridesettings"]["group"] == "member" || $_SESSION["ridesettings"]["group"] == "camprider"){
					$sql = "SELECT * FROM sports WHERE active = '1' AND (member = 3 OR member = 1)";
					//echo $sql;
				}
				else {
					$sql = "SELECT * FROM sports WHERE active = '1' AND (member = 3 OR member = 2)";
					//echo $sql;
				}
				$sports = $db->queryArray($sql);
				
				if ($_SESSION["ridesettings"]["group"] == "member" || $_SESSION["ridesettings"]["group"] == "camprider"){
					$sql = "SELECT * FROM prices WHERE boatID = '" . $_SESSION["ridesettings"]["boatID"] . "' and categoryID = (SELECT categoryID FROM members WHERE ID = '" . $_SESSION["ridesettings"]["riderID"] . "') and member ='1'";
					//echo $sql;
				}
				else {
					$sql = "SELECT * FROM prices WHERE boatID = '" . $_SESSION["ridesettings"]["boatID"] . "' and categoryID = '" . $_SESSION["ridesettings"]["catID"] . "' and member ='2'";	
					//echo $sql;
				}
				$result = $db->queryArray($sql);
				
				echo "&nbsp;";
				if(!is_array($result)){
					echo "<ul><li>no prices!</li></ul>";
				}
				else {
					foreach ($result as $key=>$elem){
						$prices[$elem["sportsID"]] = $elem["price"];
					}
					echo "<ul>";
					foreach ($sports as $sport){
						echo "<li>";
                                                        if (isset($prices[$sport["ID"]])){
                                                            echo "<a href=\"javascript:LoadPage('ride','sportID=" . $sport["ID"] . "')\">";
                                                                    echo $sport["name"] . " <span class=\"showArrow secondaryWArrow\">" . $prices[$sport["ID"]] . getPreferences("currencyHTML") . "</span>";
                                                            echo "</a>";
                                                        }
                                                        else {
                                                            echo $sport["name"] . " <span class=\"showArrow secondaryWArrow\">no price!</span>";
                                                        }

						echo "</li>";
					}
					echo "</ul>";
				}
					
					
				
				if ($_SESSION["ridesettings"]["group"] == "member" && isset($_SESSION["ridesettings"]["riderID"])){
					echo "--TITLE--Select Sport--";
					echo "--BACK_TITLE--Members--";
					echo "--BACK_PAGE--ride_UnCat--";
					echo "--HOME--";		
				}
				else {
					echo "--TITLE--Select Sport--";
					echo "--BACK_TITLE--NonMembers--";
					echo "--BACK_PAGE--ride_UnCat--";
					echo "--HOME--";
				}
					
				
			}
//			-> Member
			elseif ($_SESSION["ridesettings"]["group"] == "member"){
//			echo "<pre>";print_r($_POST);echo"</pre>";
//				-> MemberList
				echo "--TITLE--Select Member--";
				echo "--BACK_TITLE--Type--";
				echo "--BACK_PAGE--ride_UnGroup--";
				echo "--HOME--";
				
				
				$sql = "SELECT A.*,B.riderID as LockMemberID  FROM members as A LEFT JOIN members_lock AS B ON A.ID = B.riderID WHERE A.campRider='no' AND ID != '" . $_SESSION["ridesettings"]["driverID"] . "' ORDER BY A.first_name, last_name";
				$members = $db->queryArray($sql);
				
				echo "&nbsp;";
				echo "<ul>";
					if (!is_array($members)){
						echo "<li>no members available!</li>";
					}
					else {
						foreach ($members as $member){
							$credits = getCurrentCredit($member["ID"]);
							$membershipend = getMembershipEnd($member["ID"]);
							
							if(!isset($credits) || $credits == null){
								$credits = 0;
							}
							echo "<li>";
							if (is_null($member["LockMemberID"])){
								if ($credits >= 0 && $membershipend >= time()) {
									echo "<a href=\"javascript:LoadPage('ride','catID=" . $member["categoryID"] . "&riderID=" . $member["ID"]. "');\" ";	
								}
								elseif($membershipend < time()){
									echo "<a href=\"javascript:LoadPage('member_show_ride','group=member&catID=" . $member["categoryID"] . "&UserID=" . $member["ID"]. "');\" ";
								}
								else {
									echo "<a href=\"javascript:LoadPage('invoice_detail','catID=" . $member["categoryID"] . "&riderID=" . $member["ID"]. "');\" ";
								}
								if ($membershipend < time()) {
									echo " style=\"color:red;\"";
								}
								elseif ($membershipend < mktime(0, 0, 0, date("m")+3, date("d"),   date("Y")) ){
									echo " style=\"color:orange;\"";
								}
								echo ">";
								echo $member["first_name"] . " " . $member["last_name"];	
								echo "<span class=\"showArrow secondaryWArrow\"";
										if ($credits < 0) {
											echo " style=\"color:red;\"";
										}
										
										echo ">(" . $credits . " " . getPreferences("currencyHTML") . ")";
									echo "</span>";
								echo "</a>";
							}
							else {
								echo $member["first_name"] . " " . $member["last_name"] . "(locked)";	 
							}
							echo "</li>";
						}
					}
				echo "</ul>";
//					-> Guthaben < 0 || offene Rechnung => AddCredit
//					-> Guthaben > 0 && gezahlte Rechnung
//						-> Select Sport
//							-> Check Ride
//								-> Start => Ende/Reload
				
			}
			
			//			-> Camp Rider
			elseif ($_SESSION["ridesettings"]["group"] == "camprider"){
				//				-> MemberList
				echo "--TITLE--Select Rider--";
				echo "--BACK_TITLE--Group--";
				echo "--BACK_PAGE--ride_UnGroup--";
				echo "--HOME--";
		
				
				$sql = "SELECT A.*,B.riderID as LockMemberID  FROM members as A LEFT JOIN members_lock AS B ON A.ID = B.riderID WHERE A.campRider='yes' ORDER by A.first_name, A.last_name";
				$members = $db->queryArray($sql);
				
				echo "&nbsp;";
				echo "<ul>";
					if (!is_array($members)){
						echo "<li>no members available!</li>";
					}
					else {
						foreach ($members as $member){
							$credits = getCurrentCredit($member["ID"]);
							
							if(!isset($credits) || $credits == null){
								$credits = 0;
							}
							echo "<li>";
								echo "<a href=\"javascript:LoadPage('ride','catID=" . $member["categoryID"] . "&riderID=" . $member["ID"]. "');\">";	
								
								echo $member["first_name"] . " " . $member["last_name"];	
								echo "<span class=\"showArrow secondaryWArrow\"";
										if ($credits < 0) {
											echo " style=\"color:red;\"";
										}
										echo ">(" . $credits . " " . getPreferences("currencyHTML") . ")";
									echo "</span>";
								echo "</a>";
							echo "</li>";
						}
					}
				echo "</ul>";
			//					-> Guthaben < 0 || offene Rechnung => AddCredit
			//					-> Guthaben > 0 && gezahlte Rechnung
			//						-> Select Sport
			//							-> Check Ride
			//								-> Start => Ende/Reload
							
			}
//			NonMembers
			else if ($_SESSION["ridesettings"]["group"] == "nonmember"){
				$sql = "SELECT * FROM categories WHERE active = '1' AND (member ='3' OR member = '2') ";
				$categories = $db->QueryArray($sql);
				echo "&nbsp;";
				echo "<ul>";
					if (is_array($categories)){
						foreach ($categories as $category){
							echo "<li>";
								echo "<a href=\"javascript:LoadPage('ride','group=nonmember&catID=" . $category["ID"] . "');\">";
									echo $category["name"];
								echo "</a>";
							echo "</li>";
						}
					}
					else {
						echo "<li>No Category available</li>";	
					}
									
				echo "</ul>";
				echo "--TITLE--Select Category--";
				echo "--BACK_TITLE--Group--";
				echo "--BACK_PAGE--ride_UnGroup--";
				echo "--HOME--";
				
			}

		}
		//Select Group: Customer / Guest
		else {
			
			//toDo if only 1 Driver oder only 1 Boat!
			$sql = "SELECT count(*) AS anzahl FROM members WHERE driver ='1' AND active ='1'";
			$countDriver = $db->querySingleItem($sql);
			
			$sql = "SELECT count(*) AS anzahl FROM boats WHERE active ='1'";
			$countBoats = $db->querySingleItem($sql);
			
			$sql = "SELECT count(*) AS anzahl FROM members WHERE campRider ='yes'";
			$countCampRiders = $db->querySingleItem($sql);
			
			if ($countDriver > 1 ){
				echo "--BACK_PAGE--ride_UnDriver--";
				echo "--BACK_TITLE--Driver--";	
				
			}
			else if ($countBoats > 1) {
				echo "--BACK_PAGE--ride_UnBoat--";
				echo "--BACK_TITLE--Boat--";
			}
			else{
				echo "--BACK_PAGE--home--";
				echo "--BACK_TITLE--home--";
			}
			
			
			echo "--TITLE--Ridertype--";
			echo "--HOME--";
			echo "&nbsp;<ul>";
				echo "<li><a href =\"javascript:LoadPage('ride','group=member')\">Prepaid Customer</a></li>";
				echo "<li><a href =\"javascript:LoadPage('ride','group=nonmember')\">Guest</a></li>";
				if ($countCampRiders > 0){
					echo "<li><a href =\"javascript:LoadPage('ride','group=camprider')\">Postpaid Customer</a></li>";
				}
				
			echo "</ul>";
		}
	
	}
//	-> Boat gewählt oder nur 1 aktiv
	elseif (isset($_SESSION["ridesettings"]["boatID"]) && $_SESSION["ridesettings"]["boatID"] > 0){
//		-> Select Driver (Members mit Driver) => Ende/Reload
		echo "&nbsp;<ul>";
		foreach ($drivers as $driver){
			echo "<li>";
//				Driver locked!!!
				if (is_numeric($driver["LockMemberID"]) && !is_null($driver["LockMemberID"])){
					echo $driver["first_name"] . " " . $driver["last_name"] . " (locked)";
				}
//				Driver unlocked
				else {
					echo "<a href=\"javascript:LoadPage('ride','driverID=" . $driver["ID"] . "')\">";
						echo $driver["first_name"] . " " . $driver["last_name"];
					echo "</a>";
				}
			echo "</li>";
		}
		echo "</ul>";
		
		echo "--TITLE--Select Driver--";
		echo "--HOME--";
		
		$sql = "SELECT count(*) AS anzahl FROM boats WHERE active ='1'";
		$countBoats = $db->querySingleItem($sql);
		
		if ($countBoats > 1){
			echo "--BACK_TITLE--Boat--";	
			echo "--BACK_PAGE--ride_UnBoat--";	
		}
		else {
			echo "--BACK_TITLE--home--";	
			echo "--BACK_PAGE--home--";
		}
			
	}
//	-> 0 Driver oder 0 Boat?
	else if ( !is_array($drivers) || !is_array($boats) ) {
//			-> Error => Ende
	}
//	-> weder Driver, noch Boat gewählt!!!
//	-> Driver gewählt oder nur 1 aktiv
//	-> Select Boat
	else{
//		-> Select Boat => Ende/Reload
		echo "&nbsp;<ul>";
		foreach ($boats as $boat){
			echo "<li>";
//				Boat locked!!!
				if (is_numeric($boat["LockMemberID"]) && !is_null($boat["LockMemberID"])){
					echo $boat["name"] . " (locked)";
				}
//				Boat unlocked
				else {
					echo "<a href=\"javascript:LoadPage('ride','boatID=" . $boat["ID"] . "');\">";
						echo $boat["name"];
						
						$sql = "SELECT engineTime FROM maintenance WHERE boatID = '" . $boat["ID"] . "' AND (OIL = 1) ORDER BY engineTime DESC LIMIT 0,1";
						$lastOil = $db->querySingleItem($sql);
	

						$sql = "SELECT engineTime FROM maintenance WHERE boatID = '" . $boat["ID"]  . "' ORDER BY engineTime DESC LIMIT 0,1";
						$lastEnginetime = $db->querySingleItem($sql);
	

						$sql = "SELECT oilchange FROM preferences LIMIT 0,1";
						$oildiff = $db->querySingleItem($sql);
						
						if (($lastOil + $oildiff) < $lastEnginetime){
							echo "<span class=\"showArrow secondaryWArrow\" style=\"color:red;\">Pls do oil change</span>";
						}
						
					echo "</a>";
				}
			echo "</li>";
		}
		echo "</ul>";
		echo "--TITLE--Select Boat--";
		echo "--HOME--";
		//Unterscheide ob driver gesetzt...
		// wenn nein => Back = Home
		
		$sql = "SELECT count(*) AS anzahl FROM members WHERE driver ='1'";
		$countDriver = $db->querySingleItem($sql);
		
		
		
		if (isset($_SESSION["ridesettings"]["driverID"]) && $_SESSION["ridesettings"]["driverID"] > 0 && $countDriver > 1){
			echo "--BACK_TITLE--Driver--";	
			echo "--BACK_PAGE--ride_UnDriver--";			
		}
		// wenn ja => Back = driver
		else {
			echo "--BACK_TITLE--Home--";	
			echo "--BACK_PAGE--home--";
		}
	}	
	



}

?>