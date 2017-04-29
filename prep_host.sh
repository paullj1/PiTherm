#!/bin/bash

echo "Enabling 1wire GPIO (w1-gpio) and temp sensor (w1-therm) kernel modules..."
echo "w1-gpio" | sudo tee --append /etc/modules > /dev/null
echo "w1-therm" | sudo tee --append /etc/modules > /dev/null

echo "Adding 1wire to /boot/config.txt"
echo "dtoverlay=w1-gpio,gpiopin=4" | sudo tee --append /boot/config.txt > /dev/null

echo "Disabling IPv6"
sudo sed -i "s/$/ ipv6.disable=1/" /boot/cmdline.txt
