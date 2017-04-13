#!/usr/bin/env python
# Checks for phone presence
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
def cleanup(signal, frame):
    sys.exit(0)


def PktRcvd():
    db = sql.connect('db',
        os.environ['MYSQL_USER'],
        os.environ['MYSQL_PASS'],
        os.environ['MYSQL_DATABASE'])
    therm.set_value_in_db(db, therm.LAST_OCCUPIED_ID, str(datetime.datetime.now())[:19])
    db.close()

#  Main Program 
signal.signal(signal.SIGINT, cleanup)

db = sql.connect('db',
    os.environ['MYSQL_USER'],
    os.environ['MYSQL_PASS'],
    os.environ['MYSQL_DATABASE'])
ip_addresses = therm.get_value_from_id(db, therm.IP_ADDRESSES)
db.close()

if ip_addresses == "":
    print("{0}: Radar Error: couldn't get IDs from database, trying again next time...".format(datetime.datetime.now()))
    sys.exit(0)

while True:
    for address in ip_addresses.split():
        FNULL = open(os.devnull, 'w')
        pkt = subprocess.call(["arping", "-i", "eth0", "-c", NUM_PACKETS, address], stdout=FNULL, stderr=FNULL)
        FNULL.close()
    
        if pkt == 0: PktRcvd()

    time.sleep(10)	
