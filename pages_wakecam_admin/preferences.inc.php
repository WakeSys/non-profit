<?php
if (isset($_POST["sent_contact"]) && $_POST["sent_contact"] == "yes"){
	$sql = "SELECT * FROM preferences LIMIT 0,1";
	$data = $db->queryArray($sql);
	check_input("contact_name_of_school",3,"name");
	check_input("contact_name",3,"name");
	check_input("contact_address",3,"name");
	check_input("contact_postal_code",3,"number");
	check_input("contact_town",3,"name");
	check_input("contact_country",3,"name");
	check_input("contact_phone",3,"phone");
	check_input("contact_fax",null,"phone");
	check_input("contact_mail",4,"mail");
	if (!is_array($error)){
		$sql = "UPDATE preferences SET ";
		$i = 0;
		foreach ($_POST as $key=>$elem){
			if ($key != "sent_contact" && $key != "button"){
				if ($i != 0){
					$sql .= ", ";
				}
				$sql .= $key . " = '" . $db->escape($elem) . "'";
				$i++;
			}
		}
		$db->execute($sql);
		$sql = "SELECT * FROM preferences LIMIT 0,1";
		$data = $db->queryArray($sql);
	}
}
else {
   $sql = "SELECT * FROM preferences LIMIT 0,1";
   $data = $db->queryArray($sql);
}

if (isset($_POST["timezone"]) && strlen($_POST["timezone"]) > 3 ){
	setPreferences($_POST["timezone"], "timezone");
}
elseif (isset($_POST["currency"]) && is_numeric($_POST["currency"])){
	setPreferences($_POST["currency"], "currencyID");
}
elseif (isset($_POST["PaypalMail"])){
	setPreferences($_POST["PaypalMail"],"PaypalMail");
}
elseif (isset($_POST["maintenance_fueling"])){
	setPreferences($_POST["fuelingtype"],"fuelingtype");
}
elseif (isset($_POST["welcome_mail"])){
	setPreferences($_POST["welcome_mail"],"welcome_mail");
}
elseif (isset($_POST["welcome_mail_subject"])){
	setPreferences($_POST["welcome_mail_subject"],"welcome_mail_subject");
}
elseif (isset($_POST["maintenance_oil"])){
	setPreferences($_POST["oilchange"],"oilchange");
}
elseif (isset($_POST["round"])){
	setPreferences($_POST["round"],"round");
}
elseif (isset($_POST["VAT_deduce"])){
	setPreferences($_POST["VAT_deduce"],"VAT_deduce");
}
elseif (isset($_POST["VAT_nights"])){
	setPreferences($_POST["VAT_nights"],"VAT_nights");
}
elseif (isset($_POST["VAT_riding"])){
	setPreferences($_POST["VAT_riding"],"VAT_riding");
}
elseif (isset($_POST["VAT_fuel"])){
	setPreferences($_POST["VAT_fuel"],"VAT_fuel");
}
elseif (isset($_POST["contact_IBAN"])){
	setPreferences($_POST["contact_IBAN"],"contact_IBAN");
}
elseif (isset($_POST["contact_BIC"])){
	setPreferences($_POST["contact_BIC"],"contact_BIC");
}
elseif (isset($_POST["paymentMethod"])){
	$_POST["paymentPercentage"] = str_replace(",",".", $_POST["paymentPercentage"]);
	$_POST["paymentValue"] = str_replace(",",".", $_POST["paymentValue"]);
	echo setPayment($_POST["paymentMethod"],1,$_POST["paymentValue"],$_POST["paymentPercentage"]);
}
elseif (isset($_POST["changePW"]) && isset($_POST["newpw1"]) && isset($_POST["newpw2"]) && isset($_POST["oldpw"])){
	echo "<div>";
		echo changeAdminPassword($_POST["oldpw"],$_POST["newpw1"],$_POST["newpw2"]);
	echo "</div>";
}
elseif (isset($_POST["payDriver"])){
	setPreferences(str_replace(",",".",$_POST["payDriver"]),"payDriver");
}

$preferences = getPreferences();
	      	
