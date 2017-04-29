<?php

  function get_weather() {
    $url = "http://api.openweathermap.org/data/2.5/weather?";
    $data = array("q" => $_ENV["OPEN_WEATHER_LOCATION"], 
                  "APPID" => $_ENV["OPEN_WEATHER_API_KEY"],
                  "units" => "imperial");
    $url .= http_build_query($data);
    return file_get_contents($url);
  }

  echo get_weather();

?>
