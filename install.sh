#!/bin/bash
# Install PiTherm

# Installing boot init script
cp init_pitherm.sh /etc/init.d/
chmod +x /etc/init.d/init_pitherm.sh
update-rc.d init_pitherm defaults 100

# 1wire config
echo 'dtoverlay=w1-gpio,gpiopin=4' >> /boot/config.txt

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

