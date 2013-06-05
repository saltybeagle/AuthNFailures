<?php
namespace AuthNFailures;

$config_file = __DIR__ . '/../config.sample.php';

if (file_exists(__DIR__ . '/../config.inc.php')) {
    $config_file = __DIR__ . '/../config.inc.php';
}
require_once $config_file;


use RegExpRouter as RegExpRouter;

$routes = include __DIR__ . '/../data/routes.php';
$router = new RegExpRouter\Router(array('baseURL' => Controller::$url));
$router->setRoutes($routes);
if (isset($_GET['model'])) {
    // Prevent injecting a specific model through the web interface
    unset($_GET['model']);
}
// Initialize controller, and construct everything the user requested
$controller = new Controller($router->route($_SERVER['REQUEST_URI'], $_GET));

// Now render what the user has requested
$savvy = new OutputController($controller->options);
$savvy->addGlobal('controller', $controller);
$savvy->addGlobal('form_helper', new FormHelper($controller->options));

echo $savvy->render($controller);
