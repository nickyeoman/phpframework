# phpframework

Just a lightweight framework for routing and calling controllers.

# Links

* https://github.com/nickyeoman/phpframework
* https://packagist.org/packages/nickyeoman/phpframework

# Use

composer require nickyeoman/phpframework

mkdir -p Controllers tmp Public Views

cp vendor/nickyeoman/phpframework/test/index.php public/.

cp vendor/nickyeoman/phpframework/env.sample .env  (then edit)

cp test/childController.php Controllers/index.php

# Composer Dependencies

* ENV Variables: https://github.com/vlucas/phpdotenv
* Autoloader for app: Nette\Loaders\RobotLoader
