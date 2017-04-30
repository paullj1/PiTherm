# PiTherm
PiTherm is a docker-compose service built for Raspberry Pi.  With $15 in hardware (in addition to the Pi), PiTherm turns your Pi into a smart thermostat that can be controlled using Siri with [Homebridge](https://github.com/nfarina/homebridge) or on your smartphone with the built-in web interface.  Additionally, PiTherm can be configured to detect the presence of your smartphone and turn off your AC or furnace when you're not home.

Steps to Install (from base [HypriotOS](https://blog.hypriot.com) image):
---
* `./prep_host.sh`
* reboot
* `docker-compose up -d`
* `./seed_db.sh`


Config
---
* Modify env vars in the `.env` file
* Create your homebridge config and plugins named:
  * `plugins.txt`
  * `config.json`

Hardware
---
* Requires 3 relays driven by 3 GPIO pins to control heat/cool/fan.
  * by default, driven on GPIO pins 22,23, and 25
  * [can be found for $8](https://www.google.com/search?q=4+channel+relay+board)
* Uses [$3 1wire temperature sensor (ds18b20)](https://learn.adafruit.com/adafruits-raspberry-pi-lesson-11-ds18b20-temperature-sensing?view=all)

Troubleshooting
---
* If PiTherm cannot see the temperature sensor, you may need to update your firmware:
  * `sudo apt update && sudo apt upgrade`
  * `sudo apt install rpi-update`
  * `sudo rpi-update`

Contact
---
* [Paul Jordan](http://paullj1.com) @ paullj1[at]gmail[dot]com
