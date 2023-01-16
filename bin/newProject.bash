#!/bin/bash

echo "*** Starting New Project Script ***"

# TODO: check if script has already been run

# check that $1 folder does not exit
[ -d "$1" ] && echo "Folder $1 already exists" && exit 1


composer -v > /dev/null 2>&1
COMPOSER=$?
if [[ $COMPOSER -ne 0 ]]; then
    echo 'Composer is not installed'
    echo 'Checkout how to Install Composer here: https://www.nickyeoman.com/page/install-composer-on-ubuntu'
    echo 'Once installed, try running this script again'
    exit 1
else
  mkdir $1
  cd $1
  composer require nickyeoman/phpframework

  # Add controllers dir
  php vendor/nickyeoman/phpframework/bin/composer-psr4-controllers.php
  composer dump-autoload -o

  echo "Composer has installed nickyeoman/phpframework to $1"
fi

################################################################################
# Create directories
################################################################################

echo "Creating directories: controllers tmp public views scripts"
mkdir -p controllers tmp sass scripts helpers views
mkdir -p public/css public/js public/images

################################################################################
# Public Folder
################################################################################

echo "Creating index.php page in public directory"
cp vendor/nickyeoman/phpframework/public/index.php public/.

echo "Adding Apache htaccess"
cp vendor/nickyeoman/phpframework/public/htaccess public/.htaccess

################################################################################
# SASS
################################################################################

echo "Creating SASS directory for css"
cp vendor/nickyeoman/phpframework/sass/project.sass sass/.

# TODO: Check that sass is installed (above)
sass sass/project.sass public/css/main.css

################################################################################
# Configuration
################################################################################

echo "Setting up sample .env in root directory. Please edit .env file for your needs."
cp vendor/nickyeoman/phpframework/env.sample .env

################################################################################
# Docker
################################################################################

echo "Creating docker files"
cp vendor/nickyeoman/phpframework/docker/Dockerfile Dockerfile
cp vendor/nickyeoman/phpframework/docker/docker-compose.yml docker-compose.yml

################################################################################
# Creating the first controller
################################################################################

bash vendor/nickyeoman/phpframework/bin/newController.bash index

################################################################################
# Instructions
################################################################################

echo "*** End New Project Script ***"

echo "FURTHER INSTRUCTIONS: "
echo "To start a local server, edit .env file then run:"
echo "sudo docker-compose up -d"


    "autoload": {
        "psr-4": { "Nickyeoman\\Framework\\Controller\\": "controllers/" }
    }