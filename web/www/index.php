
<!DOCTYPE html>
<html>
<head>
	<title>PiTherm</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

  <!--Stylesheets-->
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/css/materialize.min.css">
	<link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

	<!--JavaScripts-->
  <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/js/materialize.min.js"></script>

	<!--Apple iOS app-->
	<meta name="apple-mobile-web-app-capable" content="yes">

	<script>

		$(document).ready(function() {
			<?php 
  			require_once "connect.php"; // Gives us $con
  			require_once "constants.php";
  			require_once "thermo_functions.php";

			echo "
				document.LOCATION=". $_ENV["OPEN_WEATHER_LOCATION"] .";
				document.APIKEY='". $_ENV["OPEN_WEATHER_API_KEY"] ."';
				document.CURRENT_TEMP_ID=". CURRENT_TEMP_ID .";
				document.CURRENT_SETPOINT_ID=". CURRENT_SETPOINT_ID .";
				document.MODE_ID=". MODE_ID .";
				document.VARIANCE_ID=". VARIANCE_ID .";
				document.FAN_ID=". FAN_ID .";
				document.LAST_OCCUPIED_ID=". LAST_OCCUPIED_ID .";
				document.UNOCCUPIED_HEAT_ID=". UNOCCUPIED_HEAT_ID .";
				document.UNOCCUPIED_COOL_ID=". UNOCCUPIED_COOL_ID .";
				document.NIGHT_OCCUPIED_HEAT_ID=". NIGHT_OCCUPIED_HEAT_ID .";
				document.DAY_OCCUPIED_HEAT_ID=". DAY_OCCUPIED_HEAT_ID .";
				document.NIGHT_OCCUPIED_COOL_ID=". NIGHT_OCCUPIED_COOL_ID .";
				document.DAY_OCCUPIED_COOL_ID=". DAY_OCCUPIED_COOL_ID .";
				document.IP_ADDRESSES=". IP_ADDRESSES .";
				document.OVERRIDE_ID=". OVERRIDE_ID .";
				document.OCCUPIED_ID=". OCCUPIED_ID .";
				document.HEAT_STATUS_ID=". HEAT_STATUS_ID .";
				document.COOL_STATUS_ID=". COOL_STATUS_ID .";
				document.FAN_STATUS_ID=". FAN_STATUS_ID .";
			";

			?>
			$('.modal-trigger').leanModal();
		});

	</script>
	<script src="js/functions.js"></script>
</head>
<body>
	<nav>
    <div class="nav-wrapper grey darken-1">
      <a href="#!" class="brand-logo">PiTherm</a>
      <a href="#settings" class="modal-trigger button-collapse"><i class="material-icons">settings</i></a>
      <ul class="right hide-on-med-and-down">
        <li><a class="modal-trigger" href="#settings"><i class="material-icons">settings</i></a></li>
      </ul>
    </div>
  </nav>

	<div id="home" class="container">
		<div class="row">
			<div class="col s12 m5 card-panel grey lighten-1">
				<h5 class="center-align">Local Weather: <span id="weather-city-container"></span></h5>
				<div class="row">
					<div class="col s6 right-align">
						<div id="weather-temp-container"></div>
					</div>
					<div class="col s6 left-align">
						<div id="weather-img-container"></div>
					</div>
					<div class="col s6 right-align">
						<div id="weather-humidity-container"></div>
					</div>
					<div class="col s6 left-align">
						<div id="weather-wind-container"></div>
					</div>
				</div>
			</div>
			<div class="col s12 m6 push-m1 card-panel grey lighten-1" style="padding-top:10px">
				<div class="row valign-wrapper">
					<div class="col s8">
						<h5>Setpoint:  <div style="display:inline" id="current-setpoint-container"></div>º F</h5>
						<h5>Actual: <div style="display:inline" id="current-temp-container"></div>º F</h5>
					</div>
					<div class="col s4 valign">
						<div id="presence-mode">
							<p class="center-align">Presence Mode Active</p>
						</div>
						<div id="current-setpoint-changer">
							<button id="increase-setpoint" class="btn col s12 therm-buttons">Up</button>
							<br /><br />
    					<button id="decrease-setpoint" class="btn col s12 therm-buttons">Down</button>
						</div>
					</div>
				</div>
				<!-- Override -->
				<span class="row col s12 valign-wrapper">
					<h5 class="col s6 row valign">Override: </h5>
  				<div class="col s6 row switch valign">
    				<label>
      				<b class="black-text">Off</b>
      				<input type="checkbox" id="override-switch" class="settings-form-sliders">
      				<span class="lever"></span>
      				<b class="black-text">On</b>
    				</label>
  				</div>
				</span>
			</div>

		</div>
		<div class="divider"></div>
			<div class="row section">
				<span class="col s6 center-align">Mode: <b><span id="current-mode-container"></span></b></span>
				<div class="col s6 center-align" id="occupied-status-container"></div>
			</div>
			<div class="row">
				<div class="col s4 center-align" id="heat-status-container"></div>
				<div class="col s4 center-align" id="cool-status-container"></div>
				<div class="col s4 center-align" id="fan-status-container"></div>
			</div>
		</div>
	</div>
		
