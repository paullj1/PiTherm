#!/usr/bin/env python
# Checks for phone presence
import datetime
import signal
import _mysql as sql
import subprocess
import os
import time
import sys

import libradar as lradar

# Constants
NUM_PACKETS = "10"

#  Functions
def cleanup(signal, frame):
    sys.exit(0)


def PktRcvd():
    db = lradar.open_db()
    lradar.set_value_in_db(db, lradar.LAST_OCCUPIED_ID, str(datetime.datetime.now())[:19])
    db.close()

#  Main Program 
signal.signal(signal.SIGINT, cleanup)

db = lradar.open_db()
ip_addresses = lradar.get_value_from_id(db, lradar.IP_ADDRESSES)
db.close()

if ip_addresses == "":
    print("{0}: Radar Error: couldn't get IDs from database, trying again next time...".format(datetime.datetime.now()))
    sys.exit(0)

while True:
    for address in ip_addresses.split():
        FNULL = open(os.devnull, 'w')
        pkt = subprocess.call(["arping", "-c", NUM_PACKETS, address], stdout=FNULL, stderr=FNULL)
        FNULL.close()
    
        if pkt == 0: PktRcvd()

    time.sleep(10)	
