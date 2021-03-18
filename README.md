# phpframework

Just a lightweight framework for routing and calling controllers.
Just working on a proof of concept at this point.


# Use

require_once '../vendor/autoload.php';
USE Nickyeoman\Framework;
$router = new Nickyeoman\Framework\Router();
$classname=$router->controller . 'Controller';
require_once '../Controllers/index.php'; //TODO: need autoloader
call_user_func_array( array(new $classname(), $router->action), $router->params );
