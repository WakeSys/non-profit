<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name = "viewport" content = "width = device-width, height = device-height, user-scalable = no" />
    <?php
        if (strstr($_SERVER['HTTP_USER_AGENT'],"iPad")){
            echo "<link rel=\"apple-touch-startup-image\" href=\"iphone_admin/css/images/loading-screen_ipad.png\">";
        }
        else {
            echo "<link rel=\"apple-touch-startup-image\" href=\"iphone_admin/css/images/loading-screen.png\">";
        }
        ?>
    	<title>WakeSys</title>
    
    <link rel="apple-touch-icon" href="/iphone_admin/apple-touch-icon.png"/>
</head>

<body>
<div class="table_back" >
	
	<div class="table_plus_button" id="header_plus"><?php
		if (isset($home) && $home == "true"){
			echo "<a href=\"javascript: LoadPage('home');\"><img src=\"iphone_admin/css/images/home.png\" width=\"30\" height=\"30\" alt=\"home\" /></a>";
		}
	?></div>
	<div class="table_back_button" id ="header_back"><?php
	if (isset($back_title) && isset($back_page)){
		echo "<a href=\"javascript: LoadPage('" . $back_page . "','');\" class=\"button\">" . $back_title . "</a>";
	}
	?></div>

	<div class="table_back_top" id ="header_title"><?php
		if (isset($title)){
			echo $title;
		}
		else {
			//echo "<img src=\"iphone_admin/images/wakesys_logo.png\" width=\"140\" height=\"25\" alt=\"WakeSys\" />";
		}
	?></div>
</div>


<div id="wakecam">