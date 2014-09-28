#!/bin/bash

sudo python radar.py >> error.log &
sudo python thermo.py >> error.log &
