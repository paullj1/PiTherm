# PiTherm Radar Dockerfile
# VERSION 1.0

FROM python:2-onbuild
MAINTAINER Paul Jordan <paullj1@gmail.com>

HEALTHCHECK --interval=5m --timeout=3s \
  CMD ps aux | grep -v grep | grep radar.py || exit 1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        iw \
    && rm -rf /var/lib/apt/lists/*

# Init monitor mode
RUN iw phy phy0 interface add mon0 type monitor \
 && iw dev wlan0 del \
 && ifconfig mon0 up \
 && iw dev mon0 set freq 2437

RUN mkdir /radar
ADD radar.py libthermostat.py /radar/
CMD [ "python", "/radar/radar.py" ]
