<?
echo '<div class="main">';
	echo '<div class="navi_2_padding">';
		echo '<table border="0" cellpadding="0" cellspacing="0" class="navi_2_bg">';
			echo '<tr>';
				echo '<td width="10"><img src="wakecam_admin_new/images/navi_left_darker.gif" width="8" height="35" /></td>';
				echo '<td width="380">&nbsp;</td>';
				echo '<td>';
					echo '<div class="navi_text">';
						echo '<a class="navi_darker_text_link" href="' . INDEX . '?p=finance&sub=invoices">Paid<span class="navi_darker_text_link_invisible">_</span>Invoices</a>';
						echo '<a class="navi_darker_text_link" href="' . INDEX .'?p=finance&sub=balance_sheet">Balance<span class="navi_darker_text_link_invisible">_</span>Sheet</a>';
					echo '</div>';
				echo '</td>';
				echo '<td width="10"><img src="wakecam_admin_new/images/navi_right_darker.gif" width="10" height="35" /></td>';
			echo '</tr>';
		echo '</table>';
	echo '</div>';
echo '</div>';
echo '<div class="spacer"></div>';
echo '<!--Video BOX End -->';
if (
	isset($_GET["sub"]) && 
	file_exists("../pages_wakecam_admin/" . $_GET["p"] . "_" . $_GET["sub"] . ".inc.php")
)
{
	include("../pages_wakecam_admin/" . $_GET["p"] . "_" . $_GET["sub"] . ".inc.php");
}
else 
{
	echo '<!--Bottom Beginning -->';
	echo '<div class="main">';
	echo '<div class="main_text">';
	echo '<div class="main_text_title">Welcome to the Finance Admin Interface</div>';
	echo 'The finance interface gives you an overview about all the financial activities of your school.';
	echo '</div>';
	echo '</div>';

	echo '<div class="bottom"></div>';

	echo '</center>';
	echo '</body>';

	echo '<!--Bottom End -->';
}