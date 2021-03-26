--TITLE--Fueling--
--HOME--
--BACK_PAGE--maintenance--
--BACK_TITLE--Maint.--
&nbsp;

<?php


echo "<form method=\"POST\" id=\"fuelingForm\" action=\"javascript: LoadPage('maintenance_form',document.getElementById('fuelingForm'));\">";
	echo "<input type=\"hidden\" name=\"driverID\" value=\"" . $_POST["driverID"]. "\">";
	echo "<input type=\"hidden\" name=\"boatID\" value=\"" . $_POST["boatID"]. "\">";
	echo "<input type=\"hidden\" name=\"fuel\" value=\"yes\">";
	echo "<h2>Selected:</h2>";
	echo "<ul>";
		echo "<li>Driver:<span class=\" secondaryWArrow\"><label>" . getDriverName($_POST["driverID"]) . "</label></span></li>";
		echo "<li>Boat:<span class=\" secondaryWArrow\"><label>" . getBoatName($_POST["boatID"]) . "</label></span></li>";
	echo "</ul>";
	?>

	<ul>    
		<li 
		<?
		if (isset($_POST["liters"]) && ($_POST["liters"] == "" || $_POST["liters"] == "enter liters") ){
				echo " class=\"listop\"";	
		}
		echo ">Total " . ucfirst($preferences[0]["fuelingtype"]) . "<span class=\"showArrow secondaryWArrow\"><label><input class=\"secondaryInput\" name=\"liters\" type=\"text\" size=\"12\" maxlength=\"30\"";
		if (isset($_POST["liters"]) && $_POST["liters"] != ""){
			echo "value=\"" . $_POST["liters"] . "\"";
		}
		else {
			echo "value=\"Enter " . ucfirst($preferences[0]["fuelingtype"]) . "\"";
		}
		?>
		 onfocus="this.value='';"
		/></label></span></li>
        <li 
		<?
		if (isset($_POST["price"]) && ($_POST["price"] == "" || $_POST["price"] == "enter price") ){
				echo " class=\"listop\"";	
		}
		echo ">Total Price<span class=\"showArrow secondaryWArrow\"><label><input class=\"secondaryInput\" name=\"price\" type=\"text\" size=\"12\" maxlength=\"30\"";
		if (isset($_POST["price"]) && $_POST["price"] != ""){
			echo "value=\"" . $_POST["price"] . "\"";
		}
		else {
			echo "value=\"Enter Price\"";
		}
		?>
		onfocus="this.value='';"
		/></label></span></li>
        <li 
		<?
		if (isset($_POST["engine_time"]) && ($_POST["engine_time"] == "" || $_POST["engine_time"] == "enter engine time") ){
				echo " class=\"listop\"";	
		}
		echo ">Engine Time<span class=\"showArrow secondaryWArrow\"><label><input class=\"secondaryInput\" name=\"engine_time\" type=\"text\" size=\"12\" maxlength=\"30\"";
		if (isset($_POST["engine_time"]) && $_POST["engine_time"] != ""){
			echo "value=\"" . $_POST["engine_time"] . "\"";
		}
		else {
			echo "value=\"Enter Engine Time\"";
		}
		?>
		onfocus="this.value='';"
		/></label></span></li>
	</ul>
	    
	<div class="start">	
		<li><a href="javascript: LoadPage('maintenance_form',document.getElementById('fuelingForm'));">Save<span class="showArrow secondaryArrow">.</span></a></li>
	</div>	
</form>
