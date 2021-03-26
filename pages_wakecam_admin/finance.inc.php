<div class="main">	
    <div class="navi_2_padding">
	<table border="0" cellpadding="0" cellspacing="0" class="navi_2_bg">
	  <tr>
      	<td width="10"><img src="wakecam_admin_new/images/navi_left_darker.gif" width="8" height="35" /></td>
        <td width="380">&nbsp;</td>
    	<td>
        <div class="navi_text">
		<a class="navi_darker_text_link" href="<?php echo INDEX;?>?p=finance&sub=invoices">Paid<span class="navi_darker_text_link_invisible">_</span>Invoices</a>
		<a class="navi_darker_text_link" href="<?php echo INDEX;?>?p=finance&sub=balance_sheet">Balance<span class="navi_darker_text_link_invisible">_</span>Sheet</a>
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
if (isset($_GET["sub"]) && file_exists("../pages_wakecam_admin/" . $_GET["p"] . "_" . $_GET["sub"] . ".inc.php")){
	include("../pages_wakecam_admin/" . $_GET["p"] . "_" . $_GET["sub"] . ".inc.php");
}
else {?>
<!--Bottom Beginning -->  
<div class="main">
	<div class="main_text">
	<div class="main_text_title">Welcome to the Finance Admin Interface</div>
	The finance interface gives you an overview about all the financial activities of your school.
	</div>
</div>

<div class="bottom"></div>

</center>
</body>

<!--Bottom End -->
<?php }
?>