<?php
//echo "<pre>";print_r($_POST);echo "</pre>";
if ( (isset($_POST["fuel"]) && $_POST["fuel"] == "yes") 
	&& 
	(
		($_POST["liters"] == "" || $_POST["liters"] == "enter liters")
		||
		($_POST["engine_time"] == "" || $_POST["engine_time"]=="enter engine time")
		||
		($_POST["price"] == "" || $_POST["price"] == "enter price" )
	)
		
	){
	include("../pages_iphone_admin/maintenance_fueling.inc.php");
}
elseif ( (isset($_POST["oil"]) && $_POST["oil"] == "yes") 
	&& 
	(
		($_POST["engine_time"] == "" || $_POST["engine_time"]=="enter engine time")
		//todo checkbox testen!
		||
		($_POST["oil_changed"] != "on" && $_POST["filter_changed"]!="on")
	)
		
	){
	include("../pages_iphone_admin/maintenance_oil.inc.php");
}
else {
	if ( isset($_POST["fuel"]) && $_POST["fuel"] == "yes") {
		$sql = "INSERT INTO maintenance (loginID,fuel_liters,price,boatID,driverID,engineTime,ts) VALUES ('" . $_SESSION["user"]["id"] . "', '" . $_POST["liters"]. "', '" . $_POST["price"]. "', '" . $_POST["boatID"]. "', '" . $_POST["driverID"]. "', '" . $_POST["engine_time"]. "', '" . date("Y-m-d H:i:s") . "')";
		$db->execute($sql);
	}
	elseif (isset($_POST["oil"]) && $_POST["oil"] == "yes") {
		if ($_POST["oil_changed"] == "on"){$_POST["oil_changed"] = 1;}
		else {$_POST["oil_changed"] = 2;}
		if ($_POST["filter_changed"] == "on"){$filter_changed = 1;}
		else {$filter_changed = 2;}
		$sql = "INSERT INTO maintenance (loginID,oil,filter,boatID,driverID,engineTime,ts) VALUES ('" . $_SESSION["user"]["id"] . "', '" . $_POST["oil_changed"]. "', '" . $filter_changed . "', '" . $_POST["boatID"]. "', '" . $_POST["driverID"]. "', '" . $_POST["engine_time"]. "', '" . date("Y-m-d H:i:s") . "')";
		$db->execute($sql);
	}
	
	
	if (isset($_POST["driverID"]) && isset($_POST["boatID"]) && isset($_POST["sub"])){
		if (isset($_POST["sub"])){
			if ($_POST["sub"] == "fueling"){
				include("../pages_iphone_admin/maintenance_fueling.inc.php");
			}
			elseif ($_POST["sub"] == "oil"){
				include("../pages_iphone_admin/maintenance_oil.inc.php");
			}
		}
	
	}
	else {
		echo "--TITLE--WakeSys--";
		echo "--HOME--";
		echo "&nbsp;";
		
		if (!isset($_POST["driverID"]) && isset($_POST["sub"])) {
			
			echo "--BACK_PAGE--Maintenance--";
			echo "--BACK_TITLE--Maint.--";
			$drivers = getAllDrivers();
			echo "<ul>";
				if (is_array($drivers)){
					foreach ($drivers as $driver){
						echo "<li><a href = \"javascript: LoadPage('maintenance','driverID=" . $driver["ID"] . "&sub=" . $_POST["sub"] . "');\">" . $driver["first_name"] . " " . $driver["last_name"] . "</a></li>";
					}
				}
				else {
					echo "<li>no drivers available</li>";			
				}
			echo "</ul>";
		}
		elseif (isset($_POST["sub"])) {
				
			echo "--BACK_PAGE--maintenance--";
			echo "--BACK_TITLE--Maint.--";
			$boats = getAllBoats();
			echo "<ul>";
				if (is_array($boats)){
					foreach ($boats as $boat){
						echo "<li><a href = \"javascript: LoadPage('maintenance','driverID=" . $_POST["driverID"] . "&boatID=" . $boat["ID"] . "&sub=" . $_POST["sub"] . "');\">" . $boat["name"] . "</a></li>";
					}
				}
				else {
					echo "<li>no boats available</li>";			
				}
			echo "</ul>";
		}
		else {
			
			echo "--BACK_PAGE--home--";
			echo "--BACK_TITLE--home--";
			echo "<ul>";
			echo "<li><a href=\"javascript: LoadPage('maintenance','sub=fueling');\">Fueling<span class=\"showArrow secondaryArrow\">.</span></a></li>";
			echo "<li><a href=\"javascript: LoadPage('maintenance','sub=oil');\">Oil &amp; Filter Change<span class=\"showArrow secondaryArrow\">.</span></a></li>";
			echo "</ul>";
		}
	}
}
	
?>