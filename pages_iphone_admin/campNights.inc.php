--TITLE--CampNights--
--BACK_PAGE--invoice_detail--
--BACK_TITLE--back--
--BACK_OPTIONS--memberID=<?php echo $_POST["memberID"];?>&invoice_detail=<?php echo $_POST["invoice_detail"];?>--
<?php
//echo "<pre>";print_r($_POST);echo "</pre>";
//echo "<pre>";print_r($_GET);echo "</pre>";
?>
<form id="campForm" method="POST" action="javascript: LoadPage('invoice_detail_campForm',document.getElementById('campForm'));">
	<input type="hidden" name="invoice_detail" value="<?php echo $_POST["invoice_detail"];?>">
	<input type="hidden" name="memberID" value="<?php echo $_POST["memberID"];?>">
	<?php
	if (isset($_POST["creditID"]) && is_numeric($_POST["creditID"])){
		echo "<h2>Edit Nights</h2>";
		echo "<input type=\"hidden\" name=\"creditID\" value=\"" . $_POST["creditID"] . "\">";
		echo "<ul>";
			$sql = "SELECT * FROM credits WHERE ID = '" . $_POST["creditID"] . "'";
			$campData = $db->queryArray($sql);
			echo "<li>Total nights<span class=\"showArrow secondaryWArrow\"><label><input class=\"secondaryInput\" name=\"nights\" type=\"text\" size=\"12\" maxlength=\"4\" id=\"nights\" onchange=\"is_numeric(this.value,'nights');\" value=\"" . $campData[0]["campNights"] . "\"></label></span></li>";
			echo "<li>Price / night<span class=\"showArrow secondaryWArrow\"><label><input class=\"secondaryInput\" name=\"nightprice\" type=\"text\" size=\"12\" maxlength=\"10\" id=\"nightprice\" onchange=\"is_numeric(this.value,'nightprice');\" value=\"" . ($campData[0]["value"]/$campData[0]["campNights"]) . "\" /></label></span></li>";
		echo "</ul>";
		
		
	}
	else {
		echo "<h2>Add Nights</h2>";
		echo "<ul>";
			echo "<li>Total nights<span class=\"showArrow secondaryWArrow\"><label><input class=\"secondaryInput\" name=\"nights\" type=\"text\" size=\"12\" maxlength=\"4\" id=\"nights\" onchange=\"is_numeric(this.value,'nights');\"></label></span></li>";
			echo "<li>Price / night<span class=\"showArrow secondaryWArrow\"><label><input class=\"secondaryInput\" name=\"nightprice\" type=\"text\" size=\"12\" maxlength=\"10\" id=\"nightprice\" onchange=\"is_numeric(this.value,'nightprice');\"/></label></span></li>";
		echo "</ul>";
	}
	?>
	
	
	
	<ul>
		<li><a href="javascript:document.getElementById('campForm').submit();">Save Nights</a></li>
	</ul>
</form>