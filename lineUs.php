<?php
class lineUs
{
function readOutput($client) {
  $Zdata = stream_get_contents($client);
  return $Zdata;
}
###################
## START OF MAIN ##
###################
function send($gcode,$addr= "line-us.local", $port = "1337") {
  if ($addr == "") {die("Unable to find line-us bot!");}
  $client = @stream_socket_client("tcp://$addr:$port", $errno, $errorMessage);
  stream_set_timeout($client, 1);
  if($client === false){die("Unable to communicate to bot!");}
  $gcode = str_replace("\n","\0\r",$gcode);
  $array = explode( "\r", $gcode );
  $lines = SplFixedArray::fromArray($array);
  unset( $array );
    foreach ($lines as $line) {
      if (readOutput($client)) {fwrite($client, $line);}
    }
  fwrite($client, "\0");
  fclose($client);
}
function sendArray($gcode,$addr= "line-us.local", $port = "1337") {
  if ($addr == "") {die("Unable to find line-us bot!");}
  $client = @stream_socket_client("tcp://$addr:$port", $errno, $errorMessage);
  stream_set_timeout($client, 1);
  if($client === false){die("Unable to communicate to bot!");}
  $lines = SplFixedArray::fromArray($gcode);
  unset( $gcode );
    foreach ($lines as $line) {
      if (readOutput($client)) {fwrite($client, $line);}
    }
  fwrite($client, "\0");
  fclose($client);
}
function getIp($port = "1337") {
  ignore_user_abort(true);     ## prevent refresh from aborting file operations and hosing file
  $counter = 0;
  while ($counter <= 254) {
    $connection = @fsockopen("192.168.0.".$counter, $port, $errno, $errstr, 0.5);
    if (is_resource($connection)) {
      fclose($connection);
      $ip = "192.168.0.".$counter;
      break;
    }
    $counter++;
  }
ignore_user_abort(false);
return $ip;
}
}
$lineUs = new lineUs();
?>
