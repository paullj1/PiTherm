#!/usr/bin/env python
import time
import sys
import signal
import RPi.GPIO as io
import _mysql as sql

import libthermostat as therm

# Close GPIO and database connections
def cleanup(signal, frame):
	global db
	therm.heat(db, False)
	therm.cool(db, False)
	io.cleanup()
	db.close()
	sys.exit(0)

# Database Connection
db = sql.connect('localhost','thermostat','password','thermostat')

therm.setup_io()
signal.signal(signal.SIGINT, cleanup)
while True:

	# Make Sure DB connection is still alive
	if not db.open :
		db = sql.connect('localhost','thermostat','password','thermostat')

	# Get variables from sensors/input
	occupied = therm.check_occupancy(db)
	indoor_temp = therm.get_temp(db)
	if (indoor_temp == -1) : break
	mode = therm.get_value_from_id(db, therm.MODE_ID)
	fan_on = therm.fan_status(db)
	override = therm.override_status(db)
	setpoint = therm.get_setpoint(db,mode,occupied,override)

	# Update output based on current state
	therm.update_pins(db, mode, fan_on, setpoint, indoor_temp)
	time.sleep(5)	
# end while

io.cleanup();
db.close();
