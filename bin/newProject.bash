#!/bin/bash

echo "*** Starting New Project Script ***"

# TODO: check if script has already been run
# TODO: check that $1 exits
# TODO: check folder doesn't exist

composer -v > /dev/null 2>&1
COMPOSER=$?
if [[ $COMPOSER -ne 0 ]]; then
    echo 'Composer is not installed'
    echo 'Checkout how to Install Composer here: https://www.nickyeoman.com/blog/php/install-composer-on-ubuntu/'
    echo 'Once installed, try running this script again'
    exit 1
else
  mkdir $1
  cd $1
  composer require nickyeoman/phpframework
  echo "Composer has installed nickyeoman/phpframework to $1"
fi

################################################################################
# Create directories
################################################################################

echo "Creating directories: controllers tmp public views scripts"
mkdir -p controllers tmp migrations sass scripts
mkdir -p public/css public/js public/images
mkdir -p views/modules views/layout views/user

################################################################################
# Public Folder
################################################################################

echo "Creating index.php page in public directory"
cp vendor/nickyeoman/phpframework/public/index.php public/.

echo "Adding Apache htaccess"
cp vendor/nickyeoman/phpframework/public/htaccess public/.htaccess

################################################################################
# Twig
################################################################################

echo "Creating scaffolding Twig templates in views directory"
cp vendor/nickyeoman/phpframework/twig/head.html.twig views/modules/head.html.twig
cp vendor/nickyeoman/phpframework/twig/master.html.twig views/layout/master.html.twig
cp vendor/nickyeoman/phpframework/twig/footer.html.twig views/modules/footer.html.twig
cp vendor/nickyeoman/phpframework/twig/nav.html.twig views/modules/nav.html.twig
cp vendor/nickyeoman/phpframework/twig/user/* views/user/.

################################################################################
# SASS
################################################################################

echo "Creating SASS directory for css"
cp vendor/nickyeoman/phpframework/sass/project.sass sass/.
touch sass/variables.sass

################################################################################
# Controller
################################################################################

cp vendor/nickyeoman/phpframework/user/user.php controllers/user.php
cp vendor/nickyeoman/phpframework/error/error.php controllers/error.php

################################################################################
# Configuration
################################################################################

echo "Setting up sample .env in root directory. Please edit .env file for your needs."
cp vendor/nickyeoman/phpframework/env.sample .env

echo "Setting up sample phinx config file in root directory. Please edit phinx.php for your needs."
cp vendor/nickyeoman/phpframework/phinx.php.sample phinx.php

################################################################################
# database
################################################################################

echo "Move user migrations to migrations folder"
cp vendor/nickyeoman/phpframework/user/20210721230307_users_database_creation.php migrations/20210721230307_users_database_creation.php

################################################################################
# Docker
################################################################################

echo "Creating a sample Dockerfile incase you would like to use docker with this project"
cp vendor/nickyeoman/phpframework/docker/Dockerfile Dockerfile

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
echo "bash vendor/nickyeoman/phpframework/bin/startServer.bash"
