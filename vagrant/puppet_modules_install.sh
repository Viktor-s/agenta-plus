#!/bin/sh

puppet module install puppetlabs-concat --version 1.2.2
puppet module install puppetlabs-apt --version 1.8
puppet module install saz-ssh
puppet module install jfryman-nginx
puppet module install puppetlabs-postgresql
puppet module install saz-ssh
puppet module install puppetlabs-git
puppet module install example42-yum
puppet module install Slashbunny-phpfpm
puppet module install cornfeedhobo-nano
puppet module install cornfeedhobo-nano

# ssh root@%host% "puppet apply --pluginsync --verbose --environment prod --hiera_config=/vagrant/hiera.yaml /vagrant/manifests/"