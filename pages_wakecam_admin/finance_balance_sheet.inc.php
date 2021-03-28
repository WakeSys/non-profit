<div class="main">
	<div class="main_text_balance_sheet">
	<div class="main_text_title">View Balance Sheet</div>
	<form action="" method="POST">
	Choose Year:

	  <?php
	  if (isset($_POST["Y"])){
	  	$_SESSION["finance_balance"]["Y"] = $_POST["Y"];
	  }
	  if (!isset($_SESSION["finance_balance"]["Y"])){
	  	$_SESSION["finance_balance"]["Y"] = date("Y");
	  }
	  $i = date("Y");
	  while ($i>=2009){
	  	$yearArr[] = $i;
	  	$i--;
	  }
	  echo "<select name=\"Y\" onchange=\"this.form.submit();\">";
	  foreach ($yearArr as $year){
	  	echo "<option value=\"" .  $year ."\"";
	  	if ($_SESSION["finance_balance"]["Y"] == $year){
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
  	$_SESSION["finance_balance"]["m"] = $_POST["m"];
  }
  if (!isset($_SESSION["finance_balance"]["m"])){
  	$_SESSION["finance_balance"]["m"] = date("n");
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
  		if ($key == $_SESSION["finance_balance"]["m"]){
  			echo " selected";
  		}
  		echo ">" . $month . "</option>";
  	}
  ?>
  </select></form>
      <br />
      <br />
        
        <?php
        $sql = "SELECT D.name AS paymentName, D.value AS paymentAddValue, D.percent AS paymentAddPercent, UNIX_TIMESTAMP(A.time) as time, A.invoiceID, A.memberID, sum(A.value) as value, sum(A.CampNights) AS campNights, A.ID, B.campRider, sum(A.payDriver) AS payDriver";
        $sql .= " FROM credits AS A
            LEFT JOIN members AS B ON A.memberID = B.ID
            LEFT JOIN invoices AS C ON A.invoiceID = C.ID
            LEFT JOIN payment AS D ON C.paymentID = D.ID
        ";

		$sql = "SELECT D.name AS paymentName, D.value AS paymentAddValue,
			D.percent AS paymentAddPercent, UNIX_TIMESTAMP(A.time) as time, A.ID AS invoiceID,
			A.memberID, sum(A.value) as value, sum(C.CampNights) AS campNights,
			A.ID, B.campRider, sum(C.payDriver) AS payDriver
			FROM invoices AS A
            LEFT JOIN members AS B ON A.memberID = B.ID
            LEFT JOIN credits AS C ON A.ID = C.invoiceID
            LEFT JOIN payment AS D ON A.paymentID = D.ID
        ";

		$sql = "SELECT UNIX_TIMESTAMP(A.time) as time, A.ID as invoiceID, A.memberID, A.value,
			A.paymentAddValue, A.paymentAddPercent,
			B.campNights, B.payDriver,
			C.campRider,
			D.name AS paymentName

			FROM invoices AS A
			LEFT JOIN credits AS B ON A.ID = B.invoiceID
			LEFT JOIN members AS C ON A.memberID = C.ID
			LEFT JOIN payment AS D ON A.paymentID = D.ID
			";

        if ($_SESSION["finance_balance"]["m"] == 13){
            $sql .= " WHERE A.time BETWEEN '" . $_SESSION["finance_balance"]["Y"] . "-01-01 00:00:00' AND '" . $_SESSION["finance_balance"]["Y"] . "-" . date("m", mktime(null,null,null,12,0,$_SESSION["finance_balance"]["Y"])). "-31 23:59:59'";
        }
        else {
            $sql .= " WHERE A.time BETWEEN '" . $_SESSION["finance_balance"]["Y"] . "-" . $_SESSION["finance_balance"]["m"] . "-01 00:00:00' AND '" . $_SESSION["finance_balance"]["Y"] . "-" . date("m", mktime(null,null,null,$_SESSION["finance_balance"]["m"]+1,0,$_SESSION["finance_balance"]["Y"])). "-31 23:59:59'";
        }

	//	echo "<hr>" . $sql . "<hr>";
        $credits = $db->queryArray($sql);
      //  echo "<pre>";print_r($credits);echo "</pre>";
        if (is_array($credits)){
            foreach ($credits as $elem){
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . $elem["invoiceID"]]["time"] = $elem["time"];
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . $elem["invoiceID"]]["campRider"] = $elem["campRider"];
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . $elem["invoiceID"]]["memberID"] = $elem["memberID"];
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . $elem["invoiceID"]]["payDriver"] = $elem["payDriver"];
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . $elem["invoiceID"]]["value"] = $elem["value"];
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . $elem["invoiceID"]]["campNights"] = $elem["campNights"];
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . $elem["invoiceID"]]["invoiceID"] = $elem["invoiceID"];
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . $elem["invoiceID"]]["paymentName"] = $elem["paymentName"];
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . $elem["invoiceID"]]["paymentAddValue"] = $elem["paymentAddValue"];
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . $elem["invoiceID"]]["paymentAddPercent"] = $elem["paymentAddPercent"];

                }
        }


        $sql = "SELECT ID, UNIX_TIMESTAMP(ts) AS time, price as value, fuel_liters FROM maintenance WHERE";
        if ($_SESSION["finance_balance"]["m"] == 13){
            $sql .= " ts BETWEEN '" . $_SESSION["finance_balance"]["Y"] . "-01-01 00:00:00' AND '" . $_SESSION["finance_balance"]["Y"] . "-" . date("m", mktime(null,null,null,12,0,$_SESSION["finance_balance"]["Y"])). "-31 23:59:59' and price > 0";
        }
        else {
            $sql .= " ts BETWEEN '" . $_SESSION["finance_balance"]["Y"] . "-" . $_SESSION["finance_balance"]["m"] . "-01 00:00:00' AND '" . $_SESSION["finance_balance"]["Y"] . "-" . date("m", mktime(null,null,null,$_SESSION["finance_balance"]["m"]+1,0,$_SESSION["finance_balance"]["Y"])). "-31 23:59:59' and price > 0";
        }

        $maintenance = $db->queryArray($sql);
        //echo $sql;

        if (is_array($maintenance)){
            foreach ($maintenance as $elem){
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . "main_" . $elem["ID"]]["time"] = $elem["time"];
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . "main_" . $elem["ID"]]["campRider"] = null;
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . "main_" . $elem["ID"]]["memberID"] = "FUEL";
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . "main_" . $elem["ID"]]["payDriver"] = null;
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . "main_" . $elem["ID"]]["value"] = $elem["value"];
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . "main_" . $elem["ID"]]["campNights"] = null;
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . "main_" . $elem["ID"]]["invoiceID"] = null;
                    $creditData[date("Ymd", $elem["time"])][date("His", $elem["time"]) . "main_" . $elem["ID"]]["fuel_liters"] = $elem["fuel_liters"];

                }
        }



        if (isset($creditData) && is_array($creditData)){
	    krsort($creditData);

             
            $TotalInMembers = 0;
            $TotalOutFuel = 0;
            $TotalOutPayDriver = 0;
            $TotalInCampNights = 0;
            $TotalInCampRider = 0;
            $TotalInNonMembers = 0;
            $TotalTotalTotal = 0;
            $TotalOutPayment = 0;
            foreach ($creditData as $time=>$creditData2){
                preg_match("/^([0-9]{4})([0-9]{2})([0-9]{2})$/i",$time,$match);
                
                echo "<br /><br /><div class=\"main_text_title\">Balance Sheet for " . $match[3] . "." . $match[2] . "." . $match[1] . "</div>";
                ?>
                <table >
                    <tr class="bg_stats">
          <td width="100">Date</td>
          <td width="110">In Prepaid Customers</td>
          <td width="110">In Guests</td>
          <td width="110">In Postpaid Customers</td>
          <td width="110">In Nights</td>
          <td width="110">Exp Fuel </td>
          <td width="110">Exp Driver </td>
          <td width="110">Transaction Fees</td>
          <td width="110">Total</td>
        </tr>
                <?php
                $i = 0;
                $inMembers = 0;
                $inNonMembers = 0;
                $inCampRider = 0;
                $inCampNights = 0;
                $outPayDriver = 0;
                $outFuel = 0;
                $outPayment = 0;
                $totalTotal = 0;
                
                foreach ($creditData2 as $data){
                    $total = 0;
                    //echo "<pre>";print_r($data);echo "</pre>";
                    if ($i%2){
                        echo "<tr class=\"bg_stats\">";
                    }
                    else {
                        echo "<tr class=\"bg_stats_lite\">";
                    }
                        echo "<td>" . date ("d\.m\.Y H:i", $data["time"]) . "</td>";
                        if ($data["memberID"] > 0){
                            if ($data["campRider"] == "yes" || $data["campRider"] == "inactive"){
                                echo "<td>-</td>";
                            }
                            else {
                                echo "<td>" . $data["value"]. " " . getPreferences("currencyHTML") . "</td>";
                                $inMembers += $data["value"];
                                $total += $data["value"];
                            }
                            echo "<td>-</td>";
                        }
                        elseif ($data["memberID"] == "FUEL"){
                            echo "<td>-</td>";
                            echo "<td>-</td>";
                        }
                        else {
                            echo "<td>-</td>";
                            echo "<td>" . $data["value"]. " " . getPreferences("currencyHTML") . "</td>";
                            $inNonMembers += $data["value"];
                            $total += $data["value"];
                        }
                        if ($data["campRider"] == "yes" || $data["campRider"] == "inactive"){
                            if ($data["campNights"] > 0){
                                $sql = "SELECT sum(value) AS value FROM credits WHERE
                                    invoiceID = '" . $data["invoiceID"] . "'
                                    AND campNights > 0 GROUP BY invoiceID";
                                $campNightsValue = $db->querySingleItem($sql);
                                $inCampRider += $data["value"] - $campNightsValue;
                                $inCampNights += $campNightsValue;
                                $total += $data["value"];
                                echo "<td>" . ($data["value"] - $campNightsValue) . " " . getPreferences("currencyHTML") . "</td>";
                                echo "<td>" . $campNightsValue . " " . getPreferences("currencyHTML") . "</td>";
                            }
                            else {
                                $inCampRider += $data["value"];
                                $total += $data["value"];
                                echo "<td>" . $data["value"]. " " . getPreferences("currencyHTML") . "</td>";
                                echo "<td>-</td>";
                            }
                            
                        }
                        else {
                            echo "<td>-</td>";
                            echo "<td>-</td>";
                        }

                        if ($data["memberID"] == "FUEL"){
                           echo "<td style=\"color:red;\">" . $data["value"]. " " . getPreferences("currencyHTML") . "</td>";
                           $outFuel += $data["value"];
                           $total -= $data["value"];
                        }
                        else {
                            echo "<td>-</td>";
                        }
                        
                        if ($data["payDriver"] > 0){
                            echo "<td style=\"color:red;\">" . $data["payDriver"] . " " . getPreferences("currencyHTML") . "</td>";
                            $outPayDriver += $data["payDriver"];
                            $total -= $data["payDriver"];
                        }
                        else {
                            echo "<td>-</td>";
                        }

                        if ($data["invoiceID"] > 0){
                            echo "<td>".  $data["paymentName"]. "";
                            if ($data["paymentAddValue"] > 0 || $data["paymentAddPercent"] > 0){
                                echo "<span style=\"color:red;\">(";
                                if ($data["paymentAddValue"] > 0 ){
                                    echo $data["paymentAddValue"];
                                    $total -= $data["paymentAddValue"];
                                    $outPayment += $data["paymentAddValue"];
                                }

                                if ($data["paymentAddPercent"] > 0 ){
                                    if ($data["paymentAddValue"] > 0 ){
                                        echo " / ";
                                    }
                                    if ($data["memberID"] != "FUEL"){
                                        $paymentPercent = round($data["value"] / 100 * $data["paymentAddPercent"],2);
                                    }
                                    echo $paymentPercent . " " . getPreferences("currencyHTML") . " (" . $data["paymentAddPercent"] . "%)";
                                    $outPayment += $paymentPercent;
                                    $total -= $paymentPercent;
                                }

                                echo ")</span>";
                            }
                            echo " </td>";
                        }
                        else {
                            echo "<td>-</td>";
                        }

                        if ($total < 0){
                            echo "<td style=\"color:red;\">" . number_format($total,2) . " " . getPreferences("currencyHTML") . "</td>";
                        }
                        else {
                            echo "<td>" . number_format($total,2) . " " . getPreferences("currencyHTML") . "</td>";
                        }
                        $totalTotal += $total;
                        
                    echo "</tr>";
                    $i++;
                }


                if ($i%2){
                    echo "<tr class=\"bg_stats\">";
                }
                else {
                    echo "<tr class=\"bg_stats_lite\">";
                }
               
                echo "<td><b>" . date ("d\.m\.Y", $data["time"]) . " Total</b></td>";
                if ($inMembers > 0){
                    echo "<td><b>" . number_format($inMembers,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                    $TotalInMembers += $inMembers;
                }
                else {
                    echo "<td>-</td>";
                }
                
                if ($inNonMembers > 0){
                    echo "<td><b>" . number_format($inNonMembers,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                    $TotalInNonMembers += $inNonMembers;
                }
                else {
                    echo "<td>-</td>";
                }

                if ($inCampRider > 0){
                    echo "<td><b>" . number_format($inCampRider,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                    $TotalInCampRider += $inCampRider;
                }
                else {
                    echo "<td>-</td>";
                }

                if ($inCampNights > 0){
                    echo "<td><b>" . number_format($inCampNights,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                    $TotalInCampNights += $inCampNights;
                }
                else {
                    echo "<td>-</td>";
                }

                if ($outFuel > 0){
                    echo "<td><b style=\"color:red;\">" . number_format($outFuel,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                    $TotalOutFuel += $outFuel;
                }
                else {
                    echo "<td>-</td>";
                }

                if ($outPayDriver > 0){
                    echo "<td><b style=\"color:red;\">" . number_format($outPayDriver,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                    $TotalOutPayDriver += $outPayDriver;
                }
                else {
                    echo "<td>-</td>";
                }

                if ($outPayment > 0){
                    echo "<td><b style=\"color:red;\">" . number_format($outPayment,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                    $TotalOutPayment += $outPayment;
                }
                else {
                    echo "<td>-</td>";
                }

                if ($totalTotal < 0){
                    echo "<td><b style=\"color:red;\">" . number_format($totalTotal,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                    $TotalTotalTotal += $totalTotal;
                }
                else {
                    echo "<td><b>" . number_format($totalTotal,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                    $TotalTotalTotal += $totalTotal;
                }


                echo "</tr>";
                echo "</table>";

            }

            echo "<br /><br /><div class=\"main_text_title\">TOTAL Balance Sheet</div>";
                ?>
                <table >
                    <tr class="bg_stats">
                      <td width="100">Date</td>
                      <td width="110">In Prepaid Customers</td>
                      <td width="110">In Guests</td>
                      <td width="110">In Postpaid Customers</td>
                      <td width="110">In Nights</td>
                      <td width="110">Exp Fuel </td>
                      <td width="110">Exp Driver </td>
                      <td width="110">Transaction Fees</td>
                      <td width="110">Total</td>
                    </tr>
                    <?php
                    echo "<tr class=\"bg_stats_lite\">";
                        echo "<td>TOTAL</td>";
                        if ($TotalInMembers > 0){
                            echo "<td><b>" . number_format($TotalInMembers,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                        }
                        else {
                            echo "<td>-</td>";
                        }

                        if ($TotalInNonMembers > 0){
                            echo "<td><b>" . number_format($TotalInNonMembers,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                        }
                        else {
                            echo "<td>-</td>";
                        }

                        if ($TotalInCampRider > 0){
                            echo "<td><b>" . number_format($TotalInCampRider,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                        }
                        else {
                            echo "<td>-</td>";
                        }

                        if ($TotalInCampNights > 0){
                            echo "<td><b>" . number_format($TotalInCampNights,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                        }
                        else {
                            echo "<td>-</td>";
                        }

                        if ($TotalOutFuel > 0){
                            echo "<td><b style=\"color:red;\">" . number_format($TotalOutFuel,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                        }
                        else {
                            echo "<td>-</td>";
                        }

                        if ($TotalOutPayDriver > 0){
                            echo "<td><b style=\"color:red;\">" . number_format($TotalOutPayDriver,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                        }
                        else {
                            echo "<td>-</td>";
                        }

                        if ($TotalOutPayment > 0){
                            echo "<td><b style=\"color:red;\">" . number_format($TotalOutPayment,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                        }
                        else {
                            echo "<td>-</td>";
                        }

                        if ($TotalTotalTotal < 0){
                            echo "<td><b style=\"color:red;\">" . number_format($TotalTotalTotal,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                        }
                        else {
                            echo "<td><b>" . number_format($TotalTotalTotal,2) . " " . getPreferences("currencyHTML") . "</b></td>";
                        }
                    echo "</tr>";

                    ?>
                </table>
                <?php
        }

           
            





        ?>
        

  </div>
</div> 
<div class="bottom"></div>

</center>
</body>