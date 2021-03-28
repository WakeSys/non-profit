<?php
//echo "<pre>";
//print_r($_POST);
//echo "<pre>";
if (!isset($_GET["nonmember"])){
	$member = 1;
}
else {
	$member = 2;
}
foreach ($_POST as $key => $elem){
	if (preg_match("/^price--([0-9]{1,3})-([0-9]{1,3})-([0-9]{1,3})$/i", $key, $match)){
		$boatID = $match[1];
		$categoryID = $match[2];
		$sportsID = $match[3];
		$price = preg_replace("/[^,.0-9]/i","",$elem);
		$price = str_replace(",",".",$price);
		if (is_numeric($price)){
			$sql = "INSERT INTO prices (boatID,categoryID,sportsID,price,member) VALUES ('" . $boatID . "','" . $categoryID . "','" . $sportsID . "','" . $price . "', '" . $member . "') ON DUPLICATE KEY UPDATE price ='" . $price . "'";
			$db->execute($sql);
			//echo $sql."<hr>";
		}
	}
	if (preg_match("/^preset-([0-9]{1,3})-([0-9]{1,3})$/i", $key, $match)){
		$boatID = $match[1];
		$categoryID = $match[2];
		$preset = $elem;
		$sportsID = 998;
		$autostop = $_POST["autostop-" . $boatID . "-" . $categoryID];
		if (is_numeric($preset)){
			$sql = "INSERT INTO prices (boatID,categoryID,sportsID,preset,autostop,member) VALUES
				('" . $boatID . "','" . $categoryID . "','" . $sportsID . "','" . $preset . "', '" . $autostop . "', '" . $member . "')
					ON DUPLICATE KEY UPDATE preset ='" . $preset . "', autostop = '" . $autostop . "'";
			$db->execute($sql);
		}
	}
}
/*
             * Werte eintragen!!!
             * sportsID, CategoryID, boatID
             * 
             * Select �* from prices!
             * $prices[boatID][catID][sportID] == XX Euro
             * 
             * 
             */
?>
<!--Bottom Beginning -->
<div class="main">
	<div class="main_text">
	<div class="main_text_title">Edit Prices for  <?php 
      	if ($member == 1){
      		echo "Prepaid/Postpaid Customers";
      	}
      	else {
      		echo "Guests";
      	}
      	?> 
