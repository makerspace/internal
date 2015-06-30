#!/bin/bash

source /vagrant/setup/config.sh

# Remove nginx default vhost
if [ -f /etc/nginx/sites-enabled/default ];
then
	rm /etc/nginx/sites-enabled/default
fi

# Setup the nginx configuration file
if [ ! -f /etc/nginx/sites-enabled/internal.dev.conf ];
then
	ln -s /vagrant/configs/internal.dev.conf /etc/nginx/sites-enabled/
fi

# Create a MySQL database and import content
if [ ! -f /var/log/databasesetup ];
then
	mysql -uroot -p$MYSQL_ROOT_PASSWORD < /vagrant/database/pre-install.sql

	if [ ! -f /vagrant/database/database.sql ];
	then
		mysql -uroot -p$MYSQL_ROOT_PASSWORD $MYSQL_DB < /vagrant/database/database.sql
	fi

	mysql -uroot -p$MYSQL_ROOT_PASSWORD < /vagrant/database/post-install.sql

	touch /var/log/databasesetup
fi

# Reload nginx
service nginx reload
