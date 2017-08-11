<?php
date_default_timezone_set("America/Bogota");
header("Content-Type: text/event-stream\n\n");

$counter = rand(1, 10);
while (1) {
  // Every second, sent a "ping" event.
  
  echo "event: ping\n";
  $curDate = date(DATE_ISO8601);
  echo 'data: {"time": "' . $curDate . '"}';
  echo "\n\n";
  
  echo "event: edalmava\n";
  echo "data: Edwin Alberto Martinez Vanegas";
  echo "\n\n";
  
  // Send a simple message at random intervals.
  
  $counter--;
  
  if (!$counter) {
    echo 'data: Este es un mensaje at time ' . $curDate . "\n";
	echo "data: Probando\n\n";
    $counter = rand(1, 10);
  }
  
  echo ": Comentario\n\n";
  
  ob_flush();
  flush();
  sleep(1);
}