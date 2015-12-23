#!/usr/bin/env python
# Checks for Phone presence
import datetime
import signal
import _mysql as sql
import subprocess
import os
import time
import sys

import libthermostat as therm

# Constants
NUM_PACKETS = "1"

#  Functions
def cleanup(signal, frame) :
	global db
	db.close()
	sys.exit(0)

def PktRcvd(db) :
	therm.set_value_in_db(db, therm.LAST_OCCUPIED_ID, str(datetime.datetime.now())[:19])

#  Main Program 
db = sql.connect('localhost','thermostat','password','thermostat')
signal.signal(signal.SIGINT, cleanup)

#  Obtain phone MAC addresses... must do it before loop since db connection tends to time out while the app
#	 blocks waiting for tcpdump to return (for several hours while I'm at work).  This means that if those
#	 values are updated in the DB, the service must be restarted.
mac_addresses = therm.get_value_from_id(db, therm.MAC_ADDRESSES)
if mac_addresses == "" :
	print str(datetime.datetime.now()) + ": Radar Error: couldn't get IDs from database, trying again next time..."
	db.close()
	sys.exit(0)

tcp_filter = ""
first = True
for address in mac_addresses.split():
	if first :
		first = False
		tcp_filter = tcp_filter + "ether src "+str(address)+" "
	else :
		tcp_filter = tcp_filter + "or ether src "+str(address)+" "

while True :
	try : 
		FNULL = open(os.devnull, 'w')
		pkt = subprocess.check_output(["tcpdump", "-i", "mon0", "-c", NUM_PACKETS, "-p", tcp_filter], stderr=FNULL)
		FNULL.close()
		
		if not db.open :
			db = sql.connect('localhost','thermostat','password','thermostat')

		if pkt : PktRcvd(db)

	except :
		print str(datetime.datetime.now()) + ": Radar Error: ", sys.exc_info()[0]

	time.sleep(10)	
