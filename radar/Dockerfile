# PiTherm Dockerfile
# VERSION 1.0

FROM arm32v6/node:alpine
MAINTAINER Paul Jordan <paullj1@gmail.com>

HEALTHCHECK --interval=5m --timeout=3s \
  CMD ps aux | grep -v grep | grep radar.js || exit 1

ADD package.json /tmp/package.json
RUN cd /tmp && npm install

RUN mkdir -p /opt/pitherm-radar/ && \
    mv /tmp/node_modules /opt/pitherm-radar/

WORKDIR /opt/pitherm-radar/
ADD package.json radar.js /opt/pitherm-radar/

CMD npm start
