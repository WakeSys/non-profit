<div class="main">
	<div class="main_text_balance_sheet">
	<div class="main_text_title">View Paid Invoices</div>
	<form action="" method="POST">
	Choose Year:  
	
	  <?php
	  if (isset($_POST["Y"])){
	  	$_SESSION["finance_invoices"]["Y"] = $_POST["Y"];
	  }
	  if (!isset($_SESSION["finance_invoices"]["Y"])){
	  	$_SESSION["finance_invoices"]["Y"] = date("Y");
	  }
	  $i = date("Y");
	  while ($i>=2009){
	  	$yearArr[] = $i; 
	  	$i--;
	  }
	  echo "<select name=\"Y\" onchange=\"this.form.submit();\">";
	  foreach ($yearArr as $year){
	  	echo "<option value=\"" .  $year ."\"";
	  	if ($_SESSION["finance_invoices"]["Y"] == $year){
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
  	$_SESSION["finance_invoices"]["m"] = $_POST["m"];
  }
  if (!isset($_SESSION["finance_invoices"]["m"])){
  	$_SESSION["finance_invoices"]["m"] = date("n");
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
  		if ($key == $_SESSION["finance_invoices"]["m"]){
  			echo " selected";
  		}
  		echo ">" . $month . "</option>";
  	}
  ?>
  </select></form>
      <br />
      <br />
<table width="980" border="0" cellspacing="0" cellpadding="0">
 
  <?php
  $sql = "SELECT concat(H.first_name, ' ', H.last_name) AS cashierName, A.loginID, UNIX_TIMESTAMP(A.time) AS timestamp, G.name as sportName, F.timeTotal as rideTime, CONCAT(E.first_name, ' ', E.last_name) as driverName, A.rideID, A.value,A.ID, A.memberID, B.riderName, C.first_name, C.last_name, C.campRider, D.name AS paymentName
      FROM invoices AS A
      LEFT JOIN rides AS B ON A.rideID = B.ID
      LEFT JOIN members AS C ON A.memberID = C.ID
      LEFT JOIN payment AS D ON A.paymentID = D.ID
      LEFT JOIN members AS E ON A.memberID = E.ID
      LEFT JOIN rides AS F ON A.rideID = F.ID
      LEFT JOIN sports AS G ON F.sportID = G.ID
      LEFT JOIN members AS H ON A.loginID = H.ID ";
      if ($_SESSION["finance_invoices"]["m"] == 13){
          $sql .= "WHERE A.time BETWEEN '" . $_SESSION["finance_invoices"]["Y"] . "-01-01 00:00:00' AND '" . $_SESSION["finance_invoices"]["Y"] . "-" . date("m", mktime(null,null,null,12,0,$_SESSION["finance_invoices"]["Y"])). "-31 23:59:59'";
      }
      else {
        $sql .= "WHERE A.time BETWEEN '" . $_SESSION["finance_invoices"]["Y"] . "-" . $_SESSION["finance_invoices"]["m"] . "-01 00:00:00' AND '" . $_SESSION["finance_invoices"]["Y"] . "-" . date("m", mktime(null,null,null,$_SESSION["finance_invoices"]["m"]+1,0,$_SESSION["finance_invoices"]["Y"])). "-31 23:59:59'";
      }

  //echo $sql;
  $invoices = $db->queryArray($sql);
  
  
  if (is_array($invoices)){
  		 
    	echo "<tr><td>Date</td><td>Invoice#</td><td>Category</td><td>Name?</td><td>Driver</td><td>Description</td><td>Amount</td><td>Paid by</td></tr>";
	  $i=0;
	  foreach ($invoices as $invoice){
	  	echo "<tr ";
		if ($i%2){
			echo "class=\"bg_stats\">";
		}
		else {
			echo "class=\"bg_stats_lite\">";
		}
		echo "<td>" . date("d\.m\.Y, H:i:s", $invoice["timestamp"]) . "</td>";
	    echo "<td><a class=\"link_ten\" href=\"" . INDEX . "?p=invoice&ID=" . $invoice["ID"] . "\" target=\"_blank\">" . $invoice["ID"] . "</a></td>";
	    if ($invoice["memberID"] > 0){
	    	if ($invoice["campRider"] == "yes"){
	    		echo "<td>CampRider</td>";
	    	}
	    	elseif ($invoice["campRider"] == "inactive"){
	    		echo "<td>CampRider (inactive)</td>";
	    	}
	    	else {
				echo "<td>Member</td>";
	    	}
	    }
	    else {
	    	echo "<td>nonMember</td>";
	    }
	    
	    if ($invoice["memberID"] > 0){
	    	echo "<td>" . $invoice["first_name"] . " " . $invoice["last_name"] . "</td>";
	    }
	    else {
	    	echo "<td>" . $invoice["riderName"] . "</td>";
	    }
	    
	    if ($invoice["loginID"] == "-1"){
	    	echo "<td>Admin</td>";
	    }
	    else {
	    	echo "<td>" . $invoice["cashierName"] . "</td>";
	    }
	    
	    if ($invoice["rideID"] > 0){
	    	
	    	echo "<td>" . secToMinutes($invoice["rideTime"]) . " of " . $invoice["sportName"] . "</td>";
	    }
	    else {
	    	echo "<td>Prepaid Credit </td>";	
	    }
	    echo "<td>" . $invoice["value"] . " " . getPreferences("currencyHTML") . "</td>";
	    echo "<td>" . $invoice["paymentName"] . "</td>";
	    echo "</tr>";
	    $i++;
	  }
  	}
	else {
		echo "<tr><td>No paid invoices yet.</td></tr>";
	}
  ?>
  
</table>
  </div>
</div> 
<div class="bottom"></div>

</center>
</body>