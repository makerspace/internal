#!/bin/bash

source /vagrant/setup/config.sh

sudo debconf-set-selections <<< "mysql-server-5.6 mysql-server/root_password password $MYSQL_ROOT_PASSWORD"
sudo debconf-set-selections <<< "mysql-server-5.6 mysql-server/root_password_again password $MYSQL_ROOT_PASSWORD"
sudo apt-get install -y mysql-server-5.6

sudo ln -s /vagrant/configs/mysql.cnf /etc/mysql/conf.d/mysql.cnf
