#!/bin/bash

echo "*** Adding User component ***"

################################################################################
# Create directories
################################################################################

echo "Creating directories: views user"
mkdir -p views/page

################################################################################
# Twig
################################################################################

echo "Creating Twig user templates"
cp vendor/nickyeoman/phpframework/components/page/twig/*.twig views/page/.

################################################################################
# Controller
################################################################################

echo "Add Controller"
cp vendor/nickyeoman/phpframework/components/page/pageController.php controllers/page.php

################################################################################
# Helper
################################################################################

echo "Add Helper"
cp vendor/nickyeoman/phpframework/components/page/userHelper.php helpers/page.php

################################################################################
# database
################################################################################

echo "Move user migrations to migrations folder"
cp vendor/nickyeoman/phpframework/components/user/20220000000002_pages_database_creation.php migrations/.
