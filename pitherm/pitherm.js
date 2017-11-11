/*
 * PiTherm Core Code
 * This file controls the GPIO output and logic behind PiTherm.
 */

const port = process.env.PORT || 8080;
const host = process.env.HOST || 'localhost';
const peer = 'http://' + host + ':' + port + '/gun';

var Gun = require('gun');
var gun = new Gun(peer);
var fs = require('fs');
var glob = require('glob');
var Gpio = require('onoff').Gpio;

var HEAT = new Gpio(23, out); // GPIO pin to turn on heating
var FAN = new Gpio(24, out);  // GPIO pin to turn on fan
var COOL = new Gpio(25, out); // GPIO pin to turn on cooling

const NIGHT = 21    // Hour night begins
const COOL_DAY = 7  // Hour day begins when cooling is on
const HEAT_DAY = 5  // Hour day begins when heating is on
const OCCUPIED_TIMEOUT = 30  // Minutes to wait before changing occupied status

var main_loop = setInterval(mainLoop, 30000);

function mainLoop() {
  var server_vars = gun.get('pitherm/server_vars');
  var browser_vars = gun.get('pitherm/server_vars');

  var current_temp = server_vars.get('current_temp');

  getTemperature(current_temp);
  //getSetpoint(server_vars);
}

/*
 * CURRENT TEMP FROM SENSOR
 */
function getTemperature(db_ref) {
  glob('/sys/bus/w1/devices/28*/w1_slave', {}, function (err, files) {
    if (err) {
      console.log('ERROR: ', err);
      console.log(' -- will try again in 30 seconds...');
    }

    // Should only have one 1wire temp sensor
    fs.readFile(files[0], 'ascii', function(err, temp) {
      if (err) {
        console.log('ERROR: ', err);
        console.log(' -- will try again in 30 seconds...');
        return;
      }

      // Check for good CRC (get bad ones if wire is too long and it's cold)
      if (!temp.match('YES')) {
        console.log('ERROR: CRC failed, maybe because it\'s cold?');
        console.log(' -- will try again in 30 seconds...');
        return;
      }

      temp = temp.substr(temp.match('t=').index + 2, 5) / 1000.0;
      db_ref.put(temp)
    });
  });
}

/*
 * CURRENT SETPOINT BASED ON PROGRAM
 */
/*
function getSetpoint(db,mode,occupied,override) {
    // Day or Night?
    var now = datetime.now()
    var DAY = COOL_DAY if mode == 'cool' else HEAT_DAY
    day = now.replace(hour=DAY, minute=0, second=0, microsecond=0)
    night = now.replace(hour=NIGHT, minute=0, second=0, microsecond=0)
    if now > day and now < night:
        daytime = True
    else:
        daytime = False

    if mode == 'off':
        return 0

    if override: 
        setpoint_id = CURRENT_SETPOINT_ID
    else:
        if occupied:
            if mode == 'cool':
                if daytime:
                    setpoint_id = DAY_OCCUPIED_COOL_ID
                else: // night
                    setpoint_id = NIGHT_OCCUPIED_COOL_ID
            if mode == 'heat':
                if daytime:
                    setpoint_id = DAY_OCCUPIED_HEAT_ID
                else: // night
                    setpoint_id = NIGHT_OCCUPIED_HEAT_ID
        else: // unoccupied
            if mode == 'cool':
                setpoint_id = UNOCCUPIED_COOL_ID
            if mode == 'heat':
                setpoint_id = UNOCCUPIED_HEAT_ID
        //end if occupied
    // end if override

    // Get setpoint from DB based on mode and day/night
    setpoint = int(get_value_from_id(db, setpoint_id))

    // Update Setpoint in db, then return it
    set_value_in_db(db, CURRENT_SETPOINT_ID, setpoint)
    return setpoint
}
*/

function cleanUp() {
  clearInterval(main_loop);

  HEAT.writeSync(0);
  HEAT.unexport();

  COOL.writeSync(0);
  COOL.unexport();

  FAN.writeSync(0);
  FAN.unexport();
}

gun.get('pitherm/browser_vars').on(function (data) {
  client_vars = data;
}, true);

gun.get('pitherm/server_vars').on(function (data) {
  server_vars = data;
}, true);


   server_vars.last_occupied
   server_vars.occupied
   browser_vars.current_setpoint
   browser_vars.mode
   browser_vars.variance
   browser_vars.fan
   browser_vars.unoccupied_heat_set_point
   browser_vars.unoccupied_cool_set_point
   browser_vars.night_occupied_heat_set_point
   browser_vars.day_occupied_heat_set_point
   browser_vars.night_occupied_cool_set_point
   browser_vars.day_occupied_cool_set_point
   browser_vars.override

/*
 * Things that I update:
   server_vars.current_temp
   server_vars.heat_status
   server_vars.cool_status
   server_vars.fan_status
 *
 * Things that I subscribe to:
   server_vars.last_occupied
   server_vars.occupied
   browser_vars.current_setpoint
   browser_vars.mode
   browser_vars.variance
   browser_vars.fan
   browser_vars.unoccupied_heat_set_point
   browser_vars.unoccupied_cool_set_point
   browser_vars.night_occupied_heat_set_point
   browser_vars.day_occupied_heat_set_point
   browser_vars.night_occupied_cool_set_point
   browser_vars.day_occupied_cool_set_point
   browser_vars.override
 *
 * Things that Radar writes:
   server_vars.last_occupied
   server_vars.occupied
   browser_vars.ip_addresses
 *
 */
