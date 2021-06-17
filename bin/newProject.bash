#!/bin/bash

echo "Creating directories: controllers tmp public views scripts"
mkdir -p controllers tmp public scripts views/modules views/layout

echo "Creating index.php page in public directory"
cp vendor/nickyeoman/phpframework/public/index.php public/.

echo "Creating scaffolding Twig templates in views directory"
cp vendor/nickyeoman/phpframework/twig/header.html.twig views/modules/header.html.twig
cp vendor/nickyeoman/phpframework/twig/master.html.twig views/layout/master.html.twig

echo "Setting up sample .env in root directory. Please edit .env file for your needs."
cp vendor/nickyeoman/phpframework/env.sample .env

echo "Creating a sample Dockerfile incase you would like to use docker with this project"
cp vendor/nickyeoman/phpframework/docker/Dockerfile Dockerfile

echo "** All Done **"
echo "Now run: bash vendor/nickyeoman/phpframework/bin/newController.bash index"
