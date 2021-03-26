&nbsp;
<?php
//echo "<pre>";print_r($_POST);echo "</pre>";
if (!isset($_POST["camp"])){
	echo "<ul>";
	$sql = "SELECT * FROM members WHERE campRider = 'no' ORDER BY first_name, last_name";
	$members = $db->queryArray($sql);
	if (is_array($members)){
		foreach ($members as $member){
			echo "<li><a href=\"javascript: LoadPage('member_show','" . $member["ID"] . "');\" ";
			$membershipend = getMembershipEnd($member["ID"]);
			if ($membershipend < time()) {
				echo " style=\"color:red;\"";
			}
			elseif ($membershipend < mktime(0, 0, 0, date("m")+3, date("d"),   date("Y")) ){
				echo " style=\"color:orange;\"";
			}
			echo ">" . $member["first_name"] . " " . $member["last_name"];
			
			$credits = getCurrentCredit($member["ID"]);
			if(!isset($credits) || $credits == null){
				$credits = 0;
			}
			echo "<span class=\"showArrow secondaryWArrow\"";
			if ($credits < 0) {
				echo " style=\"color:red;\"";
			}
			echo ">" . $credits . " " . getPreferences("currencyHTML") . "";
			echo "</span></a></li>";
		}
	}
	else {
		echo "<li>no Member available</li>";
	}
	?>
	</ul>	
	--TITLE--Members--
	--BACK_TITLE--Back--
	--BACK_PAGE--member_management--
	--PLUS_LINK--edit_memberID--
<?php
}
else {
	echo "<ul>";
	
	if (isset($_POST["status"]) && $_POST["status"] == "inactive"){
	if (isset($_POST["memberID"]) && is_numeric($_POST["memberID"]) ){
		$sql = "UPDATE members SET campRider = 'yes' WHERE ID = '" . $_POST["memberID"] . "'";
		$db->execute($sql);
	}
		$sql = "SELECT * FROM members WHERE campRider = 'inactive' ORDER BY first_name, last_name";
		$members = $db->queryArray($sql);
		if (is_array($members)){
			foreach ($members as $member){
				echo "<li><a href=\"javascript: LoadPage('campRider_activate','" . $member["ID"] . "');\" ";
				echo ">" . $member["first_name"] . " " . $member["last_name"];
				
				$credits = getCurrentCredit($member["ID"]);
				if(!isset($credits) || $credits == null){
					$credits = 0;
				}
				echo "<span class=\"showArrow secondaryWArrow\"";
				if ($credits < 0) {
					echo " style=\"color:red;\"";
				}
				echo ">" . $credits . " " . getPreferences("currencyHTML") . "";
				echo "</span></a></li>";
			}
		}
		else {
			echo "<li>no Camp Rider inactive</li>";
		}
		?>
		</ul>	
		--TITLE--Camp Riders--
		--BACK_TITLE--Back--
		--BACK_PAGE--member_management--
		--HOME--
		<?php
	}
	else {
		$sql = "SELECT * FROM members WHERE campRider = 'yes' ORDER BY first_name, last_name";
		$members = $db->queryArray($sql);
		if (is_array($members)){
			foreach ($members as $member){
				echo "<li><a href=\"javascript: LoadPage('member_show','" . $member["ID"] . "');\" ";
				echo ">" . $member["first_name"] . " " . $member["last_name"];
				
				$credits = getCurrentCredit($member["ID"]);
				if(!isset($credits) || $credits == null){
					$credits = 0;
				}
				echo "<span class=\"showArrow secondaryWArrow\"";
				
				if ($credits < 0) {
					echo " style=\"color:red;\"";
				}
				echo ">" . $credits . " " . getPreferences("currencyHTML") . "";
				echo "</span></a></li>";
			}
		}
		else {
			echo "<li>no Camp Rider available</li>";
		}
		?>
		</ul>	
		--TITLE--Camp Riders--
		--BACK_TITLE--Back--
		--BACK_PAGE--member_management--
		--PLUS_LINK--edit_campRiderID--
		<?php
	}
	
}
?>