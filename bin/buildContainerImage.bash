#!/bin/bash

#check if root
if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit;
fi

export $(grep -v '^#' .env | xargs)

docker build -t ${DOCKERIMAGE}:${DOCKERVER} .
