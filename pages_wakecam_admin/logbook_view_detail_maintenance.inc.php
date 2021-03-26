<?php
if (isset($_GET["view"]) && is_numeric($_GET["view"])) {
	$sql = "SELECT A.*,UNIX_TIMESTAMP(A.ts) as ts, B.name AS boatName, CONCAT(C.first_name , ' ', C.last_name) AS driverName FROM maintenance AS A LEFT JOIN boats AS B ON A.boatID = B.ID LEFT JOIN members AS C ON A.driverID = C.ID WHERE A.ID = '" . $_GET["view"] . "'";
	$maintenanceData = $db->queryArray($sql);
	//echo "<pre>";print_r($maintenanceData);echo "</pre>";
	$type = "view";
} elseif (isset($_GET["add"]) && $_GET["add"] == "true") {
	$type = "add";
} elseif (isset($_GET["edit"]) && is_numeric($_GET["edit"])) {
	$sql = "SELECT A.*,UNIX_TIMESTAMP(A.ts) as ts, B.name AS boatName, CONCAT(C.first_name , ' ', C.last_name) AS driverName FROM maintenance AS A LEFT JOIN boats AS B ON A.boatID = B.ID LEFT JOIN members AS C ON A.driverID = C.ID WHERE A.ID = '" . $_GET["edit"] . "'";
	$maintenanceData = $db->queryArray($sql);
	$type = "edit";
}

if ($type != "view") {
?>
	<script type="text/javascript">
		$(function() {
			$("#datepicker").attr( 'readOnly' , 'true' );
			<?php
				if ($type == "edit"){
					echo "$('#datepicker').datepicker({ dateFormat: 'dd.mm.yy',defaultDate: '" . date("Y-m-d",$maintenanceData[0]["ts"]) . "' });";
				}
				elseif (isset($_POST["ts"])){
					echo "$('#datepicker').datepicker({ dateFormat: 'dd.mm.yy',defaultDate: '" . date("Y-m-d", $_POST["ts"]) . "' });";
				}
				else {
					echo "$('#datepicker').datepicker({ dateFormat: 'dd.mm.yy',defaultDate: '" . date("Y-m-d") . "' });";
				}
			?>
			
		});
	</script>
<?php
}

if (isset($_POST["sent_form"]) && $_POST["sent_form"] == "yes") {
	$_POST["fuel_liters"] = str_replace(",",".",$_POST["fuel_liters"]);
	if (!isset($_POST["fuel_liters"]) || !is_numeric($_POST["fuel_liters"]) || $_POST["fuel_liters"] <= 0){
		$error["fuel_liters"] = true;
	}
	
	$_POST["engineTime"] = str_replace(",",".",$_POST["engineTime"]);
	if (!isset($_POST["engineTime"]) || !is_numeric($_POST["engineTime"]) || $_POST["engineTime"] <= 0){
		$error["engineTime"] = true;
	}

	$_POST["price"] = str_replace(",",".",$_POST["price"]);
	if (!isset($_POST["price"]) || !is_numeric($_POST["price"]) || $_POST["price"] <= 0){
		$error["price"] = true;
	}

	if (!isset($_POST["ts_hours"]) || !is_numeric($_POST["ts_hours"]) || $_POST["ts_hours"] <= 0 || $_POST["ts_hours"] > 23){
		$error["ts_hours"] = true;
	}

	if (!isset($_POST["ts_minutes"]) || !is_numeric($_POST["ts_minutes"]) || $_POST["ts_minutes"] < 0 || $_POST["ts_minutes"] > 59){
		$error["ts_minutes"] = true;
	}

	if (!isset($_POST["ts"]) || !preg_match("/^[0-3]?[0-9]\.[01]?[0-9]\.2[01][0-9]{2}$/i", $_POST["ts"]) ){
		$error["price"] = true;
	}


	if (!isset($error)) {
		preg_match("/^([0-3]?[0-9])\.([01]?[0-9])\.(2[01][0-9]{2})$/i", $_POST["ts"], $match);
		$timestamp = mktime($_POST["ts_hours"],$_POST["ts_minutes"],0, $match[2], $match[1], $match[3]);
		if ($type == "add"){
			$sql = "INSERT INTO maintenance (";
			$i = 0;
			foreach ($_POST as $key => $elem) {
				if ($key != "sent_form" && $key != "ts" && $key != "ts_minutes" && $key != "ts_hours") {
					if ($i != 0) {
						$sql .= ", ";
					}
					$sql .= $key;
					$i++;
				}
			}

			$sql .= ", ts) VALUES (";
			$i = 0;
			foreach ($_POST as $key => $elem) {
				if ($key != "sent_form" && $key != "ts" && $key != "ts_minutes" && $key != "ts_hours") {
					if ($i != 0) {
						$sql .= ", ";
					}
					$sql .= "'" . $db->escape($elem) . "'";
				$i++;
				}
				
			}
			$sql .= ", '" . date("Y-m-d H:i:s",$timestamp) . "')";
		}
		

		if ($type == "edit"){
			$sql = "UPDATE maintenance SET ";
			$i = 0;
			foreach ($_POST as $key => $elem) {
				if ($key != "sent_form" && $key != "ts" && $key != "ts_minutes" && $key != "ts_hours") {
					if ($i != 0) {
						$sql .= ", ";
					}
					$sql .= $key . " = '" . $db->escape($elem) . "'";
					$i++;
				}
			}
			$sql .= ", ts = '" . date("Y-m-d H:i:s",$timestamp) . "' WHERE ID = '" . $_GET["edit"] . "'";
		}
		$db->execute($sql);
		$editID = $db->insertID();
		$type = "edit";
		$redirect = "true";
	}
	elseif (!is_array($error) && $type == "edit") {

	}
}

