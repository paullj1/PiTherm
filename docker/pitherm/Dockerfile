# PiTherm Dockerfile
# VERSION 1.0

FROM resin/rpi-raspbian:jessie
MAINTAINER Paul Jordan <paullj1@gmail.com>

HEALTHCHECK --interval=5m --timeout=3s \
  CMD ps aux | grep -v grep | grep pitherm.py || exit 1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        python python-pip \
    && rm -rf /var/lib/apt/lists/*

RUN echo 'dtoverlay=w1-gpio,gpiopin=4' >> /boot/config.txt

RUN mkdir /pitherm
ADD pitherm.py libthermostat.py requirements.txt /pitherm/
RUN pip install -r /radar/requirements.txt

CMD [ "python", "/pitherm/pitherm.py" ]
