<?php

/**
 * webapp
 * Driver for PHP Web Application
 */

// Setup configuration files
$dir = dirname(__DIR__);
$appDir = $dir . '/app';

// Necessary requires to get things going
require $appDir . '/library/utilities/debug/PhpError.php';
require $appDir . '/library/interfaces/IRun.php';
require $appDir . '/library/application/Web.php';

// Necessary paths to autoload & config settings
$configPath = $appDir . '/config/';
$viewsPath = $appDir . '/views/';
$config = $configPath . 'config.php';
$autoLoad = $configPath . 'autoload.php';
$routes = $configPath . 'routes.php';


try {

	$app = new Application\Web();

	 // Setup Web App (dependency injector, configuration variables, routes)
    $app->setAutoload($autoLoad, $appDir);
    $app->setConfig($config);
	$app->setRoutes($routes);
	$app->setSessions();//start session
//	$app->setDebugMode(TRUE);//debug 
	$app->setView($viewsPath, $volt = TRUE);
	//$app->setEvents();

	// Boom, Run
	$app->run();

} catch(Exception $e) {
    $app->response->setStatusCode(500, "Server Error");
    $app->response->setContent($e->getMessage());
    $app->response->send();
}
