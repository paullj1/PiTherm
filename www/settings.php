<?php

require_once "connect.php"; // Gives us $con
require_once "thermo_functions.php";
require_once "constants.php";

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`
	IN ('.FAN_ID.','.OVERRIDE_ID.','.MODE_ID.','.UNOCCUPIED_HEAT_ID.',
	'.UNOCCUPIED_COOL_ID.','.DAY_OCCUPIED_HEAT_ID.','.DAY_OCCUPIED_COOL_ID.',
	'.NIGHT_OCCUPIED_HEAT_ID.','.NIGHT_OCCUPIED_COOL_ID.');';

$result = query_db($con, $qry_str);

$entry = fetch_array_db($result);
$result['fan'] = $entry['value'];

$entry = fetch_array_db($result);
$result['override'] = $entry['value'];

$entry = fetch_array_db($result);
$result['mode'] = $entry['value'];

$entry = fetch_array_db($result);
$result['unoccupied_heat'] = $entry['value'];

$entry = fetch_array_db($result);
$result['unoccupied_cool'] = $entry['value'];

$entry = fetch_array_db($result);
$result['day_occupied_heat'] = $entry['value'];

$entry = fetch_array_db($result);
$result['day_occupied_cool'] = $entry['value'];

$entry = fetch_array_db($result);
$result['night_occupied_heat'] = $entry['value'];

$entry = fetch_array_db($result);
$result['night_occupied_cool'] = $entry['value'];

echo json_encode($result);

?>
