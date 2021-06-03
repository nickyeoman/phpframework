#!/bin/bash

# Create directories needed
mkdir -p controllers tmp public views

# Copy index page
cp vendor/nickyeoman/phpframework/test/index.php public/.

# Copy env file
cp vendor/nickyeoman/phpframework/env.sample .env
echo "Please edit the .env file for your needs."

# Copy index controller
cp vendor/nickyeoman/phpframework/test/childController.php controllers/index.php
echo "Please edit the Controllers/index.php file for your needs."

# TODO: prep git (gitignore)
