<?php


$ip = $_SERVER['REMOTE_ADDR'];
$ipAllowed = '130.185.249.127 130.185.251.53';
$isAllowed = strpos($ipAllowed, $ip) !== false;

if (!$isAllowed) exit('denied');

$files = glob('*.txt');

foreach ($files as $filepath) {
	$filename = basename($filepath);
	echo "<PRE><a href='$filename'>$filename</a><BR>";
}


?>
