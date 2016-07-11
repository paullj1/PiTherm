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
NUM_PACKETS = "10"

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

ip_addresses = therm.get_value_from_id(db, therm.IP_ADDRESSES)
if ip_addresses == "" :
    print("{0}: Radar Error: couldn't get IDs from database, trying again next time...".format(datetime.datetime.now()))
    db.close()
    sys.exit(0)

while True :
    for address in ip_addresses.split():
        FNULL = open(os.devnull, 'w')
        pkt = subprocess.call(["arping", "-i", "eth0", "-c", NUM_PACKETS, address], stdout=FNULL, stderr=FNULL)
        FNULL.close()
    
        if not db.open :
            db = sql.connect('localhost','thermostat','password','thermostat')

        if pkt == 0 : PktRcvd(db)

    time.sleep(10)	
