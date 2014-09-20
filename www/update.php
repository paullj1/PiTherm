<?php

require_once "connect.php"; // Gives us $con
require_once "thermo_functions.php";
require_once "constants.php";

$qry_str = 'UPDATE `thermostat`.`status` SET `value` ="'.$_POST['value'].'" WHERE `status`.`id` ='.$_POST['id'].';';
$entry = query_db($con, $qry_str);
echo json_encode($entry);

?>
