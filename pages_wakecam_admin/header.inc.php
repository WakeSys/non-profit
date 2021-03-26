
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>WakeSys Admin Panel</title>
<link href="/wakecam_admin_new/styles/styles.css" rel="stylesheet" type="text/css" />
<link href="/wakecam_admin_new/jquery-ui-1_8_2/css/ui-lightness/jquery-ui-1.8.2.custom.css" rel="stylesheet" type="text/css" />
<script src="/wakecam_admin_new/jquery-ui-1_8_2/js/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="/wakecam_admin_new/jquery-ui-1_8_2/js/jquery-ui-1.8.2.custom.min.js" type="text/javascript"></script>
</head>

<body>
<center>
<!--Logo & Navi Bar -->
<div class="box">
<div class="main_top"></div>
<div class="logo_and_navi_bg">
	<div class="navi_padding">
	<table border="0" cellpadding="0" cellspacing="0" class="navi_bg">
	  <tr>
    	<td width="10"><img src="/wakecam_admin_new/images/navi_left.gif" width="10" height="35" /></td>
    	<td><div class="navi_text">
        <a class="navi_text_link" href="<?php echo INDEX;?>?p=home">Home</a>
  		<a class="navi_text_link" href="/admin.php?p=logbook">Logbook</a>
 		<a class="navi_text_link" href="/admin.php?p=prices">Prices</a>
  		<a class="navi_text_link" href="/admin.php?p=members">Riders</a>
  		<a class="navi_text_link" href="/admin.php?p=statistics">Statistics</a>
  		<a class="navi_text_link" href="/admin.php?p=finance">Finance</a>
  		<a class="navi_text_link" href="/admin.php?p=preferences">Preferences</a>
  		<?php
    	if (isset($_SESSION["user_id"]) && strlen($_SESSION["user_id"])){
    		echo "<a class=\"navi_text_link\" href=\"/admin.php?p=logout\">";
    		echo "Logout";
    		echo "</a>";
    	}
    	?>     
       </div>
        </td>
    	<td width="10"><img src="/wakecam_admin_new/images/navi_right.gif" width="10" height="35" /></td>
  	  </tr>
	</table>
	</div>
</div>
