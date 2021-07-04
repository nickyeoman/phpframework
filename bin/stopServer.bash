#!/bin/bash

## Check sudo
#check if root
if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit;
fi

#Grab dotenv
export $(grep -v '^#' .env | xargs)

if [ "$USEDOCKER" != "docker" ]; then

  echo "TODO, find and stop php server, use docker it's working";

  exit;

fi;

# Docker

if [ "$DOCKERVOL" == "local" ]; then

  docker stop ${DOCKERNAME};
  docker rm ${DOCKERNAME};

  # Create a container for a database?
  if [ "$DOCKERDB" == "mariadb" ] || [ "$DOCKERDB" == "mysql" ]; then

    docker stop ${DOCKERNAME}-db;
    docker rm ${DOCKERNAME}-db;

  fi

  # Do you want phpmyadmin?
  if [[ $DOCKERPHPMYADMIN =~ '^[0-9]+$' ]] ; then

    docker stop ${DOCKERNAME}-phpmyadmin;
    docker rm ${DOCKERNAME}-phpmyadmin;

  fi

else

  docker stop ${DOCKERNAME}

fi