//echo "<pre>";print_r($_POST);echo "</pre>";
?>

	<div class="main">	
	    <div class="navi_2_padding">
		<table border="0" cellpadding="0" cellspacing="0" class="navi_2_bg">
		  <tr>
	      	<td width="10"><img src="wakecam_admin_new/images/navi_left_darker.gif" width="8" height="35" /></td>
	        <td width="380">&nbsp;</td>
	    	<td>
	        <div class="navi_text">
			Preferences
	       </div>
	        </td>
	    	<td width="10"><img src="wakecam_admin_new/images/navi_right_darker.gif" width="10" height="35" /></td>
	  	  </tr>
		</table>
	    </div>
	</div>
	<div class="spacer"></div>
	
	<div class="main">
		<div class="main_text">
		<div class="main_text_title">Edit Preferences</div>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
	    <td width="40%"><div class="main_text_title">Contact Settings</div></td>
	    <td width="20%">&nbsp;</td>
	    <td width="20%">&nbsp;</td>
	    <td width="20%">&nbsp;</td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
      </tr>  
	   <form method="POST" action="">
	   
	   <input type="hidden" name="sent_contact" value="yes">
	  
           <tr>
	    <td>Name of your school:</td>
	    <td colspan="2"><label>
	      <?php inputField("contact_name_of_school",$data[0]["contact_name_of_school"]);?>
	    </label></td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>c/o first &amp; last name </td>
	    <td colspan="2"><label>
	      <?php inputField("contact_name",$data[0]["contact_name"]);?>
	    </label></td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>Address &amp; housenumber:</td>
	    <td colspan="2"><label>
	      <?php inputField("contact_address",$data[0]["contact_address"]);?>
	    </label></td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>Postal code:</td>
	    <td colspan="2"><label>
	    	<?php inputField("contact_postal_code",$data[0]["contact_postal_code"]);?>
	    </label></td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>Town:</td>
	    <td colspan="2"><label>
	      <?php inputField("contact_town",$data[0]["contact_town"]);?>
	    </label></td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>Country:</td>
	    <td colspan="2"><label>
	      <?php inputField("contact_country",$data[0]["contact_country"]);?>
	    </label></td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>Phone:</td>
	    <td colspan="2"><label>
	      <?php inputField("contact_phone",$data[0]["contact_phone"]);?>
	    </label></td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>Fax:</td>
	    <td colspan="2"><label>
	      <?php inputField("contact_fax",$data[0]["contact_fax"]);?>
	    </label></td>
	    <td>&nbsp;</td>
	  </tr>

      <tr>
	    <td>Email:</td>
	    <td colspan="2"><label>
	      <?php inputField("contact_mail",$data[0]["contact_mail"]);?>
	    </label></td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>Website:</td>
	    <td colspan="2"><label>
	      <?php inputField("contact_website",$data[0]["contact_website"]);?>
	    </label></td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>Business registration number</td>
	    <td colspan="3"><label>
	      <?php inputField("contact_BRN",$data[0]["contact_BRN"]);?>
	      </label></td>
	    </tr>
      <tr>
	    <td>VAT Number:</td>
	    <td colspan="3"><label>
	      <?php inputField("contact_VAT",$data[0]["contact_VAT"]);?>
	      </label></td>
	    </tr>
        <tr>
	    <td>&nbsp;</td>
	    <td><label>
	      <input type="submit" name="button" id="button" value="Save Contact Settings" />
	      </label></td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
  	  </form>
      <tr>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td><div class="main_text_title">Welcome Mail Settings</div></td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
	  <form method="POST">
	      <tr>
		    <td valign="top">Subject that will be sent to a new member or camp rider after signing up</td>
		    <td colspan="3"><label>
		      <input name="welcome_mail_subject" type="text" onchange="this.form.submit();" size="45" <?php
		      if (isset($preferences[0]["welcome_mail_subject"])){
		      	echo "value=\"" . $preferences[0]["welcome_mail_subject"] . "\"";
		      }
		      ?>>
	        </label></td>
		  </tr>
	  </form> 
	  <form method="POST">
	      <tr>
		    <td valign="top">Text that will be sent to a new member or camp rider after signing up</td>
		    <td colspan="3"><label>
		      <textarea name="welcome_mail" cols="45" rows="25"><?php
		      if (isset($preferences[0]["welcome_mail"])){
		      	echo $preferences[0]["welcome_mail"];
		      }
		      ?></textarea><br><input type="submit" value="change welcome message">
	        </label></td>
		  </tr>
	  </form>
      <tr>
	    <td>Variables that can be used</td>
	    <td colspan="3">[first_name_of_member], [last_name_of_member], [name_of_school]</td>
	    </tr>
      <tr>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td><div class="main_text_title">Global Settings</div></td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
      
	  <tr>
	    <td>How do you want your ride-times to be rounded?</td>
	    <td><label>
	    <form action="" method="POST">
	      <select name="round" onchange="this.form.submit();">
	        <option value="up" <?php if ($preferences[0]["round"] == "up"){echo " selected";}?>>Round Up - 1:01 will be 2:00</option>
	        <option value="round" <?php if ($preferences[0]["round"] == "round"){echo " selected";}?>>Round - 1:01 will be 1:00 and 1:30 will be 2:00</option>
	         <option value="down" <?php if ($preferences[0]["round"] == "down"){echo " selected";}?>>Round Down - 1:59 will be 1:00</option>
              <option value="second" <?php if ($preferences[0]["round"] == "second"){echo " selected";}?>>Round to the second - 1:25 will be 1:25</option>
	      </select>
		</form>
	    </label></td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td>Time zone you are  living in?</td>
	    <td><label>
	      <form method="POST" action="">
	      <select name="timezone" onchange="this.form.submit();">
	      	<?php
                $timezones = DateTimeZone::listIdentifiers();

	      	foreach ($timezones as $timezone){
                        $atz = new DateTimeZone($timezone);
                        $aDate = new DateTime("now", $atz);
                        
	      		echo "<option value=\"" . $timezone . "\"";
	      		if ($timezone == $preferences[0]["timezone"]){
	      			echo " selected";
	      		}
	      		echo ">" . $timezone . " (" . date_format($aDate, "d\.m\.Y H:i:s") . ")</option>";
	      	}
	      	?>
	       
	      </select>
	      </form>
	    </label></td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td>Your local currency:</td>
	    <td><label>
	    <form method="POST" action="">
	      <select name="currency" onchange="this.form.submit();">
	      	<?php
	      	$currencies = getCurrencies();
	      	foreach ($currencies as $currency){
	      		echo "<option value=\"" . $currency["ID"] . "\"";
	      		if ($currency["ID"] == $preferences[0]["currencyID"]){
	      			echo " selected";
	      		}
	      		echo ">";
	      		echo $currency["HTML"]. " (" . $currency["short"] . ")";
	      		echo "</option>";
	      	}
	      	
	      	?>
	      </select>
	    </form>
	    </label></td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
	  <form method="POST">
	  <input type="hidden" name="maintenance_fueling">
      <tr>
	    <td>Liters or Gallons?</td>
	    <td><select name="fuelingtype" onchange="this.form.submit();">
	      <option value="liters" <?php if ($preferences[0]["fuelingtype"] == "liters"){echo "selected";}?>>Liters</option>
	      <option value="gallons" <?php if ($preferences[0]["fuelingtype"] == "gallons"){echo "selected";}?>>Gallons</option>
	    </select></td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
	  </form>
	  <form method="POST">
	  <input type="hidden" name="maintenance_oil">
      <tr>
	    <td>Oil &amp; filter change for boats every</td>
	    <td><select name="oilchange"  onchange="this.form.submit();">
	    	<?php
	    	
	    	$oilchanges = array(40,50,60,70,80,90);
	    	foreach ($oilchanges as $oilchange){
	    		echo "<option value=\"" . $oilchange . "\"";
	    		if ($preferences[0]["oilchange"] == $oilchange){echo " selected";}
	    		echo ">" . $oilchange . "</option>";
	    	}
	    	?>
        </select>
	    hours</td>
	    <td>(50h recommended)</td>
	    <td>&nbsp;</td>
	  </tr>
	  </form><form method="POST">
	  <tr>
	    <td>How much do you want to pay your drivers per minute?</td>
	    <td><label>
	          <input name="payDriver" type="text" value="<?php echo $preferences[0]["payDriver"];?>" size="5"  onchange="this.form.submit();"/>
	        </label>
        </td>
	    <td><span class="text_admin"><?php echo getPreferences("currencyHTML");?> / min</span></td>
	    <td>&nbsp;</td>
	  </tr>
      </form>
      <form method="POST">
      <tr>
	    <td>Do you need to deduce VAT / sales tax?</td>
	    <td><label>
	      <input type="radio" name="VAT_deduce" id="radio" value="yes"  onchange="this.form.submit();"
	    	<?php if ($preferences[0]["VAT_deduce"] == "yes"){echo " checked";}?>/>
        Yes
        <input name="VAT_deduce" type="radio"  value="no"  onchange="this.form.submit();"<?php if ($preferences[0]["VAT_deduce"] == "no"){echo " checked";}?>/>
