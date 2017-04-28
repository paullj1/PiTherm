<?php

require_once "connect.php"; // Gives us $con
require_once "thermo_functions.php";
require_once "constants.php";

// Sanitize Input
$val = $con->real_escape_string($_POST['value']);
$id = $con->real_escape_string($_POST['id']);

// Update DB
$qry_str = 'UPDATE `thermostat`.`status` SET `value` ="'.$val.'" WHERE `status`.`id` ='.$id.';';
$entry = query_db($con, $qry_str);
echo json_encode($entry);

?>
