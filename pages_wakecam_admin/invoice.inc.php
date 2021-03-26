<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>invoice</title>
<style type="text/css">
<!--
.invoice_title {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 16px;
	font-weight: bold;
	color: #000;
}
.invoice_text {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
	font-weight: normal;
	color: #000;
}
.title_lines {
	border-bottom-width: medium;
	border-bottom-style: solid;
	border-bottom-color: #000;
	border-right-width: medium;
	border-right-style: solid;
	border-right-color: #000;
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #000;
	padding-right: 5px;
	padding-bottom: 5px;
	padding-left: 5px;
}
.title_lines_no_right_line {
	border-bottom-width: medium;
	border-bottom-style: solid;
	border-bottom-color: #000;
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #000;
	padding-right: 5px;
	padding-bottom: 5px;
	padding-left: 5px;
}
.vert_lines {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
	font-weight: normal;
	color: #000;
	padding-top: 5px;
	padding-right: 5px;
	padding-bottom: 5px;
	padding-left: 5px;
	border-right-width: medium;
	border-right-style: solid;
	border-right-color: #000;
}
.amount {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
	font-weight: normal;
	color: #000;
	padding: 5px;
}
.subtotal {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
	font-weight: normal;
	color: #000;
	padding: 5px;
	border-top-width: medium;
	border-bottom-width: medium;
	border-top-style: solid;
	border-bottom-style: solid;
	border-top-color: #000;
	border-bottom-color: #000;
}
.invoice_amount {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 16px;
	font-weight: bold;
	color: #000;
	padding: 5px;
}
.invoice_amount_with_double_line {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 16px;
	font-weight: bold;
	color: #000;
	padding: 5px;
	border-bottom-width: medium;
	border-bottom-style: double;
	border-bottom-color: #000;
}
-->
</style>
</head>

<?php
if (!isset($_GET["ID"]) || !is_numeric($_GET["ID"])){
	die ("ERROR no invoice!");
}

$sql = "SELECT A.*, C.rounding, CONCAT(F.first_name, ' ', F.last_name) AS cashier, C.timeTotal, C.priceTotal, E.name as paymentName, C.price, D.name AS sportName, B.campRider,A.ID AS invoiceID, UNIX_TIMESTAMP(A.time) AS timestamp, B.*,
		G.start, C.counter, C.autostop
        FROM invoices AS A LEFT JOIN members AS B ON A.memberID = B.ID
        LEFT JOIN rides AS C ON A.rideID = C.ID LEFT JOIN sports AS D ON C.sportID = D.ID
        LEFT JOIN payment AS E ON A.paymentID = E.ID
        LEFT JOIN members AS F ON A.loginID = F.ID
		LEFT JOIN rideTimes AS G ON A.rideID = G.rideID
        WHERE A.ID = '" . $_GET["ID"] . "'";

