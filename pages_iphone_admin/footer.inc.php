</div>	
</body>
</html>
<?php
if (strstr($_SERVER['HTTP_USER_AGENT'],"iPad")){
    echo "<link rel=\"stylesheet\" href=\"/iphone_admin/css/styles_ipad.css\" />";
}
else if(strstr($_SERVER['HTTP_USER_AGENT'],'Android') ||
		strstr($_SERVER['HTTP_USER_AGENT'],'webOS') ||
		strstr($_SERVER['HTTP_USER_AGENT'],"iPhone") ||
        strstr($_SERVER['HTTP_USER_AGENT'],"iPod")){
    echo "<link rel=\"stylesheet\" href=\"/iphone_admin/css/styles_iphone_2G-3GS.css\" />";
}
else {
    echo "<link rel=\"stylesheet\" href=\"/iphone_admin/css/styles_safari.css\" />";
}
?>
<script type="text/javascript" src="/iphone_admin/js/script.js"></script>