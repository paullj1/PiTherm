/*
 * Seed the PiTherm Db
 * Author: Paul Jordan <paullj1@gmail.com>
 */

var port = process.env.PORT || 8080;
var Gun = require('gun');
var gun = new Gun(['http://localhost:' + port + '/gun']);

gun.get('pitherm/server_vars').put({
  current_temp: '22',
  last_occupied: '1970-01-01 00:00:00',
  occupied: 'false',
  heat_status: 'off',
  cool_status: 'off',
  fan_status: 'off'
});

gun.get('pitherm/browser_vars').put({
  current_setpoint: '22',
  mode: 'off',
  variance: '0.55',
  units: 'f',
  fan: 'auto',
  unoccupied_heat_set_point: '22',
  unoccupied_cool_set_point: '22',
  night_occupied_heat_set_point: '22',
  day_occupied_heat_set_point: '22',
  night_occupied_cool_set_point: '22',
  day_occupied_cool_set_point: '22',
  ip_addresses: '127.0.0.1',
  override: 'true'
});

gun.get('pitherm/server_vars').val(function (data) {
  console.log('Server vars:', data);
  gun.get('pitherm/browser_vars').val(function (data) {
    console.log('Browser vars:', data);
    process.exit(0);
  });
});


