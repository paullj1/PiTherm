#!/usr/bin/python
# Checks for Phone presence
import datetime
import signal
import _mysql as sql
import subprocess
import os
import time

import libthermostat as therm
#from therm import get_value_from_id, set_value_in_db, LAST_OCCUPIED_ID, PJ_PHONE_ID, KT_PHONE_ID

# PLIST Constants
#LAST_OCCUPIED_ID = 7
#PJ_PHONE_ID = 15
#KT_PHONE_ID = 16

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

while True :
	pj_id = therm.get_value_from_id(db, therm.PJ_PHONE_ID)
	kt_id = therm.get_value_from_id(db, therm.KT_PHONE_ID)
	tcp_filter = "ether src "+pj_id+" or ether src "+kt_id

	FNULL = open(os.devnull, 'w')
	pkt = subprocess.check_output(["tcpdump", "-i", "mon0", "-c", "1", "-p", tcp_filter], stderr=FNULL)
	if pkt : PktRcvd(db)
	time.sleep(10)	
