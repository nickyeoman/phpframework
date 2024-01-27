#!/bin/bash

echo "*** Starting New Project Script ***"

# Check if no directory argument is provided
if [ -z "$1" ]; then
    project_name="myproject"
else
    project_name="$1"
fi

# Check if the provided argument is a directory and exists
if [ -d "$project_name" ]; then
    echo "Error: '$project_name' directory already exists."
    exit 1
fi

composer -v > /dev/null 2>&1
COMPOSER=$?
if [[ $COMPOSER -ne 0 ]]; then
    echo 'Composer is not installed'
    echo 'Checkout how to Install Composer here: https://www.nickyeoman.com/page/install-composer-on-ubuntu'
    echo 'Once installed, try running this script again'
    exit 1
else
    mkdir -p "$project_name"
    cd "$project_name" || exit 1
    composer require nickyeoman/phpframework
    echo "Composer has installed nickyeoman/phpframework to $project_name"
fi

#Check if Sass is installed
if command -v sass &> /dev/null; then
    echo "Sass is installed."
    sass --version
else
    echo "Sass is not installed."
    exit 1
fi

################################################################################
# Create directories
################################################################################

echo "Creating directories"

mkdir -p tmp sass scripts 
mkdir -p app/Controllers app/Helpers app/Views
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
