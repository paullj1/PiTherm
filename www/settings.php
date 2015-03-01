<?php

require_once "connect.php"; // Gives us $con
require_once "thermo_functions.php";
require_once "constants.php";

$qry_str = 'SELECT  `value` FROM `status` WHERE `id`
	IN ('.FAN_ID.','.OVERRIDE_ID.','.MODE_ID.','.UNOCCUPIED_HEAT_ID.',
	'.UNOCCUPIED_COOL_ID.','.DAY_OCCUPIED_HEAT_ID.','.DAY_OCCUPIED_COOL_ID.',
	'.NIGHT_OCCUPIED_HEAT_ID.','.NIGHT_OCCUPIED_COOL_ID.') ORDER BY `id` ASC;';

$result = query_db($con, $qry_str);

# 3
$entry = fetch_array_db($result);
$return['mode'] = $entry['value'];

# 6
$entry = fetch_array_db($result);
$return['fan'] = $entry['value'];

# 8
$entry = fetch_array_db($result);
$return['unoccupied_heat'] = $entry['value'];

# 9
$entry = fetch_array_db($result);
$return['unoccupied_cool'] = $entry['value'];

# 11
$entry = fetch_array_db($result);
$return['night_occupied_heat'] = $entry['value'];

# 12
$entry = fetch_array_db($result);
$return['day_occupied_heat'] = $entry['value'];

# 13
$entry = fetch_array_db($result);
$return['night_occupied_cool'] = $entry['value'];

# 14
$entry = fetch_array_db($result);
$return['day_occupied_cool'] = $entry['value'];

# 17
$entry = fetch_array_db($result);
$return['override'] = $entry['value'];

echo json_encode($return);

?>
