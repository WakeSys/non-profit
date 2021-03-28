<?php
function inputField ($name,$value=null){
	global $error;
	if ($value == "POST"){
		global $_POST;
		if ($name == "birthday" && isset($_POST[$name])){
			preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/",$_POST[$name], $match);
			$value = $match[3] . "." . $match[2] . "." . $match[1];
		}
		elseif (isset($_POST[$name])){
			$value = $_POST[$name];	
		}
		else {
			$value = "";
		}
		
	}
	echo "<input type=\"text\" name=\"" . $name . "\"";
	if (is_array($error) && in_array($name,$error)){
		echo " style=\"background-color:red;\"";
	}
	echo " value=\"" . $value . "\">";
}

?>

<div class="main">
	<div class="main_text">
	<?php
	if (isset($_GET["view"])){
		$sql = "SELECT A.*,UNIX_TIMESTAMP(A.birthday) AS birthday, B.name AS categoryName FROM members AS A LEFT JOIN categories AS B ON A.categoryID = B.ID WHERE A.ID = '" . $_GET["view"] . "'";
		$member = $db->queryArray($sql);
		$type = "view";
		if ($member[0]["campRider"] == 'no'){
			echo "<div class=\"main_text_title\">View Prepaid Customer</div>";	
		}
		else {
			echo "<div class=\"main_text_title\">View Postpaid Customer</div>";
		}
		
	}
	elseif (isset($_GET["edit"])){
		$sql = "SELECT A.*,UNIX_TIMESTAMP(A.birthday) AS birthday,B.name AS categoryName FROM members AS A LEFT JOIN categories AS B ON A.categoryID = B.ID WHERE A.ID = '" . $_GET["edit"] . "'";
		$member = $db->queryArray($sql);
		$type = "edit";
		if ($member[0]["campRider"] == 'no'){
			echo "<div class=\"main_text_title\">Edit Prepaid Customer</div>";	
		}
		else {
			echo "<div class=\"main_text_title\">Edit Postpaid Customer</div>";
		}
	}
	else {
		echo "<div class=\"main_text_title\">Add Customer</div>";
		$type = "add";
	}
	
	
	
	if (isset($_POST["form_sent"]) && $_POST["form_sent"] == "yes"){
		check_input("first_name",1,"name");
		check_input("last_name",1,"name");
		check_input("phone_number",1,"phone");
		if ($type == "add"){
			if (!preg_match("/^[-.,_ a-z0-9]+\@[-.,_ a-z0-9]+\.[a-z]{1,}$/i",$_POST["mail"])){
				$error[] = "mail";
			}
			else {
				$sql = "SELECT mail FROM members WHERE mail = '" . $_POST["mail"] . "'";
				if ($db->querySingleItem($sql) != "-1"){
					echo "<div style=\"color:red;\">ERROR Mail exists!</div>";
					$error[] = "mail";
				}
			}
		}
		else {
			check_input("mail",5,"mail");
		}
		check_input("birthday",null,"birthday");
		check_input("social_security",1,"");
		check_input("postal_code",3,"number");
		check_input("town",1,"name");
		check_input("address",5,"address");
		check_input("country",1,"name");
		check_input("facebookmail",null,"mail");
		if (isset($_GET["edit"]) && is_numeric($_GET["edit"])){
			check_input("password",null,"password");
		}
		else {
			check_input("password",4,"password");	
		}
		//echo "<pre>";print_r($_POST);echo "</pre>";
		if (!is_array($error)){
			if ($type == "edit"){
				$sql = "UPDATE members SET ";
				$i =0;
				foreach ($_POST as $key => $elem){
					if ($key != "form_sent"){
						if ($key == "password" && strlen($elem)<4){
							
						}
						else {
							if ($i != 0){
								$sql .= ", ";
							}
							$sql .= $key . " = '" . $db->escape($elem) . "'";
							$i++;
						}
					}
				}
				$sql .= " WHERE ID = '" . $_GET["edit"] . "'";
				echo $sql;
				$db->execute($sql);
			}
			elseif ($type == "add"){
				$sql = "INSERT INTO members (";
				$i =0;
				foreach ($_POST as $key => $elem){
					if ($key != "form_sent"){
						if ($i != 0){
							$sql .= ", ";
						}
						$sql .= $key;
						$i++;
					}
				}
				$sql .= ") VALUES (";
				$i=0;
				foreach ($_POST as $key => $elem){
					if ($key != "form_sent"){
						if ($i != 0){
							$sql .= ", ";
						}
						if ($key == "password"){
							$sql .= "'" . $db->escape(md5($elem)) . "'";
						}
						else {
							$sql .= "'" . $db->escape($elem) . "'";							
						}
						$i++;
					}
				}
				$sql .= ")";
				//echo $sql;
				$db->execute($sql);
				$memberID = $db->insertId();
				
				first_mail($memberID);
				echo "<div style=\"color:green;\">member / camp rider entered successfully</div>";
				//add 1 Year membership!!!
				if (
					isset($_POST["campRider"]) && 
					$_POST["campRider"] == "no"
				)
				{
					extendMembership($memberID);
				}
				
				$sql = "SELECT A.*,B.name AS categoryName FROM members AS A LEFT JOIN categories AS B ON A.categoryID = B.ID WHERE A.ID = '" . $memberID . "'";
				$member = $db->queryArray($sql);
				$type = "edit";
			}
		}
		else {
			echo "ERROR!!!";
			if (in_array("password",$error)){
				echo "<br><span style=\"color:red;\">Please insert a correct Password!</span><br>";
			}
			//echo "<pre>";print_r($error);echo "</pre>";
		}
	}
	
	?>
	<form action="" method="POST">
	<input type="hidden" name="form_sent" value="yes">
	<table width="600" border="0" cellspacing="0" cellpadding="0" class="text_invoice_boxes_white">
