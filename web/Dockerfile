# PiTherm Dockerfile
# VERSION 1.0

FROM arm32v6/node:alpine
MAINTAINER Paul Jordan <paullj1@gmail.com>

HEALTHCHECK --interval=5m --timeout=3s \
  CMD echo -ne "HEAD / HTTP/1.0\r\n\r\n" | nc -n -i 1 localhost 8080

RUN apk add --no-cache python make g++

ADD package.json /tmp/package.json
RUN cd /tmp && npm install

RUN mkdir -p /opt/pitherm/ && \
    mv /tmp/node_modules /opt/pitherm/

VOLUME /opt/pitherm/
WORKDIR /opt/pitherm/

ADD package.json server.js /opt/pitherm/
COPY public/ /opt/pitherm/public/
RUN mkdir /opt/pitherm/data

EXPOSE 8080
CMD npm start

