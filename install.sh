#!/bin/bash
# Install PiTherm

echo 'airmon-ng start wlan0' >> /etc/rc.local

cp radar.py /usr/local/bin/radar
chmod +x    /usr/local/bin/radar

cp thermo.py /usr/local/bin/pitherm
chmod +x     /usr/local/bin/pitherm

cp libthermostat.py /usr/local/bin/

cp pitherm /etc/init.d/pitherm
chmod +x   /etc/init.d/pitherm 

update-rc.d pitherm defaults
