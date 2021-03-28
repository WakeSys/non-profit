--TITLE--Invoice--
--BACK_PAGE--home--
--BACK_TITLE--home--
--HOME--
<?php
function getOpenInvoiceValue($type,$memberID=null){
	global $db;
	if ($type == "nonMember_today"){
		$sql = "SELECT sum(value) FROM credits WHERE rideID IN (SELECT rideID FROM rideTimes WHERE start BETWEEN '" . mktime(0,0,0,date("m"),date("d"),date("Y")) . "' AND '" . mktime(23,59,59,date("m"),date("d"),date("Y")) ."') AND (memberID IS NULL OR memberID = 0) AND invoiceID IS NULL";
	}
	elseif ($type == "nonMember_all"){
		$sql = "SELECT sum(value) FROM credits WHERE memberID IS NULL AND invoiceID IS NULL";	
	}
	elseif ($type == "camp_all"){
		$sql = "SELECT sum(value) FROM credits WHERE (invoiceID IS NULL OR invoiceID = 0) AND memberID IN (SELECT ID from members WHERE campRider = 'yes')";	
	}
	//echo $sql;
	$value = $db->querySingleItem($sql);
	if ($value <= 0 || !is_numeric($value)){
		$value = 0;
	}
	return $value;
}

//echo "<pre>";print_r($_POST);echo "</pre>";
?>
<h2>Guests</h2>
<ul>
	<?php
	echo "<li>";
		$invoiceValue = getOpenInvoiceValue("nonMember_today",null);
		if ($invoiceValue > 0){
			echo "<a href=\"javascript: LoadPage('invoice_detail','nonMember=today');\">";
				echo "Today <span class=\"showArrow secondaryWArrow\" style=\"color:red;\">" . $invoiceValue . " " . getPreferences("currencyHTML") . "";
			echo "</span></a>";
		}
		else {
			echo "no invoices today";
		}
	echo "</li>";
	echo "<li>";
		$invoiceValue = getOpenInvoiceValue("nonMember_all",null);
		if ($invoiceValue > 0){
			echo "<a href=\"javascript: LoadPage('invoice_detail','nonMember=all');\">";
				echo "All time <span class=\"showArrow secondaryWArrow\" style=\"color:red;\">" . $invoiceValue . " " . getPreferences("currencyHTML") . "";
			echo "</span></a>";
		}
		else {
			echo "no open invoices!";
		}
	echo "</li>";
	?>
</ul>
<h2>Postpaid Customers</h2>
<ul>
	<?php
	echo "<li>";
		if (getOpenInvoiceValue("camp_all",null) > 0){
			echo "<a href=\"javascript: LoadPage('invoice_detail','camp=all');\">";
				echo "All time <span class=\"showArrow secondaryWArrow\" style=\"color:red;\">" . getOpenInvoiceValue("camp_all",null) . " " . getPreferences("currencyHTML") . "";
			echo "</span></a>";
		}
		else {
			echo "no open invoices!";
		}
	echo "</li>";
	?>
</ul>