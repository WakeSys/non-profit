<div class="main">
	<div class="main_text">
	<div class="main_text_title">View Driver Stats</div>
	<form action="" method="POST">
	Choose Year:  
	
	  <?php
	  if (isset($_POST["Y"])){
	  	$_SESSION["stats_driver"]["Y"] = $_POST["Y"];
	  }
	  if (!isset($_SESSION["stats_driver"]["Y"])){
	  	$_SESSION["stats_driver"]["Y"] = date("Y");
	  }
	  $i = date("Y");
	  while ($i>=2009){
	  	$yearArr[] = $i; 
	  	$i--;
	  }
	  echo "<select name=\"Y\" onchange=\"this.form.submit();\">";
	  foreach ($yearArr as $year){
	  	echo "<option value=\"" .  $year ."\"";
	  	if ($_SESSION["stats_driver"]["Y"] == $year){
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
  	$_SESSION["stats_driver"]["m"] = $_POST["m"];
  }
  if (!isset($_SESSION["stats_driver"]["m"])){
  	$_SESSION["stats_driver"]["m"] = date("n");
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
  		if ($key == $_SESSION["stats_driver"]["m"]){
  			echo " selected";
  		}
  		echo ">" . $month . "</option>";
  	}
  ?>
  </select></form>
      <br /><br />
     <div class="main_text_title">Stats for drivers <?php echo $MonthArr[$_SESSION["stats_driver"]["m"]] . " " . $_SESSION["stats_driver"]["Y"];?></div><br />
    <table width="500" border="0" cellspacing="0" cellpadding="0">
	<?php
		$sql = "SELECT A.*,sum(A.payDriver) AS totalPaied, sum(A.timeTotal) AS totalTime, CONCAT(B.first_name ,' ', B.last_name) as DriverName FROM  rides AS A ";
		$sql .= "LEFT JOIN members AS B ON A.driverID = B.ID";

                if ($_SESSION["stats_driver"]["m"] == 13){
                    $sql .= " WHERE A.ID IN (SELECT rideID FROM rideTimes WHERE start BETWEEN '" . mktime(null,null,null,1,1,$_SESSION["stats_driver"]["Y"]) . "' AND '" . mktime(null,null,null,12,0,$_SESSION["stats_driver"]["Y"]) . "')";
                }
                else {
                    $sql .= " WHERE A.ID IN (SELECT rideID FROM rideTimes WHERE start BETWEEN '" . mktime(null,null,null,$_SESSION["stats_driver"]["m"],1,$_SESSION["stats_driver"]["Y"]) . "' AND '" . mktime(null,null,null,$_SESSION["stats_driver"]["m"]+1,0,$_SESSION["stats_driver"]["Y"]) . "')";
                }
		
		
		$sql .= " AND A.payDriver > 0 GROUP BY A.driverID ORDER BY totalTime DESC";
		$dataArr = $db->queryArray($sql);
		if (!is_array($dataArr) || $dataArr == "-1" || (count($dataArr)==1 && !is_numeric($dataArr[0]["payDriver"]) ) ){
			echo "<tr><td>no data available!</td></tr>";
		}
		else {
			echo "<tr><td width=\"30\">&nbsp;</td><td width=\"200\">Name</td><td width=\"135\">Minutes</td><td width=\"135\">Salary paid</td></tr>";
			$i = 0;
			foreach ($dataArr as $elem){
				echo "<tr ";
				if ($i%2){
					echo "class=\"bg_stats\">";
				}
				else {
					echo "class=\"bg_stats_lite\">";
				}
				
					echo "<td >" . ($i + 1) . "</td>";
					echo "<td >" . $elem["DriverName"]. "</td>";
					echo "<td >" . secToMinutes($elem["totalTime"]) . "</td>";
					echo "<td >" . $elem["totalPaied"]. "&euro;</td>";
				echo "</tr>";
				$i++;
			}
		}
		
		?>
      
    </table>
<br />
    <br />
  </div>
</div> 
<div class="bottom"></div>
</center>
</body>