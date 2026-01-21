<?php
// 1. Connect to the database
require_once('db/db.php');

// 2. Redirect the user to the login page
header("Location: auth/login.php");
exit();
?>