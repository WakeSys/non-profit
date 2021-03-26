<?php
if (isset($_POST["payDriver"])){
	setPreferences(str_replace(",",".",$_POST["payDriver"]),"payDriver");
}
$preferences = getPreferences();
?>

<form method="POST" action="">
	<div class="main">
		<div class="main_text">
		<div class="main_text_title">Edit Prices for Boat Driver</div>
		
	    <table width="100%" border="0" cellspacing="0" cellpadding="0">
	      <tr>
	        <td width="100" align="left">Money recieved</td>
	        <td colspan="2" align="left"><label>
	          <input name="payDriver" type="text" value="<?php echo $preferences[0]["payDriver"];?>" size="5" />
	        </span><span class="text_admin"><?php echo getPreferences("currencyHTML");?> / min</label></td>
	        </tr>
	
	    </table>
	    
	    
		</div>
	</div>
	<input type="submit" value="change">
</form>
<div class="bottom"></div>
</center>
</body>  

      
<!--Bottom End -->