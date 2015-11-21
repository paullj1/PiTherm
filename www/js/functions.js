/**********************************/
/*                                */
/* Copyright (c) Paul Jordan 2013 */
/* paullj1@gmail.com              */
/*                                */
/**********************************/

// Constants
var CURRENT_TEMP_ID = 1;
var CURRENT_SETPOINT_ID = 2;
var MODE_ID = 3;
var VARIANCE_ID = 4;
var FAN_ID = 6;
var LAST_OCCUPIED_ID = 7;
var UNOCCUPIED_HEAT_ID = 8;
var UNOCCUPIED_COOL_ID = 9;
var NIGHT_OCCUPIED_HEAT_ID = 11;
var DAY_OCCUPIED_HEAT_ID = 12;
var NIGHT_OCCUPIED_COOL_ID = 13;
var DAY_OCCUPIED_COOL_ID = 14;
var OVERRIDE_ID = 17;
var OCCUPIED_ID = 18
var HEAT_STATUS_ID = 19
var COOL_STATUS_ID = 20
var FAN_STATUS_ID = 21

var LOCATION = "Dayton"
var APIKEY="YOUR KEY HERE"
var LOCAL_REFRESH_RATE = 10000
var WEATHER_REFRESH_RATE = 60000
var SETTINGS_REFRESH_RATE = 10000
var setpoint = 0;
var modify_setpoint_id = 0;
var modify_setpoint_value = 0;

/************************/
/*                      */
/*      HOME PAGE       */
/*                      */
/************************/
$(document).ready(function() {
	get_data();

	// Connect Update Functions
	$(".settings-form-sliders").bind( 'click', function( event ) { 
		var id = 0;
		var value = '';
		if ( event.currentTarget.id == "fan-switch" ) {
			id = FAN_ID;
			value = event.currentTarget.checked ? 'on' : 'auto';
		}

		if ( event.currentTarget.id == "override-switch" ) {
			id = OVERRIDE_ID;
			value = event.currentTarget.checked ? 'True' : 'False';
		}

		if ( id != 0 && value != '')  {
			set_val_db(id, value);
			get_data();	
		}
	});

	$(".settings-form-radios").bind( "click", function(event) {
		//console.dir(event); // for debug
		set_val_db(MODE_ID, event.currentTarget.value);
		get_data();
	});

});

function get_data() {
	get_weather();
	get_local();
	get_settings();
}

function get_local() {
  // Get local DB stuff
	$.ajax({
		type : "POST",
		async: false,
		url : "home.php",
		cache : false,
		dataType : "json",
		success : update_local,
		error: function(xhr) { console.log("AJAX request failed: " + xhr.status); }
	});

	// Update info regularly
	setTimeout(get_local, LOCAL_REFRESH_RATE);
}

function get_weather() {
  // Get Weather data
	$.ajax({
		type : "GET",
		async: false,
		url : "http://api.openweathermap.org/data/2.5/weather",
		cache : false,
		dataType : "json",
		data : {
			q: LOCATION,
			APPID: APIKEY,
			units: "imperial"
		},
		success : update_weather,
		error: function(xhr) { console.log("AJAX request failed: " + xhr.status); }
	});

	// Update info regularly
	setTimeout(get_weather, WEATHER_REFRESH_RATE);
}

function get_settings() {
	$.ajax({
		type : "POST",
		async: false,
		url : "settings.php",
		cache : false,
		dataType : "json",
		success : update_settings,
		error: function(xhr) { console.log("AJAX request failed: " + xhr.status); }
	});

	// Update info regularly
	setTimeout(get_settings, SETTINGS_REFRESH_RATE);
}

function update_weather(data) {
	//console.dir(data);

	$("#weather-city-container").html(data.name);
	$("#weather-img-container").html("<img src='http://openweathermap.org/img/w/"+data.weather[0].icon+".png'></img>");
	$("#weather-temp-container").html("<h5>"+data.main.temp+"º F</h5>");
	$("#weather-humidity-container").html("Humidity: "+data.main.humidity+" %");
	$("#weather-wind-container").html("Wind: "+data.wind.speed+" mph");
}

function update_local(data) {
	//console.dir(data);
	$("#current-temp-container").text(Math.floor(data.current_temp));
	$("#current-setpoint-container").text((setpoint = data.current_setpoint));

	var text_color = 'black-text';
	var text = 'Off';
	if (data.mode == 'heat') {
		text_color = 'red-text';
		text = 'Heat';
	} else if (data.mode == 'cool') {
		text_color = 'blue-text';
		text = 'Cool';
	}
		
	$("#current-mode-container").html(text);
	$("#current-mode-container").addClass(text_color);
	
	var light = 'off';
	if ( data.occupied == "True" ) light = 'on';
	$("#occupied-status-container").html("Occupied: <img style='vertical-align:middle' src='images/"+light+".png'></img>");

	light = 'off';
	if ( data.heat_status == "on" ) light = 'on';
	$("#heat-status-container").html("Heat: <img style='vertical-align:middle' src='images/"+light+".png'></img>");

	light = 'off';
	if ( data.cool_status == "on" ) light = 'on';
	$("#cool-status-container").html("Cool: <img style='vertical-align:middle' src='images/"+light+".png'></img>");

	light = 'off';
	if ( data.fan_status == "on" ) light = 'on';
	$("#fan-status-container").html("Fan: <img style='vertical-align:middle' src='images/"+light+".png'></img>");

}

