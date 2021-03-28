&nbsp;
<?php 
unset($_SESSION["ride"]);


?>
<ul>
<li>
<?php 
	
	$boats = getAvailableBoats($Member_Lockdata[0]["loginID"]);
	
	$drivers = getAvailableDrivers($Member_Lockdata[0]["loginID"]);
	
	if (isset($Member_Lockdata[0]["rideID"]) && $Member_Lockdata[0]["rideID"] > 0){
		echo "<a href=\"javascript:LoadPage('riderunning','');\">running ride!<span class=\"showArrow secondaryArrow\">.</span></a>";
	}
	else if (!is_array($boats)){
		echo "ERROR: no Boats available!!";
	}
	else if (!is_array($drivers)){
		echo "ERROR: no Drivers available!!";
	}
	else if (count($boats) == 1 && count($drivers) == 1){
		$_SESSION["ride"]["boatID"] = $boats[0]["ID"];
		$_SESSION["ride"]["driverID"] = $drivers[0]["ID"];
		echo "<a href=\"javascript: LoadPage('ride','nodata=no');\">";
		echo "Ride<span class=\"showArrow secondaryArrow\">.</span></a>";
	}
	else if (count($boats) == 1){
		$_SESSION["ride"]["boatID"] = $boats[0]["ID"];
		echo "<a href=\"javascript: LoadPage('ride','nodata=no');\">";
		echo "Ride<span class=\"showArrow secondaryArrow\">.</span></a>";
	}
	else if (count($drivers) == 1){
		$_SESSION["ride"]["driverID"] = $drivers[0]["ID"];
		echo "<a href=\"javascript: LoadPage('ride','nodata=no');\">";
		echo "Ride<span class=\"showArrow secondaryArrow\">.</span></a>";
	}
	else {
		echo "<a href=\"javascript: LoadPage('ride','nodata=no');\">";
		echo "Ride<span class=\"showArrow secondaryArrow\">.</span></a>";
	}
?>

</li>
</ul>

<ul>
<li><a href="javascript: LoadPage('maintenance','');">Maintenance<span class="showArrow secondaryArrow">.</span></a></li>
</ul>

<ul>
<li><a href="javascript: LoadPage('member_management','');">Customer Management<span class="showArrow secondaryArrow">.</span></a></li>
<li><a href="javascript: LoadPage('invoice_management','');">Invoice Management<span class="showArrow secondaryArrow">.</span></a></li>
</ul>
<ul>
	<li><a href="javascript: LoadPage('logout','');">Logout<span class="showArrow secondaryArrow">.</span></a></li>

</ul>
--TITLE--<img src="iphone_admin/images/wakesys_logo.png" width="140" height="25" alt="WakeSys" />--