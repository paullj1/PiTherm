/*
 * PiTherm Server
 * Author: Paul Jordan <paullj1@gmail.com>
 */

// Constants 
const port = process.env.PORT || 80;

// Imports
var http = require('http');
var serveStatic = require('serve-static');
var finalhandler = require('finalhandler')
var Gun = require('gun');
var gunlevel = require('gun-level')

// LevelDB
const levelup = require('levelup')
const encode = require('encoding-down')
const leveldown = require('leveldown')
const levelDB = levelup(
  encode(
    leveldown('data'),
    { valueEncoding: 'json' }
  )
);

// Setup HTTP server
var server = http.Server();
var serve = serveStatic('public', { 'index': 'index.html' });

// Setup Gun
var gun = Gun({
  file: false,
  level: levelDB,
  web: server // Handles real-time requests and updates.
});

// Get data for HomeKit
var browser_vars = null;
var server_vars = null;

gun.get('pitherm/browser_vars').on(function (data) { browser_vars = data; });
gun.get('pitherm/server_vars').on(function (data) { server_vars = data; });


server.on('request', function (req, res) {
  // Gun DB
  if ( Gun.serve(req, res) ) { return; }

  // HomeKit API
  if ( req.url.match(/^\/status$/) ) { return get_status(res); }
  if ( req.url.match(/^\/targetHeatingCoolingState\/[0123]$/) ) {
    return set_mode(req.url.split("/").slice(-1)[0], res);
  }
  if ( req.url.match(/^\/targetTemperature\/([0-9]*[.])?[0-9]+$/) ) { 
    return set_temp(parseFloat(req.url.split("/").slice(-1)[0]), res);
  }
    
  // Static Content
  serve(req, res, finalhandler(req, res));
});

server.listen(port, function () {
  console.log('\nPiTherm listening on port', port);
});


function get_status(res) {

  if ( server_vars.heat_status == true ) { current_state = 1; }
  else if ( server_vars.cool_status == true ) { current_state = 2; }
  else { current_state = 0; }

  if ( browser_vars.mode == 'heat' ) { target_state = 1; }
  else if ( browser_vars.mode == 'cool' ) { target_state = 2; }
  else { target_state = 0; }

  data = {
    currentTemperature: server_vars.current_temp,
    targetTemperature: browser_vars.current_setpoint,
    currentHeatingCoolingState: current_state,
    targetHeatingCoolingState: target_state
  }

  res.writeHead(200, {'Content-Type': 'application/json'});
  res.write(JSON.stringify(data));
  res.end();
  return res;
}

function set_mode(mode, res) {
  if (mode == '1') {
    gun.get('pitherm/browser_vars').get('mode').put('heat');
  } else if (mode == '2') {
    gun.get('pitherm/browser_vars').get('mode').put('cool');
  } else {
    gun.get('pitherm/browser_vars').get('mode').put('off');
  }
  res.writeHead(200, {'Content-Type': 'text/html'});
  res.end();
  return res;
}

function set_temp(temp, res) {
  gun.get('pitherm/browser_vars').get('override').put(false);
  gun.get('pitherm/browser_vars').get('current_setpoint').put(temp);
  res.writeHead(200, {'Content-Type': 'text/html'});
  res.end();
  return res;
}

