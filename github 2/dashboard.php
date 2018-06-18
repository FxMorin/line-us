<?php
header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
require_once('includes/config.php');

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "Line-Us";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta charset="utf-8"/>
  <meta http-equiv="cache-control" content="max-age=0" />
  <meta http-equiv="cache-control" content="no-cache" />
  <meta http-equiv="expires" content="0" />
  <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
  <meta http-equiv="pragma" content="no-cache" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <style>
    body {
      background: linear-gradient(to right, red , yellow);
      padding:8px;
    }
    .selector-for-some-widget {
      box-sizing: content-box;
    }
    .column {
      float: left;
      width: 50%;
    }
    /* Clear floats after the columns */
    .row:after {
      content: "";
      display: table;
      clear: both;
    }
    #queue {
      padding:5px;
    }
    #queue1 {
      padding:5px;
    }
    .panel-group {
      padding:8px;
      background-color:rgb(175,175,175);
      border-radius: 8px;
    }
    .num1 {
      background-color:rgb(0,185,0);
    }
    #smartImg {
      float:none;
      align-self: center;
      margin: auto;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <h1 style="width:240px;font-family:sans-serif;font-weight:bold;font-size:38px;">DashBoard</h1>
      </div>
      <div style="vertical-align:middle;height:100%;">
        <img src="img/back.png" onclick="window.location.href = 'http://192.168.0.245/';" style="width:35px;height:35px;margin:20px;" alt="Back"></img>
        <?php
        if (isset($_SESSION['Username'])) {
          if ($_SESSION['Username'] == "Admin") {
            echo '<button onclick="kill();" style="float:right;width:65px;height:35px;margin:20px;" alt="Kill">Kill</button>';
          }
        }
        ?>
        <button onclick="ping();" style="float:right;width:65px;height:35px;margin:20px;" alt="Ping">Ping</button>
      </div>
    </div>
  </nav>
  <div class="column">
    <div class="panel-group">
      <?php
      $link = "user";
      $sql = "SELECT Place, Username FROM Queue ORDER BY Place ASC";
      $result = $conn->query($sql);
      $first = 0;
      $firstInLine = "";

      if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
          if (isset($_SESSION['Username'])) {
            if ($row["Username"] == $_SESSION['Username']) {
              $link = "userC";
            } else {
              $link = "user";
            }
          }
          if ($first == 0) {
            $first = 1;
            $firstInLine = $row["Username"];
            echo '<div id="queue1" class="container-fluid panel num1">
              <figure class="figure">
                <img src="img/'.$link.'.png" style="width:40px;height:40px;display:inline-block;" class="figure-img img-fluid rounded" alt="User">
                <figcaption style="display:inline-block;font-family:sans-serif;font-weight:bold;margin-left:5px;" class="figure-caption">'.$row["Username"].'</figcaption>
                <!--<button onclick="requestKill();">Kill Process</button>-->
                <div class="progress" style="margin:10px;">
                  <div id="show" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%"></div>
                </div>
              </figure>
            </div>';
          } else {
            echo '<div id="queue" class="container-fluid panel">
              <figure class="figure">
                <img src="img/'.$link.'.png" style="width:40px;height:40px;display:inline-block;" class="figure-img img-fluid rounded" alt="User">
                <figcaption style="display:inline-block;font-family:sans-serif;font-weight:bold;margin-left:5px;" class="figure-caption">'.$row["Username"].'</figcaption>
              </figure>
            </div>';
          }
        }
      } else {
        echo '<div id="queue1" class="container-fluid panel">
          <figure class="figure">
            <img src="img/user.png" style="width:40px;height:40px;display:inline-block;" class="figure-img img-fluid rounded" alt="User">
            <figcaption style="display:inline-block;font-family:sans-serif;font-weight:bold;margin-left:5px;" class="figure-caption">Queue Empty!</figcaption>
          </figure>
        </div>';
      }
      $conn->close();
      ?>


    </div>
  </div>
  <div class="column">
    <div style="margin-left:8px;position:relative;">
      <img id="smartImg" style="width:640px;height:480px;" src="ipCam/frame.jpeg" alt="Live Line-us Bot Feed"/>
      <img id="smartImg2" style="width:640px;height:480px;position:absolute;z-index:-1;left:0px;top:0px;" src="ipCam/frame.jpeg"/>
      <img style="width:640px;height:480px;position:absolute;z-index:-2;left:0px;top:0px;" src="ipCam/frame.jpeg"/>
      <!--<canvas id="mainFeed" style="width:640px;height:480px;"></canvas>-->
    </div>
  </div>
  <script>
  wait = 0;
  load = 0;
  function getProgress() {
    if (load == 2) {
      var image = document.getElementById("smartImg");
      var theImage = new Image();
      theImage.onload = function(){
        image.src = this.src;
      };
      var d = new Date();
      theImage.src = "ipCam/frame.jpeg?t=" + d.getTime();
      load = 0;
    } else if (load == 1) {
      var image = document.getElementById("smartImg2");
      var theImage = new Image();
      theImage.onload = function(){
        image.src = this.src;
      };
      var d = new Date();
      theImage.src = "ipCam/frame.jpeg?t=" + d.getTime();
      load++;
    } else {
      load++;
    }
    if (document.getElementById("queue1").classList.contains("num1")) {
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("show").style.width = this.responseText + "%";
            document.getElementById("show").innerHTML = this.responseText;
            if (this.responseText == 0) {
              setInterval(Refresh,1000);
            }
          }
      };
      xmlhttp.open("GET", "includes/progress.php?q=true", true);
      xmlhttp.send();
    } else {
      if (wait == 32) { //Divide by 4 to get the amount of seconds
        location.reload();
      } else {
        wait++;
      }
    }
  }
  setInterval(getProgress,250);
  function Refresh() {
    location.reload();
  }
  function requestKill() {
    if (<?php if ($firstInLine == $_SESSION['Username']) {echo "true";} else {echo "false";} ?>) {
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {}
      };
      xmlhttp.open("GET", "includes/requestKill.php", true);
      xmlhttp.send();
    }
  }
  function ping() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("POST", "includes/draw.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send();
  }
  function kill() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("POST", "includes/kill.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send();
  }
  </script>
</body>
</html>
