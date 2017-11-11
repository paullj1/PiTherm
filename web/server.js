/*
 * PiTherm Server
 * Author: Paul Jordan <paullj1@gmail.com>
 */

// Constants 
const port = process.env.PORT || 8080;

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

server.on('request', function (req, res) {
  if ( Gun.serve(req, res) ) { return; }
  serve(req, res, finalhandler(req, res));
});

server.listen(port, function () {
  console.log('\nPiTherm listening on port', port);
});
