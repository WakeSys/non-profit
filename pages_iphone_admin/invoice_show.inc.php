--TITLE--Show Invoices????--
--BACK_PAGE--invoice_management--
--BACK_TITLE--back--
--HOME--
&nbsp;
<?php 
//echo "<pre>";print_r($_POST);echo"</pre>";
if (isset($_POST["data"])){
	echo "<h2>List invoices details...</h2>";
}


if (isset($_POST["type"])){
	if ($_POST["type"] == "credit" || $_POST["type"] == "ride"){
		//todo erweitern um Date
		// todo erweitern um Membership!
		if ($_POST["type"] == "credit"){
			if (isset($_POST["paied"]) && $_POST["paied"] == "no"){
				$sql = "SELECT memberID,value, CONCAT(B.first_name , ' ' , B.last_name) AS name, A.ID FROM credits AS A LEFT JOIN members AS B ON A.memberID = B.ID WHERE paied IS NULL and value > 0";
			}
			elseif (isset($_POST["paied"]) && $_POST["paied"] == "yes"){
				$sql = "SELECT memberID,value, CONCAT(B.first_name , ' ' , B.last_name) AS name, A.ID FROM credits AS A LEFT JOIN members AS B ON A.memberID = B.ID WHERE paied > 0 and value > 0";
			}
			else {
				$sql = "SELECT memberID,value, CONCAT(B.first_name , ' ' , B.last_name) AS name, A.ID FROM credits AS A LEFT JOIN members AS B ON A.memberID = B.ID WHERE value > 0";
			}
			$data = $db->queryArray($sql);
			echo "<ul>";
				if (is_array($data)){
					foreach ($data as $elem){
						echo "<li><a href=\"javascript: LoadPage('invoice_detail','memberID=" . $elem["memberID"];
						foreach($_POST as $key2 => $elem2){
							echo "&" . $key2 . "=" . $elem2;
						}
						echo "');\">" . $elem["name"] .  "<span class=\"showArrow secondaryWArrow\">" . $elem["value"]. " " . getPreferences("currencyHTML") . "</span></a></li>";	
					}	
				}
				else {
					echo "<li>no entry</li>";
				}
			echo "</ul>";
		}
		elseif ($_POST["type"] == "ride"){
			if (isset($_POST["paied"]) && $_POST["paied"] == "no"){
				echo "<h2>Unpaid Rides</h2>";
				$sql = "SELECT value, riderName AS name,A.rideID FROM credits AS A LEFT JOIN rides AS B ON A.rideID = B.ID WHERE paied IS NULL and value < 0 AND memberID =0";
			}
			elseif (isset($_POST["paied"]) && $_POST["paied"] == "yes"){
				echo "<h2>Paid Rides</h2>";
				$sql = "SELECT value, riderName AS name,A.rideID FROM credits AS A LEFT JOIN rides AS B ON A.rideID = B.ID WHERE paied > 0 and value < 0 AND memberID =0";
			}	
			else {
				echo "<h2>All Rides</h2>";
				$sql = "SELECT value, riderName AS name,A.rideID FROM credits AS A LEFT JOIN rides AS B ON A.rideID = B.ID WHERE value < 0 AND memberID =0";
			}	
			
			$data = $db->queryArray($sql);
			echo "<ul>";
				if (is_array($data)){
					foreach ($data as $elem){
						echo "<li><a href=\"javascript: LoadPage('invoice_detail','rideID=" . $elem["rideID"];
						foreach($_POST as $key2 => $elem2){
							echo "&" . $key2 . "=" . $elem2;
						}
						echo "');\">" . $elem["name"] .  "<span class=\"showArrow secondaryWArrow\">" . -$elem["value"]. " " . getPreferences("currencyHTML") . "</span></a></li>";	
					}	
				}
				else {
					echo "<li>no entry</li>";
				}
			echo "</ul>";
		}
		
		
		
	}
	else {
		echo "<ul><li>unknown type!!!</li></ul>";
	}
}
else {
	echo "<ul><li>no type!!!</li></ul>";
}
?>

		