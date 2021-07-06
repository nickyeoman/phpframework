#!/bin/bash

## Check sudo
#check if root
if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit;
fi

#Grab dotenv
export $(grep -v '^#' .env | xargs)

# Do we want PHP server?
if [ "$USEDOCKER" != "docker" ]; then

  echo "Starting php server";

  # Run in background as Devon advised
  nohup php -S localhost:${DOCKERPORT} -t public/ > phpd.log 2>&1 &

  # Get last background process PID
  PHP_SERVER_PID=$!

  # Send SIGQUIT to php built-in server running in background to stop it
  echo "PHP Server is running on port ${DOCKERPORT}"
  echo "to stop: kill -9 $PHP_SERVER_PID"
  echo "optionallly: rm phpd.log"

  exit;

fi;

# Start Docker work

docker build -t ${DOCKERIMAGE}:${DOCKERVER} .

# TODO: chmod this to the right user, not secure (www-data)
chmod -R 777 tmp/

if [ "$DOCKERVOL" == "local" ]; then

  docker run -d -p ${DOCKERPORT}:80 --name ${DOCKERNAME} --net ${DOCKERNET} -v ${PWD}:/website ${DOCKERIMAGE}:${DOCKERVER}
  #echo "for debug local: docker run -d -p ${DOCKERPORT}:80 --name ${DOCKERNAME} --net ${DOCKERNET} -v ${PWD}:/website ${DOCKERIMAGE}:${DOCKERVER}"

  # Create a container for a database?
  if [ "$DOCKERDB" == "mariadb" ] || [ "$DOCKERDB" == "mysql" ]; then

    docker run -d -p ${DBPORT}:3306 --name ${DOCKERNAME}-db --net ${DOCKERNET} -v ${DOCKERNAME}-db:/var/lib/mysql \
    -e MYSQL_ROOT_PASSWORD=${DBPASSWORD} \
    -e MYSQL_PASSWORD=${DBPASSWORD} \
    -e MYSQL_USER=${DBUSER} \
    -e MYSQL_DATABASE=${DB} \
    mariadb:latest;

    echo "Started docker container ${DOCKERNAME}-db on port ${DBPORT}"

  fi

  # Do you want phpmyadmin?
  if [[ $DOCKERPHPMYADMIN =~ ^[0-9]+$ ]] ; then

    # TODO: Can we just put in the info an auto connect?
    docker run -d -p ${DOCKERPHPMYADMIN}:80 --name ${DOCKERNAME}-phpmyadmin --net ${DOCKERNET} \
    -e PMA_ARBITRARY="1" \
    -e UPLOAD_LIMIT="200M" \
    phpmyadmin/phpmyadmin:latest

    echo "Started docker container ${DOCKERNAME}-phpmyadmin on port ${DOCKERPHPMYADMIN}"

  fi

  echo "Started docker container ${DOCKERNAME}"
  echo "When done: docker stop ${DOCKERNAME}; docker rm ${DOCKERNAME}"

else

  docker run -d -p ${DOCKERPORT}:80 --name ${DOCKERNAME} -v ${DOCKERNAME}:/website --net ${DOCKERNET} ${DOCKERIMAGE}:${DOCKERVER}
  #echo "for debug nonlocal: docker run -d -p ${DOCKERPORT}:80 --name ${DOCKERNAME} -v ${DOCKERNAME}:/website --net ${DOCKERNET} ${DOCKERIMAGE}:${DOCKERVER}"

fi