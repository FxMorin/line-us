<?php
require_once('../includes/config.php');
session_destroy();
header('Location: '.DIR.'auth/login.php');
exit();
?>
