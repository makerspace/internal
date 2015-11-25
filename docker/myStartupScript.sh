#!/bin/bash

# Start php-fpm and web server
/usr/local/sbin/php-fpm -D
nginx -g "daemon off;"
