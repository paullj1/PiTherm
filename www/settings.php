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

echo json_encode($result);

?>
