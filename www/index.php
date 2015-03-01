<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="css/jquery-mob.css" />
<script src="js/jquery.js"></script>
<script src="js/jquery-mob.js"></script>
<script src="js/functions.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<script>

$(document).ready(function() {
<?php 
  require_once "connect.php"; // Gives us $con
  require_once "constants.php";
  require_once "thermo_functions.php";
?>
});

</script>
</head>
<body>
<div data-role="page" id="home">
  <div data-role="header" data-position="fixed" >
    <h1>PiTherm</h1>
		<a href="#settings" data-transition="slideup" data-role="button" data-icon="grid" class="ui-btn-right">Settings</a>
  </div>
  <div data-role="content">
		<table style="width:350px;height:100%">
			<tr colspan=2 style="width:100%">
				<td style="text-align:center"> <div id="weather-city-container"></div> </td>
			</tr>
			<tr>
				<td> <div id="weather-img-container"></div> </td>
				<td> <div id="weather-temp-container"></div> </td>
			</tr>
			<tr>
				<td> <div id="weather-humidity-container"></div> </td>
				<td> <div id="weather-wind-container"></div> </td>
			</tr>
		</table>

		<table style="width:350px">
			<tr>
				<td rowspan=2> 
					<div id="current-setpoint-container"></div>
					<div id="current-temp-container"></div>
				</td>
				<td style="width:120px"> <button id="increase-setpoint" class="therm-buttons">Up</button> </td>
			</tr>
			<tr>
    		<td> <button id="decrease-setpoint" class="therm-buttons">Down</button> </td>
			</tr>
		</table>

		<div id="current-mode-container"></div>
		<div id="occupied-status-container"></div>
		<table style="width:350px;height:100%">
			<tr>
				<td> <div id="heat-status-container"></div></td>
				<td> <div id="cool-status-container"></div></td>
				<td> <div id="fan-status-container"></div></td>
			</tr>
		</table>
		
  </div>
  <div data-role="footer" data-position="fixed" >
  </div>
</div>



<!--************--!>
<!--*  POPUPS  *--!>
<!--************--!>

<!-- Settings -->
<div data-role="page" id="settings">
  <div data-role="header" data-position="fixed" >
    <h1>Settings</h1>
    <a href="#" data-role="button" data-transition="slidedown" data-rel="back" data-icon="back" class="ui-btn-right">Back</a>
  </div>
  <div data-role="content">
		<!-- Override -->
		<label for="flip-1">Override:</label> </br>
		<select name="flip-1" class="settings-form-sliders" id="override-switch" data-role="slider">
			<option value="False">Off</option>
			<option value="True">On</option>
		</select> <br />

		<!-- Mode -->
		<label for="mode-selector">Mode:</label> <br />
		<fieldset data-role="controlgroup" data-type="horizontal">
			<input type="radio" name="mode-selector" id="mode-selector-heat" class="settings-form-radios" value="heat"  />
			<label for="mode-selector-heat">Heat</label>

			<input type="radio" name="mode-selector" id="mode-selector-cool" class="settings-form-radios" value="cool"  />
			<label for="mode-selector-cool">Cool</label>

			<input type="radio" name="mode-selector" id="mode-selector-off" class="settings-form-radios" value="off"  />
			<label for="mode-selector-off">Off</label>
		</fieldset> 

		<!-- Fan -->
		<label for="flip-2">Fan:</label> <br />
		<select name="flip-2" class="settings-form-sliders" id="fan-switch" data-role="slider">
			<option value="auto">Auto</option>
			<option value="on">On</option>
		</select>  <br />

		<!-- Setpoints -->
		<table style="width:350px;height:100%">
			<tr>
				<td>Occupied Day Heat:</td>
				<td><a href="#setpoint-changer" data-rel="popup" data-role="button" data-position-to="window" data-inline="true"
						 style="width:80px;padding:10px" id="occupied-day-heat" class="therm-buttons"></a></td>
			</tr>
			<tr>
				<td style="width:60%">Occupied Night Heat:</td>
				<td><a href="#setpoint-changer" data-rel="popup" data-role="button" data-position-to="window" data-inline="true" 
							style="width:80px;padding:10px" id="occupied-night-heat" class="therm-buttons"></a></td>
			</tr>
			<tr>
				<td>Unoccupied Heat:</td>
				<td><a href="#setpoint-changer" data-rel="popup" data-role="button" data-position-to="window" data-inline="true" 
							style="width:80px;padding:10px" id="unoccupied-heat" class="therm-buttons"></a></td>
			</tr>
			<tr>
				<td>Occupied Day Cool:</td>
				<td><a href="#setpoint-changer" data-rel="popup" data-role="button" data-position-to="window" data-inline="true" 
							style="width:80px;padding:10px" id="occupied-day-cool" class="therm-buttons"></a></td>
			</tr>
			<tr>
				<td>Occupied Night Cool:</td>
				<td><a href="#setpoint-changer" data-rel="popup" data-role="button" data-position-to="window" data-inline="true" 
							style="width:80px;padding:10px" id="occupied-night-cool" class="therm-buttons"></a></td>
			</tr>
			<tr>
				<td>Unoccupied Cool:</td>
				<td><a href="#setpoint-changer" data-rel="popup" data-role="button" data-position-to="window" data-inline="true" 
							style="width:80px;padding:10px" id="unoccupied-cool" class="therm-buttons"></a></td>
			</tr>
		</table>
  </div>

	<!-- Popup Setpoint Changer -->
	<div data-role="popup" id="setpoint-changer" data-overlay-theme="a" style="width:350px">
			<table width="90%" align="center">
        <tr>
  	      <td colspan="2" align="center"><h2>Change Setpoint</h2></td>
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
