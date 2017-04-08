# PiTherm Dockerfile
# VERSION 1.0

FROM python:2-onbuild
MAINTAINER Paul Jordan <paullj1@gmail.com>

HEALTHCHECK --interval=5m --timeout=3s \
  CMD ps aux | grep -v grep | grep pitherm.py || exit 1

RUN echo 'dtoverlay=w1-gpio,gpiopin=4' >> /boot/config.txt

RUN mkdir /pitherm
ADD pitherm.py libthermostat.py /pitherm/
CMD [ "python", "/pitherm/pitherm.py" ]
