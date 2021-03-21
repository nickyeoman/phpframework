# phpframework

Just a lightweight framework for routing and calling controllers.


# Use

composer require nickyeoman/phpframework

mkdir -p Controllers tmp public

cp vendor/nickyeoman/phpframework/test/index.php public/.

cp env.sample .env  (then edit)

cp test/childController.php Controllers/index.php

# Composer Dependencies

* ENV Variables: https://github.com/vlucas/phpdotenv
* Autoloader for app: Nette\Loaders\RobotLoader
