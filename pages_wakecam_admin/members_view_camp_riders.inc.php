

<div class="main">
	<div class="main_text">
    <div class="main_text_title">View active Postpaid Customers</div>
    <table width="980" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100">First Name</td>
        <td width="100">Last Name</td>
        <td width="180">Phone</td>
        <td width="250">Email</td>
        <td width="120">Price Cat</td>
        <td width="70">Prepaid</td>
        <td width="160">&nbsp;</td>
      </tr>
       <?php
      $sql = "SELECT A.*,B.name as categoryName FROM members AS A LEFT JOIN categories AS B ON A.categoryID=B.ID WHERE A.campRider = 'yes' AND A.active = 1";
      $members =$db->queryArray($sql);
      if (is_array($members)){
	      foreach ($members as $member){
	      	echo "<tr>";
	      		echo "<td>" . $member["first_name"] . "</td>";
	      		echo "<td>" . $member["last_name"] . "</td>";
	      		echo "<td>" . $member["phone_number"] . "</td>";
	      		echo "<td><a class=\"link\" href=\"mailto:" . $member["mail"] . "\">" . $member["mail"] . "</a></td>";
	      		echo "<td>" . $member["categoryName"] . "</td>";
			$credit = getCurrentCredit($member["ID"]);
                        if ($credit < 0){
                            echo "<td style=\"color:red;\">" . $credit . "</td>";
                        }
                        else {
                            echo "<td>" . $credit . " " . getPreferences("currencyHTML") . "</td>";
                        }
	        	echo "<td><a class=\"link\" href=\"" . INDEX . "?p=members&sub=add&view=" . $member["ID"] . "\">View</a> / <a class=\"link\" href=\"" . INDEX . "?p=members&sub=add&edit=" . $member["ID"] . "\">Edit</a></td>";
	      	echo "</tr>";
	      }
      }
      else {
      	echo "<tr><td>No Data</td></tr>";
      }
      ?>
      
    </table>
    <br /><br />
    <div class="main_text_title">View inactive Postpaid Customers</div>
    <table width="980" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100">First Name</td>
        <td width="100">Last Name</td>
        <td width="180">Phone</td>
        <td width="250">Email</td>
        <td width="120">Price Cat</td>
        <td width="70">Prepaid</td>
        <td width="160">&nbsp;</td>
      </tr>
      <?php
      $sql = "SELECT A.*,B.name as categoryName FROM members AS A LEFT JOIN categories AS B ON A.categoryID=B.ID WHERE A.campRider = 'inactive'";
      $members =$db->queryArray($sql);
      if (is_array($members)){
      	 foreach ($members as $member){
	      	echo "<tr>";
	      		echo "<td>" . $member["first_name"] . "</td>";
	      		echo "<td>" . $member["last_name"] . "</td>";
	      		echo "<td>" . $member["phone_number"] . "</td>";
	      		echo "<td><a class=\"link\" href=\"mailto:" . $member["mail"] . "\"" . $member["mail"] . "</a></td>";
	      		echo "<td>" . $member["categoryName"] . "</td>";
			$credit = getCurrentCredit($member["ID"]);
                        if ($credit < 0){
                            echo "<td style=\"color:red;\">" . $credit . "</td>";
                        }
                        else {
                            echo "<td>" . $credit . " " . getPreferences("currencyHTML") . "</td>";
                        }
	        	echo "<td><a class=\"link\" href=\"" . INDEX . "?p=members&sub=add&view=" . $member["ID"] . "\">View</a> / <a class=\"link\" href=\"" . INDEX . "?p=members&sub=add&edit=" . $member["ID"] . "\">Edit</a><br /> Reactivate / Delete</td>";
	      	echo "</tr>";
	      }
      }
      else {
      	echo "<tr><td>No Postpaid Customers yet. To add a member please click on the Add Button.</td></tr>";
      }
	     ?>
      
    </table>
	</div>
    
</div>

<div class="bottom"></div> 
</center>
</body>