#!/bin/bash

echo "*** Adding User component ***"

################################################################################
# Create directories
################################################################################

echo "Creating directories: views user"
mkdir -p views/user

################################################################################
# Twig
################################################################################

echo "Creating Twig user templates"
cp vendor/nickyeoman/phpframework/components/user/twig/*.twig views/user/.

################################################################################
# Controller
################################################################################

echo "Add Controller"
cp vendor/nickyeoman/phpframework/components/user/userController.php controllers/user.php

################################################################################
# Helper
################################################################################

echo "Add Helper"
cp vendor/nickyeoman/phpframework/components/user/userHelper.php helpers/user.php

################################################################################
# database
################################################################################

echo "Move user migrations to migrations folder"
cp vendor/nickyeoman/phpframework/components/user/20220000000001_users_database_creation.php migrations/.
