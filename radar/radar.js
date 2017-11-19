/*
 * PiTherm Radar Module
 * This module searches for phones with IP addresses identified by the user
 */

console.log('Starting PiTherm\'s radar...');

const port = process.env.PORT || 8080;
const host = process.env.HOST || 'localhost';
const peer = 'http://' + host + ':' + port + '/gun';

const Gun = require('gun');
const gun = new Gun(peer);
const cp = require('child_process');

const NUM_PACKETS = 10;
var addresses = [];

gun.get('pitherm/browser_vars').get('ip_addresses').on(function (addrs) {
  addresses = addrs.split(',');
});

const pingAddresses = setInterval(function () {
  addresses.forEach(function(addr) {

    const child = cp.spawn('arping', ['-c', NUM_PACKETS, addr]);
    child.on('exit', (code, signal) => {
      if (code == 0) {
        gun.get('pitherm/server_vars').get('last_occupied').put(Date.now());
      }
    });

  });
}, 30000);



console.log('Startup complete...');


