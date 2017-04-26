# PiTherm Radar Dockerfile
# VERSION 1.0

FROM resin/rpi-raspbian:jessie
MAINTAINER Paul Jordan <paullj1@gmail.com>

HEALTHCHECK --interval=5m --timeout=3s \
  CMD ps aux | grep -v grep | grep radar.py || exit 1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        python \
        python-dev \
        python-pip \
        gcc \
        libmysqlclient-dev \
        arping \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir /radar
ADD radar.py libradar.py requirements.txt /radar/
RUN pip install -r /radar/requirements.txt

CMD [ "python", "/radar/radar.py" ]
