# PiTherm Dockerfile
# VERSION 1.0

FROM hypriot/rpi-node:7
MAINTAINER Paul Jordan <paullj1@gmail.com>

#HEALTHCHECK --interval=5m --timeout=3s \
  #CMD ps aux | grep -v grep | grep pitherm.py || exit 1

RUN apt-get update
RUN apt-get install -y --no-install-recommends \
        avahi-daemon \
        avahi-discover \
        build-essential \
        libavahi-compat-libdnssd-dev \
        libnss-mdns && \
        apt-get clean && \
        rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN npm install -g --unsafe-perm \
        homebridge \
        hap-nodejs \
        node-gyp && \
    cd /usr/local/lib/node_modules/homebridge/ && \
    npm install --unsafe-perm bignum && \
    cd /usr/local/lib/node_modules/hap-nodejs/node_modules/mdns && \
    node-gyp BUILDTYPE=Release rebuild

EXPOSE 5353 51826

RUN mkdir -p /var/run/dbus/

USER root

RUN mkdir -p /root/.homebridge
ADD entrypoint.sh plugins.txt config.json /root/.homebridge/

CMD /root/.homebridge/entrypoint.sh
