<div class="main">	
    <div class="navi_2_padding">
	<table border="0" cellpadding="0" cellspacing="0" class="navi_2_bg">
	  <tr>
      	<td width="10"><img src="wakecam_admin_new/images/navi_left_darker.gif" width="8" height="35" /></td>
        <td width="380">&nbsp;</td>
    	<td>
        <div class="navi_text">
	<a class="navi_text_link" href="<?php echo INDEX;?>?p=prices&sub=members">Members<span class="navi_darker_text_link_invisible">_</span>and<span class="navi_darker_text_link_invisible">_</span>Camp<span class="navi_darker_text_link_invisible">_</span>Riders</a>
    <a class="navi_text_link" href="<?php echo INDEX;?>?p=prices&sub=non_members">Non<span class="navi_darker_text_link_invisible">_</span>Members</a>
    <a class="navi_text_link" href="<?php echo INDEX;?>?p=prices&sub=edit">Edit</a>
       </div>
        </td>
    	<td width="10"><img src="wakecam_admin_new/images/navi_right_darker.gif" width="10" height="35" /></td>
  	  </tr>
	</table>
    </div>
</div>
<div class="spacer"></div>
<!--Video BOX End -->
<?php
if (isset($_GET["sub"]) && $_GET["sub"] == "non_members"){
	$_GET["sub"] = "members";
	$_GET["nonmember"] = "yes";
}

if (isset($_GET["sub"]) && file_exists("../pages_wakecam_admin/" . $_GET["p"] . "_" . $_GET["sub"] . ".inc.php")){
	include("../pages_wakecam_admin/" . $_GET["p"] . "_" . $_GET["sub"] . ".inc.php");
}
else {?>

<div class="main">
	<div class="main_text">
	<div class="main_text_title">Welcome to the Price Admin Interface</div>
	In the navigation above you can chose which prices you want to change. You can change prices for members, non members, and the amount of money which drivers get paid to their credit line by choosing others.
	</div>
</div>
<div class="bottom"></div>
</center>
</body>

<?php 
}
?>