if (isset($redirect) && $redirect == "true"){
	include ('logbook_view.inc.php');
}
else {
?>
<div class="main">
	<div class="main_text">

		<?php
		if (isset($type) && $type == "view"){
			echo "<div class=\"main_text_title\">View Fueling to Logbook</div>";
		}
		if (isset($type) && $type == "edit"){
			echo "<div class=\"main_text_title\">Edit Fueling to Logbook</div>";
		}
		else {
			echo "<div class=\"main_text_title\">Add Fueling to Logbook</div>";
		}

		$boats = getAvailableBoats();
		if ($type != "view" && !is_array($boats)) {
			echo "no Boats available";
		} else {
		echo "<form action=\"\" method=\"POST\">";

		?>
	        <form action="" method="POST">
				<input type="hidden" name="sent_form" value="yes">
	            <table width="600" border="0" cellspacing="0" cellpadding="0" class="text_invoice_boxes_white">
					<tr>
						<td width="195">Date</td>
					<?php
					if ($type == "view") {
						echo "<td>" . date("d\.m\.Y", $maintenanceData[0]["ts"]) . "</td>";
					} 
					elseif ($type == "add") {
						echo "<td>";
						echo "<input type=\"text\" name=\"ts\" id=\"datepicker\" ";
						if (isset($_POST["ts"])) {
							echo " value=\"" . $_POST["ts"] . "\"";
						}
						else {
							echo " value=\"" . date("d\.m\.Y") . "\"";
						}
						echo ">";
						echo "</td>";
					}
					else {
						echo "<td>";
						echo "<input type=\"text\" name=\"ts\" id=\"datepicker\" ";
						echo " value=\"" . date("d\.m\.Y", $maintenanceData[0]["ts"]) . "\"";
						echo ">";
						echo "</td>";
					}
					?>
				</tr>
				<?php
					if ($type != "view") {
						echo "<tr>";
							echo "<td width=\"195\">Time</td>";
							echo "<td>";
								echo "<select name=\"ts_hours\">";
									for ($i=0;$i <= 23;$i++){
										echo "<option value=\"" . $i . "\"";
											if ($type == "edit" && $i == date("H", $maintenanceData[0]["ts"])){
												echo " selected";
											}
											elseif (!isset($_POST["ts_hours"])){
												if (date("H") == $i){
													echo " selected";
												}
											}
											elseif ($i == $_POST["ts_hours"]){
												echo " selected";
											}
										echo ">" . $i . "</option>";
									}
								echo "</select>hours, ";
								echo "<select name=\"ts_minutes\">";
									for ($i=0;$i <= 59;$i++){
										echo "<option value=\"" . $i . "\"";
											if ($type == "edit" && $i == date("i", $maintenanceData[0]["ts"])){
												echo " selected";
											}
											elseif ($type != "edit" && !isset($_POST["ts_minutes"])){
												if (date("i") == $i){
													echo " selected";
												}
											}
											elseif ($type != "edit" && $i == $_POST["ts_minutes"]){
												echo " selected";
											}
										echo ">" . $i . "</option>";
									}
								echo "</select>minutes, ";
							echo "</td>";
						echo "</tr>";
					}
					?>

				<tr>
					<td>Boat</td>
					<?php
					if (isset($error)){
						echo "error:<pre>";
						print_r($error);
						echo "</pre>";
					}
					
					if ($type == "view") {
						echo "<td>" . $maintenanceData[0]["boatName"] . "</td>";
					} else {
						if (count($boats) == 1) {
							echo "<td>" . $boats[0]["name"] . "</td>";
							echo "<input type=\"hidden\" name=\"boatID\" value=\"" . $boats[0]["ID"] . "\"";
						} else {
							echo "<td>";
							echo "<select name=\"boatID\">";
							foreach ($boats as $boat) {

								echo "<option value=\"" . $boat["ID"] . "\"";
								if ($type == "edit" && $boat["ID"] == $maintenanceData[0]["boatID"]){
									echo " selected";
								}
								elseif ($type != "edit" && isset($_POST["boatID"]) && $_POST["boatID"] == $boat["ID"]) {
									echo " selected";
								}
								echo ">" . $boat["name"] . "</option>";
							}

							echo "</select>";
							echo "</td>";
						}
					}
					?>
				</tr>
				<tr>
					<td>Name of Driver</td>
					<?php
					if ($type == "view") {
						echo "<td>" . $maintenanceData[0]["driverName"] . "</td>";
					} else {
						$drivers = getAllDrivers();
						if (is_array($drivers) && count($drivers) > 1) {
							echo "<td><select name=\"driverID\">";
							foreach ($drivers as $driver) {
								echo "<option value=\"" . $driver["ID"] . "\"";
								if ($type == "edit" && $driver["ID"] == $maintenanceData[0]["driverID"]){
									echo " selected";
								}
								elseif (isset($_POST["driverID"]) && $_POST["driverID"] == $driver["ID"]) {
									echo " selected";
								}
								echo ">" . $driver["first_name"] . " " . $driver["last_name"] . "</option>";
							}
							echo "</select></td>";
						} elseif ($count($drivers) == 1) {
							echo "<td>" . $drivers[0]["first_name"] . " " . $drivers[0]["last_name"] . "</td>";
							echo "<input type=\"hidden\" name=\"driverID\" value=\"" . $drivers[0]["ID"] . "\">";
						} else {
							echo "<td>no drivers available</td>";
						}
					}
					?>
				</tr>
				<tr>
					<td>Engine  Time</td>
					<?php
					if ($type == "view") {
						echo "<td>" . $maintenanceData[0]["engineTime"] . "</td>";
					} else {
						echo "<td><input type=\"text\" name=\"engineTime\"";
						if (isset($error) && isset($error["engineTime"])){
							echo " style=\"background-color:red;\"";
						}
						if ($type == "edit"){
							echo " value=\"" . $maintenanceData[0]["engineTime"] . "\"";
						}
						elseif (isset($_POST["engineTime"])) {
							echo " value=\"" . $_POST["engineTime"] . "\"";
						}
						echo "></td>";
					}
					?>
				</tr>
				<tr>
					<td><?php echo ucfirst($preferences[0]["fuelingtype"]); ?></td>
					<?php
					if ($type == "view") {
						echo "<td>" . number_format($maintenanceData[0]["fuel_liters"], 2, ".", "") . "</td>";
					} else {
						echo "<td><input type=\"text\" name=\"fuel_liters\"";
						if (isset($error) && isset($error["fuel_liters"])){
							echo " style=\"background-color:red;\"";
						}

						if ($type == "edit"){
							echo " value=\"" . $maintenanceData[0]["fuel_liters"] . "\"";
						}
						elseif (isset($_POST["fuel_liters"])) {
							echo " value=\"" . $_POST["fuel_liters"] . "\"";
						}
						echo "></td>";
					}
					?>
				</tr>
				<tr>
					<td>Price</td>
					<?php
					if ($type == "view") {
						echo "<td>" . number_format($maintenanceData[0]["price"], 2, ".", "") . " " . getPreferences("currencyHTML") . "</td>";
					} else {
						echo "<td><input type=\"text\" name=\"price\"";
						if (isset($error) && isset($error["price"])){
							echo " style=\"background-color:red;\"";
						}

						if ($type == "edit"){
							echo " value=\"" . $maintenanceData[0]["price"] . "\"";
						}
						elseif (isset($_POST["price"])) {
							echo " value=\"" . $_POST["price"] . "\"";
						}
						echo "></td>";
					}
					?>
				</tr>
				<tr>
					<?php
					if ($type != "view") {
						echo "<td><input type=\"submit\" value=\"send\"></td><td>&nbsp;</td>";
					} else {
						echo "<td>&nbsp;</td><td>&nbsp;</td>";
					}
					?>

				</tr>
				<?php
				if ($type == "view"){
					echo "<tr><td>&nbsp;</td></tr>";
					echo "<tr><td><a href=\"" . INDEX . "?p=logbook&sub=view_detail_maintenance&edit=" . $_GET["view"] . "\">Edit this fueling</a></td></tr>";
				}
				?>
			</table>
        </form>
		<?php
				}
		?>
			</div>
		</div>
		<div class="bottom"></div>
		</center>
		</body>


<?php

				function inputField($name, $value=null) {
					global $error;
					if ($value == "POST") {
						global $_POST;
						if (isset($_POST[$name])) {
							$value = $_POST[$name];
						} else {
							$value = "";
						}
					}
					echo "<input type=\"text\" name=\"" . $name . "\"";
					if (is_array($error) && in_array($name, $error)) {
						echo " style=\"background-color:red;\"";
					}
					echo " value=\"" . $value . "\">";
				}
}
?>