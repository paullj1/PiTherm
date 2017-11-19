/*
 * PiTherm Core Code
 * This file controls the GPIO output and logic behind PiTherm.
 */

console.log('Starting PiTherm\'s core...');
const port = process.env.PORT || 8080;
const host = process.env.HOST || 'localhost';
const peer = 'http://' + host + ':' + port + '/gun';

const Gun = require('gun');
const gun = new Gun(peer);
const fs = require('fs');
const glob = require('glob');
const Gpio = require('onoff').Gpio;

const HEAT = new Gpio(23, 'out'); // GPIO pin to turn on heating
const FAN = new Gpio(24, 'out');  // GPIO pin to turn on fan
const COOL = new Gpio(25, 'out'); // GPIO pin to turn on cooling

const ON = 0
const OFF = 1
const NIGHT = 21    // Hour night begins
const COOL_DAY = 7  // Hour day begins when cooling is on
const HEAT_DAY = 5  // Hour day begins when heating is on
const OCCUPIED_TIMEOUT = 30  // Minutes to wait before changing occupied status

let last_temp = 22;
let last_setpoint = 22;
let last_mode = 'off';
let last_variance = 0.55;
let last_fan = false;
let last_unoccupied_heat_set_point = 22;
let last_unoccupied_cool_set_point = 22;
let last_night_occupied_heat_set_point = 22;
let last_day_occupied_heat_set_point = 22;
let last_night_occupied_cool_set_point = 22;
let last_day_occupied_cool_set_point = 22;
let last_override = false;
let last_occupied_status = false;
let failedTempRetrievalreturn = 0;

/*
 * SUBSCRIPTION BASED UPDATES
 */
gun.get('pitherm/browser_vars').get('last_occupied').on(function (data) {
  if (Date(data) + (OCCUPIED_TIMEOUT * 60 * 1000) > Date.now()) {
    gun.get('pitherm/server_vars').get('occupied').put(true);
    last_occupied_status = true;
  } else {
    gun.get('pitherm/server_vars').get('occupied').put(false);
    last_occupied_status = false;
  }
  updateSystem();
});

gun.get('pitherm/browser_vars').get('mode').on(function (mode) {
  last_mode = mode;
  updateSystem();
});
gun.get('pitherm/browser_vars').get('unoccupied_heat_set_point').on(function (setpoint) {
  last_unoccupied_heat_set_point = setpoint;
  updateSystem();
});
gun.get('pitherm/browser_vars').get('unoccupied_cool_set_point').on(function (setpoint) {
  last_unoccupied_cool_set_point = setpoint;
  updateSystem();
});
gun.get('pitherm/browser_vars').get('night_occupied_heat_set_point').on(function (setpoint) {
  last_night_occupied_heat_set_point = setpoint;
  updateSystem();
});
gun.get('pitherm/browser_vars').get('day_occupied_heat_set_point').on(function (setpoint) {
  last_day_occupied_heat_set_point = setpoint;
  updateSystem();
});
gun.get('pitherm/browser_vars').get('night_occupied_cool_set_point').on(function (setpoint) {
  last_night_occupied_cool_set_point = setpoint;
  updateSystem();
});
gun.get('pitherm/browser_vars').get('day_occupied_cool_set_point').on(function (setpoint) {
  last_day_occupied_cool_set_point = setpoint;
  updateSystem();
});
gun.get('pitherm/browser_vars').get('variance').on(function (variance) {
  last_variance = variance;
  updateSystem();
});
gun.get('pitherm/browser_vars').get('fan').on(function (fan) {
  last_fan = fan;
  updateSystem();
});
gun.get('pitherm/browser_vars').get('override').on(function (override) {
  last_override = override;
  updateSystem();
});
gun.get('pitherm/browser_vars').get('setpoint').on(function (setpoint) {
  last_setpoint = setpoint;
  updateSystem();
});

/*
 * Helper Functions
 */
function daytime() {
  const DAY = (last_mode == 'cool' ? COOL_DAY : HEAT_DAY);

  const day = new Date();
  day.setHours(DAY, 0, 0, 0);

  const night = new Date();
  night.setHours(NIGHT, 0, 0, 0);

  const now = Date.now();
  return (now > day && now < night) ? true : false;
}

function heat(on) {
  HEAT.writeSync((on) ? ON : OFF);
  if (!last_fan) { fan(on); }
  gun.get('pitherm/server_vars').get('heat_status').put(on);
}

function cool(on) {
  COOL.writeSync((on) ? ON : OFF);
  if (!last_fan) { fan(on); }
  gun.get('pitherm/server_vars').get('cool_status').put(on);
}

function fan(on) {
  FAN.writeSync((on) ? ON : OFF);
  gun.get('pitherm/server_vars').get('fan_status').put(on);
}

function checkFail() {
  failedTempRetrieval++
  // If we have failed to get the temperature for 15 minutes, exit...
  if (failedTempRetrieval > 30) { 
    console.log('Failed to retrieve temperature for 15 minutes.  Something is seriously wrong...');
    process.exit(-1);
  }
}

function updateSystem() {
  // mode,occupied,override
  var setpoint = last_setpoint;
  const dt = daytime();

  if (last_override) { // Presence mode on, use programmed values
    if (last_occupied_status) {
      if (last_mode == 'cool')
        setpoint = (dt) ? last_day_occupied_cool_set_point :
                               last_night_occupied_cool_set_point;

      if (last_mode == 'heat')
        setpoint = (dt) ? last_day_occupied_heat_set_point :
                               last_night_occupied_heat_set_point;
    } else { // unoccupied
      if (last_mode == 'cool') { setpoint = last_unoccupied_cool_set_point; }
      if (last_mode == 'heat') { setpoint = last_unoccupied_heat_set_point; }
    }
    gun.get('pitherm/server_vars').get('setpoint').put(setpoint);
  }

  if      (last_mode == 'cool') { cool(last_temp > (setpoint + last_variance)); }
  else if (last_mode == 'heat') { heat(last_temp < (setpoint - last_variance)); }
  else if (last_mode == 'off')  { heat(false); cool(false); fan(last_fan); }

}


process.on('exit', (code) => {
  console.log('About to exit with code:', code);
  console.log('Cleaning up...');

  clearInterval(pollTemperature);

  HEAT.writeSync(0);
  HEAT.unexport();

  COOL.writeSync(0);
  COOL.unexport();

  FAN.writeSync(0);
  FAN.unexport();

  console.log('Done!  Until next time...');
});

const pollTemperature = setInterval(function () {
  glob('/sys/bus/w1/devices/28*/w1_slave', {}, function (err, files) {
    if (err) {
      console.log('ERROR: ', err);
      console.log(' -- will try again in 30 seconds...');
      checkFail();
      return;
    }

    // Should only have one 1wire temp sensor
    fs.readFile(files[0], 'ascii', function(err, temp) {
      if (err) {
        console.log('ERROR: ', err);
        console.log(' -- will try again in 30 seconds...');
        checkFail();
        return;
      }

      // Check for good CRC (get bad ones if wire is too long and it's cold)
      if (!temp.match('YES')) {
        console.log('ERROR: CRC failed, maybe because it\'s cold?');
        console.log(' -- will try again in 30 seconds...');
        checkFail();
        return;
      }

      failedTempRetrieval = 0;
      last_temp = temp.substr(temp.match('t=').index + 2, 5) / 1000.0;
      gun.get('pitherm/server_vars').get('current_temp').put(last_temp);
    });
  });

  // Make any changes necessary...
  updateSystem();

}, 30000);

console.log('Startup complete...');