$invoiceData = $db->queryArray($sql);
//echo "<pre>";print_r($invoiceData);echo "</pre>";
?>
<body leftmargin="75" topmargin="75" marginwidth="75" marginheight="75">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="50%" align="left" valign="top" class="invoice_title"><?php echo $preferences[0]["contact_name_of_school"];?></td>
    <td width="50%" align="right" valign="top" class="invoice_text"><?php echo $preferences[0]["contact_address"];?><br />
      <?php echo $preferences[0]["contact_postal_code"];?> <?php echo $preferences[0]["contact_town"];?><br />
      <?php echo $preferences[0]["contact_country"];?><br />
      <?php echo $preferences[0]["contact_phone"];?><br />
      <?php echo $preferences[0]["contact_website"];?></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="invoice_title">&nbsp;</td>
    <td align="right" valign="top" class="invoice_text">&nbsp;</td>
  </tr>
  <tr>
    <td align="left" valign="top" class="invoice_text"><?php
    	if ($invoiceData[0]["memberID"] > 0){
    		echo $invoiceData[0]["first_name"] . " " . $invoiceData[0]["last_name"] . "<br>";
    		echo $invoiceData[0]["address"] . "<br>";
    		echo $invoiceData[0]["postal_code"] . " " . $invoiceData[0]["town"] . "<br>";
    		echo $invoiceData[0]["country"] ;
    	}
    	else {
    		echo "<br>&nbsp;<br>&nbsp;<br>&nbsp;";
    	}
    ?></td>
    <td align="right" valign="top" class="invoice_text">&nbsp;</td>
  </tr>
  <tr>
    <td align="left" valign="top" class="invoice_title">&nbsp;</td>
    <td align="right" valign="top" class="invoice_text">&nbsp;</td>
  </tr>
  <tr>
    <td align="left" valign="top" class="invoice_title">Invoice#: <?php echo $invoiceData[0]["invoiceID"];?></td>
    <td align="right" valign="top" class="invoice_title"><?php echo date("d\.m\.Y \a\t H:i", $invoiceData[0]["timestamp"]);?></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="invoice_title">&nbsp;</td>
    <td align="right" valign="top" class="invoice_text">&nbsp;</td>
  </tr>
