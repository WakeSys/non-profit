<?php
echo '<div class="main">';
echo '<div class="main_text">';
echo '<div class="main_text_title">View Logbook</div>';
echo '<form action="" method="POST">';
echo 'Choose Year:  ';

if (isset($_POST["Y"])){
	$_SESSION["view_logbook"]["Y"] = $_POST["Y"];
}
if (!isset($_SESSION["view_logbook"]["Y"])){
	$_SESSION["view_logbook"]["Y"] = date("Y");
}
$i = date("Y");
while ($i>=2009){
	$yearArr[] = $i; 
	$i--;
}
echo "<select name=\"Y\" onchange=\"this.form.submit();\">";
foreach ($yearArr as $year){
	echo "<option value=\"" .  $year ."\"";
	if ($_SESSION["view_logbook"]["Y"] == $year){
		echo " selected";
	}
	echo ">" . $year . "</option>";
}


echo '</select>';
echo '<br />';
echo 'Choose Month: ';
echo '<select name="m" id="select2" onchange="this.form.submit();">';

	if (isset($_POST["m"])){
		$_SESSION["view_logbook"]["m"] = $_POST["m"];
	}
	if (!isset($_SESSION["view_logbook"]["m"])){
		$_SESSION["view_logbook"]["m"] = date("n");
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
		if ($key == $_SESSION["view_logbook"]["m"]){
			echo " selected";
		}
		echo ">" . $month . "</option>";
	}

echo '</select></form>';
echo '<br /><br />';

// Zeiteinschränken ;-)
$sql = "
	SELECT 
		B.payDriver,
		B.riderID,
		A.rideID,
		A.start,
		B.priceTotal,
		C.name as boatName, 
		B.riderName as NonMemberRiderName, 
		CONCAT(D.first_name ,' ', D.last_name) as riderName, 
		D.campRider, CONCAT(E.first_name ,' ', E.last_name) as driverName, 
		F.name AS sportName 
	FROM 
		rideTimes AS A 
	LEFT JOIN rides AS B ON A.rideID = B.ID 
	LEFT JOIN boats AS C ON B.boatID=C.ID 
	LEFT JOIN members AS D ON B.riderID = D.ID  
	LEFT JOIN members AS E ON B.driverID = E.ID 
	LEFT JOIN sports AS F ON B.sportID=F.ID
";

if ($_SESSION["view_logbook"]["m"] == 13){
	$sql .= " WHERE A.start BETWEEN '" . mktime(null,null,null,1,1,$_SESSION["view_logbook"]["Y"]) . "' AND '" . mktime(null,null,null,12,0,$_SESSION["view_logbook"]["Y"]) . "'";
}
else {
	$sql .= " WHERE A.start BETWEEN '" . mktime(null,null,null,$_SESSION["view_logbook"]["m"],1,$_SESSION["view_logbook"]["Y"]) . "' AND '" . mktime(null,null,null,$_SESSION["view_logbook"]["m"]+1,1,$_SESSION["view_logbook"]["Y"]) . "'";
}

$sql .= " GROUP BY rideID ORDER BY start DESC";

$rides = $db->queryArray($sql);

if (is_array($rides)){
	foreach ($rides as $ride){
		$rideData[date("Ymd", $ride["start"])][date("His", $ride["start"]) . $ride["rideID"]]["start"] = date("H:i:s", $ride["start"]);
		$rideData[date("Ymd", $ride["start"])][date("His", $ride["start"]) . $ride["rideID"]]["boatName"] = $ride["boatName"];
		$rideData[date("Ymd", $ride["start"])][date("His", $ride["start"]) . $ride["rideID"]]["driverName"] = $ride["driverName"];
		if ($ride["riderID"]>0)
		{
			$rideData[date("Ymd", $ride["start"])][date("His", $ride["start"]) . $ride["rideID"]]["riderName"] = $ride["riderName"];
			if ($ride["campRider"]=='no'){
				$rideData[date("Ymd", $ride["start"])][date("His", $ride["start"]) . $ride["rideID"]]["type"] = "Member";	
			}
			else {
				$rideData[date("Ymd", $ride["start"])][date("His", $ride["start"]) . $ride["rideID"]]["type"] = "CampMember";		
			}
		}
		else {
			$rideData[date("Ymd", $ride["start"])][date("His", $ride["start"]) . $ride["rideID"]]["riderName"] = $ride["NonMemberRiderName"];
			$rideData[date("Ymd", $ride["start"])][date("His", $ride["start"]) . $ride["rideID"]]["type"] = "NonMember";
		}
		$rideData[date("Ymd", $ride["start"])][date("His", $ride["start"]) . $ride["rideID"]]["sportName"] = $ride["sportName"];
		$rideData[date("Ymd", $ride["start"])][date("His", $ride["start"]) . $ride["rideID"]]["campRider"] = $ride["campRider"];
		$rideData[date("Ymd", $ride["start"])][date("His", $ride["start"]) . $ride["rideID"]]["priceTotal"] = $ride["priceTotal"];
		$rideData[date("Ymd", $ride["start"])][date("His", $ride["start"]) . $ride["rideID"]]["priceOut"] = $ride["payDriver"];
		$rideData[date("Ymd", $ride["start"])][date("His", $ride["start"]) . $ride["rideID"]]["rideID"] = $ride["rideID"];
	}
}


