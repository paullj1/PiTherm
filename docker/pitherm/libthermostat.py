#!/usr/bin/python
import RPi.GPIO as io
import MySQLdb as sql
import datetime
import sys
import time
import subprocess
import glob
import urllib
import os

# Constants
ON = io.LOW
OFF = io.HIGH
HEAT_PIN = 23
FAN_PIN = 24
COOL_PIN = 25
NIGHT = 21
DAY = 5
OCCUPIED_TIMEOUT = 30

# PLIST Constants
CURRENT_TEMP_ID = 1
CURRENT_SETPOINT_ID = 2
MODE_ID = 3
VARIANCE_ID = 4
FAN_AUTO_ID = 6
LAST_OCCUPIED_ID = 7
UNOCCUPIED_HEAT_ID = 8
UNOCCUPIED_COOL_ID = 9
NIGHT_OCCUPIED_HEAT_ID = 11
DAY_OCCUPIED_HEAT_ID = 12
NIGHT_OCCUPIED_COOL_ID = 13
DAY_OCCUPIED_COOL_ID = 14
IP_ADDRESSES = 15
OVERRIDE_ID = 17
OCCUPIED_ID = 18
HEAT_STATUS_ID = 19
COOL_STATUS_ID = 20
FAN_STATUS_ID = 21

# Init GPIO
def setup_io():
    io.setmode(io.BCM)
    io.setwarnings(False)
    io.setup(HEAT_PIN, io.OUT, initial=OFF)
    io.setup(FAN_PIN, io.OUT, initial=OFF)
    io.setup(COOL_PIN, io.OUT, initial=OFF)

def open_db():
    return sql.connect('127.0.0.1', 'root',
        os.environ['MYSQL_ROOT_PASSWORD'],
        os.environ['MYSQL_DATABASE'])

def read_sensor_file():

    count = 0
    raw_text = None

    while True:
        try: 
            f = open((glob.glob('/sys/bus/w1/devices/28*')[0] + '/w1_slave'), 'r') 
            raw_text = f.readlines()
            f.close()
        except:
            if count > 10:
                print str(datetime.datetime.now()) + ": IO error getting indoor temperature... shutting down"
                print "     - More details: ", sys.exc_info()[0]
                break
            else:
                count += 1
                time.sleep(30)
                continue
        
        # Got contents, break
        break

    return raw_text

# Read temperature
def get_temp(db):
    indoor_temp = -1.0
    raw_text = read_sensor_file()

    if raw_text is None:
        return indoor_temp

    # Check for good CRC (get bad ones if wire is too long and it's cold)
    while raw_text[0].strip()[-3:] != 'YES':
        time.sleep(0.5)
        raw_text = read_sensor_file()
        if raw_text is None:
            return indoor_temp

    equals_pos = raw_text[1].find('t=')
    if equals_pos == -1:
        return indoor_temp

    temp_string = raw_text[1][equals_pos+2:]
    temp_c = float(temp_string) / 1000.0
    temp_f = temp_c * 9.0 / 5.0 + 32.0
    indoor_temp = temp_f # TODO make this dependent upon db val

    # Write it out to database
    set_value_in_db(db, CURRENT_TEMP_ID, indoor_temp)
    return indoor_temp

def get_value_from_id(db, db_id):
    try:
        query = "SELECT `value` FROM `status` WHERE `id`="+str(db_id)+";"
        cursor = db.cursor()
        cursor.execute(query)
        db.commit()

        return str(cursor.fetchone()[0])
    except: # Try again next time
        print str(datetime.datetime.now()) + ": Error getting db_id: '"+str(db_id)+"'"
        print "     - More details: ", sys.exc_info()[0]
        return ""

def set_value_in_db(db, db_id, value):
    try:
        query = "UPDATE  `thermostat`.`status` SET  `value` =  '"+str(value)+"' WHERE  `status`.`id` ="+str(db_id)+";"
        db.query(query)
    except: # Try again next time
        print str(datetime.datetime.now()) + ": Error setting db_id: '"+str(db_id)+"' to '"+value+"'"
        print "     - More details: ", sys.exc_info()[0]

