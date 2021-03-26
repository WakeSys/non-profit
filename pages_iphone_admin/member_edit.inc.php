<?php 
//echo "<pre>";print_r($_POST);echo"</pre>";
//Formular abgeschickt???
if (isset($_POST["form_sent"]) && $_POST["form_sent"] == "yes"){
	//echo "<pre>";print_r($_POST);echo"</pre>";
	$error = array();

	check_input("first_name",1,"name");
	check_input("last_name",null,"name");
	check_input("phone_number",5,"phone");
	if (isset($_POST["UserID"]) && is_numeric($_POST["UserID"])){
		check_input("mail",5,"mail");
	}
	else {
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
	check_input("birthday",null,"birthday");
	check_input("social_security",1,"");
	check_input("postal_code",4,"number");
	check_input("town",2,"name");
	check_input("address",5,"address");
	check_input("country",1,"name");
	check_input("facebookmail",null,"mail");
	if (isset($_POST["UserID"]) && is_numeric($_POST["UserID"])){
		check_input("password",null,"password");
	}
	else {
		check_input("password",4,"password");	
	}
	
	
	// wenn kein Fehler, dann Update/Insert?!?!?
	
	if (!is_array($error) || count($error) < 1){
		if (isset($_POST["UserID"]) && is_numeric($_POST["UserID"])){
			//echo "<h1>UPDATE!!!!</h1>";
			$sql = "UPDATE members SET ";
			$sql .= "first_name = '" . $_POST["first_name"] . "'";
			$sql .= ", last_name = '" . $_POST["last_name"] . "'";
			$sql .= ", driver = '" . $_POST["driver"] . "'";
			$sql .= ", phone_number = '" . $_POST["phone_number"] . "'";
			$sql .= ", driver = '" . $_POST["driver"] . "'";
			$sql .= ", mail = '" . $_POST["mail"] . "'";
			if (strlen($_POST["birthday"]) >= 7){
				$sql .= ", birthday = '" . $_POST["birthday"] . "'";	
			}
			else {
				$sql .= ", birthday = '0000-00-00'";
			}
			
			$sql .= ", social_security = '" . $_POST["social_security"] . "'";
			$sql .= ", postal_code = '" . $_POST["postal_code"] . "'";
			$sql .= ", town = '" . $_POST["town"] . "'";
			$sql .= ", address = '" . $_POST["address"] . "'";
			$sql .= ", country = '" . $_POST["country"] . "'";
			$sql .= ", ballast = '" . $_POST["ballast"] . "'";
			$sql .= ", categoryID = '" . $_POST["category"] . "'";
			$sql .= ", facebookON = '" . $_POST["facebookON"] . "'";
			$sql .= ", facebookmail = '" . $_POST["facebookmail"] . "'";
			
			
			if (isset($_POST["password"]) && strlen($_POST["password"]) > 4){
				//$sql .= ", password = '" . md5($_POST["password"]) . "'";
				$sql .= ", password = '" . md5($_POST["password"]) . "'";
			}
			
			
			$sql .= " WHERE ID = '" . $_POST["UserID"]. "'";
			
			//echo $sql;
			$db->execute($sql);
			
			$edited = "true";
		}
		else {
			//echo "<h1>Insert!!!</h1>";
			$sql = "INSERT INTO members ";
			$sql .= "(first_name, last_name, driver,phone_number,mail,birthday,social_security,postal_code,town,address,country,ballast,categoryID,facebookmail,facebookON,campRider, password";
			$sql .= ") VALUES";
			$sql .= " ('" . $_POST["first_name"]. "'";
			$sql .= ", '" . $_POST["last_name"]. "'";
			$sql .= ", '" . $_POST["driver"]. "'";
			$sql .= ", '" . $_POST["phone_number"]. "'";
			$sql .= ", '" . $_POST["mail"]. "'";
			if (strlen($_POST["birthday"]) >= 7){
				$sql .= ", '" . $_POST["birthday"] . "'";
			}
			else {
				$sql .= ", '00-00-0000 00:00:00'";
			}
			$sql .= ", '" . $_POST["social_security"]. "'";
			$sql .= ", '" . $_POST["postal_code"]. "'";
			$sql .= ", '" . $_POST["town"]. "'";
			$sql .= ", '" . $_POST["address"]. "'";
			$sql .= ", '" . $_POST["country"]. "'";
			$sql .= ", '" . $_POST["ballast"]. "'";
			$sql .= ", '" . $_POST["category"]. "'";
			$sql .= ", '" . $_POST["facebookmail"] . "'";
			$sql .= ", '" . $_POST["facebookON"]. "'";
			if (isset($_POST["camp"]) && $_POST["camp"] == "true"){
				$sql .= ", 'yes'";
			}
			else {
				$sql .= ", 'no'";
			}
				
			$sql .= ", '" . md5($_POST["password"]) . "'";
			$sql .= ")";
			
			//echo $sql;
			$db->execute($sql);
			$_POST["UserID"] = $db->insertId();
			
			first_mail($_POST["UserID"]);
			
			//add 1 Year membership!!!
			if (isset($_POST["camp"]) && $_POST["camp"] == "true"){
			}
			else {
				extendMembership($_POST["UserID"]);
			}
			$edited = "true";
		}
	}
	else {
		//echo "<h1>no update???</h1>";
	}
	
	if (isset($_POST["UserID"]) && is_numeric($_POST["UserID"])){
		$sql = "SELECT * FROM members WHERE ID = '" . $_POST["UserID"] . "'";
		$data = $db->queryArray($sql);
	}
}
elseif (isset($_POST["UserID"]) && is_numeric($_POST["UserID"])){
	$sql = "SELECT * FROM members WHERE ID = '" . $_POST["UserID"] . "'";
	$data = $db->queryArray($sql);
}



if (isset($_POST["UserID"]) && is_numeric($_POST["UserID"])){
	$type = "Edit";
	if (isset($data[0]["campRider"]) && $data[0]["campRider"] == "yes"){
		echo "--TITLE--Add CampRider--";
		echo "--BACK_TITLE--members--";
		echo "--BACK_PAGE--camp_members--";
	}
	else {
		echo "--TITLE--Edit Member--";
		echo "--BACK_TITLE--members--";
		echo "--BACK_PAGE--members--";
	}
		
}
else {
	$type = "Add";
	if (isset($data[0]["campRider"]) && $data[0]["campRider"] == "yes"){
		echo "--TITLE--Add CampRider--";
		echo "--BACK_TITLE--members--";
		echo "--BACK_PAGE--camp_members--";
	}
	else {
		echo "--TITLE--Add Member--";
		echo "--BACK_TITLE--members--";
		echo "--BACK_PAGE--members--";
	}
		
}



if (isset($edited) && $edited == "true"){
	//gehe zu view members!!!!
	include ('../pages_iphone_admin/members.inc.php');
}
else {

/*<h2><?php echo $type;?> Member</h2>*/
?>
&nbsp;
    

<form method="POST" name="login" action="javascript: LoadPage('member_edit',document.getElementById('form_add_member'));" id="form_add_member">
<?php 
if (isset($_POST["UserID"]) && is_numeric($_POST["UserID"])){
	echo "<input type=\"hidden\" name=\"UserID\" value=\"" . $_POST["UserID"] . "\">";
}
if (isset($data[0]["campRider"]) && $data[0]["campRider"] == "yes"){
	echo "<input type=\"hidden\" name=\"camp\" value=\"true\">";
}
elseif (isset($_POST["camp"]) && $_POST["camp"] == "true") {
	echo "<input type=\"hidden\" name=\"camp\" value=\"true\">";
}
?>
<input type="hidden" name="form_sent" value="yes">

	<ul>
		<li>Category<span class="secondaryWLink"><label><select name="category">
	    
		<?php 
			$sql = "SELECT * FROM categories WHERE (member = 3 OR member = '1') AND active = '1'";
			$cats = $db->queryArray($sql);
			foreach ($cats as $cat){
				echo "<option value=\"" . $cat["ID"]. "\"";
					if(isset($_POST["category"]) && is_numeric($_POST["category"]) && $cat["ID"] == $_POST["category"]){
						echo " selected";
					}
					elseif (isset($data[0]["categoryID"]) && $cat["ID"] == $data[0]["categoryID"]) {
						echo " selected";
					}
					echo ">" . $cat["name"];
				echo "</option>";
			}
			//echo $sql;
		?>
		
		</select></label></span></li>
	</ul>

	<ul>    
		<li>First Name<span class="showArrow secondaryWArrow"><label>
			<input class="secondaryInput" name="first_name" type="text" size="12" maxlength="30" <?php getTextValue("first_name");?>/>
		</label></span></li>
	    <li>Last Name<span class="showArrow secondaryWArrow"><label>
	    	<input class="secondaryInput" name="last_name" type="text" size="12" maxlength="30" <?php getTextValue("last_name");?>/></label>
	    </label></span></li>
	    <li>Password<span class="showArrow secondaryWArrow"><label>
	    	<input class="secondaryInput" name="password" type="password" size="12" maxlength="30" <?php getTextValue("password");?>/>
	    </label></span></li>
		<li>Birthday (d.m.Y)<span class="showArrow secondaryWArrow"><label>
			<input name="birthday" type="text" class="secondaryInput" <?php getTextValue("birthday");?> size="12" maxlength="10"/>
		</label></span></li>
		<li>Social Security<span class="showArrow secondaryWArrow"><label>
			<input class="secondaryInput" name="social_security" type="tel" size="12" maxlength="30" <?php getTextValue("social_security");?>/>
		</label></span></li>
	    <li>Address<span class="showArrow secondaryWArrow"><label>
	    	<input class="secondaryInput" name="address" type="text" size="12" maxlength="30" <?php getTextValue("address");?>/>
	    </label></span></li>
		<li>Postal Code<span class="showArrow secondaryWArrow"><label>
			<input class="secondaryInput" name="postal_code" type="text" pattern="[0-9]*" size="12" maxlength="30"  <?php getTextValue("postal_code");?>/>
		</label></span></li>
		<li>Town<span class="showArrow secondaryWArrow"><label>
			<input class="secondaryInput" name="town" type="text" size="12" maxlength="30" <?php getTextValue("town");?>/>
		</label></span></li>
		<li>Country<span class="showArrow secondaryWArrow"><label>
			<input class="secondaryInput" name="country" type="text" size="12" maxlength="30" <?php getTextValue("country");?>/>
		</label></span></li>
		<li>Phone Number<span class="showArrow secondaryWArrow"><label>
			<input class="secondaryInput" name="phone_number" type="tel" size="12" maxlength="30" <?php getTextValue("phone_number");?>/>
		</label></span></li>
		<li>Email<span class="showArrow secondaryWArrow"><label>
			<input class="secondaryInput" name="mail" type="email" size="12" maxlength="100" <?php getTextValue("mail");?>/>
		</label></span></li>
	</ul>

	<h2>Preconfigurations</h2>
	
	<ul>   
	
	       
		<?php 
			button("Ballast","ballast","yes_no");
			button("Driver","driver","yes_no");
		
		?>
	</ul><h2>Facebook</h2>
	
	<ul>   
		<li>Personal Mail<span class="showArrow secondaryWArrow"><label>
				<input class="secondaryInput" name="facebookmail" type="email" size="12" maxlength="30" value=""/>
		</label></span></li>
		<?php 
			button("Push to Facebook","facebookON","yes_no");
		
		?>
	</ul>
	
</form>

<div class="start">	
	<li><a href="javascript: LoadPage('member_edit',document.getElementById('form_add_member'));">Save</a></li>
</div> 	

<?php 
}

function button ($name, $field, $type){
	GLOBAL $data;
	echo "<li>" . $name . "<span class=\"secondaryWLink\">";
	echo "<span class=\"secondaryWLink\">";
	echo "<a href=\"javascript:button('" . $field . "','" . $type . "');\" id=\"href_" . $field . "\"><img src=\"iphone_admin/images/";
	if ($type == "yes_no"){
		
		if ($field == "ballast"){
			if ( (isset($data[0][$field]) && $data[0][$field] == "yes") || (isset($_POST[$field]) && $_POST[$field] == "yes")){
				echo "yes";
			}
			else {
				echo "no";
			}
		}
		else {
			if ( (isset($data[0][$field]) && $data[0][$field] == 1) || (isset($_POST[$field]) && $_POST[$field] == 1)){
				echo "yes";
			}
			else {
				echo "no";
			}
		}
			
	}
	else {
		if ( (isset($data[0][$field]) && $data[0][$field] == 1) || (isset($_POST[$field]) && $_POST[$field] == 1)){
			echo "on";
		}
		else {
			echo "off";
		}
	}
	echo ".png\"></a>";
	echo "<input type=\"hidden\" id=\"input_" . $field . "\" name=\"" . $field . "\" value=\"";
		if ($field == "ballast"){
			if ( (isset($data[0][$field]) && $data[0][$field] == "yes") || (isset($_POST[$field]) && $_POST[$field] == "yes")){
				echo "yes";
			}
			else {
				echo "no";
			}
		}
		else {
			if ( (isset($data[0][$field]) && $data[0][$field] == 1) || (isset($_POST[$field]) && $_POST[$field] == 1)){
				echo "1";
			}
			else {
				echo "2";
			}
		}
			
	echo "\" id=\"input_" . $field . "\"></span></li>";
}

function getTextValue($field){
	GLOBAL $data;
	GLOBAL $error;
	if ($field == "birthday"){
		if (isset($data[0][$field]) && strlen($data[0][$field]) > 6){
			echo "value=\"" ;
			preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/",$data[0][$field],$match);
			echo $match[3] . "." . $match[2] . "." . $match[1];
			echo "\"";	
		}
	}
	elseif ($field == "password"){
		echo "value=\"\"";
	}
	elseif (isset($data[0][$field])){
		echo "value=\"" . $data[0][$field] . "\"";
	}
	
	if (isset($error) && in_array($field,$error)){
		echo "style=\"background-color:red;\"";
	}
}
?>