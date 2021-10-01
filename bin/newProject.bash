#!/bin/bash

# More colours: https://stackoverflow.com/questions/5947742/how-to-change-the-output-color-of-echo-in-linux
RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color

echo "*** Starting New Project Script ***"

# TODO: check if script has already been run

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
cp vendor/nickyeoman/phpframework/twig/header.html.twig views/modules/header.html.twig
cp vendor/nickyeoman/phpframework/twig/master.html.twig views/layout/master.html.twig
cp vendor/nickyeoman/phpframework/twig/footer.html.twig views/modules/footer.html.twig
cp vendor/nickyeoman/phpframework/twig/user/* views/user/.

################################################################################
# SASS
################################################################################

echo "Creating SASS directory for css"
cp vendor/nickyeoman/phpframework/sass/project.sass sass/.

################################################################################
# Controller
################################################################################

cp vendor/nickyeoman/phpframework/user/user.php controllers/user.php

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
# Instructions
################################################################################

echo "*** End New Project Script ***"

echo "FURTHER INSTRUCTIONS: "
echo "Next, to create a controller run: ${GREEN}bash vendor/nickyeoman/phpframework/bin/newController.bash index${NC}"
echo ""
echo "To start a local server, after editintg the .env file, run:"
echo "${GREEN}bash vendor/nickyeoman/phpframework/bin/startServer.bash${NC}"
