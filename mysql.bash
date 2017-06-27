#!/bin/bash

PASS=$1$(( ( RANDOM % 10000000000000 )  + 1 ))
mysql -uroot -proot<<MYSQL_SCRIPT
CREATE DATABASE $1;
CREATE USER '$1'@'localhost' IDENTIFIED BY '$PASS';
GRANT ALL PRIVILEGES ON $1.* TO '$1'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT

sed -i -e 's/database_name_here/'$1'/g' wp-config-sample.php
sed -i -e 's/username_here/'$1'/g' wp-config-sample.php
sed -i -e 's/password_here/'$PASS'/g' wp-config-sample.php

mv wp-config-sample.php wp-config.php
