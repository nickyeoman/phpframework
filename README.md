# phpframework

Just a lightweight framework for routing and calling controllers.


# Use

composer require nickyeoman/phpframework

mkdir -p Controllers tmp public

cp vendor/nickyeoman/phpframework/test/index.php public/.

cp env.sample .env  (then edit)

vi Controllers/index.php

```
<?php
class indexController extends Nickyeoman\Framework\BaseController {

	function index() {

    $data = array( ['templatevar1' => 'title page' ] );
    $this->twig('viewName', $data);

  }
}

```

# Composer Dependencies

* ENV Variables: https://github.com/vlucas/phpdotenv
* Autoloader for app: Nette\Loaders\RobotLoader
