<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "Line-Us";
$place = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT Active FROM GlobalVar";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
  // output data of each row
  $row = $result->fetch_assoc();
  if ($row["Active"] == 1) {$conn->close();die("Already Running!!");}
}
function sqlProcess($conn, $process) {
  $sql = "UPDATE GlobalVar SET Process='".$process."'";
  if ($conn->query($sql) === TRUE) {
    //echo "Record updated successfully\n";
  } else {
    //echo "Error updating record: " . $conn->error;
  }
}
function sqlPieces($conn, $pieces) {
  $sql = "UPDATE GlobalVar SET Pieces='".$pieces."'";
  if ($conn->query($sql) === TRUE) {
    //echo "Record updated successfully\n";
  } else {
    //echo "Error updating record: " . $conn->error;
  }
}
function sqlKill($conn) {
  $sql = "SELECT killProcess FROM GlobalVar";
  $result = $conn->query($sql);
  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    if ($row["killProcess"] == 1) {
      $sql = "UPDATE GlobalVar SET killProcess='0'";
      if ($conn->query($sql) === TRUE) {
        $conn->close();
        die("Killed Process!!");
      }
    }
  }
}
function Active($conn, $stat) {
  $sql = "UPDATE GlobalVar SET Active='".$stat."'";
  if ($conn->query($sql) === TRUE) {
    //echo "Record updated successfully\n";
  } else {
    //echo "Error updating record: " . $conn->error;
  }
}
function perc($count, $amount) {
  return (($count/$amount)*100);
}
function logger($logs) {
    ignore_user_abort(true);
    $fh = fopen('/Applications/XAMPP/xamppfiles/htdocs/logs/main.log','a');
    fclose($fh);
    sleep(1);
    file_put_contents("/Applications/XAMPP/xamppfiles/htdocs/logs/main.log",$logs, FILE_APPEND | LOCK_EX);
    ignore_user_abort(false);
}
function putIp($conn, $ip) {
    $sql = "UPDATE GlobalVar SET ip='".$ip."'";
    if ($conn->query($sql) === TRUE) {
    //echo "Record updated successfully\n";
    } else {
    //echo "Error updating record: " . $conn->error;
    }
}
function getIp($conn) {
    $sql = "SELECT ip FROM GlobalVar";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
    // output data of each row
    $row = $result->fetch_assoc();
    return $row['ip'];
  }
}
function get_hello_message($client) {
  $line = "";
  while (true) {
    $data = stream_get_contents($client, 1);
    if ($data == "\0") {
      break;
    } else {
      $line .= $data;
    }
  }
  //logger($line);
  return true;
}
function getBotIp($conn) {
  $ip = "";
  ignore_user_abort(true);     ## prevent refresh from aborting file operations and hosing file
  if (getIp($conn) != "") {
    $ip = getIp($conn);
    $connection = @fsockopen($ip, $port, $errno, $errstr, 0.8);
    if (is_resource($connection)) {
      fclose($connection);
      return $ip;
    }
  }
  $counter = 0;
  $port = "1337";
  while ($counter <= 254) {
    $connection = @fsockopen("192.168.0.".$counter, $port, $errno, $errstr, 0.5);
    if (is_resource($connection)) {
      fclose($connection);
      $ip = "192.168.0.".$counter;
      putIp($conn, $ip);
      break;
    }
    $counter++;
  }
ignore_user_abort(false);
return $ip;
}
function readOutput($client) {
  $line = "";
  //while (true) {
  $Zdata = stream_get_contents($client);
    /*if (strlen($Zdata) >= 1) {
      $data = stream_get_contents($client, 1);
      if ($data == "\0" || $data == "") {
        break;
      } else {
        $line .= $data;
      }
    }*/
  //}
  return true;
}
###################
## START OF MAIN ##
###################
function start($conn) {
$gcode = "";
$output = "";
$count = 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $sql = "SELECT Place, GCode FROM Queue ORDER BY Place ASC LIMIT 1";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    // output data of each row
    $row = $result->fetch_assoc();
    $gcode = $row['GCode'];
    $place = $row['Place'];
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
  stream_set_timeout($client, 1);
  if($client === false){
    Active($conn, 0);
    $conn->close();
    die("Unable to communicate to bot!");
  }
  //$gcode .= "\0";
  $gcode = str_replace("\n","\0\r",$gcode);
  $array = explode( "\r", $gcode );
  $lines = SplFixedArray::fromArray($array);
  unset( $array );
  //$lines = explode( "\r", $gcode );
    foreach ($lines as $line) {
      $count++;
      $percent = perc($count,count($lines));
      //logger(perc($count,count($lines))."\n");
      if (readOutput($client)) {
        fwrite($client, $line);
      }
      sqlProcess($conn, $percent);
      //sqlKill($conn);
      //logger($line);
      if ($percent >= 98) {
        sleep(0.1);
      }
    }
  //logger("G28");
  fwrite($client, "\0");
  //logger("\0");
  fclose($client);
  $sql = "DELETE FROM Queue WHERE Place='".$place."'";
  if ($conn->query($sql) === TRUE) {
    echo "Record deleted successfully";
  } else {
    echo "Error deleting record: " . $conn->error;
  }
  sqlProcess($conn, 0);
  sleep(2);
  start($conn);
}
}
start($conn);
?>
