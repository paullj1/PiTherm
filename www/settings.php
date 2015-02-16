<?php

require_once "connect.php"; // Gives us $con
require_once "thermo_functions.php";
require_once "constants.php";

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.FAN_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$result['fan'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.OVERRIDE_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$result['override'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.MODE_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$result['mode'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.UNOCCUPIED_HEAT_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$result['unoccupied_heat'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.UNOCCUPIED_COOL_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$result['unoccupied_cool'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.DAY_OCCUPIED_HEAT_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$result['day_occupied_heat'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.DAY_OCCUPIED_COOL_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$result['day_occupied_cool'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.NIGHT_OCCUPIED_HEAT_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$result['night_occupied_heat'] = $entry['value'];

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`='.NIGHT_OCCUPIED_COOL_ID.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$result['night_occupied_cool'] = $entry['value'];

echo json_encode($result);

?>
