<?php
if (isset($_POST["add"]) && isset($_POST["name"]) && strlen($_POST["name"]) > 2){
	
	$member = 0;
	
	if (isset($_POST["member"]) && $_POST["member"] == "1"){
		$member = 1;	
	}
	if (isset($_POST["nonmember"]) && $_POST["nonmember"] == "1"){
		$member = $member + 2;
	}
	
	
	if(isset($_POST["ID"]) && is_numeric($_POST["ID"])){
		$sql = "UPDATE " . $_POST["add"] . " SET ";
		if ($_POST["add"] != "boats") {$sql .= "member = '" . $member. "',";}
		$sql .= "name = '" . $_POST["name"]. "', active = 1 WHERE ID = '" . $_POST["ID"]. "'";
	}
	else {
		$sql = "INSERT INTO " . $_POST["add"] . " (name, active";
		if ($_POST["add"] != "boats") { $sql .= ",member";}
		$sql .= ") VALUES ('" . $_POST["name"]. "', '1'";
		if ($_POST["add"] != "boats") { $sql .= ",'" . $member . "'";}
		$sql .= ") ON DUPLICATE KEY UPDATE ";
		if ($_POST["add"] != "boats") { $sql .= "member = '" . $member . "', ";}
		$sql .= "active = '1'";	
	}
	$db->execute($sql);
}

