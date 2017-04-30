<?php
  
  require_once "connect.php";
  require_once "thermo_functions.php";
  require_once "constants.php";

  $qry_str = 'SELECT  `value` FROM `status` WHERE `id` 
    IN ('.CURRENT_SETPOINT_ID.', '.CURRENT_TEMP_ID.', '.MODE_ID.') ORDER BY `id` ASC;';

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
      $state = 1;
      break;
    case "cool":
      $state = 2;
      break;
    default:
      $state = 0;
  }
  $return["currentHeatingCoolingState"] = $state;
  $return["targetHeatingCoolingState"] = $state;

  $weather = json_decode(get_weather(), true);
  $return["targetRelativeHumidity"] = $weather['main']['humidity'];
  $return["currentRelativeHumidity"] = $weather['main']['humidity'];

  echo json_encode($return);
?>
