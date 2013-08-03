#!/bin/bash

# Script for Debian Squeeze

# Variables
PARENT_DIR=/web             # Parent for all web files
PROJECT_NAME=project        # Project name

MYSQLROOTPASSWD=mysqlpasswd # Mysql root pass
DBNAME=project

# Making dirs
mkdir -p ${PARENT_DIR}/${PROJECT_NAME}

# Repository for nginx 1.0.0 or above
wget  http://nginx.org/keys/nginx_signing.key
apt-key add nginx_signing.key
echo "deb http://nginx.org/packages/debian/ wheezy nginx" >> /etc/apt/sources.list
echo "deb-src http://nginx.org/packages/debian/ wheezy nginx" >> /etc/apt/sources.list

# Installing software
apt-get update
# Common software
apt-get install screen mc make -y
# Install nginx
apt-get install nginx -y
# PHP
apt-get install php5 php5-cli php5-dev php5-fpm -y
# PHP extensions
apt-get install php-apc php5-mcrypt -y
# MySQL
echo mysql-server mysql-server/root_password select ${MYSQLROOTPASSWD} | debconf-set-selections
echo mysql-server mysql-server/root_password_again select ${MYSQLROOTPASSWD} | debconf-set-selections
apt-get install nginx mysql-server mysql-client php5-mysql -y
# Git
apt-get install git-core -y

# Moving project files
cp -frp ../* ${PARENT_DIR}/${PROJECT_NAME}/

# Nginx config
rm /etc/nginx/nginx.conf
ln -s ${PARENT_DIR}/${PROJECT_NAME}/misc/configs/nginx/nginx.conf /etc/nginx/
ln -s ${PARENT_DIR}/${PROJECT_NAME}/misc/configs/nginx/sites-enabled /etc/nginx/
ln -s ${PARENT_DIR}/${PROJECT_NAME}/misc/configs/nginx/locations.conf /etc/nginx/

# Mysql config
mysqladmin -u root password ${MYSQLROOTPASSWD}
mysqladmin -u root -p${MYSQLROOTPASSWD} create ${DBNAME}

# php config
rm /etc/php5/fpm/php.ini
ln -s ${PARENT_DIR}/${PROJECT_NAME}/misc/configs/php/php.ini /etc/php5/fpm/php.ini

# php-fpm config
rm /etc/php5/fpm/php-fpm.conf
ln -s ${PARENT_DIR}/${PROJECT_NAME}/misc/configs/php/php-fpm.conf /etc/php5/fpm/php-fpm.conf

# All permissions
chmod -R 777 ${PARENT_DIR}/${PROJECT_NAME}

# Restarting
/etc/init.d/nginx restart
/etc/init.d/php5-fpm restart

# Finish
echo "Done!"