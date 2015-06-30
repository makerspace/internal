#!/bin/bash

# Setup PHP
sudo apt-get install -y php5-cli
sudo apt-get install -y php5-fpm
sudo apt-get install -y php5-xdebug
sudo apt-get install -y php5-mcrypt
sudo apt-get install -y php5-mysql
sudo apt-get install -y php5-curl

# Setup Nginx
sudo apt-get install -y nginx

sudo ln -s /vagrant/configs/xdebug.ini /etc/php5/fpm/conf.d/30-xdebug.ini

sudo service nginx reload
