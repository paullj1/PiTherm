# PiTherm's web front end UI
# VERSION 1.0

FROM php:7
MAINTAINER Paul Jordan <paullj1@gmail.com>

HEALTHCHECK --interval=1m --timeout=5s \
  CMD curl -f https://localhost/ || exit 1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        sqlite3 \
    && rm -rf /var/lib/apt/lists/* 

COPY ./www/ /var/www/html/

EXPOSE 80
