<?php

  require_once "connect.php";
  require_once "thermo_functions.php";
  require_once "constants.php";

  if ( isset($_GET["m"]) ) {

    $target_mode = $con->real_escape_string($_GET['m']);
    $qry_str = 'UPDATE `status` SET `value` ="';
    switch ($target_mode) {
      case 1:
        $qry_str .= 'heat';
        break;
      case 2:
        $qry_str .= 'cool';
        break;
      default:
        $qry_str .= 'off';
    }
    $qry_str .= '" WHERE `status`.`id` ='.MODE_ID.';';
    query_db($con, $qry_str);
    http_response_code(200);

  } else if ( isset($_GET["t"]) ) {

    # Comes in Celcius
    $target_temp = floatval($con->real_escape_string($_GET['t']));
    $target_temp = round(($target_temp * (9/5)) + 32);

    if ($target_temp > 50 && $target_temp < 100) {
      # Set override to True
      $qry_str = 'UPDATE `status` SET `value` ="True" WHERE 
                  `status`.`id` ='.OVERRIDE_ID.';';
      query_db($con, $qry_str);

      # Set temperature
      $qry_str = 'UPDATE `status` SET `value` ="'.$target_temp.'" WHERE 
                  `status`.`id` ='.CURRENT_SETPOINT_ID.';';
      query_db($con, $qry_str);

      http_response_code(200);
    } else {
      http_response_code(400);
    }

  } else {
    http_response_code(400);
  }

?>
