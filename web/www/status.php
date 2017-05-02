<?php
  
  require_once "connect.php";
  require_once "thermo_functions.php";
  require_once "constants.php";

  $qry_str = 'SELECT  `value` FROM `status` WHERE `id` 
    IN ('.CURRENT_SETPOINT_ID.', '.CURRENT_TEMP_ID.', '.MODE_ID.',
    '.COOL_STATUS_ID.', '.HEAT_STATUS_ID.') ORDER BY `id` ASC;';

  $result = query_db($con, $qry_str);

  # 1
  $entry = fetch_array_db($result);
  $return["currentTemperature"] = floatval($entry['value'] - 32) * (5/9);

  # 2
  $entry = fetch_array_db($result);
  $return["targetTemperature"] = floatval($entry['value'] - 32) * (5/9);

  # 3
  $entry = fetch_array_db($result);
  switch ($entry['value']) {
    case "heat":
      $return["targetHeatingCoolingState"] = 1;
      break;
    case "cool":
      $return["targetHeatingCoolingState"] = 2;
      break;
    default:
      $return["targetHeatingCoolingState"] = 0;
  }

  # 19
  $entry = fetch_array_db($result);
  $heat_status = $entry['value'];

  # 20
  $entry = fetch_array_db($result);
  $cool_status = $entry['value'];

  if ($heat_status == 'on')
    $return["currentHeatingCoolingState"] = 1;
  else if ($cool_status == 'on')
    $return["currentHeatingCoolingState"] = 2;
  else
    $return["currentHeatingCoolingState"] = 0;

  echo json_encode($return);
?>
