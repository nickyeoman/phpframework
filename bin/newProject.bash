#!/bin/bash

# Create directories needed
mkdir -p controllers tmp public scripts views/modules views/layout
echo "Created directories: controllers tmp public views scripts"

# Copy index page
cp vendor/nickyeoman/phpframework/public/index.php public/.
echo "index page created in public"

# Copy sample layouts
cp vendor/nickyeoman/phpframework/twig/header.html.twig views/modules/header.html.twig
cp vendor/nickyeoman/phpframework/twig/master.html.twig views/layout/master.html.twig
echo "Twig templates setup"

# Copy env file
cp vendor/nickyeoman/phpframework/env.sample .env
echo "Please edit the .env file for your needs."

# Copy env file
cp vendor/nickyeoman/phpframework/docker/Dockerfile Dockerfile
echo "Dockerfile created"

echo "Now run: bash vendor/nickyeoman/phpframework/bin/newController.bash index"