<?php
	if ($type == "add"){
		?>
		 <tr>
			<td width="194">Prepaid or Postpaid Customer?</td>
			<td><select name="campRider" id="select">
				<?php
					echo "<option value = \"no\"";
					if (isset($_POST["campRider"]) && $_POST["campRider"] == "no"){
						echo " selected";
					}
					echo ">Prepaid Customer</option>";
					echo "<option value = \"yes\"";
					if (isset($_POST["campRider"]) && $_POST["campRider"] == "yes"){
						echo " selected";
					}
					echo ">Postpaid Customer</option>";
				?>
			  
			</select> 
			</td>
		  </tr>
		<?php
	}
	?>	 
	  <td>Category</td>
		<?php
		if ($type == "view"){
			echo "<td>" . $member[0]["categoryName"] . "</td>";
		}
		else {
			?>
			<td><select name="categoryID">
			<?php
				$sql = "SELECT * FROM categories WHERE active = 1 ";
				$sql .= "AND (member = 1 OR member = 3)";
				$catArr = $db->queryArray($sql);
				foreach ($catArr as $category){
					echo "<option value=\"" . $category["ID"] . "\"";
					if ($type == "add" && isset($_POST["categoryID"])){
						if ($_POST["categoryID"] == $category["ID"]){
							echo " selected";
						}
					}
					elseif($type == "edit"){
						if (isset($_POST["categoryID"])){
							if ($_POST["categoryID"] == $category["ID"]){
								echo " selected";
							}
						}
						elseif (!isset($_POST["categoryID"]) && $member[0]["categoryID"] == $category["ID"]){
							echo " selected";
						}
					}
					echo ">";
					echo $category["name"];
					echo "</option>";
				}
			?>
			</select></td>
			<?php
		}
		?>
			
	  </tr>
	  <tr>
		<td>First Name</td>
		<?php
		if ($type == "view"){
			echo "<td>" . $member[0]["first_name"] . "</td>";
		}
		elseif($type == "add") {
			echo "<td>";
			inputField("first_name","POST");
			echo "</td>";
		}
		elseif($type == "edit") {
			echo "<td>";
				if (isset($_POST["first_name"])){
					inputField("first_name",$_POST["first_name"]);
				}
				else {
					inputField("first_name",$member[0]["first_name"]);		
				}
			echo "</td>";
		}
		?>
	  </tr>
	  <tr>
		<td>Last Name</td>
		<?php
		if ($type == "view"){
			echo "<td>" . $member[0]["last_name"] . "</td>";
		}
		elseif($type == "add") {
			echo "<td>";
			inputField("last_name","POST");
			echo "</td>";
		}
		elseif($type == "edit") {
			echo "<td>";
				if (isset($_POST["last_name"])){
					inputField("last_name",$_POST["last_name"]);
				}
				else {
					inputField("last_name",$member[0]["last_name"]);		
				}
			echo "</td>";
		}
		?>
	  </tr>
	  <?php
	  if ($type != "view"){
		  echo "<tr>";
			echo "<td>Password</td>";
			echo "<td><input name=\"password\" type=\"password\" size=\"30\" /></td>";
		  echo "</tr>";
	  }
	  ?>
	  <tr>
	  <tr>
		<td>Birthday (dd.mm.yyyy)</td>
		<?php
		if ($type == "view"){
			echo "<td>" . date("d\.m\.Y",$member[0]["birthday"]) . "</td>";
		}
		elseif($type == "add") {
			echo "<td>";
			inputField("birthday","POST");
			echo "</td>";
		}
		elseif($type == "edit") {
			echo "<td>";
				if (isset($_POST["birthday"])){
					inputField("birthday","POST");
				}
				else {
					inputField("birthday",date("d\.m\.Y",$member[0]["birthday"]));		
				}
			echo "</td>";
		}
		?>
	  </tr>
	  <tr>
		<td>Social Security</td>
		<?php
		if ($type == "view"){
			echo "<td>" . $member[0]["social_security"] . "</td>";
		}
		elseif($type == "add") {
			echo "<td>";
			inputField("social_security","POST");
			echo "</td>";
		}
		elseif($type == "edit") {
			echo "<td>";
				if (isset($_POST["social_security"])){
					inputField("social_security",$_POST["social_security"]);
				}
				else {
					inputField("social_security",$member[0]["social_security"]);		
				}
			echo "</td>";
		}
		?>
	  </tr>
	  <tr>
		<td>Address</td>
		<?php
		if ($type == "view"){
			echo "<td>" . $member[0]["address"] . "</td>";
		}
		elseif($type == "add") {
			echo "<td>";
			inputField("address","POST");
			echo "</td>";
		}
		elseif($type == "edit") {
			echo "<td>";
				if (isset($_POST["address"])){
					inputField("address","POST");
				}
				else {
					inputField("address",$member[0]["address"]);		
				}
			echo "</td>";
		}
		?>
	  </tr>
	  <tr>
		<td>Postal Code</td>
		<?php
		if ($type == "view"){
			echo "<td>" . $member[0]["postal_code"] . "</td>";
		}
		elseif($type == "add") {
			echo "<td>";
			inputField("postal_code","POST");
			echo "</td>";
		}
		elseif($type == "edit") {
			echo "<td>";
				if (isset($_POST["postal_code"])){
					inputField("postal_code",$_POST["postal_code"]);
				}
				else {
					inputField("postal_code",$member[0]["postal_code"]);		
				}
			echo "</td>";
		}
		?>
	  </tr>
	  </tr>
	  <tr>
		<td>Town</td>
		<?php
		if ($type == "view"){
			echo "<td>" . $member[0]["town"] . "</td>";
		}
		elseif($type == "add") {
			echo "<td>";
			inputField("town","POST");
			echo "</td>";
		}
		elseif($type == "edit") {
			echo "<td>";
				if (isset($_POST["town"])){
					inputField("town",$_POST["town"]);
				}
				else {
					inputField("town",$member[0]["town"]);		
				}
			echo "</td>";
		}
		?>
	  </tr>
	  <tr>
		<td>Country</td>
		<?php
		if ($type == "view"){
			echo "<td>" . $member[0]["country"] . "</td>";
		}
		elseif($type == "add") {
			echo "<td>";
			inputField("country","POST");
			echo "</td>";
		}
		elseif($type == "edit") {
			echo "<td>";
				if (isset($_POST["country"])){
					inputField("country",$_POST["country"]);
				}
				else {
					inputField("country",$member[0]["country"]);		
				}
			echo "</td>";
		}
		?>
	  </tr>
	  <tr>
		<td>Phone Number</td>
		<?php
		if ($type == "view"){
			echo "<td>" . $member[0]["phone_number"] . "</td>";
		}
		elseif($type == "add") {
			echo "<td>";
			inputField("phone_number","POST");
			echo "</td>";
		}
		elseif($type == "edit") {
			echo "<td>";
				if (isset($_POST["phone_number"])){
					inputField("phone_number",$_POST["phone_number"]);
				}
				else {
					inputField("phone_number",$member[0]["phone_number"]);		
				}
			echo "</td>";
		}
		?>
	  </tr>
	  <tr>
		<td>Email</td>
		<?php
		if ($type == "view"){
			echo "<td>" . $member[0]["mail"] . "</td>";
		}
		elseif($type == "add") {
			echo "<td>";
			inputField("mail","POST");
			echo "</td>";
		}
		elseif($type == "edit") {
			echo "<td>";
				if (isset($_POST["mail"])){
					inputField("mail",$_POST["mail"]);
				}
				else {
					inputField("mail",$member[0]["mail"]);		
				}
			echo "</td>";
		}
		?>
	  </tr>
	  <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	  </tr>
	  <tr>
		<td>Ballast</td>
		<?php
		if ($type == "view"){
			echo "<td>";
			if ($member[0]["ballast"] == "yes"){
				echo "yes";
			}
			else {
				echo "no";
			}
			echo "</td>";
		}
		else {
			echo "<td>";
			if ($type == "add"){
				echo "<input type=\"radio\" name=\"ballast\" value=\"yes\"";
				if (isset($_POST["ballast"]) && $_POST["ballast"] == "yes"){
					echo " checked";
				}
				echo ">yes";
				echo "<input type=\"radio\" name=\"ballast\" value=\"no\"";
				if ((isset($_POST["ballast"]) && $_POST["ballast"] == "no") || !isset($_POST["ballast"])){
					echo " checked";
				}
				
				echo ">no</td>";
			}
			elseif ($type == "edit"){
				echo "<input type=\"radio\" name=\"ballast\" value=\"yrs\"";
				if (isset($_POST["ballast"]) && $_POST["ballast"] == "yes"){
					echo " checked";
				}
				elseif (!isset($_POST["ballast"]) && $member[0]["ballast"] == "yes"){
					echo " checked";
				}
				echo ">yes";
				echo "<input type=\"radio\" name=\"ballast\" value=\"no\"";
				if ((isset($_POST["ballast"]) && $_POST["ballast"] == "no") || (!isset($_POST["ballast"]) && $member[0]["ballast"] != "yes") ){
					echo " checked";
				}
				
				echo ">no</td>";
			}
					
		}
		?>
	  </tr>
	  <tr>
		<td>Driver</td>
		<?php
		if ($type == "view"){
			echo "<td>";
			if ($member[0]["driver"] == 1){
				echo "yes";
			}
			else {
				echo "no";
			}
			echo "</td>";
		}
		else {
			echo "<td>";
			if ($type == "add"){
				echo "<input type=\"radio\" name=\"driver\" value=\"1\"";
				if (isset($_POST["driver"]) && $_POST["driver"] == 1){
					echo " checked";
				}
				echo ">yes";
				echo "<input type=\"radio\" name=\"driver\" value=\"2\"";
				if ((isset($_POST["driver"]) && $_POST["driver"] == 2) || !isset($_POST["driver"])){
					echo " checked";
				}
				
				echo ">no</td>";
			}
			elseif ($type == "edit"){
				echo "<input type=\"radio\" name=\"driver\" value=\"1\"";
				if (isset($_POST["driver"]) && $_POST["driver"] == 1){
					echo " checked";
				}
				elseif (!isset($_POST["driver"]) && $member[0]["driver"] == 1){
					echo " checked";
				}
				echo ">yes";
				echo "<input type=\"radio\" name=\"driver\" value=\"2\"";
				if ((isset($_POST["driver"]) && $_POST["driver"] == 2) || (!isset($_POST["driver"]) && $member[0]["driver"] != 1) ){
					echo " checked";
				}
				
				echo ">no</td>";
			}	
		}
		?>
	  </tr>
	  <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	  </tr>
	<?php
	if ($type != "add")
	{

		echo '$member[0]: ';
		echo '<pre>';
			print_r($member[0]);
		echo '</pre>';
		if(!$member["campRider"] == 'no')
		{
			echo '<tr>';
				echo '<td>Active</td>';
				if ($type == "view")
				{
					echo "<td>";
					if ($member["campRider"] == 'yes')
					{
						echo "yes";
					}
					else 
					{
						echo "no";
					}
					echo "</td>";
				}
				else if ($type == "edit"){
					echo "<td>";
					echo "<input type=\"radio\" name=\"campRider\" value=\"yes\"";
					if (
						isset($_POST["campRider"]) && 
						$_POST["campRider"] == 'yes'
					)
					{
						echo " checked";
					}
					elseif (
						!isset($_POST["campRider"]) && 
						$member[0]["campRider"] == 'yes'
					)
					{
						echo " checked";
					}
					echo ">yes";
					echo "<input type=\"radio\" name=\"campRider\" value=\"inactive\"";
					if (
						(isset($_POST["campRider"]) &&
						$_POST["campRider"] == 'inactive') || 
						(
							!isset($_POST["campRider"]) && 
							$member[0]["campRider"] != 'inactive'
						) 
					)
					{
						echo " checked";
					}
					
					echo ">no</td>";
						
				}
			echo '</tr>';
			echo '<tr>';
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';
			echo '</tr>';
		}
	}

	  echo '<tr>';
		echo '<td>&nbsp;</td>';

		if ($type == "view"){
			echo "<td>";
				if ($member[0]["campRider"] == 'no')
				{
					echo "<a href=\"" . INDEX . "?p=members&sub=view_members\">Back</a>";
				}
				else {
					echo "<a href=\"" . INDEX . "?p=members&sub=view_camp_riders\">Back</a>";	
				}
				
				echo " <a href=\"" . INDEX . "?p=members&sub=add&edit=" . $_GET["view"] . "\">edit</a>";
			echo "</td>";
		}
		elseif($type == "edit"){
			echo "<td><input type=\"submit\" value=\"Submit\" /></td>";
		}
		elseif($type == "add"){
			echo "<td><input type=\"submit\" value=\"Add\" /></td>";
		}
			
		?>
		
	  </tr>
	  </table>
	  </form>
	</div>
</div> 
<div class="bottom"></div>
</center>
</body>
<!--Bottom End -->