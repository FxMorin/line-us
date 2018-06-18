<?php
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "Line-Us";
  $place = "";

  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "SELECT Place, Username FROM Queue ORDER BY Place ASC LIMIT 1";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    // output data of each row
    $row = $result->fetch_assoc();
    $place = $row['Place'];
    $username = $row['Username'];
  }
    $sql = "DELETE FROM Queue WHERE Place='".$place."'";
    if ($conn->query($sql) === TRUE) {
      echo "Record deleted successfully";
    } else {
      echo "Error deleting record: " . $conn->error;
    }
    $sql = "UPDATE GlobalVar SET Active='0'";
    if ($conn->query($sql) === TRUE) {
      //echo "Record updated successfully\n";
    }
    $sql = "UPDATE GlobalVar SET Process='0'";
    if ($conn->query($sql) === TRUE) {
      //echo "Record updated successfully\n";
    }
    $conn->close();
    echo shell_exec('/Applications/XAMPP/xamppfiles/bin/apachectl -k restart');
    die();
?>
