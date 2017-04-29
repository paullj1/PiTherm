<?php

// Create connection
$db_name = getenv("MYSQL_DATABASE");
$db_pass = getenv("MYSQL_ROOT_PASSWORD");

$con = mysqli_connect("127.0.0.1","root",$db_pass,$db_name);
$con->set_charset("utf8");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

?>