def fan_status(db):
    status = get_value_from_id(db, FAN_AUTO_ID)
    if status == 'on':
        return True
    return False

def override_status(db):
    status = get_value_from_id(db, OVERRIDE_ID)
    if status == 'True':
        return True
    return False

# Get Setpoint
def get_setpoint(db,mode,occupied,override):
    # Day or Night?
    now = datetime.datetime.now()
    day = now.replace(hour=DAY, minute=0, second=0, microsecond=0)
    night = now.replace(hour=NIGHT, minute=0, second=0, microsecond=0)
    if now > day and now < night:
        daytime = True
    else:
        daytime = False

    if mode == 'off':
        return 0

    if override: 
        setpoint_id = CURRENT_SETPOINT_ID
    else:
        if occupied:
            if mode == 'cool':
                if daytime:
                    setpoint_id = DAY_OCCUPIED_COOL_ID
                else: # night
                    setpoint_id = NIGHT_OCCUPIED_COOL_ID
            if mode == 'heat':
                if daytime:
                    setpoint_id = DAY_OCCUPIED_HEAT_ID
                else: # night
                    setpoint_id = NIGHT_OCCUPIED_HEAT_ID
        else: # unoccupied
            if mode == 'cool':
                setpoint_id = UNOCCUPIED_COOL_ID
            if mode == 'heat':
                setpoint_id = UNOCCUPIED_HEAT_ID
        #end if occupied
    # end if override

    # Get setpoint from DB based on mode and day/night
    setpoint = int(get_value_from_id(db, setpoint_id))

    # Update Setpoint in db, then return it
    set_value_in_db(db, CURRENT_SETPOINT_ID, setpoint)
    return setpoint
# end get_setpoint()

# Turn heat on/off
def heat(db,status):
    if status:
        io.output(HEAT_PIN, ON)
        set_value_in_db(db, HEAT_STATUS_ID, 'on')
    else:
        io.output(HEAT_PIN, OFF)
        set_value_in_db(db, HEAT_STATUS_ID, 'off')

# Turn cooling on/off
def cool(db,status):
    if status:
        io.output(COOL_PIN, ON)
        set_value_in_db(db, COOL_STATUS_ID, 'on')
    else:
        io.output(COOL_PIN, OFF)
        set_value_in_db(db, COOL_STATUS_ID, 'off')

# Turn fan on/auto
def fan(db,status):
    if status:
        io.output(FAN_PIN, ON)
        set_value_in_db(db, FAN_STATUS_ID, 'on')
    else:
        io.output(FAN_PIN, OFF)
        set_value_in_db(db, FAN_STATUS_ID, 'off')

# Check to see if anyone is home
def check_occupancy(db):
    # Get last occupied
    result = get_value_from_id(db, LAST_OCCUPIED_ID)
    if result:
        last_occupied = datetime.datetime.strptime(result, '%Y-%m-%d %H:%M:%S') 
        if last_occupied < (datetime.datetime.now() - datetime.timedelta(minutes=OCCUPIED_TIMEOUT)):
            return False
    else:
        return False

    set_value_in_db(db, OCCUPIED_ID, 'True')
    return True
# end check_occupancy()

def update_pins(db, mode, fan_on, setpoint, indoor_temp):
    if get_value_from_id(db, HEAT_STATUS_ID) == 'off' and get_value_from_id(db, COOL_STATUS_ID) == 'off':
        variance = float(get_value_from_id(db, VARIANCE_ID))
    else:
        variance = 1

    # Auto = false
    fan(db, fan_on)

    if mode == 'cool':
        if indoor_temp > (setpoint + variance): cool(db, True)
        else: cool(db, False)
        return

    if mode == 'heat':
        if indoor_temp < (setpoint - variance): heat(db, True)
        else: heat(db, False)
        return
    
    if mode == 'off':
        heat(db, False)
        cool(db, False)

# end update_pins