$sql = "
	SELECT 
		A.ID,
		UNIX_TIMESTAMP(A.ts) as start,
		A.loginID,B.name as boatName,
		CONCAT(C.first_name, ' ', last_name) AS driverName,
		A.price 
	FROM 
		maintenance AS A 
	LEFT JOIN boats AS B ON A.boatID = B.ID 
	LEFT JOIN members AS C ON A.driverID = C.ID 
	WHERE 1
	AND A.oil IS NULL 
	AND A.filter IS NULL AND A.rideID IS NULL 
	AND A.price > 0
";
if (strlen($_SESSION["view_logbook"]["m"]) == 1){
	$sql .= " AND A.ts LIKE '" . $_SESSION["view_logbook"]["Y"] . "-0" . $_SESSION["view_logbook"]["m"] . "%'";
}
else {
	$sql .= " AND A.ts LIKE '" . $_SESSION["view_logbook"]["Y"] . "-" . $_SESSION["view_logbook"]["m"] . "%'";
}


$maintenance = $db->queryArray($sql);
if (is_array($maintenance)){
	foreach ($maintenance as $elem){
		$rideData[date("Ymd", $elem["start"])][date("His", $elem["start"]) . $elem["ID"]]["start"] = date("H:i:s", $elem["start"]);
		$rideData[date("Ymd", $elem["start"])][date("His", $elem["start"]) . $elem["ID"]]["priceOut"] = $elem["price"];
		$rideData[date("Ymd", $elem["start"])][date("His", $elem["start"]) . $elem["ID"]]["priceTotal"] = "";
		$rideData[date("Ymd", $elem["start"])][date("His", $elem["start"]) . $elem["ID"]]["driverName"] = $elem["driverName"];
		$rideData[date("Ymd", $elem["start"])][date("His", $elem["start"]) . $elem["ID"]]["riderName"] = "";
		$rideData[date("Ymd", $elem["start"])][date("His", $elem["start"]) . $elem["ID"]]["boatName"] = $elem["boatName"];
		$rideData[date("Ymd", $elem["start"])][date("His", $elem["start"]) . $elem["ID"]]["type"] = "fueling";
		$rideData[date("Ymd", $elem["start"])][date("His", $elem["start"]) . $elem["ID"]]["maintID"] = $elem["ID"];
	}
}

echo '<div class="main_text_title">Logbook for '. $MonthArr[$_SESSION["view_logbook"]["m"]] . " " . $_SESSION["view_logbook"]["Y"] .'</div><br />';

