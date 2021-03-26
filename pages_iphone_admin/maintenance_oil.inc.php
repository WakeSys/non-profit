--TITLE--Oil &amp; Filter --
--HOME--
--BACK_PAGE--maintenance--
--BACK_TITLE--Maint.--
&nbsp;
<?php
echo "<form method=\"POST\" id=\"fuelingForm\" action=\"javascript: LoadPage('maintenance_form',document.getElementById('fuelingForm'));\">";
	echo "<input type=\"hidden\" name=\"driverID\" value=\"" . $_POST["driverID"]. "\">";
	echo "<input type=\"hidden\" name=\"boatID\" value=\"" . $_POST["boatID"]. "\">";
	echo "<input type=\"hidden\" name=\"oil\" value=\"yes\">";
	echo "<h2>Selected:</h2>";
	echo "<ul>";
		echo "<li>Driver:<span class=\" secondaryWArrow\"><label>" . getDriverName($_POST["driverID"]) . "</label></span></li>";
		echo "<li>Boat:<span class=\" secondaryWArrow\"><label>" . getBoatName($_POST["boatID"]) . "</label></span></li>";
	echo "</ul>";
?>

	<ul>    
		<?php
			if (isset($_POST["filter_changed"]) && isset($_POST["oil_changed"])){
				if ($_POST["filter_changed"] != "on" && $_POST["oil_changed"] != "on"){
					$checkboxError = "true";
				}
			}
		
		echo "<li";
		if (isset($checkboxError) && $checkboxError == "true"){ echo " class=\"listop\"";}
		echo ">Filter Changed<span class=\"showArrow secondaryWArrow\"><input type=\"checkbox\" name=\"filter_changed\"";
		if (isset($_POST["filter_changed"]) && $_POST["filter_changed"] == "on"){ echo " checked";}
		echo "></span></li>";
        echo "<li";
        if (isset($checkboxError) && $checkboxError == "true"){ echo " class=\"listop\"";}
        echo ">Oil Changed<span class=\"showArrow secondaryWArrow\"><input type=\"checkbox\" name=\"oil_changed\"";
        if (isset($_POST["oil_changed"]) && $_POST["oil_changed"] == "on"){ echo " checked";}
        echo "></span></li>";
        
	         echo "<li ";
			if (isset($_POST["engine_time"]) && ($_POST["engine_time"] == "" || $_POST["engine_time"] == "enter engine time") ){
					echo " class=\"listop\"";	
			}
			echo ">EngineTime<span class=\"showArrow secondaryWArrow\"><label><input class=\"secondaryInput\" name=\"engine_time\" type=\"text\" size=\"12\" maxlength=\"30\"";
			if (isset($_POST["engine_time"]) && $_POST["engine_time"] != ""){
				echo "value=\"" . $_POST["engine_time"] . "\"";
			}
			else {
				echo "value=\"enter engine time\"";
			}
			?>
			onfocus="this.value='';"
			/></label></span></li>
		</ul>
	    
	<div class="start">	
		<li><a href="javascript: LoadPage('maintenance_form',document.getElementById('fuelingForm'));">Save<span class="showArrow secondaryArrow">.</span></a></li>
	</div>
	
</form>