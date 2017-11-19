# PiTherm Dockerfile
# VERSION 1.0

FROM arm32v6/node:alpine
MAINTAINER Paul Jordan <paullj1@gmail.com>

HEALTHCHECK --interval=5m --timeout=3s \
  CMD ps aux | grep -v grep | grep pitherm.js || exit 1

RUN apk add --no-cache python make g++

ADD package.json /tmp/package.json
RUN cd /tmp && npm install

RUN mkdir -p /opt/pitherm/ && \
    mv /tmp/node_modules /opt/pitherm/

WORKDIR /opt/pitherm/
ADD package.json pitherm.js /opt/pitherm/

CMD npm start
