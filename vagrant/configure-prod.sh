#!/bin/sh

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

$DIR/sync.sh

ssh root@5.45.115.134 "/configuration/puppet_modules_install.sh && puppet apply --pluginsync --verbose --environment prod --hiera_config=/configuration/hiera_prod.yaml /configuration/manifests/"