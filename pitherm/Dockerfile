# PiTherm Dockerfile
# VERSION 1.0

FROM resin/rpi-raspbian:jessie
MAINTAINER Paul Jordan <paullj1@gmail.com>

HEALTHCHECK --interval=5m --timeout=3s \
  CMD ps aux | grep -v grep | grep pitherm.py || exit 1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        python \
        python-dev \
        python-pip \
        gcc \
        libmysqlclient-dev \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir /pitherm
ADD requirements.txt /pitherm/
RUN pip install -r /pitherm/requirements.txt

ADD pitherm.py libthermostat.py /pitherm/

CMD [ "python", "/pitherm/pitherm.py" ]
