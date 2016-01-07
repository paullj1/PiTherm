#!/bin/bash
service=pitherm

if (( $(ps -ef | grep -v grep | grep -v pitherm.local | grep pitherm | wc -l) > 0 ))
then
echo "$service is running!!!" > /dev/null
else
/etc/init.d/$service restart
echo "$service crashed at `date`, restarting..." >> /var/log/pitherm.log
fi
