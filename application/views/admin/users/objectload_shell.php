<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

ob_implicit_flush(true);
while (@ob_end_flush()) {}

$command = $_GET["command"];
$proc = popen("php index.php --task=Objectload ".$command." &", 'r');
echo "data: ".$proc."\n";
	echo "\n";
	
$i=0;
while (!feof($proc)) {
	echo "data: ".fread($proc, 4096)."\n";
	echo "\n";
}

pclose($proc);
echo "data: stop\n\n";