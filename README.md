PiTherm
Copyright 2014 - Paul Jordan (paullj1[at]gmail[dot]com)
========================================================================================
Thermostat back-end and front-end for Raspberry Pi with presence detection

Dependencies:
For Presence Monitoring
- The aircrack-ng suite (http://www.aircrack-ng.org)
- tcpdump

Others
- MySQL
- RPi GPIO Python Module

Notes:
- By default the install script will install the web front end into the root of /var/www/.  
  This should be modified if you do not want it there.
- Presence detection requires alternate wireless LAN card capable of entering monitor 
	mode
- Requires 3 relays driven by 3 GPIO pins to control heat/cool/fan.
- Uses 1wire temperature sensor
