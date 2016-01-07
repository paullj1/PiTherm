#!/bin/bash
service=pitherm

if (( $(ps -ef | grep -v grep | grep -v pitherm.local | grep pitherm | wc -l) > 0 ))
then
echo "$service is running!!!"
else
/etc/init.d/$service start
fi
