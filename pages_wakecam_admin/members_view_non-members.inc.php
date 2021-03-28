<div class="main">
	<div class="main_text">
	<div class="main_text_title">View Guests</div>
	<form action="" method="POST">
	Choose Year:

	  <?php
	  if (isset($_POST["Y"])){
	  	$_SESSION["view_non-member"]["Y"] = $_POST["Y"];
	  }
	  if (!isset($_SESSION["view_non-member"]["Y"])){
	  	$_SESSION["view_non-member"]["Y"] = date("Y");
	  }
	  $i = date("Y");
	  while ($i>=2009){
	  	$yearArr[] = $i;
	  	$i--;
	  }
	  echo "<select name=\"Y\" onchange=\"this.form.submit();\">";
	  foreach ($yearArr as $year){
	  	echo "<option value=\"" .  $year ."\"";
	  	if ($_SESSION["view_non-member"]["Y"] == $year){
	  		echo " selected";
	  	}
	  	echo ">" . $year . "</option>";
	  }
	  ?>

      </select>
	<br />
          Choose Month:
  <select name="m" id="select2" onchange="this.form.submit();">
  <?php
  if (isset($_POST["m"])){
  	$_SESSION["view_non-member"]["m"] = $_POST["m"];
  }
  if (!isset($_SESSION["view_non-member"]["m"])){
  	$_SESSION["view_non-member"]["m"] = date("n");
  }
  	$MonthArr[1] = "January";
  	$MonthArr[2] = "February";
  	$MonthArr[3] = "March";
  	$MonthArr[4] = "April";
  	$MonthArr[5] = "May";
  	$MonthArr[6] = "June";
  	$MonthArr[7] = "July";
  	$MonthArr[8] = "August";
  	$MonthArr[9] = "September";
  	$MonthArr[10] = "October";
  	$MonthArr[11] = "November";
  	$MonthArr[12] = "December";
  	$MonthArr[13] = "all months";
  	foreach ($MonthArr as $key=>$month){
  		echo "<option value=\"" . $key . "\"";
  		if ($key == $_SESSION["view_non-member"]["m"]){
  			echo " selected";
  		}
  		echo ">" . $month . "</option>";
  	}
  ?>
  </select>
   
  </form>
      <br /><br />

    <table width="600" border="0" cellspacing="0" cellpadding="0">
        <?php
        $sql = "SELECT A.ID, A.riderName, B.nonMemberMail, C.start
            FROM rides AS A
            LEFT JOIN invoices AS B ON A.ID = B.rideID
            LEFT JOIN rideTimes AS C ON C.rideID = A.ID
            WHERE riderID = 0
            ";

        if ($_SESSION["view_non-member"]["m"] != 13){
            $sql .= " AND A.ID IN (SELECT rideID FROM rideTimes WHERE start BETWEEN '" . mktime(null,null,null,$_SESSION["view_non-member"]["m"],1,$_SESSION["view_non-member"]["Y"]) . "' AND '" . mktime(null,null,null,$_SESSION["view_non-member"]["m"]+1,0,$_SESSION["view_non-member"]["Y"]) . "') ";
        }
        else {
            $sql .= " AND A.ID IN (SELECT rideID FROM rideTimes WHERE start BETWEEN '" . mktime(null,null,null,1,1,$_SESSION["view_non-member"]["Y"]) . "' AND '" . mktime(null,null,null,12,0,$_SESSION["view_non-member"]["Y"]) . "') ";
        }

        $sql .= " GROUP BY A.ID";

        if (isset($_Get["order"])){
            $sql .= " ORDER BY riderName";
            $sql .= " ORDER BY nonMemberMail";
            $sql .= " ORDER BY start DESC";
            $sql .= " ORDER BY start ASC";
        }
        else {
            $sql .= " ORDER BY start";
        }
        

         
        $nonMemberData = $db->queryArray($sql);

        if (is_array($nonMemberData)){
            echo "<tr class=\"bg_stats\">
                <td width=\"150\"> Name</td>
                <td width=\"250\">Email</td>
                <td width=\"200\">Last trime at your spot</td>
              </tr>";
            $i=0;
            foreach ($nonMemberData as $data){
                if ($i%2){
                        echo "<tr class=\"bg_stats\">";
                }
                else {
                        echo "<tr class=\"bg_stats_lite\">";
                }
                    echo "<td>" . $data["riderName"] . "</td>";
                    if (isset($data["nonMemberMail"]) && strlen ($data["nonMemberMail"]) > 5){
                        echo "<td><a class=\"link\" href=\"mailto:" . $data["nonMemberMail"] . "\">" . $data["nonMemberMail"] . "</a></td>";
                    }
                    else {
                        echo "<td>-</td>";
                    }
                    
                    echo "<td>" . date("d\.m\.Y", $data["start"]) . "</td>";
                echo "</tr>";
                $i++;
            }
        }
        else {
            echo "<tr><td>no data available!</td></tr>";
        }
        ?>
      
    </table>
	</div>
    
</div>

<div class="bottom"></div> 
</center>
</body>