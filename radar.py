#!/usr/bin/python
# Checks for Phone presence

import select
import sys
import pybonjour
import datetime
import _mysql as sql

regtype = '_apple-mobdev2._tcp'
timeout  = 5
resolved = []

# When callback happens, resolve here
def resolve_callback(sdRef, flags, interfaceIndex, errorCode, fullname,
                     hosttarget, port, txtRecord):
	if errorCode == pybonjour.kDNSServiceErr_NoError:
		justName = hosttarget.split(".", 1)
		#print 'Host: ' + justName[0]
		# Update Last Occupied in the DB
		db = sql.connect('localhost','thermostat','password','thermostat')
		query = "UPDATE  `thermostat`.`status` SET  `value` =  '"+str(datetime.datetime.now())[:19]+"' WHERE  `status`.`id` =7;"
		db.query(query)
		db.close()
	#end if
#end def


def browse_callback(sdRef, flags, interfaceIndex, errorCode, serviceName,
					regtype, replyDomain):
	if errorCode != pybonjour.kDNSServiceErr_NoError:
		return

	#print 'Time: ' + str(datetime.datetime.now())
	resolve_sdRef = pybonjour.DNSServiceResolve(0,
						    interfaceIndex,
						    serviceName,
						    regtype,
						    replyDomain,
						    resolve_callback)

	try:
		while not resolved:
			ready = select.select([resolve_sdRef], [], [], timeout)
			if resolve_sdRef not in ready[0]:
				break
			pybonjour.DNSServiceProcessResult(resolve_sdRef)
		else:
			resolved.pop()
	finally:
		resolve_sdRef.close()
#end def


# Main
browse_sdRef = pybonjour.DNSServiceBrowse(regtype = regtype, callBack = browse_callback)

# Look for keyboard interrupt
try:
	try:
		while True:
			ready = select.select([browse_sdRef], [], [])
			if browse_sdRef in ready[0]:
				pybonjour.DNSServiceProcessResult(browse_sdRef)
		#end while
	#end try
	except KeyboardInterrupt:
		pass
finally:
	browse_sdRef.close()
