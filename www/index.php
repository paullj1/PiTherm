<!DOCTYPE html>
<html>
<head>
	<title>PiTherm</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

  <!--Import materialize.css-->
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/css/materialize.min.css">
	<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

	<!--JavaScripts-->
  <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/js/materialize.min.js"></script>
	<script src="js/functions.js"></script>

	<!--Apple iOS app-->
	<meta name="apple-mobile-web-app-capable" content="yes">

	<script>

		$(document).ready(function() {
			<?php 
  			require_once "connect.php"; // Gives us $con
  			require_once "constants.php";
  			require_once "thermo_functions.php";
			?>
			$(".button-collapse").sideNav();
			$('.modal-trigger').leanModal();
		});

	</script>
</head>
<body>
	<nav>
    <div class="nav-wrapper grey darken-1">
      <a href="#!" class="brand-logo">PiTherm</a>
      <a href="#" data-activates="mobile-nav" class="button-collapse"><i class="material-icons">menu</i></a>
      <ul class="right hide-on-med-and-down">
        <li><a class="modal-trigger" href="#settings"><i class="material-icons">settings</i></a></li>
      </ul>
      <ul class="side-nav" id="mobile-nav">
        <li><a class="modal-trigger mdi-settings" href="#settings">Settings</a></li>
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
						<div id="current-setpoint-container"></div>
						<div id="current-temp-container"></div>
					</div>
					<div class="col s4 valign">
						<button id="increase-setpoint" class="btn col s12 therm-buttons">Up</button>
						<br /><br />
    				<button id="decrease-setpoint" class="btn col s12 therm-buttons">Down</button>
					</div>
				</div>
			</div>
		</div>
		<div class="divider"></div>
			<div class="row section">
				<div class="col s6 center-align" id="current-mode-container"></div>
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

		<!-- Override -->
		<h5>Override</h5>
  	<div class="switch">
    	<label>
      	Off
      	<input type="checkbox" id="override-switch" class="settings-form-sliders">
      	<span class="lever"></span>
      	On
    	</label>
  	</div>

		<!-- Mode -->
		<h5>Mode</h5>
		<button class="waves-effect waves-teal btn-flat settings-form-radios" value="heat" id="mode-selector-heat">Heat</button>
		<button class="waves-effect waves-teal btn-flat settings-form-radios" value="cool" id="mode-selector-cool">Cool</button>
		<button class="waves-effect waves-teal btn-flat settings-form-radios" value="off" id="mode-selector-of">Off</button>

		<!-- Fan -->
		<h5>Fan</h5>
  	<div class="switch">
    	<label>
      	Off
      	<input type="checkbox" id="fan-switch" class="settings-form-sliders">
      	<span class="lever"></span>
      	On
    	</label>
  	</div>

		<!-- Setpoints -->
		<table style="width:350px;height:100%">
			<tr>
				<td>Occupied Day Heat:</td>
				<td><a class="modal-trigger btn" href="#setpoint-changer" id="occupied-day-heat" class="therm-buttons"></a></td>
			</tr>
			<tr>
				<td style="width:60%">Occupied Night Heat:</td>
				<td><a class="modal-trigger btn" href="#setpoint-changer" id="occupied-night-heat" class="therm-buttons"></a></td>
			</tr>
			<tr>
				<td>Unoccupied Heat:</td>
				<td><a class="modal-trigger btn" href="#setpoint-changer" id="unoccupied-heat" class="therm-buttons"></a></td>
			</tr>
			<tr>
				<td>Occupied Day Cool:</td>
				<td><a class="modal-trigger btn" href="#setpoint-changer" id="occupied-day-cool" class="therm-buttons"></a></td>
			</tr>
			<tr>
				<td>Occupied Night Cool:</td>
				<td><a class="modal-trigger btn" href="#setpoint-changer" id="occupied-night-cool" class="therm-buttons"></a></td>
			</tr>
			<tr>
				<td>Unoccupied Cool:</td>
				<td><a class="modal-trigger btn" href="#setpoint-changer" id="unoccupied-cool" class="therm-buttons"></a></td>
			</tr>
		</table>
  </div>
</div>

	<!-- Popup Setpoint Changer -->
	<div class="modal" id="setpoint-changer">
		<div class="modal-content row">
			<table width="90%" align="center">
        <tr>
  	      <td colspan="2" align="center"><h4>Change Setpoint</h4></td>
				</tr>
				<tr>
				  <td align="left"> <div id="change-setpoint-label"></div> </td>
				  <td width="100px" align="right"> <div id="change-setpoint-container"></div> </td>
				</tr>
			</table>
			<table width="90%" align="center">
				<tr>
    		  <td align="center" style="width:50%"> <button id="mod-setpoint-down" class="therm-buttons">Down</button> </td>
				  <td align="center" style="width:50%"> <button id="mod-setpoint-up" class="therm-buttons">Up</button> </td>
				</tr>
			</table>
		</div>
	</div>


</body>
</html>
