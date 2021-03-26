<div class="main">
	<div class="main_text_balance_sheet">
	<div class="main_text_title">View Boat Maintenance Stats</div>
    <table width="690" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="90">Boat</td>
    <td width="200">Last Oil Change</td>
    <td width="200">Hours Since Last Oil Change</td>
    <td width="200">Hours on the boat</td>
    </tr>
	<?php
	function getBoatMaintenance($ID){
		GLOBAL $db;
					$sql = "SELECT engineTime FROM maintenance WHERE boatID = '" . $ID . "' ORDER BY engineTime DESC LIMIT 0,1";
					$engineTime = $db->querySingleItem($sql);
					$data["engineTime"] = $engineTime;

					$sql = "SELECT UNIX_TIMESTAMP(ts) AS ts FROM maintenance WHERE boatID = '" . $ID . "' AND oil = '1' ORDER BY ts DESC LIMIT 0,1";
					$lastOilChange = $db->querySingleItem($sql);
					if ($lastOilChange == "-1"){
						$data["lastOilChange"] = "no oil Change in DB";
					}
					else {
						$data["lastOilChange"] = date("d\.m\.Y H:i", $db->querySingleItem($sql) );
					}

					$sql = "SELECT engineTime FROM maintenance WHERE boatID = '" . $ID . "' AND oil = '1' ORDER BY engineTIme DESC limit 0,1";
					$engineTimeLastChange = $db->querySingleItem($sql);

					$data["lastOilChangeHoursSince"] = $engineTime - $engineTimeLastChange;
					
					return $data;
				}

	$boats = getAllBoats();
	if (is_array($boats)){
		foreach ($boats as $boat){
			echo "<tr>";
				
				echo "<td>" . $boat["name"] . "</td>";
				
				$boatMaintenance = getBoatMaintenance($boat["ID"]);
				echo "<td>" . $boatMaintenance["lastOilChange"] . "</td>";
				echo "<td>" . $boatMaintenance["lastOilChangeHoursSince"] . "</td>";
				echo "<td>" . $boatMaintenance["engineTime"] . "</td>";
			echo "</tr>";
		}
	}



	?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
    <div class="main_text_title">View Boat Stats</div>
	<form action="" method="POST">
	Choose Year:  
	
	  <?php
	  if (isset($_POST["Y"])){
	  	$_SESSION["stats_boats"]["Y"] = $_POST["Y"];
	  }
	  if (!isset($_SESSION["stats_boats"]["Y"])){
	  	$_SESSION["stats_boats"]["Y"] = date("Y");
	  }
	  $i = date("Y");
	  while ($i>=2009){
	  	$yearArr[] = $i; 
	  	$i--;
	  }
	  echo "<select name=\"Y\" onchange=\"this.form.submit();\">";
	  foreach ($yearArr as $year){
	  	echo "<option value=\"" .  $year ."\"";
	  	if ($_SESSION["stats_boats"]["Y"] == $year){
	  		echo " selected";
	  	}
	  	echo ">" . $year . "</option>";
	  }
	  ?>
	    
      </select>
	<br />
          Choose Month: 
  <select name="m" id="select2" onchange="this.form.submit();">
  <?php
  if (isset($_POST["m"])){
  	$_SESSION["stats_boats"]["m"] = $_POST["m"];
  }
  if (!isset($_SESSION["stats_boats"]["m"])){
  	$_SESSION["stats_boats"]["m"] = date("n");
  }
  	$MonthArr[1] = "January";
  	$MonthArr[2] = "February";
  	$MonthArr[3] = "March";
  	$MonthArr[4] = "April";
  	$MonthArr[5] = "May";
  	$MonthArr[6] = "June";
  	$MonthArr[7] = "July";
  	$MonthArr[8] = "August";
  	$MonthArr[9] = "September";
  	$MonthArr[10] = "October";
  	$MonthArr[11] = "November";
  	$MonthArr[12] = "December";
  	$MonthArr[13] = "All months";
  	foreach ($MonthArr as $key=>$month){
  		echo "<option value=\"" . $key . "\"";
  		if ($key == $_SESSION["stats_boats"]["m"]){
  			echo " selected";
  		}
  		echo ">" . $month . "</option>";
  	}
  ?>
  </select></form>
      <br /><br />
    <div class="main_text_title">
      <div class="main_text_title">Stats for Boats <?php echo $MonthArr[$_SESSION["stats_boats"]["m"]] . " " . $_SESSION["stats_boats"]["Y"];?></div>
    </div> 
    <table width="980" border="0" cellspacing="0" cellpadding="0">
    <?php
		$sql = "SELECT C.ID as boatID, C.name AS boatName,
                    sum(B.payDriver) as payDriverTotal,
                    sum(B.priceTotal) AS totalPaied,
                    sum(B.timeTotal) AS totalTime
                    FROM  rides AS B
                    LEFT JOIN boats AS C ON B.boatID = C.ID

                    WHERE B.ID IN ";
                
                if ($_SESSION["stats_boats"]["m"] == 13){
                    $sql .= "(SELECT rideID FROM rideTimes WHERE start BETWEEN '" . mktime(null,null,null,1,1,$_SESSION["stats_boats"]["Y"]) . "' AND '" . mktime(null,null,null,12,0,$_SESSION["stats_boats"]["Y"]) . "')";
                }
                else {
                    $sql .= "(SELECT rideID FROM rideTimes WHERE start BETWEEN '" . mktime(null,null,null,$_SESSION["stats_boats"]["m"],1,$_SESSION["stats_boats"]["Y"]) . "' AND '" . mktime(null,null,null,$_SESSION["stats_boats"]["m"]+1,0,$_SESSION["stats_boats"]["Y"]) . "')";
                }



		
		
		$sql .= " GROUP BY B.boatID ORDER BY totalTime DESC";
		$dataArr = $db->queryArray($sql);
                //echo "<hr>" . $sql."====><pre>" . print_r($dataArr) . "</pre><hr>";
		if (!is_array($dataArr) || $dataArr == "-1" || (count($dataArr)==1 && !is_numeric($dataArr[0]["totalTime"]) ) ){
			echo "<tr><td>no data available!</td></tr>";
		}
		else {
			echo "<tr>";
				echo "<td width=\"90\">Boat</td>";
				echo "<td width=\"90\">Total Time</td>";
				echo "<td width=\"90\">Total Income</td>";
				echo "<td width=\"85\">Av. Income</td>";
				echo "<td width=\"90\">Pay Driver</td>";
				echo "<td width=\"80\">Total Fuel</td>";
				echo "<td width=\"80\">Av. Fuel</td>";
				echo "<td width=\"80\">Av. Profit</td>";
			echo "</tr>";
			$i = 0;
			foreach ($dataArr as $elem){
				echo "<tr ";
				if ($i%2){
					echo "class=\"bg_stats\">";
				}
				else {
					echo "class=\"bg_stats_lite\">";
				}
				
					echo "<td >" . $elem["boatName"]. "</td>";
					echo "<td >" . secToMinutes($elem["totalTime"]) . "</td>";
					//Todo PayDriver abziehen????
					echo "<td style=\"color:green;\">" . $elem["totalPaied"]. "&euro;</td>";
					
					if ($elem["totalPaied"] > 0 && $elem["totalTime"] > 0){
						echo "<td >" . round ( ($elem["totalPaied"]/$elem["totalTime"])*60, 2) . "&euro;/'</td>";	
					}
					else {
						echo "<td>0&euro;</td>";
					}
					
					echo "<td style=\"color:red;\">" . $elem["payDriverTotal"]. "&euro;</td>";
					
					$sql = "SELECT sum(price) as fuelTotal, sum(fuel_liters) as fuel_litersTotal FROM maintenance WHERE";
					if ($_SESSION["stats_boats"]["m"] == 13){
                                            $sql .= " ts BETWEEN '" . $_SESSION["stats_boats"]["Y"] . "-01-01 00:00:00' AND '" . $_SESSION["stats_boats"]["Y"] . "-" . date("m", mktime(null,null,null,12,0,$_SESSION["stats_boats"]["Y"])). "-31 23:59:59' AND boatID = '" . $elem["boatID"] . "' and price > 0";
                                        }
                                        else {
                                            $sql .= " ts BETWEEN '" . $_SESSION["stats_boats"]["Y"] . "-" . $_SESSION["stats_boats"]["m"] . "-01 00:00:00' AND '" . $_SESSION["stats_boats"]["Y"] . "-" . date("m", mktime(null,null,null,$_SESSION["stats_boats"]["m"]+1,0,$_SESSION["stats_boats"]["Y"])). "-31 23:59:59' AND boatID = '" . $elem["boatID"] . "' and price > 0";
                                        }

					$totalFuel = $db->queryArray($sql);
					echo "<td style=\"color:red;\">" ;
					if ($totalFuel[0]["fuelTotal"] > 0){
						echo $totalFuel[0]["fuelTotal"];
					}
					else {
						echo "0";
					}
					echo "&euro;</td>";
					
					
					if ($totalFuel[0]["fuel_litersTotal"] > 0 && $elem["totalTime"] > 0){
						echo "<td >" . round ( ($elem["totalTime"]/$totalFuel[0]["fuel_litersTotal"])/60, 2) . " " . ucfirst($preferences[0]["fuelingtype"]) . "/'</td>";
					}
					else {
						echo "<td>0 " . ucfirst($preferences[0]["fuelingtype"]) . "</td>";
					}
					
					
					//todo PayDriver
					$total = $elem["totalPaied"] - $totalFuel[0]["fuelTotal"] - $elem["payDriverTotal"];
					if ($total > 0){
						echo "<td style=\"color:green;\">";
					}
					else {
						echo "<td style=\"color:red;\">";
					}
					echo $total . "</td>";

                                      
				echo "</tr>";
				$i++;
			}
		}
		
		?>
		

 
    </table>
  </div>
</div> 
<div class="bottom"></div>
</center>
</body>