#!/bin/bash

tempSqlFile='/tmp/seed.sql'
cat > "$tempSqlFile" <<-EOSQL
	SET time_zone = "+00:00";
	USE thermostat

	CREATE TABLE IF NOT EXISTS \`status\` (
		\`id\` int(11) NOT NULL AUTO_INCREMENT,
		\`description\` varchar(128) COLLATE utf8_bin NOT NULL,
		\`value\` varchar(128) COLLATE utf8_bin NOT NULL,
		PRIMARY KEY (\`id\`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=22 ;

	INSERT INTO thermostat.status (\`id\`, \`description\`, \`value\`) VALUES
		(1, 'Current Temperature', '75.0866'),
		(2, 'Current Set Point', '71'),
		(3, 'Mode (heat/cool/off)', 'off'),
		(4, 'Variance', '1.5'),
		(5, 'Units (C/F)', 'F'),
		(6, 'Fan (auto/on)', 'auto'),
		(7, 'Last Occupied (time)', '2014-04-23 13:37:00'),
		(8, 'Unoccupied Heat Set Point', '60'),
		(9, 'Unoccupied Cool Set Point', '77'),
		(11, 'Night Occupied Heat Set Point', '62'),
		(12, 'Day Occupied Heat Set Point', '68'),
		(13, 'Night Occupied Cool Set Point', '71'),
		(14, 'Day Occupied Cool Set Point', '73'),
		(15, 'IP Addresses', '127.0.0.1'),
		(17, 'Override (True/False)', 'True'),
		(18, 'Occupied (True/False)', 'False'),
		(19, 'Heat Status (on/off)', 'off'),
		(20, 'Cool Status (on/off)', 'off'),
		(21, 'Fan Status (on/off)', 'off');

	CREATE TABLE IF NOT EXISTS \`history\` (
		\`id\` int(11) NOT NULL AUTO_INCREMENT,
		\`time_changed\` datetime COLLATE utf8_bin NOT NULL,
		\`outdoor_temp\` float COLLATE utf8_bin NOT NULL,
		\`indoor_temp\` float COLLATE utf8_bin NOT NULL,
		\`wind_speed\` float COLLATE utf8_bin NOT NULL,
		\`wind_dir\` smallint COLLATE utf8_bin NOT NULL,
		\`weather_condition\` varchar(128) COLLATE utf8_bin NOT NULL,
		\`visibility\` smallint COLLATE utf8_bin NOT NULL,
		\`humidity\` smallint COLLATE utf8_bin NOT NULL,
		\`pressure\` smallint COLLATE utf8_bin NOT NULL,
		\`setpoint\` smallint COLLATE utf8_bin NOT NULL,
		\`system_mode\` varchar(16) COLLATE utf8_bin NOT NULL,
		\`cool\` varchar(3) COLLATE utf8_bin NOT NULL,
		\`fan\` varchar(3) COLLATE utf8_bin NOT NULL,
		\`heat\` varchar(3) COLLATE utf8_bin NOT NULL,
		\`last_occupied\` datetime COLLATE utf8_bin NOT NULL,
		PRIMARY KEY (\`id\`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=22 ;

EOSQL

mysql --user=root --password=$MYSQL_ROOT_PASSWORD < /tmp/seed.sql
