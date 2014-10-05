#!/bin/bash
# Uninstall PiTherm

echo "Remove 'airmon-ng start wlan0' from: /etc/rc.local"
rm /usr/local/bin/radar
rm /usr/local/bin/pitherm
rm /usr/local/bin/libthermostat.py 
service pitherm uninstall
