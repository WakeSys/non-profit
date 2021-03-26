<?php
include('../functions/db.inc.php');
$db = new MyDB();


$fh = fopen("paypal.txt","a+");

$text = "\r\n--------------\r\n";

$text .= "POST\r\n";
foreach ($_POST as $key => $elem){
	$text .= $key . "===>>>" . $elem ."\r\n"; 
}
$text .= "\r\n--------------\r\nSQL:\r\n";


$sql = "INSERT INTO paypal (";
$i=0;
foreach ($_POST as $key => $elem){
       	if ($i != 0){$sql .= ", ";}
        $sql .= $key;
        $i++;
}
$sql .= ") VALUES (";
$i=0;
foreach ($_POST as $key => $elem){                
        if ($i != 0){$sql .= ", ";}
        $sql .= "'" . $db->escape($elem) . "'";
	$i++;
}
$sql .= ")";
$text .= $sql;                        

$db->execute($sql);
if ($db->error()){
	mail("info@bastimueller.de","Wakesystems PayPal to MySQL Error!!!",$sql,"from:info@bastimueller.de\nBCC:boeinglover@gmail.com");
}
$text .= "\r\n--------------\r\n";


fwrite($fh,$text);
fclose($fh);
?>