<br />
<br />
All Prices MUST include VAT / sales tax!<br />
<br />
</div></td>
      </tr>
      <?php 
      $sql = "SELECT ID,name FROM sports WHERE active = '1' AND (member = '" . $member . "' OR member = 3)";
      $sports = $db->queryArray($sql);
	  

      $sql = "SELECT ID,name  FROM categories WHERE active = '1' AND (member = '" . $member . "' OR member = 3)";
      $categories = $db->queryArray($sql);
	  
      
      $sql = "SELECT ID,name FROM boats WHERE active = '1'";
      $boats = $db->queryArray($sql);
	  
      
      $sql = "SELECT * FROM prices WHERE member ='" . $member . "'";
      $prices_result = $db->queryArray($sql);
      if (is_array($prices_result)){
	      foreach ($prices_result as $data){
	      	$prices[$data["boatID"]][$data["categoryID"]][$data["sportsID"]] = $data["price"];
	      	$autostops[$data["boatID"]][$data["categoryID"]] = $data["autostop"];
	      	$presets[$data["boatID"]][$data["categoryID"]] = $data["preset"];
	      }
      }
	  
	  
	if (!is_array($categories)  || !is_array($sports) || !is_array($boats)){
		die ("<tr><td>FEHLER!!!! Boats, Categories, oder SPorts == 0!!!!!</td></tr>");
	}
	else { 
		echo "<form name=\"\" action=\"\" method = \"POST\">";?>
		
		<?php
		if ($member == 1){?>
		 <tr><td>
		       <div class="main_text_title">Price Matrix for Membership (1 Year)</div>
                
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
		          <tr>
		            <td width="<?php 
						$zuel = count($categories)+1;
						$pourcentage = 100 / $zuel;
						echo "$pourcentage";
						?>%" align="left" valign="middle">&nbsp;</td>
		            <?php
		            foreach ($categories as $category){
		            ?>
		            	<td width="<?php 
						$zuel = count($categories)+1;
						$pourcentage = 100 / $zuel;
						echo "$pourcentage";
						?>%" align="left" valign="middle"><span class="text_admin"><?php echo $category["name"];?></span></td>
		            <?php 
		            }
		            ?>
		            </tr>
		            <tr>
		            <?php 
		          echo "<td align=\"left\" valign=\"middle\"><span class=\"text_admin\">Membership</span></td>";
		   foreach ($categories as $category){
			            	?>
			            	<td align="left" valign="middle"><span class="text_admin">
			            		
			              		<input name="price--<?php echo "999-" . $category["ID"] . "-999\"";?> type="text" class="text_admin" id="textfield3" size="5" <?php 
			              		echo "value=\"";
			              		if (isset($prices["999"][$category["ID"]]["999"])){
			              			echo $prices["999"][$category["ID"]]["999"];
			              		}
			              		echo " \">";
			              		echo getPreferences("currencyHTML");
			              		?> / Year</span></td>
			            <?php 
			            }
		            ?>
		           </tr>
                   <tr>
		            <td align="left" valign="middle" colspan="<?php $zuel = count($categories)+1; echo "$zuel"; ?>"><label>
		              <input type="submit" value="Save" />
		            </label></td>
		            </tr>
		        </table>
		      </td></tr>
		      
		      
		      <?php
		      }
		      
		      
		  foreach ($boats as $boat){
	      ?>
		      <div class="main_text_title">Price Matrix for <?php echo $boat["name"];?></div>
                
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
		          <tr>
		            <td width="<?php 
						$zuel = count($categories)+1;
						$pourcentage = 100 / $zuel;
						echo "$pourcentage";
						?>%" align="left" valign="middle">&nbsp;</td>
		            <?php
		            foreach ($categories as $category){
		            ?>
		            	<td width="<?php 
						$zuel = count($categories)+1;
						$pourcentage = 100 / $zuel;
						echo "$pourcentage";
						?>%" align="left" valign="middle"><span class="text_admin"><?php echo $category["name"];?></span></td>
		            <?php 
		            }
		            ?>
		            </tr>
                    
                    
                    <tr>
			            <td align="left" valign="middle" valign="top"><span class="text_admin">&nbsp;</span></td>
			            <?php
			            foreach ($categories as $category){
			            ?>
			            <td>&nbsp;</td>
			            <?php 
			            }
			            ?>
			           		
			        </tr>
                    
                     
			          <tr>
			            <td align="left" valign="middle" valign="top"><span class="text_admin">Timer Presets</span></td>
			            <?php
			            foreach ($categories as $category){
			            	
			            ?>
			            	<td align="left" valign="middle"><span class="text_admin">
                            
							<?php
							echo "<select name=\"preset-" . $boat["ID"] . "-" . $category["ID"] . "\" id=\"select\">";
								$presetArr = array(0, 300, 600, 900, 1200, 1500, 1800, 2100, 2400, 2700, 3000, 3300, 3600);
								foreach ($presetArr as $presetSec){
									echo "<option value=\"" . $presetSec . "\"";
									if (isset($presets[$boat["ID"]][$category["ID"]]) && $presets[$boat["ID"]][$category["ID"]] == $presetSec){
										echo " selected";
									}
									echo ">" ;
									if ($presetSec > 0){
										echo $presetSec/60;
										echo "' Preset";
									}
									else {
										echo "No Preset";
									}
									echo "</option>";
								}
							?>
		                  </select>
		                </label></td>
			              <?php 
			            }
			            ?>
			           		
			          </tr>
                      
                      <tr>
			            <td align="left" valign="middle" valign="top"><span class="text_admin">AutoStop?</span></td>
			            <?php
			            foreach ($categories as $category){
			            	
			            ?>
			            <td align="left" valign="middle"><span class="text_admin">	
						<label>
  						<?php
							echo "<input type=\"radio\" name=\"autostop-" . $boat["ID"] . "-" . $category["ID"] . "\" id=\"radio\" value=\"yes\" ";
								if (isset($autostops[$boat["ID"]][$category["ID"]]) && $autostops[$boat["ID"]][$category["ID"]] == "yes"){
									echo "checked";
								}
							echo "/>";
						?>
						</label> Yes <label>
  						<?php
							echo "<input type=\"radio\" name=\"autostop-" . $boat["ID"] . "-" . $category["ID"] . "\" id=\"radio\" value=\"no\" ";
								if (isset($autostops[$boat["ID"]][$category["ID"]]) && $autostops[$boat["ID"]][$category["ID"]] != "yes"){
									echo "checked";
								}
							echo "/>";
						?>
						</label> No</span></td>
			              <?php 
			            }
			            ?>
			           		
			          </tr>
                      
                      <tr>
			            <td align="left" valign="middle" valign="top"><span class="text_admin">&nbsp;</span></td>
			            <?php
			            foreach ($categories as $category){
			            ?>
			            <td>&nbsp;</td>
			            <?php 
			            }
			            ?>
			           		
			        </tr>
		          
                  
                  
		          <?php 
		          foreach ($sports as $sport){
		          ?>
			          <tr>
			            <td align="left" valign="middle"><label>
			            <span class="text_admin"><?php echo $sport["name"];?></span></td>
			            <?php
			            foreach ($categories as $category){
			            	
			            ?>
			            	<td align="left" valign="middle"><span class="text_admin">
			            		
			              		<input name="price--<?php echo $boat["ID"] . "-" . $category["ID"] . "-" . $sport["ID"];?>" type="text" class="text_admin" id="textfield3" size="5" <?php 
			              		echo "value=\"";
			              		if (isset($prices[$boat["ID"]][$category["ID"]][$sport["ID"]])){
			              			echo $prices[$boat["ID"]][$category["ID"]][$sport["ID"]];
			              		}
			              		echo " \">";
			              		echo getPreferences("currencyHTML");?> / min</span></td>
			              <?php 
			            }
			            ?>
			           		
			          </tr>
		          <?php 
		          }
		          ?>
		           <tr>
			            <td align="left" valign="middle"><span class="text_admin">Additional Ballast</span></td>
			            <?php
			            foreach ($categories as $category){
			            	?>
			            	<td align="left" valign="middle"><span class="text_admin">
			            		
			              		<input name="price--<?php echo $boat["ID"] . "-" . $category["ID"] . "-999\"";?> type="text" class="text_admin" id="textfield3" size="5" <?php 
			              		echo "value=\"";
			              		if (isset($prices[$boat["ID"]][$category["ID"]]["999"])){
			              			echo $prices[$boat["ID"]][$category["ID"]]["999"];
			              		}
			              		echo " \">";
			              		echo getPreferences("currencyHTML");?> / min</span></td>
			            <?php 
			            }
			            ?>
			       </tr>   
			      <tr>
		            <td align="left" valign="middle" colspan="<?php $zuel = count($categories)+1; echo "$zuel"; ?>"><label>
		              <input type="submit" value="Save" />
		            </label></td>
		            </tr>
		              
		        </table></td>
		      </tr> 
		     
		      <tr>
		        <td colspan="3" align="left">&nbsp;</td>
		      </tr>
	      <?php
	      }
	      echo "</form>";
		}
      ?>
</div>
<div class="bottom"></div>
</center>
</body>
<!--Bottom End -->