if (isset($_GET["del"])  && is_numeric($_GET["ID"])){
	$sql = "UPDATE " . $_GET["del"] . " SET active = '2' WHERE ID = '" . $_GET["ID"]. "'";
	$db->execute($sql);
}
elseif (isset($_GET["edit"]) && is_numeric($_GET["ID"])){
	$sql = "SELECT * FROM " . $_GET["edit"] . " WHERE ID = '" . $_GET["ID"] . "'";
	if ($_GET["edit"] == "boats"){
		$boat_data = $db->queryArray($sql);	
		//echo "<pre>";print_r($boat_data);echo "</pre>";
	}
	elseif ($_GET["edit"] == "categories"){
		$categories_data = $db->queryArray($sql);
		//echo "<pre>";print_r($categories_data);echo "</pre>";	
	}
	elseif ($_GET["edit"] == "sports"){
		$sports_data = $db->queryArray($sql);
		//echo "<pre>";print_r($sports_data);echo "</pre>";	
	}
		
}
/*
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<pre>";
print_r($_POST);
echo "</pre>";
*/
?>
<!--Bottom Beginning -->  
<div class="main">
	<div class="main_text">
    <div class="main_text_title">Add or Edit Boats</div>
    <table width="600" border="0" cellpadding="0" cellspacing="0">
      <tr class="bg_stats">
        <form action="/wakecam_admin.php?p=prices&amp;sub=edit" method="post">
          <td align="left" class="text_admin">Add Boat</td>
          <td width="275" align="left"><input name="name" type="text" class="text_admin" size="25" value="<?php if(isset($boat_data[0]["name"])){echo $boat_data[0]["name"];}?>"/></td>
          <td width="155" align="left">&nbsp;</td>
          <td width="60" align="left"><input type="submit" id="button3" value="Save" /></td>
          <input type="hidden" name="add" value="boats" />
          <?php if (isset($boat_data[0]["ID"])){echo "<input type=\"hidden\" name=\"ID\" value=\"" . $boat_data[0]["ID"] . "\">";}?>
        </form>
      </tr>
      <tr>
        <td width="110" align="left" valign="top" class="text_admin"> Boats</td>
      </tr>
      <?php
                $sql = "SELECT * FROM boats WHERE active = '1'";
                
                $result = $db->queryArray($sql);
				if (!is_array($result)){
                	echo "<tr><td>NO BOATS!!!!! ?????</td></tr>";
                }
                else {
					foreach ($result as $data){
	                	echo "<tr><td>&nbsp;</td><td>" . $data["name"] ;
	                	echo "</td><td>";
	                	echo "<a class=\"main_text_link\" href=\"/wakecam_admin.php?p=prices&sub=edit&edit=boats&ID=" . $data["ID"] . "\">Edit</a>";
	                	echo " / ";
	                	echo "<a class=\"main_text_link\" href=\"/wakecam_admin.php?p=prices&sub=edit&del=boats&ID=" . $data["ID"] . "\">Delete</a>";
	                	echo "</td></tr>";
	                }                    
                }
                
                ?>
      <tr>
        <td colspan="3"><br />
          <br />
          <div class="main_text_title">Add or Edit Category</div></td>
      </tr>
      <tr class="bg_stats">
        <form action="/wakecam_admin.php?p=prices&amp;sub=edit" method="post">
          <td align="left" class="text_admin">Add Category</td>
          <td align="left"><input name="name" type="text" class="text_admin" size="25" value="<?php if(isset($categories_data[0]["name"])){echo $categories_data[0]["name"];}?>"/></td>
          <td align="left"><input name="member" type="checkbox" value="1"<?php 
	                	if (isset($categories_data[0]["member"])){
	                		if ($categories_data[0]["member"] == 1 || $categories_data[0]["member"] == 3){
	                			echo "checked";
	                		}
	                	}
	                	else {
	                		echo "checked";
	                	}
	                	?>/>
            Member<br />
            <input name="nonmember" type="checkbox" value="1" <?php 
	                	if (isset($categories_data[0]["member"])){
	                		if ($categories_data[0]["member"] == 2 || $categories_data[0]["member"] == 3){
	                			echo "checked";
	                		}
	                	}
	                	else {
	                		echo "checked";
	                	}
	                	?> />
            NON-Member </td>
          <td align="left"><input type="submit" id="button3" value="Save" /></td>
          <input type="hidden" name="add" value="categories" />
          <?php if (isset($categories_data[0]["ID"])){echo "<input type=\"hidden\" name=\"ID\" value=\"" . $categories_data[0]["ID"] . "\">";}?>
        </form>
      </tr>
      <tr>
        <td width="110" align="left" valign="top" class="text_admin"> Categories</td>
      </tr>
      <?php
                $sql = "SELECT * FROM categories WHERE active = '1'";
                
                $result = $db->queryArray($sql);
				if (!is_array($result)){
                	echo "<tr><td>NO Cats!!!!! ?????</td></tr>";
                }
                else {
					foreach ($result as $data){
	                	echo "<tr><td>&nbsp;</td><td>" . $data["name"] ;
					
						if ($data["member"] == 1 || $data["member"] == 2 || $data["member"] == 3) {
	                		echo " (";
	                			if ($data["member"] == 1 || $data["member"] == 3) {
	                				echo "Members";
	                			}
	                			if ($data["member"] == 3) { echo " & ";}
	                			if ($data["member"] == 2 || $data["member"] == 3) {
	                				echo "Non-Members";
	                			}
	                			
	                		echo ")";
	                	}
	                	else {
	                		echo " (inactive!)";
	                	}
	                	
	                	echo "</td><td>";
	                	echo "<a class=\"main_text_link\" href=\"/wakecam_admin.php?p=prices&sub=edit&edit=categories&ID=" . $data["ID"] . "\">Edit</a>";
	                	echo " / ";
	                	echo "<a class=\"main_text_link\" href=\"/wakecam_admin.php?p=prices&sub=edit&del=categories&ID=" . $data["ID"] . "\">Delete</a>";
	                	echo "</td></tr>";
	                }                    
                }
                
                ?>
      <tr>
        <td colspan="3"><br />
          <br />
          <div class="main_text_title">Add or Edit Sports</div></td>
      </tr>
      <tr class="bg_stats">
        <form action="/wakecam_admin.php?p=prices&amp;sub=edit" method="post">
          <td align="left" class="text_admin">Add Sports</td>
          <td align="left"><input name="name" type="text" class="text_admin" size="25" value="<?php if(isset($sports_data[0]["name"])){echo $sports_data[0]["name"];}?>"/></td>
          <td align="left"><input name="member" type="checkbox" value="1"<?php 
	                	if (isset($sports_data[0]["member"])){
	                		if ($sports_data[0]["member"] == 1 || $sports_data[0]["member"] == 3){
	                			echo "checked";
	                		}
	                	}
	                	else {
	                		echo "checked";
	                	}
	                	?>/>
            Member<br />
            <input name="nonmember" type="checkbox" value="1" <?php 
	                	if (isset($sports_data[0]["member"])){
	                		if ($sports_data[0]["member"] == 2 || $sports_data[0]["member"] == 3){
	                			echo "checked";
	                		}
	                	}
	                	else {
	                		echo "checked";
	                	}
	                	?> />
            NON-Member </td>
          <td align="left"><input type="submit" id="button3" value="Save" /></td>
          <input type="hidden" name="add" value="sports" />
          <?php if (isset($sports_data[0]["ID"])){echo "<input type=\"hidden\" name=\"ID\" value=\"" . $sports_data[0]["ID"] . "\">";}?>
        </form>
      </tr>
      <tr>
        <td width="110" align="left" valign="top" class="text_admin"> Sports</td>
      </tr>
      <?php
                $sql = "SELECT * FROM sports WHERE active = '1'";
                
                $result = $db->queryArray($sql);
				if (!is_array($result)){
                	echo "<tr><td>NO Sports!!!!! ?????</td></tr>";
                }
                else {
					foreach ($result as $data){
	                	echo "<tr><td>&nbsp;</td><td>" . $data["name"] ;
					
						if ($data["member"] == 1 || $data["member"] == 2 || $data["member"] == 3) {
	                		echo " (";
	                			if ($data["member"] == 1 || $data["member"] == 3) {
	                				echo "Members";
	                			}
	                			if ($data["member"] == 3) { echo " & ";}
	                			if ($data["member"] == 2 || $data["member"] == 3) {
	                				echo "Non-Members";
	                			}
	                			
	                		echo ")";
	                	}
	                	else {
	                		echo " (inactive!)";
	                	}
	                	
	                	echo "</td><td>";
	                	echo "<a class=\"main_text_link\" href=\"/wakecam_admin.php?p=prices&sub=edit&edit=sports&ID=" . $data["ID"] . "\">Edit</a>";
	                	echo " / ";
	                	echo "<a class=\"main_text_link\" href=\"/wakecam_admin.php?p=prices&sub=edit&del=sports&ID=" . $data["ID"] . "\">Delete</a>";
	                	echo "</td></tr>";
	                }                    
                }
                
                ?>
    </table>
	</div></div>
    <div class="bottom"></div>
    </center>
</body>
<!--Bottom End -->  