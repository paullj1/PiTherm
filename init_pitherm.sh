#!/bin/sh
### BEGIN INIT INFO
# Provides:          airmon-ng
# Required-Start:    $local_fs $network $named $time $syslog
# Required-Stop:     $local_fs $network $named $time $syslog
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Description:       Sets wlan0 to monitor mode
### END INIT INFO

# Init temp sensor 
modprobe w1-gpio
modprobe w1-therm

# Init monitor mode
iw phy phy0 interface add mon0 type monitor
iw dev wlan0 del
ifconfig mon0 up
iw dev mon0 set freq 2437
