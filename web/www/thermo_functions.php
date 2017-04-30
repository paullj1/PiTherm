<?php

function query_db($con, $query) {
  return mysqli_query($con,$query);
}

function fetch_array_db($result) {
  return mysqli_fetch_array($result);
}

function fetch_assoc_db($result) {
	return mysql_fetch_assoc($result);
}

function get_weather() {
  $url = "http://api.openweathermap.org/data/2.5/weather?";
  $data = array("q" => $_ENV["OPEN_WEATHER_LOCATION"], 
                "APPID" => $_ENV["OPEN_WEATHER_API_KEY"],
                "units" => "imperial");
  $url .= http_build_query($data);
  return file_get_contents($url);
}

?>
