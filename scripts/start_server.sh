#!/bin/bash
systemctl start mariadb.service
systemctl start httpd.service
systemctl start php-fpm.service