<?php

// Create connection
$db_name = getenv("DB_NAME");
$db_user = getenv("DB_USER");
$db_pass = getenv("DB_PASS");

$con = mysqli_connect("db",$db_user,$db_pass,$db_name);
$con->set_charset("utf8");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