if (isset($rideData) && is_array($rideData)){
	krsort($rideData);
	$sumTotalIn = 0;
	$sumTotalOut = 0;
	foreach ($rideData as $time=>$rideData2){
		preg_match("/^([0-9]{4})([0-9]{2})([0-9]{2})$/i",$time,$match);
		echo '<br />';
		echo '<div class="main_text_title">Logbook for ' . $match[3] . "." . $match[2] . "." . $match[1] . "</div>";
		echo '<div class="hours_in_view_logbook">';

		$boats = getAllBoats();
		foreach ($boats as $boat){
			$sql = "
				SELECT 
					engineTime 
				FROM 
					maintenance 
				WHERE 1
				AND boatID = '" . $boat["ID"] . "' 
				AND ts like '" . $match[1] . "-" . $match[2] . "-" . $match[3] . "%' 
				LIMIT 0,1
			";

		$sql2 = "
			SELECT 
				engineTime 
			FROM 
				maintenance 
			WHERE 1
			AND boatID = '" . $boat["ID"] . "' AND ts > '" . $match[1] . "-" . $match[2] . "-" . $match[3] . " 23:59:59' 
			LIMIT 0,1
		";
		//echo $sql."<br><br>";
		//echo $sql2."<br><br>";
		$enginehours = $db->querySingleItem($sql);
		$enginehours2 = $db->querySingleItem($sql2);
		//echo $enginehours."<br><br>";
		//echo $enginehours2."<br><br>";
		if (isset($enginehours) && isset($enginehours2) && $enginehours != "-1" && $enginehours2 != "-1"){
			$enginesecs = round(abs(($enginehours - $enginehours2)),2);
			echo "Engine hours done on " .  $boat["name"] . ": " .  $enginesecs . "<br />";
			$sql = "
				SELECT 
					sum(stop-start) as time 
				FROM rideTimes AS A
				LEFT JOIN rides AS B ON A.rideID = B.ID
				WHERE 1
				AND A.start > '" . mktime(0,0,0,$match[2],$match[3],$match[1]) . "' 
				AND A.start < '" . mktime(23,59,59,$match[2],$match[3],$match[1]) . "'
				AND B.boatID = '" . $boat["ID"] . "'
			";
			$time = round( ($db->querySingleItem($sql)/60/60),2);

			echo "Hours entered into WakeSys on " .  $boat["name"] . ": " . $time;
			$difference = abs($enginesecs-$time);
			echo "<br>";
			echo "<span";
			if ($difference > ($time * 0.9) || $difference < ($time * 0.9) || $difference > ($enginesecs * 0.9) || $difference < ($enginesecs * 0.9) ){
				echo " style=\"color:red;\"";
			}
			echo ">Difference: " . $difference . "</span>";
			//echo "(Wenn differenz > als 10%, ROT<br /><br />";
		}
	}

	echo '</div>';

	echo '<table width="980" border="0" cellspacing="0" cellpadding="0">';
	echo '<tr>';
	echo '<td width="75">Time</td>';
	echo '<td width="175">Boat</td>';
	echo '<td width="250">Driver</td>';
	echo '<td width="130">Type</td>';
	echo '<td width="250">Rider Name</td>';
	echo '<td width="50"><strong>In</strong></td>';
	echo '<td width="50"><strong>Out</strong></td>';
	echo '</tr>';

	$i = 0;
	$totalIn = 0;
	$totalOut = 0;
	foreach ($rideData2 as $ride){
		if ($i%2){
			echo "<tr class=\"bg_stats_lite\">";
		}
		else {
			echo "<tr class=\"bg_stats\">";
		}

		echo "<td><a class=\"link\" href=\"".INDEX."?p=logbook";
		if (isset($ride["rideID"])){ echo "&sub=view_detail&rideID=" . $ride["rideID"];}elseif (isset($ride["maintID"])){ echo "&sub=view_detail_maintenance&sub=view_detail_maintenance&view=" . $ride["maintID"];}
		echo "\">" . $ride["start"] . "</a></td>";
		echo "<td><a class=\"link\" href=\"".INDEX."?p=logbook";
		if (isset($ride["rideID"])){ echo "&sub=view_detail&rideID=" . $ride["rideID"];}elseif (isset($ride["maintID"])){ echo "&sub=view_detail_maintenance&view=" . $ride["maintID"];}
		echo "\">" . $ride["boatName"] . "</a></td>";
		echo "<td><a class=\"link\" href=\"".INDEX."?p=logbook";
		if (isset($ride["rideID"])){ echo "&sub=view_detail&rideID=" . $ride["rideID"];}elseif (isset($ride["maintID"])){ echo "&sub=view_detail_maintenance&view=" . $ride["maintID"];}
		echo "\">" . $ride["driverName"] . "</a></td>";
		echo "<td><a class=\"link\" href=\"".INDEX."?p=logbook";
		if (isset($ride["rideID"])){ echo "&sub=view_detail&rideID=" . $ride["rideID"];}elseif (isset($ride["maintID"])){ echo "&sub=view_detail_maintenance&view=" . $ride["maintID"];}
		echo "\">" . $ride["type"] . "</a></td>";
		echo "<td><a class=\"link\" href=\"".INDEX."?p=logbook";
		if (isset($ride["rideID"])){ echo "&sub=view_detail&rideID=" . $ride["rideID"];}elseif (isset($ride["maintID"])){ echo "&sub=view_detail_maintenance&view=" . $ride["maintID"];}
		echo "\">" . $ride["riderName"] . "</a></td>";
		echo "<td><strong><a class=\"in\" href=\"".INDEX."?p=logbook";
		if (isset($ride["rideID"])){ echo "&sub=view_detail&rideID=" . $ride["rideID"];}elseif (isset($ride["maintID"])){ echo "&sub=view_detail_maintenance&view=" . $ride["maintID"];}
		echo "\">" . $ride["priceTotal"] . "</a></strong></td>";
		$totalIn += $ride["priceTotal"];

		echo "<td><strong><a class=\"out\" href=\"".INDEX."?p=logbook";
		if (isset($ride["rideID"])){ echo "&sub=view_detail&rideID=" . $ride["rideID"];}elseif (isset($ride["maintID"])){ echo "&sub=view_detail_maintenance&view=" . $ride["maintID"];}
		echo "\">" . $ride["priceOut"] . "</a></strong></td>";
		$totalOut += $ride["priceOut"];

		echo "</tr>";
		$i++;
	}

echo '</table><table width="980" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="50"></td>
<td width="200"></td>
<td width="250"></td>
<td width="230"></td>
<td width="150"><strong>Total for this day :</strong></td>
<td width="50" class="in"><strong>' . $totalIn . '</strong></td>
<td width="50" class="out"><strong>' . $totalOut . '</strong></td>
</tr>
</table>';
$sumTotalIn += $totalIn;
$sumTotalOut += $totalOut;
echo '<br />';

}
echo '<table width="980" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="50"></td>
<td width="200"></td>
<td width="250"></td>
<td width="230"></td>
<td width="150"><strong>Total for this month :</strong></td>
<td width="50" class="in"><strong>' . $sumTotalIn . '</strong></td>
<td width="50" class="out"><strong>' . $sumTotalOut . '</strong></td>
</tr>
</table>';



}
else {
	echo "<h1>No rides for this month, (yet)!</h1>";
}




echo '</div>';
echo '</div> ';
echo '<div class="bottom"></div>';
echo '</center>';
echo '</body>';