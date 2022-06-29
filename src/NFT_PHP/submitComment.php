<?php

session_start();
echo "<PRE>";

//print_r($_POST);

$productId = $_POST['productId'];

$comments = $_POST['comments'];

if ($comments == null) {
	header("Location: ./?productId=$productId");
	exit;
}


$comments = preg_replace("/[^a-zA-Z0-9\.\- ]/", '', $comments);

$ip = $_SERVER['REMOTE_ADDR'];

//echo "\n $productId \n $comments \n $ip";

$unixtime = time();
$datetimeUTC = gmdate("Y-m-d H:i:s", $unixtime);
$date = gmdate("Y-m-d", $unixtime);

$session = $_SESSION;
unset($session['scache']);


$out .= "date: $datetimeUTC\n";
$out .= "productId: $productId\n";
$out .= "$comments\n";
$out .= json_encode($_SERVER, JSON_PRETTY_PRINT);
$out .= json_encode($session, JSON_PRETTY_PRINT);
$out .= "\nend____________________________\n";

$savepath = "./comments/$ip $date.txt";

file_put_contents($savepath, $out, FILE_APPEND);

 
header("Location: ./?productId=$productId&commentsubmitted");


?>

