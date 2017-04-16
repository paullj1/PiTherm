#!/bin/bash

echo "Enabling 1wire GPIO (w1-gpio) and temp sensor (w1-therm) kernel modules..."
sudo modprobe w1-gpio
sudo modprobe w1-therm
