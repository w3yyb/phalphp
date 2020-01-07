<?php

/**
 * webapp
 * Driver for PHP Web Application
 */

// Setup configuration files
$dir = dirname(__DIR__);
$appDir = $dir . '/app';
$adminDir = $dir . '/app/modules/Admin';

// Necessary requires to get things going
require $appDir . '/library/utilities/debug/PhpError.php';
require $appDir . '/library/interfaces/IRun.php';
require $appDir . '/library/application/Web.php';

// Necessary paths to autoload & config settings
$configPath = $adminDir  . '/config/';
$configfile = $appDir  . '/config/';//全局config
$viewsPath = $adminDir  . '/views/';
$config = $configfile . 'config.php';
$autoLoad = $configPath . 'autoload.php';
$routes = $configPath . 'routes.php';


try {
    $app = new Application\Web();

    // Setup Web App (dependency injector, configuration variables, routes)
    $app->setAutoload($autoLoad, $appDir);
    $app->setConfig($config);
    $app->setRoutes($routes);
    $app->setSessions();//start session
    $app->setDebugMode(false);//debug
    $app->setBaseUrl('/');
    $app->setView($viewsPath, $volt = true);
    //$app->setEvents();
    //  Run
    $app->run();

} catch (Exception $e) {
    $app->response->setStatusCode(500, "Server Error");
    $app->response->setContent($e->getMessage());
    $app->response->send();
}