<!--************--!>
<!--*  POPUPS  *--!>
<!--************--!>

<!-- Settings -->
<div class="modal" id="settings">
	<div class="modal-content row">
    <h4 class="col s12 center-align">Settings</h4>

		<!-- Mode -->
		<h5 class="row">Mode</h5>
		<button class="waves-effect waves-teal btn-flat settings-form-radios row col s4" value="heat" id="mode-selector-heat">Heat</button>
		<button class="waves-effect waves-teal btn-flat settings-form-radios row col s4" value="cool" id="mode-selector-cool">Cool</button>
		<button class="waves-effect waves-teal btn-flat settings-form-radios row col s4" value="off" id="mode-selector-off">Off</button>

		<!-- Fan -->
		<h5 class="row">Fan</h5>
  	<div class="row switch">
    	<label>
      	Off
      	<input type="checkbox" id="fan-switch" class="settings-form-sliders">
      	<span class="lever"></span>
      	On
    	</label>
  	</div>

		<!-- Setpoints -->
		<h5 class="row">Setpoints</h5>
		<div class="container col s10 offset-s1">
			<div class="col s9">Occupied Day Heat:</div>
			<div class="col s3"><a class="modal-trigger btn therm-buttons row" href="#setpoint-changer" id="occupied-day-heat"></a></div>

			<div class="col s9">Occupied Night Heat:</div>
			<div class="col s3"><a class="modal-trigger btn therm-buttons row" href="#setpoint-changer" id="occupied-night-heat"></a></div>

			<div class="col s9">Unoccupied Heat:</div>
			<div class="col s3"><a class="modal-trigger btn therm-buttons row" href="#setpoint-changer" id="unoccupied-heat"></a></div>

			<div class="col s9">Occupied Day Cool:</div>
			<div class="col s3"><a class="modal-trigger btn therm-buttons row" href="#setpoint-changer" id="occupied-day-cool"></a></div>

			<div class="col s9">Occupied Night Cool:</div>
			<div class="col s3"><a class="modal-trigger btn therm-buttons row" href="#setpoint-changer" id="occupied-night-cool"></a></div>

			<div class="col s9">Unoccupied Cool:</div>
			<div class="col s3"><a class="modal-trigger btn therm-buttons row" href="#setpoint-changer" id="unoccupied-cool"></a></div>
		</div>

		<!-- IP Addresses -->
		<div class="col s10 offset-s1">
				<label for="ip-addresses">
					<h5 class="row black-text">IP Addresses</h5>
					<h6 class="row grey-text lighten-1">separate multiple entries with a space or newline</h6>
				</label>
			<div class="input-field">
				<textarea id="ip-addresses" class="materialize-textarea"></textarea>
			</div>
			<button class="row btn" onclick="save_ip_addresses()">Save</button>
		</div>

  </div>
</div>

	<!-- Popup Setpoint Changer -->
	<div class="modal" id="setpoint-changer">
		<div class="modal-content container row">
			<div class="col s12">
  	    <h4 class="center-align">Change Setpoint</h4>
				<div id="change-setpoint-label" class="left-align col s12 m8"></div>
				<div id="change-setpoint-container" class="right-align col s12 m4"></div>
			</div>
			<div class="col s12">
				<button id="mod-setpoint-up" class="btn therm-buttons col s12 m6">Up</button>
				<br /><br />
    		<button id="mod-setpoint-down" class="btn therm-buttons col s12 m6">Down</button>
			</div>
		</div>
	</div>

</body>
</html>
