#!/bin/sh

# Configure server enviroment
echo "cd /var/www/agentplus.dev" >> /home/vagrant/.bashrc

# Update composer
/usr/local/bin/composer selfupdate

# Configure agentplus system
cd /var/www/agentplus.dev
sudo chmod 0777 -R var
/usr/local/bin/composer install --no-interaction
