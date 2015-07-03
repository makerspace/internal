#!/bin/bash

source /vagrant/setup/config.sh

# Remove nginx default vhost
if [ -f /etc/nginx/sites-enabled/default ];
then
	sudo rm /etc/nginx/sites-enabled/default
fi

# Setup nginx configuration
sudo rm /etc/nginx/nginx.conf
sudo cp /vagrant/configs/nginx.conf /etc/nginx/
sudo chown root:root /etc/nginx/nginx.conf
sudo chmod 644 /etc/nginx/nginx.conf

# Setup php fpm pool
sudo rm /etc/php5/fpm/pool.d/www.conf
sudo cp /vagrant/configs/www.conf /etc/php5/fpm/pool.d/
sudo chown root:root /etc/php5/fpm/pool.d/www.conf
sudo chmod 644 /etc/php5/fpm/pool.d/www.conf

# Setup the nginx vhost configuration file
if [ ! -f /etc/nginx/sites-enabled/internal.dev.conf ];
then
	sudo ln -s /vagrant/configs/internal.dev.conf /etc/nginx/sites-enabled/
fi

# Create a MySQL database and import content
if [ ! -f /var/log/databasesetup ];
then
	mysql -uroot -p$MYSQL_ROOT_PASSWORD < /vagrant/database/pre-install.sql

	if [ -f /vagrant/database/database.sql ];
	then
		mysql -uroot -p$MYSQL_ROOT_PASSWORD $MYSQL_DB < /vagrant/database/database.sql
	fi

	mysql -uroot -p$MYSQL_ROOT_PASSWORD < /vagrant/database/post-install.sql

	touch /var/log/databasesetup
fi

# Reload nginx and php5-fpm
sudo service nginx restart
sudo service php5-fpm restart
