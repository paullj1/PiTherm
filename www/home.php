<?php

require_once "connect.php"; // Gives us $con
require_once "thermo_functions.php";
require_once "constants.php";

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.CURRENT_SETPOINT_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$return['current_setpoint'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.CURRENT_TEMP_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$return['current_temp'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.MODE_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$return['mode'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.OCCUPIED_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$return['occupied'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.HEAT_STATUS_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$return['heat_status'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.COOL_STATUS_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$return['cool_status'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.FAN_STATUS_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$return['fan_status'] = $entry['value'];

echo json_encode($return);

?>
