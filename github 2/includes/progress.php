<?php
$q = $_REQUEST["q"];
if ($q == true) {
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "Line-Us";

  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "SELECT Process FROM GlobalVar";
  $result = $conn->query($sql);

  if ($result->num_rows == 1) {
    // output data of each row
    $row = $result->fetch_assoc();
    echo $row["Process"];
  }
  $conn->close();
}
?>
