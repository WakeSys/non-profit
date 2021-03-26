<?php
//echo "<pre>";print_r($_GET);echo"</pre>";
//echo "<pre>";print_r($_POST);echo"</pre>";
//echo "<pre>";print_r($_SESSION);echo"</pre>";
?>
<div class="main">
	<div class="main_text">
	<div class="main_text_title">View Rider Stats (<?php
	if (!isset($_GET["type"])){
		$type = "member";
		echo "Members";
	}
	elseif ($_GET["type"] == "camp"){
		$type = "camp";
		echo "CampMember";
	}
	elseif ($_GET["type"] == "nonmember"){
		$type = "nonmember";
		echo "NonMember";
		
	}
	?>) </div>
	<form action="" method="POST">
	Choose Year:  
	
	  <?php
	  if (isset($_POST["Y"])){
	  	$_SESSION["stats_member"]["Y"] = $_POST["Y"];
	  }
	  if (!isset($_SESSION["stats_member"]["Y"])){
	  	$_SESSION["stats_member"]["Y"] = date("Y");
	  }
	  $i = date("Y");
	  while ($i>=2009){
	  	$yearArr[] = $i; 
	  	$i--;
	  }
	  echo "<select name=\"Y\" onchange=\"this.form.submit();\">";
	  foreach ($yearArr as $year){
	  	echo "<option value=\"" .  $year ."\"";
	  	if ($_SESSION["stats_member"]["Y"] == $year){
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
  	$_SESSION["stats_member"]["m"] = $_POST["m"];
  }
  if (!isset($_SESSION["stats_member"]["m"])){
  	$_SESSION["stats_member"]["m"] = date("n");
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
  	$MonthArr[13] = "all months";
  	foreach ($MonthArr as $key=>$month){
  		echo "<option value=\"" . $key . "\"";
  		if ($key == $_SESSION["stats_member"]["m"]){
  			echo " selected";
  		}
  		echo ">" . $month . "</option>";
  	}
  ?>
  </select>
     <p>Chose Sport
        <label>
          <select name="sportID" onchange="this.form.submit();">
            <option value = "all"<?php
	            if (isset($_POST["sportID"])){
				  	$_SESSION["stats_member"]["sportID"] = $_POST["sportID"];
				  }
				  if (!isset($_SESSION["stats_member"]["sportID"])){
				  	$_SESSION["stats_member"]["sportID"] = "all";
				  }
            	if ($_SESSION["stats_member"]["sportID"] == "all"){
		  			echo " selected";
		  		}
		  	?>>All Sports</option>
            <?php
            $sql = "SELECT * FROM sports WHERE active = 1";
            $sports = $db->queryArray($sql);
            foreach ($sports as $sport){
            	echo "<option value= \"" . $sport["ID"] . "\"";
            	if ($sport["ID"] == $_SESSION["stats_member"]["sportID"]){
		  			echo " selected";
		  		}
            	echo ">" . $sport["name"] . "</option>";
            }
            ?>
            
          </select>
        </label>
      </p>
  </form>
      <br /><br />
    <div class="main_text_title">
      <p>Stats for members in <?php echo $MonthArr[$_SESSION["stats_member"]["m"]] . " " . $_SESSION["stats_member"]["Y"];?></p>
   
    </div> 
    <br /><br />
    <?php
    $sql = "SELECT * FROM boats WHERE active = 1";
    $boats = $db->queryArray($sql);
    foreach ($boats as $boat){
    	?>
    	<div class="main_text_title">Stats for <?php echo $boat["name"];?></div>
		<br /><br />
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td width="25%">Category</td>
		    <td width="25%">minutes without ballast</td>
		    <td width="25%">minutes with ballast</td>
		    <td width="25%">minutes total</td>
		  </tr>
		  <?php
		  	if ($type == "nonmember"){
		  		$sql = "SELECT * FROM categories WHERE active = 1 AND (member = 2 OR member = 3)";	
		  	}
		  	else {
		  		$sql = "SELECT * FROM categories WHERE active = 1 AND (member = 1 OR member = 3)";
		  	}
		  	$cats = $db->queryArray($sql);
		  	$i = 0;
		  	
		  	if ($type == "member"){
  				$sql = "SELECT sum(timeTotal) as timeTotal, sum(priceTotal) as priceTotal FROM rides AS A LEFT JOIN members AS B ON A.riderID = B.ID WHERE A.riderID > 0 AND B.campRider = 'no' ";	
  			}
  			elseif ($type == "camp"){
  				$sql = "SELECT sum(timeTotal) as timeTotal, sum(priceTotal) as priceTotal FROM rides AS A LEFT JOIN members AS B ON A.riderID = B.ID WHERE A.riderID > 0 AND (B.campRider = 'yes' OR B.campRider = 'inactive') ";	
  			}
  			elseif ($type == "nonmember"){
  				$sql = "SELECT sum(timeTotal) as timeTotal, sum(priceTotal) as priceTotal FROM rides AS A WHERE (A.riderID IS null OR A.riderID = 0) ";
  			}
  			
  			$sql .= "AND boatID = '" . $boat["ID"] . "' ";
  			if ($_SESSION["stats_member"]["sportID"] != "all"){
  				$sql .= "AND A.sportID = '" . $_SESSION["stats_member"]["sportID"] ."'";
  			}

                        if ($_SESSION["stats_member"]["m"] != 13){
                            $sql .= " AND A.ID IN (SELECT rideID FROM rideTimes WHERE start BETWEEN '" . mktime(null,null,null,$_SESSION["stats_member"]["m"],1,$_SESSION["stats_member"]["Y"]) . "' AND '" . mktime(null,null,null,$_SESSION["stats_member"]["m"]+1,0,$_SESSION["stats_member"]["Y"]) . "') ";
                        }
                        else {
                            $sql .= " AND A.ID IN (SELECT rideID FROM rideTimes WHERE start BETWEEN '" . mktime(null,null,null,1,1,$_SESSION["stats_member"]["Y"]) . "' AND '" . mktime(null,null,null,12,0,$_SESSION["stats_member"]["Y"]) . "') ";
                        }
  			
  			
		  	foreach ($cats as $cat){
		  		if ($i%2){
		  			echo "<tr class=\"bg_stats\">";
		  		}
		  		else {
		  			echo "<tr class=\"bg_stats_lite\">";	
		  		}
		  			
		  			
		  			
		  			
		  			
				    echo "<td>" . $cat["name"] . "</td>";
				    //without ballast
				   	$catsql = $sql . " AND A.categoryID = '" . $cat["ID"] . "'";
				   	$withoutBallast = $db->queryArray($catsql . " AND A.ballast = 'no'");
				   	$withBallast = $db->queryArray($catsql . " AND A.ballast = 'yes'");
				   	$total = $db->queryArray($catsql);
//                                        echo "<hr>" . $catsql."===> ". print_r($total) ."<hr>";
				   	
				    echo "<td>";
				    if ($withoutBallast[0]["timeTotal"] > 0 && $withoutBallast[0]["priceTotal"] > 0){
				    	echo secToMinutes($withoutBallast[0]["timeTotal"]) . " / ";
				    	echo $withoutBallast[0]["priceTotal"] . " ";
				    	echo getPreferences("currencyHTML");
				    }
				    else {
				    	echo "-";
				    }
			    	echo "</td>";
			    	
				    echo "<td>";
				    if ($withBallast[0]["timeTotal"] > 0 && $withBallast[0]["priceTotal"] > 0){
				    	echo secToMinutes($withBallast[0]["timeTotal"]) . " / ";
				    	echo $withBallast[0]["priceTotal"] . " ";
				    	echo getPreferences("currencyHTML");
				    }
				    else {
				    	echo "-";
				    }
			    	echo "</td>";
				    
			    	echo "<td>";
			    	if ($total[0]["timeTotal"] > 0 && $total[0]["priceTotal"] > 0){
				    	echo secToMinutes($total[0]["timeTotal"]) . " / ";
				    	echo $total[0]["priceTotal"] . " ";
				    	echo getPreferences("currencyHTML");
				    }
				    else {
				    	echo "-";
				    }
			    	echo "</td>";
				echo "</tr>";
		  		$i++;
		  	}
		  ?>
			  
		  <tr >
		    <td>&nbsp;</td>
		    <td>&nbsp;</td>
		    <td>&nbsp;</td>
		  </tr>
		  
		  
		  <tr class="bg_stats">
		    <td>Total:</td>
		    	<?php
			   	$withoutBallast = $db->queryArray($sql . " AND A.ballast = 'no'");
			   	$withBallast = $db->queryArray($sql . " AND A.ballast = 'yes'");
			   	$total = $db->queryArray($sql);
			   	
			    echo "<td>";
			    if ($withoutBallast[0]["timeTotal"] > 0 && $withoutBallast[0]["priceTotal"] > 0){
			    	echo secToMinutes($withoutBallast[0]["timeTotal"]) . " / ";
			    	echo $withoutBallast[0]["priceTotal"] . " ";
			    	echo getPreferences("currencyHTML");
			    }
			    else {
			    	echo "-";
			    }
		    	echo "</td>";
		    	
			    echo "<td>";
			    if ($withBallast[0]["timeTotal"] > 0 && $withBallast[0]["priceTotal"] > 0){
			    	echo secToMinutes($withBallast[0]["timeTotal"]) . " / ";
			    	echo $withBallast[0]["priceTotal"] . " ";
			    	echo getPreferences("currencyHTML");
			    }
			    else {
			    	echo "-";
			    }
			    echo "</td>";
			    
		    	echo "<td>";
		    	if ($total[0]["timeTotal"] > 0 && $total[0]["priceTotal"] > 0){
			    	echo secToMinutes($total[0]["timeTotal"]) . " / ";
			    	echo $total[0]["priceTotal"] . " ";
			    	echo getPreferences("currencyHTML");
		    	}
		    	else {
		    		echo "-";
		    	}
		    	echo "</td>";
		    	?>
		  </tr>
		  <tr class="bg_stats_lite">
		    <td>Total fuel cons:</td>
		    <td>&nbsp;</td>
		    <td>&nbsp;</td>
		    <?php
                        if ($_SESSION["stats_member"]["m"] != 13){
                            $sql = "SELECT sum(fuel_liters) as fuelTotal, sum(price) AS priceTotal FROM maintenance WHERE fuel_liters > 0 AND price > 0 AND ts BETWEEN '" . $_SESSION["stats_member"]["Y"] . "-" . $_SESSION["stats_member"]["m"] . "-01 00:00:00' AND '" . $_SESSION["stats_member"]["Y"] . "-" . date("m", mktime(null,null,null,$_SESSION["stats_member"]["m"]+1,0,$_SESSION["stats_member"]["Y"])). "-31 23:59:59' AND boatID = '" . $boat["ID"] . "'";
                        }
                        else {
                            $sql = "SELECT sum(fuel_liters) as fuelTotal, sum(price) AS priceTotal FROM maintenance WHERE fuel_liters > 0 AND price > 0 AND ts BETWEEN '" . $_SESSION["stats_member"]["Y"] . "-01-01 00:00:00' AND '" . $_SESSION["stats_member"]["Y"] . "-" . date("m", mktime(null,null,null,12,0,$_SESSION["stats_member"]["Y"])). "-31 23:59:59' AND boatID = '" . $boat["ID"] . "'";
                        }

		    	
		    	$maintenanceData = $db->queryArray($sql);
		    	if ($maintenanceData[0]["fuelTotal"] > 0 && $maintenanceData[0]["priceTotal"] > 0){
		    		echo "<td>" . $maintenanceData[0]["fuelTotal"] . " / " . $maintenanceData[0]["priceTotal"] . " " . getPreferences("currencyHTML") . "</td>";	
		    	}
		    	else {
		    		echo "<td>-</td>";
		    	}
		    	
		    	
		    ?>
		    
		  </tr>
		  <tr>
		    <td>&nbsp;</td>
		    <td>&nbsp;</td>
		    <td>&nbsp;</td>
		  </tr>
		  <tr class="bg_stats">
		    <td>Average income / min:</td>
		    <?php
		    	if ($withoutBallast[0]["timeTotal"] > 0 && $withoutBallast[0]["priceTotal"] > 0){
		    		echo "<td style=\"color:green;\">";
		    			echo round ( ($withoutBallast[0]["priceTotal"] / $withoutBallast[0]["timeTotal"] * 60),2);
		    		echo " " . getPreferences("currencyHTML") . "/min";
		    	}
		    	else {
		    		echo "<td>-</td>";
		    	}
		    
		    	if ($withBallast[0]["timeTotal"] > 0 && $withBallast[0]["priceTotal"] > 0){
		    		echo "<td style=\"color:green;\">";
		    			echo round ( ($withBallast[0]["priceTotal"] / $withBallast[0]["timeTotal"] * 60),2);
		    		echo " " . getPreferences("currencyHTML") . "/min";
		    	}
		    	else {
		    		echo "<td>-</td>";
		    	}
		    
		    	if ($total[0]["timeTotal"] > 0 && $total[0]["priceTotal"] > 0){
		    		echo "<td style=\"color:green;\">";
		    			echo round ( ($total[0]["priceTotal"] / $total[0]["timeTotal"] * 60),2);
		    		echo " " . getPreferences("currencyHTML") . "/min";
		    	}
		    	else {
		    		echo "<td>-</td>";
		    	}
		    
		    ?>
		  </tr>
		  <tr class="bg_stats_lite">
		    <td>Average fuel cons / min:</td>
		    <?php
                        

                        if ($_SESSION["stats_member"]["m"] != 13){
                            $sql = "SELECT sum(timeTotal) as timeTotal FROM rides AS A LEFT JOIN members AS B ON A.riderID = B.ID WHERE boatID = '" . $boat["ID"] . "'AND A.ID IN (SELECT rideID FROM rideTimes WHERE start BETWEEN '" . mktime(null,null,null,$_SESSION["stats_member"]["m"],1,$_SESSION["stats_member"]["Y"]) . "' AND '" . mktime(null,null,null,$_SESSION["stats_member"]["m"]+1,0,$_SESSION["stats_member"]["Y"]) . "') ";
                        }
                        else {
                            $sql = "SELECT sum(timeTotal) as timeTotal FROM rides AS A LEFT JOIN members AS B ON A.riderID = B.ID WHERE boatID = '" . $boat["ID"] . "'AND A.ID IN (SELECT rideID FROM rideTimes WHERE start BETWEEN '" . mktime(null,null,null,1,1,$_SESSION["stats_member"]["Y"]) . "' AND '" . mktime(null,null,null,12,0,$_SESSION["stats_member"]["Y"]) . "') ";
                        }

                        $fuelTime = $db->querySingleItem($sql);
		    	if ($maintenanceData[0]["priceTotal"] > 0 && $fuelTime > 0){
		    		$fuel_min = round( ($maintenanceData[0]["priceTotal"] / $fuelTime * 60),2) . " " . getPreferences("currencyHTML") . "/min";
		    		echo "<td style=\"color:red;\"></td>";	
		    		echo "<td style=\"color:red;\"></td>";	
		    		echo "<td style=\"color:red;\">" . $fuel_min . "</td>";	
		    	}
		    	else {
		    		echo "<td>-</td><td>-</td><td>-</td>";	
		    	}
		    ?>
		  </tr>
		</table>
		<br /><br />
    	<?php
    }
	?>	
  </div>
</div> 
<div class="bottom"></div>
</center>
</body>