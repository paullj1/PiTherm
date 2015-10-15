#!/bin/bash
# Install PiTherm

# Setting up 1wire sensor
modprobe w1-gpio
modprobe w1-therm

# Configuring wlan0 to run in monitor mode on boot
echo 'airmon-ng start wlan0' >> /etc/rc.local

# Installing radar
cp radar.py /usr/local/bin/radar
chmod +x    /usr/local/bin/radar

# Installing daemon
cp thermo.py /usr/local/bin/pitherm
chmod +x     /usr/local/bin/pitherm

cp libthermostat.py /usr/local/bin/

cp pitherm /etc/init.d/pitherm
chmod +x   /etc/init.d/pitherm 

update-rc.d pitherm defaults

# Installing web front-end
cp -ur ./www/* /var/www/
