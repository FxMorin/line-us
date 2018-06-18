<?php
//Warning this file has no error logging or process updates!!! Its only for speed!
ignore_user_abort(true);
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "Line-Us";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT Active FROM GlobalVar";
$result = $conn->query($sql);
if ($result->num_rows == 1) {
  $row = $result->fetch_assoc();
  if ($row["Active"] == 1) {$conn->close();die("Already Running!!");}
}
function Active($conn, $stat) {
  $sql = "UPDATE GlobalVar SET Active='".$stat."'";
  if ($conn->query($sql) === TRUE) {
  }
}
function perc($count, $amount) {
  return (($count/$amount)*100);
}
function putIp($conn, $ip) {
    $sql = "UPDATE GlobalVar SET ip='".$ip."'";
    if ($conn->query($sql) === TRUE) {
    }
}
function getIp($conn) {
    $sql = "SELECT ip FROM GlobalVar";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    return $row['ip'];
  }
}
function get_hello_message($client) {
  $line = stream_get_contents($client);
  return $line;
}
function getBotIp($conn) {
  $ip = getIp($conn);
  if ($ip != "") {
    $connection = @fsockopen($ip, $port, $errno, $errstr, 0.5);
    if (is_resource($connection)) {
      fclose($connection);
      return $ip;
    }
  }
  $counter = 0;
  $port = "1337";
  while ($counter <= 254) {
    $connection = @fsockopen("192.168.0.".$counter, $port, $errno, $errstr, 0.3);
    if (is_resource($connection)) {
      fclose($connection);
      $ip = "192.168.0.".$counter;
      putIp($conn, $ip);
      break;
    }
    $counter++;
  }
return $ip;
}
function readOutput($client) {
  $Zdata = stream_get_contents($client);
  return $Zdata;
}
function start($conn) {
$gcode = "";
$output = "";
$count = 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $sql = "SELECT Place, GCode FROM Queue ORDER BY Place ASC LIMIT 1";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $gcode = $row['GCode'];
    $sql = "DELETE FROM Queue WHERE Place='".$row['Place']."'";
    if ($conn->query($sql) === TRUE) {
    }
  } else {
    Active($conn, 0);
    $conn->close();
    exit;
  }
  Active($conn, 1);
  $addr = getBotIp($conn);
  if ($addr == "") {
    Active($conn, 0);
    $conn->close();
    die("Unable to find line-us bot!");
  }
  $port = "1337";
  $client = @stream_socket_client("tcp://$addr:$port", $errno, $errorMessage);
  stream_set_timeout($client, 0.8);
  if($client === false){
    Active($conn, 0);
    $conn->close();
    die("Unable to communicate to bot!");
  }
  $gcode = str_replace("\n","\0\r",$gcode);
  $array = explode( "\r", $gcode );
  $lines = SplFixedArray::fromArray($array);
  unset( $array );
  get_hello_message($client)."\n\n";
  foreach ($lines as $line) {
    $count++;
    fwrite($client, $line);
    readOutput($client)."\n";
    if (perc($count,count($lines)) >= 98.5) {
      sleep(0.1);
    } else {
      sleep(0.04);
    }
  }
  fwrite($client, "\0");
  fclose($client);
  sleep(1.25);
  start($conn);
}
}
start($conn);
?>
