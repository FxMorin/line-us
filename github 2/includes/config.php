<?php
//header("Cache-Control: no-cache");
//header("Pragma: no-cache");
ob_start();
session_start();

date_default_timezone_set('America/Toronto');
// FX's Config File
define("DIR", "http://192.168.0.245/");

//////////////////////// DO NOT EDIT BEYOND THIS POINT ////////////////////////
function safe($string) {
  return htmlspecialchars(stripslashes(trim($string)));
}
function loggedIn() {
  if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == True) {return False;}else{return True;}
}
function security($loc) {
  if (loggedIn()) {header('Location: '.DIR.$loc);exit();}
}
?>
