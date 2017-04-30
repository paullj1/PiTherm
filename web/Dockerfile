# PiTherm's web front end UI
# VERSION 1.0

FROM nidorpi/rpi-php:7.0-apache
MAINTAINER Paul Jordan <paullj1@gmail.com>

HEALTHCHECK --interval=1m --timeout=5s \
  CMD curl -f http://localhost/ || exit 1

RUN docker-php-ext-install mysqli
RUN a2enmod rewrite

COPY ./www/ /var/www/html/

EXPOSE 80