No </label></td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
	  </form>
      <tr>
	    <td>VAT for riding </td>
	    <td><label>
	    	<form method="POST">
	          <input name="VAT_riding" type="text" value="<?php echo $preferences[0]["VAT_riding"];?>"  onchange="this.form.submit();" size="5" />
	          </form>
	        </label>
        </td>
	    <td><span class="text_admin">%</span></td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>VAT for nights </td>
	    <td><label>
	    <form method="POST">
	          <input name="VAT_nights" type="text"  value="<?php echo $preferences[0]["VAT_nights"];?>"  onchange="this.form.submit();" size="5" />
	          </form>
	        </label>
        </td>
	    <td><span class="text_admin">%</span></td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>VAT on fuel</td>
	    <td><label>
	    	<form method="POST">
	          <input name="VAT_fuel" type="text"  value="<?php echo $preferences[0]["VAT_fuel"];?>"  onchange="this.form.submit();" size="5" />
	          </form>
	        </label>
        </td>
	    <td><span class="text_admin">%</span></td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td><div class="main_text_title">Admin Settings</div></td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
	  <form method="POST" action="">
	  <input type="hidden" name="changePW" value="true">
		  <tr>
		    <td>Change Admin Password:</td>
		    <td>Old PW:
		      <input name="oldpw" type="password" size="10" /></td>
		    <td>New PW:
		      <input name="newpw1" type="password" size="10" /></td>
		    <td>New PW:
		      <label>
		        <input name="newpw2" type="password" size="10" />
		      </label></td>
		      <td><input type="submit" value="change PW"></td>
		  </tr>
	  </form>
	  <tr>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td><div class="main_text_title">Financial Settings</div></td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>Bank account for unpaid invoices (IBAN):</td>
	    <td colspan="2"><label>
	    <form method="POST">
	      <input name="contact_IBAN" type="text" size="25" value="<?php echo $preferences[0]["contact_IBAN"];?>"  onchange="this.form.submit();"/>
	      </form>
	    </label></td>
	    <td>&nbsp;</td>
	  </tr>
      <tr>
	    <td>BIC/SWIFT:</td>
	    <td colspan="2"><label>
	    <form method="POST">
	      <input name="contact_BIC" type="text" size="25" value="<?php echo $preferences[0]["contact_BIC"];?>"  onchange="this.form.submit();"/>
	      </form>
	    </label></td>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td>Email address of your paypal dealer account :</td>
	    <td colspan="2">
	    	<form action="" method="POST">
	    		<input name="PaypalMail" type="text" size="25" value="<?php echo $preferences[0]["PaypalMail"];?>" onchange="this.form.submit();"/>
	    	</form>
	    </td>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td>Payment Options:</td>
	    <td>Method of Payment </td>
	    <td>Tarif Rates</td>
	    <td>&nbsp;</td>
	  </tr>
	  <form method="POST" action="">
		  <tr>
		    <td>&nbsp;</td>
		    <td><input name="paymentMethod" type="text" size="10" /></td>
		    <td><input name="paymentPercentage" type="text" size="5" value="0"/>
		      %  +
		      <input name="paymentValue" type="text" size="5" value="0"/>
		      <?php echo getPreferences("currencyHTML");?></td>
		    <td><label>
		      <input type="hidden" name="changePayment" value="add"/>
		      <input type="submit" value="Add" />
		    </label></td>
		  </tr>
	  </form>
	  <?php
	  if( isset($_POST["changePayment"]) && ( is_numeric($_POST["changePayment"]) || $_POST["changePayment"] == "add") && isset($_POST["name"]) && strlen($_POST["name"]) > 3 && isset($_POST["value"]) && isset($_POST["percent"])){
	  	setPayment($_POST["name"],1,$_POST["value"],$_POST["percent"]);
	  }
	  $payments = getPaymentData();
	  if (is_array($payments)){
	  	foreach ($payments as $payment){
	  		echo "<form method=\"POST\">";
			   	echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td>" . $payment["name"] . "</td>";
					echo "<input type=\"hidden\" name=\"name\" value=\"" . $payment["name"] . "\">";
					echo "<td>";
						if (isset($_POST["changePayment"]) && $payment["ID"] == $_POST["changePayment"]){
							echo "<input type=\"text\" name = \"percent\" value = \"" . $payment["percent"] . "\" size=\"10\">";
						}
						else {
							echo $payment["percent"];	
						}
						echo " % + ";
						if (isset($_POST["changePayment"]) && $payment["ID"] == $_POST["changePayment"]){
							echo "<input type=\"text\" name = \"value\" value = \"" . $payment["value"] . "\" size=\"5\">";
						}
						else {
							echo $payment["value"];	
						}
						echo getPreferences("currencyHTML");
					echo "</td>";
					echo "<input type=\"hidden\" name=\"changePayment\" value=\"" . $payment["ID"] . "\">";
					if (isset($_POST["changePayment"]) && $payment["ID"] == $_POST["changePayment"]){
						echo "<td><input type=\"submit\" value=\"Save\" />";
						echo "<a onclick=\"if(confirm('Do you really want to delete this payment method?')){alert('delete');}\">X</a></td>";
					}
					else {
						echo "<td><input type=\"submit\" value=\"Edit\" />";
						echo "</td>";
					}
				echo "</tr>";
			echo "</form>";
		  }
	  }
	  else {
  		echo "<tr>";
			echo "<td colspan=\"4\"><b>no payment methodactive!!!</b></td>";
		echo "</tr>";
	  }
	  
	  ?>
		
	    </table>
		</div>
	</div>
	
	<div class="bottom"></div>
<?php
function inputField ($name,$value=null){
	global $error;
	if ($value == "POST"){
		global $_POST;
		if (isset($_POST[$name])){
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