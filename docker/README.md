PiTherm
===

Steps to Install:
---
* sudo apt update
* sudo apt upgrade
* sudo apt install rpi-update
* sudo rpi-update
* prep_host.sh
* reboot
* docker-compose up -d
* seed_db.sh


Config:
---
* Modify env vars in the `.env` file
* Create your homebridge config and plugins named:
  * `plugins.txt`
  * `config.json`
