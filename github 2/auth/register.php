<?php
require_once('../includes/config.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['Username']) && isset($_POST['Password']) && isset($_POST['RePassword']) && isset($_POST['Email'])) {

    if ($_POST['Password'] != $_POST['RePassword']) {
      header('Location: '.DIR.'auth/register.php?nope=1');
      exit();
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Line-Us";

    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    $sql = "INSERT INTO Users (Username, Email, Password)
    VALUES ('".safe($_POST['Username'])."', '".safe($_POST['Email'])."', '".md5($_POST['Password'])."')";
    if ($conn->query($sql) === TRUE) {
      header('Location: '.DIR.'auth/login.php?yay=1');
      exit();
    } else {
      header('Location: '.DIR.'auth/register.php?nope=1');
      exit();
    }
    $conn->close();
  }
}
?>
<html>
<head>
  <style>
  body,head {
    font-family:sans-serif;
  }
  *:focus {
    outline:none;
  }
  /* Bordered form */
  form {
      border: 3px solid #f1f1f1;
  }

  /* Full-width inputs */
  input[type=text], input[type=password], input[type=email] {
      width: 100%;
      padding: 12px 20px;
      margin: 8px 0;
      display: inline-block;
      border: 1px solid #ccc;
      box-sizing: border-box;
  }

  /* Set a style for all buttons */
  button {
      background-color: #4CAF50;
      color: white;
      padding: 14px 20px;
      margin: 8px 0;
      border: none;
      cursor: pointer;
      width: 100%;
  }

  /* Add a hover effect for buttons */
  button:hover {
      opacity: 0.8;
  }

  /* Extra style for the cancel button (red) */
  .cancelbtn {
      width: auto;
      padding: 10px 18px;
      background-color: #f44336;
  }
  .registerbtn {
      width: auto;
      padding: 10px 18px;
      background-color: blue;
  }
  /* Center the avatar image inside this container */
  .imgcontainer {
      text-align: center;
      margin: 24px 0 12px 0;
  }

  /* Avatar image */
  img.avatar {
      width: 40%;
      border-radius: 50%;
  }

  /* Add padding to containers */
  .container {
      padding: 16px;
  }

  /* The "Forgot password" text */
  span.psw {
      float: right;
      padding-top: 16px;
  }

  /* Change styles for span and cancel button on extra small screens */
  @media screen and (max-width: 300px) {
      span.psw {
          display: block;
          float: none;
      }
      .cancelbtn {
          width: 100%;
      }
  }
  </style>
</head>
<body>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
    <div class="imgcontainer">
      <h1>Remote Line-Us Register</h1>
    </div>
    <div id="error" style="width:100%;background-color:red;font-size:16px;color:black;display:none;text-align:center;">
      <h3>An error has occured! Please contact the web-administrator</h3>
    </div>
    <div class="container">
      <label for="Username"><b>Username or Email</b></label>
      <input type="text" placeholder="Enter Username/Email" name="Username" required>

      <label for="Email"><b>Email</b></label>
      <input type="email" placeholder="Enter Email" name="Email" required>

      <label for="Password"><b>Password</b></label>
      <input type="password" placeholder="Enter Password" name="Password" required>

      <label for="RePassword"><b>Retype Password</b></label>
      <input type="password" placeholder="Retype your Password" name="RePassword" required>

      <button type="submit">Register</button>
    </div>
    <div class="container" style="background-color:#f1f1f1">
      <button onclick="window.location.href='login.php'" type="button" class="registerbtn">Login</button>
    </div>
    <!--<div class="container" style="background-color:#f1f1f1">
      <button type="button" class="cancelbtn">Cancel</button>
      <span class="psw">Forgot <a href="#">password?</a></span>
    </div>-->
  </form>
  <script>
  function getQuery(q) {
    return (window.location.search.match(new RegExp('[?&]' + q + '=([^&]+)')) || [, null])[1];
  }
  if (getQuery("nope") != null) {
    document.getElementById("error").style.display = "block";
  }
  </script>
</body>
</html>
