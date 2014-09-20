<?php

// Create connection
$con = mysqli_connect("localhost","thermostat","password","thermostat");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

