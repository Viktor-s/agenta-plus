#!/bin/sh

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

rsync \
    -avz \
    $DIR/ \
    --exclude=.vagrant/ \
    --exclude=Vagrantfile \
    --exclude=setup.sh \
    --exclude=bootstrap.sh \
    root@5.45.115.134:/configuration
