#!/usr/bin/python
import MySQLdb as sql
import datetime
import sys
import os

# PLIST Constants
LAST_OCCUPIED_ID = 7
IP_ADDRESSES = 15

def open_db():
    return sql.connect('127.0.0.1', 'root',
        os.environ['MYSQL_ROOT_PASSWORD'],
        os.environ['MYSQL_DATABASE'])

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
        cursor = db.cursor()
        cursor.execute(query)
        db.commit()
    except: # Try again next time
        db.rollback()
        print str(datetime.datetime.now()) + ": Error setting db_id: '"+str(db_id)+"' to '"+value+"'"
        print "     - More details: ", sys.exc_info()[0]
