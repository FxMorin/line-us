<?php
$place = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usernames = $_POST['username'];
  $GCODE = $_POST['gcode'];
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "Line-Us";

  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "SELECT Place, Username FROM Queue ORDER BY Place DESC LIMIT 1";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // output data of each row
    $row = $result->fetch_assoc();
    $place = $row["Place"];
  }
  echo ($place+1)."\n";
  $sql = "INSERT INTO Queue (Place, Username, GCode)
  VALUES ('".($place+1)."', '".$usernames."', '".$GCODE."')";

  if ($conn->query($sql) === TRUE) {
      echo "New record created successfully";
  } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
  }
  sleep(4);
  $conn->close();
  exit();
} else {
// Display Queue as list

  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "Line-Us";

  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "SELECT Place, Username FROM Queue ORDER BY Place ASC";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo $row["Place"]. ")  Username: " . $row["Username"]. "<br>";
    }
  } else {
    //echo "0 results";
  }
  $conn->close();

}
?>
