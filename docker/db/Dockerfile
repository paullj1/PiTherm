# PiTherm's seeded database
# VERSION 1.0

FROM mysql:5
MAINTAINER Paul Jordan <paullj1@gmail.com>

RUN mkdir /db
ADD seed.sql /db/
RUN mysql --user ${MYSQL_USER} \
          --password ${MYSQL_PASS} \
          ${MYSQL_DATABASE} < /db/seed.sql