function update_settings(data) {
	//console.dir(data); // for debug
	if (data.fan == "on") $("#fan-switch").prop("checked", true);
	else $("#fan-switch").prop("checked", false);

	if (data.override == "True") {
		$("#presence-mode").hide();
		$("#setpoint-changer").show();
		$("#override-switch").prop("checked", true);
	} else {
		$("#presence-mode").show();
		$("#setpoint-changer").hide();
		$("#override-switch").prop("checked", false);
	}

	$("#mode-selector-heat").removeClass('btn').removeClass('btn-flat');
	$("#mode-selector-cool").removeClass('btn').removeClass('btn-flat');
	$("#mode-selector-off").removeClass('btn').removeClass('btn-flat');

	if (data.mode == "heat") {
		$("#mode-selector-heat").addClass('btn')
		$("#mode-selector-cool").addClass('btn-flat')
		$("#mode-selector-off").addClass('btn-flat')
	} else if (data.mode == "cool") {
		$("#mode-selector-heat").addClass('btn-flat')
		$("#mode-selector-cool").addClass('btn')
		$("#mode-selector-off").addClass('btn-flat')
	} else { // OFF
		$("#mode-selector-heat").addClass('btn-flat')
		$("#mode-selector-cool").addClass('btn-flat')
		$("#mode-selector-off").addClass('btn')
	}

	$("#occupied-day-heat").text(data.day_occupied_heat+"º F");
	$("#occupied-night-heat").text(data.night_occupied_heat+"º F");
	$("#unoccupied-heat").text(data.unoccupied_heat+"º F");
	$("#occupied-day-cool").text(data.day_occupied_cool+"º F");
	$("#occupied-night-cool").text(data.night_occupied_cool+"º F");
	$("#unoccupied-cool").text(data.unoccupied_cool+"º F");
}

function get_setpoint(id) {
	$.ajax({
		type : "POST",
		async: false,
		url : "setpoint_changer.php",
		cache : false,
		dataType : "json",
		data : {
			id: id
		},
		success : update_setpoint,
		error: function(xhr) { console.log("AJAX request failed: " + xhr.status); }
	});
  modify_setpoint_id = id;
}

function update_setpoint(data) {
	//console.dir(data);
	$("#change-setpoint-label").html("<h5>"+data.label+": </h5>");
	$("#change-setpoint-container").html("<h5>"+data.value+"º F</h5>");
  modify_setpoint_value = data.value; // MESSY! need a better way to do this
}

/************************/
/*                      */
/*        GENERIC       */
/*                      */
/************************/

function set_val_db(id,value) {
	$.ajax({
		type : "POST",
		async: false,
		url : "update.php",
		cache : false,
		dataType : "json",
		data : {
			id: id,
			value: value
		},
		success : function(xhr) { console.log("Successufully Updated DB"); },
		error: function(xhr) { console.log("AJAX request failed: " + xhr.status); }
	});
}

// Button Handler
$(function() {
    $(".therm-buttons").click(function() { 
			var id = $(this).attr("id");
			switch(id) {
				case "increase-setpoint":
					set_val_db(CURRENT_SETPOINT_ID, ++setpoint);
					get_data();
					break;
				case "decrease-setpoint":
					set_val_db(CURRENT_SETPOINT_ID, --setpoint);
					get_data();
					break;
				case "mod-setpoint-up":
          if (modify_setpoint_id != 0 && modify_setpoint_value != 0)
					  set_val_db(modify_setpoint_id, ++modify_setpoint_value);
					get_setpoint(modify_setpoint_id);
					get_data();
					break;
				case "mod-setpoint-down":
          if (modify_setpoint_id != 0 && modify_setpoint_value != 0)
					  set_val_db(modify_setpoint_id, --modify_setpoint_value);
					get_setpoint(modify_setpoint_id);
					get_data();
					break;
				case "occupied-night-heat":
					get_setpoint(NIGHT_OCCUPIED_HEAT_ID);
					break;
				case "occupied-day-heat":
					get_setpoint(DAY_OCCUPIED_HEAT_ID);
					break;
				case "unoccupied-heat":
					get_setpoint(UNOCCUPIED_HEAT_ID);
					break;
				case "occupied-night-cool":
					get_setpoint(NIGHT_OCCUPIED_COOL_ID);
					break;
				case "occupied-day-cool":
					get_setpoint(DAY_OCCUPIED_COOL_ID);
					break;
				case "unoccupied-cool":
					get_setpoint(UNOCCUPIED_COOL_ID);
					break;
			}
		})
});
