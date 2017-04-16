# PiTherm's web front end UI
# VERSION 1.0

FROM nidorpi/rpi-php:7.0-apache
MAINTAINER Paul Jordan <paullj1@gmail.com>

HEALTHCHECK --interval=1m --timeout=5s \
  CMD curl -f http://localhost/ || exit 1

RUN docker-php-ext-install mysqli

COPY ./www/ /var/www/html/
RUN sed -i -e "s/OPEN_WEATHER_API_KEY/${OPEN_WEATHER_API_KEY}/" /var/www/html/js/functions.js
RUN sed -i -e "s/OPEN_WEATHER_LOCATION/${OPEN_WEATHER_LOCATION}/" /var/www/html/js/functions.js

EXPOSE 80
