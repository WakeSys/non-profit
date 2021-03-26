<?php 
//echo "<pre>";print_r($_POST);echo "</pre>";
if (!isset($_POST["UserID"]) || !is_numeric($_POST["UserID"])){die ("ERROR");}

if (isset($_POST["extend"]) && $_POST["extend"] == "yes"){
	
	extendMembership($_POST["UserID"]);
}
$sql = "SELECT A.*, B.name AS catName FROM members AS A LEFT JOIN categories AS B ON A.categoryID=B.ID WHERE A.ID = '" . $_POST["UserID"]. "'";
$data = $db->queryArray($sql);

echo "--TITLE--Show Member--";
/*
[group] => member
    [cat] => 4
    [riderID] => 4
    [UserID] => 4
*/

if (isset($_POST["group"]) ){
	echo "--BACK_TITLE--ride--";
	echo "--BACK_PAGE--ride--";
	echo "--BACK_TITLE--back--";
	$i=0;
	$backoptions = "";
	foreach ($_POST as $key => $elem){
		if ($key != "UserID"){
			if ($i>0){ $backoptions .= "&";}
			$backoptions .= $key . "=" . $elem;
			$i++;
		}
	}
	echo "--BACK_OPTIONS--" . $backoptions . "--";
}
else {
	echo "--BACK_TITLE--members--";
	
	if ($data[0]["campRider"] == "yes"){
		//echo "backcamp????";
		echo "--BACK_OPTIONS--active--";
		echo "--BACK_PAGE--camp_members--";
	}
	else {
		echo "--BACK_PAGE--members--";
	}
}
echo "--BUTTON_EDIT--" . $_POST["UserID"] . "--";

?>
<h2>Invoices/Category</h2>

<ul>
	<li>Category<span class="secondaryWLink"><?php echo $data[0]["catName"];?></span></li>
	
	<?php
        if ($data[0]["campRider"] != "yes"){
            $credits = getCurrentCredit($_POST["UserID"]);
            if(!isset($credits) || $credits == null){
                    $credits = 0;
            }


            echo "<li><a href=\"javascript: LoadPage('invoice_detail','memberID=" .  $_POST["UserID"]. "&showmember=" .  $_POST["UserID"]. "');\">Edit Prepaid";
                    echo "<span class=\"showArrow secondaryWArrow\"";
                            if ($credits < 0) {
                                    echo " style=\"color:red;\"";
                            }
                            echo ">" . $credits . " " . getPreferences("currencyHTML") . "";
                    echo "</span>";

            echo "</a></li>";
        }
        ?>
</ul>
	

<?php 
	//Membership nur bei Members!
	if($data[0]["campRider"] == "no"){
		$sql = "SELECT UNIX_TIMESTAMP(end) as end from membership WHERE memberID = '" .  $_POST["UserID"]. "' AND end > '" . date("Y-m-d") . "' ORDER BY end DESC LIMIT 0,1";
		$membership = $db->querySingleItem($sql);
		if ($membership > (time() + 60*60*24*31*3) ){
			echo "<h2>Membership until " . date("d.m.Y", $membership). "</h2>";
			echo "<ul>";
				echo "<li>no extension possible!</li>";
			echo "</ul>";
		}
		elseif (is_numeric($membership) && $membership > 0 ){
			echo "<h2>Membership until " . date("d.m.Y", $membership). "</h2>";
			echo "<ul>";
				echo "<a href=\"javascript: LoadPage('extendMembership','" . $_POST["UserID"] . "');\"><li>Extend + 1 Year</li></a>";	
		}
		else {
			echo "<h2>no valid membership</h2>";
			echo "<ul>";
			echo "<a href=\"javascript: LoadPage('extendMembership','" . $_POST["UserID"] . "');\"><li>buy 1 year</li></a>";
		}
	}

?>
</ul>

<h2>General Information</h2>

<ul>    
	<li>First Name<span class="secondaryWLink"><?php echo $data[0]["first_name"];?></span></li>
	<li>Last Name<span class="secondaryWLink"><?php echo $data[0]["last_name"];?></span></li>
	<li><a href="tel:<?php echo $data[0]["phone_number"];?>">Phone Number<span class="showArrow secondaryWArrow"><?php echo $data[0]["phone_number"];?></span></a></li>
	<li><a href="mailto:<?php echo $data[0]["mail"];?>">Email<span class="showArrow secondaryWArrow"><?php echo $data[0]["mail"];?></span></a></li>
	<li>Social Security<span class="secondaryWLink"><?php echo $data[0]["social_security"];?></span></span></li>
	<li>Birthday<span class="secondaryWLink"><?php 
	preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/",$data[0]["birthday"],$match);
	echo $match[3] . "." . $match[2] . "." . $match[1];
	?></span></span></li>
	<li><a href="http://maps.google.com/maps?q=<?php echo $data[0]["address"];?>,<?php echo $data[0]["postal_code"];?>,<?php echo $data[0]["town"];?>,<?php echo $data[0]["country"];?>">Address<span class="showArrow secondaryWArrow"><?php echo $data[0]["address"];?></span></a></li>
	<li>Postal Code<span class="secondaryWLink"><?php echo $data[0]["postal_code"];?></span></span></li>
	<li>Town<span class="secondaryWLink"><?php echo $data[0]["town"];?></span></li>
	<li>Country<span class="secondaryWLink"><?php echo $data[0]["country"];?></span></li>
</ul>
    
<h2>Preconfigurations</h2>

<ul>   
	<li>Ballast<span class="secondaryWLink"><a><img src="/iphone_admin/images/<?php if($data[0]["ballast"] == "yes"){echo "yes";}else{echo "no";}?>.png"></a></span></li>
	<li>Driver<span class="secondaryWLink"><a><img src="/iphone_admin/images/<?php if($data[0]["driver"] == 1){echo "yes";}else{echo "no";}?>.png"></a></span></li>
</ul>

<h2>Facebook</h2>
<ul>   
	<li>Personal mail<span class="secondaryWLink">*****</span></li>
	<li>Push to Facebook<span class="secondaryWLink"><a><img src="/iphone_admin/images/<?php if($data[0]["facebookON"] == 1){echo "yes";}else{echo "no";}?>.png"></a></span></li>
</ul>
    
       
    		
    <div class="start">	
		<li><a href="javascript: LoadPage('edit_memberID','<?php echo $_POST["UserID"];?>');">Edit</a></li>
	</div> 	