</table>
<br />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    <td width="5%" align="left" valign="top" class="title_lines">Pos</td>
    <td width="45%" align="left" valign="top" class="title_lines">Description</td>
    <td width="10%" align="right" valign="top" class="title_lines">Quantity</td>
    <td width="10%" align="right" valign="top" class="title_lines">Unit</td>
    <td width="15%" align="right" valign="top" class="title_lines">Respective Price</td>
    <td width="15%" align="right" valign="top" class="title_lines_no_right_line">Amount</td>
  </tr>
  <?php
        $VAT[$preferences[0]["VAT_riding"]] = 0;
        $VAT[$preferences[0]["VAT_nights"]] = 0;
  	$subtotal = 0;
  	if ($invoiceData[0]["memberID"] < 1){

				if ($invoiceData[0]["counter"] > 0 && $invoiceData[0]["autostop"] == "yes"){
					$invoiceData[0]["timeTotal"] = $invoiceData[0]["counter"];
				}
				
                $roundedTime = $invoiceData[0]["timeTotal"] / 60;
                if ($invoiceData[0]["rounding"] == "round"){
                        $roundedTime = round ($roundedTime);
                }
				 elseif ($invoiceData[0]["rounding"] == "second"){
                        $roundedTime = round ($roundedTime,2);
                }

                elseif ($invoiceData[0]["rounding"] == "up"){
                        $roundedTime = ceil ($roundedTime);
                }

                elseif ($invoiceData[0]["rounding"] == "down"){
                        $roundedTime = floor ($roundedTime);
                }


                if ($preferences[0]["VAT_riding"] > 0 && $preferences[0]["VAT_deduce"] == "yes"){
                    $pricePerMin = round($invoiceData[0]["value"]/$roundedTime/(1+$preferences[0]["VAT_riding"]/100),2);
                    $priceRideTotal = round($invoiceData[0]["value"]/(1+$preferences[0]["VAT_riding"]/100),2);
                    $VAT[$preferences[0]["VAT_riding"]] += ($invoiceData[0]["value"] - $priceRideTotal);
                }
                else {
                    $pricePerMin = round($invoiceData[0]["value"]/$roundedTime,2);
                    $priceRideTotal = $invoiceData[0]["value"];
                }


		echo "<tr>
		    <td align=\"left\" valign=\"top\" class=\"vert_lines\">1</td>
		    <td align=\"left\" valign=\"top\" class=\"vert_lines\">" . $invoiceData[0]["sportName"] . " (" . $roundedTime . "') on " . date("d\.m\.Y", $invoiceData[0]["start"]) . " at " . date("H:i", $invoiceData[0]["start"]) . "</td>
		    <td align=\"right\" valign=\"top\" class=\"vert_lines\">" . $roundedTime . "'</td>
		    <td align=\"right\" valign=\"top\" class=\"vert_lines\">minute(s)</td>";

                echo "<td align=\"right\" valign=\"top\" class=\"vert_lines\">" . $pricePerMin . " " . getPreferences("currencyHTML") . "</td>";


		echo "<td align=\"right\" valign=\"top\" class=\"amount\">" . $priceRideTotal . " " . getPreferences("currencyHTML") . "</td>
		</tr>";
		
		
		
  	}
        elseif ($invoiceData[0]["campRider"] == "yes" || $invoiceData[0]["campRider"] == "inactive"){
            $sql = "SELECT * FROM credits WHERE campNights > 0 AND invoiceID = '" . $invoiceData[0]["invoiceID"] . "'";
            $nightData = $db->queryArray($sql);
           
            $i = 1;
            if (is_array($nightData)){
                if ($preferences[0]["VAT_nights"] > 0 && $preferences[0]["VAT_deduce"] == "yes"){
                    $pricePerNight = round($nightData[0]["value"]/$nightData[0]["campNights"]/(1+$preferences[0]["VAT_nights"]/100),2);
                    $priceNightTotal = round($nightData[0]["value"]/(1+$preferences[0]["VAT_nights"]/100),2);
                    $VAT[$preferences[0]["VAT_nights"]] += ($nightData[0]["value"] - $priceNightTotal);
                }
                else {
                    $pricePerNight = $nightData[0]["value"]/$nightData[0]["campNights"];
                    $priceNightTotal = $nightData[0]["value"];
                }

                echo "<tr>
                    <td align=\"left\" valign=\"top\" class=\"vert_lines\">" . $i . "</td>
                    <td align=\"left\" valign=\"top\" class=\"vert_lines\">night(s)</td>
                    <td align=\"right\" valign=\"top\" class=\"vert_lines\">" . $nightData[0]["campNights"] . "</td>
                    <td align=\"right\" valign=\"top\" class=\"vert_lines\">night(s)</td>
                    <td align=\"right\" valign=\"top\" class=\"vert_lines\">" . $pricePerNight . " " . getPreferences("currencyHTML") . "</td>
                    <td align=\"right\" valign=\"top\" class=\"amount\">" . $priceNightTotal . " " . getPreferences("currencyHTML") . "</td>
                </tr>";
                $i++;
            }

            $sql = "SELECT A.*, B.*, C.name as sportName,
					D.start
					FROM credits AS A
                    LEFT JOIN rides AS B ON A.rideID = B.ID
                    LEFT JOIN sports AS C ON B.sportID = C.ID
					LEFT JOIN rideTimes AS D ON A.rideID = D.rideID
                    WHERE campNights IS NULL AND invoiceID = '" . $invoiceData[0]["invoiceID"] . "'
					GROUP BY rideID
					";

			$rideData = $db->queryArray($sql);
            if (is_array($rideData)){
                foreach ($rideData as $ride){

					if ($ride["counter"] > 0 && $ride["autostop"] == "yes"){
						$ride["timeTotal"] = $ride["counter"];
					}

                    $roundedTime = $ride["timeTotal"] / 60;
                    if ($ride["rounding"] == "round"){
                            $roundedTime = round ($roundedTime);
                    }
					elseif ($ride["rounding"] == "second"){
                            $roundedTime = round ($roundedTime,2);
                    }

                    elseif ($ride["rounding"] == "up"){
                            $roundedTime = ceil ($roundedTime);
                    }

                    elseif ($ride["rounding"] == "down"){
                            $roundedTime = floor ($roundedTime);
                    }

                    if ($preferences[0]["VAT_riding"] > 0 && $preferences[0]["VAT_deduce"] == "yes"){
                        $pricePerMin = round($ride["value"]/$roundedTime/(1+$preferences[0]["VAT_riding"]/100),2);
                        $priceRideTotal = round($ride["value"]/(1+$preferences[0]["VAT_riding"]/100),2);
                        $VAT[$preferences[0]["VAT_riding"]] += ($ride["value"] - $priceRideTotal);
                    }
                    else {
                        $pricePerMin = round($ride["value"]/$roundedTime,2);
                        $priceRideTotal = $ride["value"];
                    }

                     echo "<tr>
                    <td align=\"left\" valign=\"top\" class=\"vert_lines\">" . $i . "</td>
                    <td align=\"left\" valign=\"top\" class=\"vert_lines\">" . $ride["sportName"] . " (" . $roundedTime . "') on " . date("d\.m\.Y", $ride["start"]) . " at " . date("H:i", $ride["start"]) . "</td>
                    <td align=\"right\" valign=\"top\" class=\"vert_lines\">" . $roundedTime . "'</td>
                    <td align=\"right\" valign=\"top\" class=\"vert_lines\">minute(s)</td>
                    <td align=\"right\" valign=\"top\" class=\"vert_lines\">" . $pricePerMin . " " . getPreferences("currencyHTML") . "</td>
                    <td align=\"right\" valign=\"top\" class=\"amount\">" . $priceRideTotal . " " . getPreferences("currencyHTML") . "</td>
                </tr>";
                     $i++;
                }
            }

        }
	else {


                if ($preferences[0]["VAT_riding"] > 0 && $preferences[0]["VAT_deduce"] == "yes"){
                    $priceRideTotal = round($invoiceData[0]["value"]/(1+$preferences[0]["VAT_riding"]/100),2);
                    $VAT[$preferences[0]["VAT_riding"]] += ($invoiceData[0]["value"] - $priceRideTotal);
                }
                else {
                    $priceRideTotal = $invoiceData[0]["value"];
                }

		echo "<tr>
		    <td align=\"left\" valign=\"top\" class=\"vert_lines\">1</td>
		    <td align=\"left\" valign=\"top\" class=\"vert_lines\">Prepaid - Membership / ";
			$sql = "SELECT * FROM sports WHERE active ='1'";
			$sports = $db->queryArray($sql);
			$i = 0;
			foreach ($sports as $sport){
				if ($i != 0){
					echo " / ";
				}
				echo $sport["name"];
				$i++;
			}
			echo "</td>
		    <td align=\"right\" valign=\"top\" class=\"vert_lines\">1 </td>
		    <td align=\"right\" valign=\"top\" class=\"vert_lines\">piece(s)</td>";

        		echo "<td align=\"right\" valign=\"top\" class=\"vert_lines\">" . $priceRideTotal . " " . getPreferences("currencyHTML") . "</td>";
		    
		    echo "<td align=\"right\" valign=\"top\" class=\"amount\">" . $priceRideTotal . " " . getPreferences("currencyHTML") . "</td>
		</tr>";
			
	}
	?>
  

  

  <tr>
    <td align="left" valign="top" class="vert_lines">&nbsp;</td>
    <td align="left" valign="top" class="vert_lines">&nbsp;</td>
    <td align="right" valign="top" class="vert_lines">&nbsp;</td>
    <td align="right" valign="top" class="vert_lines">&nbsp;</td>
    <td align="right" valign="top" class="vert_lines">&nbsp;</td>
    <td align="right" valign="top" class="amount">&nbsp;</td>
  </tr>
 
  <?php
  if ($preferences[0]["VAT_deduce"] == "yes"){
        echo "<tr>
    <td align=\"left\" valign=\"top\" class=\"vert_lines\">&nbsp;</td>
    <td align=\"left\" valign=\"top\" class=\"vert_lines\"><strong>Subtotal</strong></td>
    <td align=\"right\" valign=\"top\" class=\"vert_lines\">&nbsp;</td>
    <td align=\"right\" valign=\"top\" class=\"vert_lines\">&nbsp;</td>
    <td align=\"right\" valign=\"top\" class=\"vert_lines\">&nbsp;</td>";
    if ($VAT[$preferences[0]["VAT_nights"]] == $VAT[$preferences[0]["VAT_riding"]]){
        $subtotal = $invoiceData[0]["value"] -  $VAT[$preferences[0]["VAT_riding"]];
    }
    else {
        $subtotal = $invoiceData[0]["value"] -  $VAT[$preferences[0]["VAT_nights"]] -  $VAT[$preferences[0]["VAT_riding"]];
    }
    echo "<td align=\"right\" valign=\"top\" class=\"subtotal\">" . $subtotal . " " . getPreferences("currencyHTML") . "</td>
    </tr>";
  	if ($VAT[$preferences[0]["VAT_nights"]] > 0 ){
  		echo "<tr>";
		    echo "<td align=\"left\" valign=\"top\" class=\"vert_lines\">&nbsp;</td>";
		    echo "<td align=\"left\" valign=\"top\" class=\"vert_lines\">VAT for night(s)</td>";
		    echo "<td align=\"right\" valign=\"top\" class=\"vert_lines\">&nbsp;</td>";
		    echo "<td align=\"right\" valign=\"top\" class=\"vert_lines\">&nbsp;</td>";
		    echo "<td align=\"right\" valign=\"top\" class=\"vert_lines\">" . $preferences[0]["VAT_nights"] . " %</td>";
		    echo "<td align=\"right\" valign=\"top\" class=\"amount\">" . $VAT[$preferences[0]["VAT_nights"]] . getPreferences("currencyHTML") . "</td>";
	    echo "</tr>";
  	}
  	if ($VAT[$preferences[0]["VAT_riding"]] > 0 && $preferences[0]["VAT_riding"]  != $preferences[0]["VAT_nights"]){
  		echo "<tr>";
		    echo "<td align=\"left\" valign=\"top\" class=\"vert_lines\">&nbsp;</td>";
		    echo "<td align=\"left\" valign=\"top\" class=\"vert_lines\">VAT for riding</td>";
		    echo "<td align=\"right\" valign=\"top\" class=\"vert_lines\">&nbsp;</td>";
		    echo "<td align=\"right\" valign=\"top\" class=\"vert_lines\">&nbsp;</td>";
		    echo "<td align=\"right\" valign=\"top\" class=\"vert_lines\">" . $preferences[0]["VAT_riding"] . " %</td>";
		    echo "<td align=\"right\" valign=\"top\" class=\"amount\">" . $VAT[$preferences[0]["VAT_riding"]] . getPreferences("currencyHTML") . "</td>";
	    echo "</tr>";
  	}
  }

 ?>
  <tr>
    <td align="left" valign="top">&nbsp;</td>
    <td align="left" valign="top" class="invoice_amount"> Amount Paid</td>
    <td align="right" valign="top">&nbsp;</td>
    <td align="right" valign="top">&nbsp;</td>
    <td align="right" valign="top" class="vert_lines">&nbsp;</td>
    <td align="right" valign="top" class="invoice_amount_with_double_line"><?php echo $invoiceData[0]["value"] . " " . getPreferences("currencyHTML"); ?></td>
  </tr>
</table>
<p><br />
  <span class="invoice_text"><br />
  
  <strong>Invoice paid by <?php echo $invoiceData[0]["paymentName"]?> on <?php echo date("d\.m\.Y \a\\t H:i", $invoiceData[0]["timestamp"]);?><br />
Your cashier was <?php if ($invoiceData[0]["loginID"] != "-1"){
	echo $invoiceData[0]["cashier"];
}
else {
	echo "Admin";
}
?></strong><br />
  <br /><br />
  
Thank you for your order!<br />
<br />
VAT Number: <?php echo $preferences[0]["contact_VAT"];?><br />
Business Registration Number: <?php echo $preferences[0]["contact_BRN"];?></span><br />
  <br />
  <center><span class="invoice_text">Page 1 / 1 </span></center><br />
  <br />
</p>
</body>
</html>