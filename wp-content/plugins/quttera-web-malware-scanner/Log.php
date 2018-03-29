<?php

require_once('qtrLogger.php');

$logger = new CQtrLogger();
$lines = $logger->GetAllLines();

foreach($lines as $line){
    printf("%s %s\n",$line[1],$line[2]);
}

?>
