<?php
require_once('../includes/config.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['Username']) && isset($_POST['Password'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Line-Us";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
      exit("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT UserId, Username, Email, Password FROM Users WHERE `Password`='".md5($_POST['Password'])."' AND (`Username`='".safe($_POST['Username'])."' OR `Email`='".safe($_POST['Username'])."');";
    $result = $conn->query($sql);
    $conn->close();
    if ($result->num_rows == 1) {
      $row = $result->fetch_assoc();
      $_SESSION['loggedIn'] = true;
      $_SESSION['UserId'] = $row['UserId'];
      $_SESSION['Username'] = $row['Username'];
      $_SESSION['Email'] = $row['Email'];
      header('Location: '.DIR.'index.php');
      exit();
    } else {
      header('Location: '.DIR.'auth/login.php?nope=1');
      exit();
    }
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
  input[type=text], input[type=password] {
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
      <h1>Remote Line-Us Login</h1>
    </div>
    <div id="error" style="width:100%;background-color:red;font-size:16px;color:black;display:none;text-align:center;">
      <h3>Username or Password is incorrect!</h3>
    </div>
    <div id="yay" style="width:100%;background-color:green;font-size:16px;color:black;display:none;text-align:center;">
      <h3>You have sucessfully created your account!</h3>
    </div>
    <div class="container">
      <label for="Username"><b>Username or Email</b></label>
      <input type="text" placeholder="Enter Username/Email" name="Username" required>

      <label for="Password"><b>Password</b></label>
      <input type="password" placeholder="Enter Password" name="Password" required>

      <button type="submit">Login</button>
    </div>

    <div class="container" style="background-color:#f1f1f1">
      <button onclick="window.location.href='register.php'" type="button" class="registerbtn">Register</button>
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
  } else if (getQuery("yay") != null) {
    document.getElementById("yay").style.display = "block";
  }
  </script>
</body>
</html>
