<?php

/**
 * cli
 * a cli launcher
 *
 * a cli script that launches phalcon tasks
 *
 * @package cli
 * @author Itv 
 * @version 1.0
 * @copyright never
 *
 * @example php cli.php [task] [action] [param1 [param2 ...]]
 * @example php cli.php Example index
 * @example php cli.php Example index --debug --single --no-record
 */


// Setup configuration directories
$dir = dirname(__DIR__);
$appDir = $dir . '/app';
$cliDir = $dir . '/app/modules/Cli';

// Necessary requires to get things going
require $appDir . '/library/utilities/debugcli/PhpError.php';
require $appDir . '/library/interfaces/IRun.php';
require $appDir . '/library/application/Cli.php';

// Capture runtime errors
register_shutdown_function(['Utilities\Debugcli\PhpError','runtimeShutdown']);

// Necessary paths to autoload & config files
$configPath = $appDir . '/config/';
$configdir = $cliDir  . '/config/';

$config = $configPath . 'config.php';
$autoLoad = $configdir . 'autoload.php';

try {

	$app = new Application\Cli();

	// Record any php warnings/errors
	set_error_handler(['Utilities\Debugcli\PhpError','errorHandler']);

	$app->setAutoload($autoLoad, $appDir);
	$app->setConfig($config);

	// Check if only run single instance
	if ($key = array_search('--single', $argv)) {
		$app->setSingleInstance(TRUE);
		// Ensure pid removes even on fatal error
		register_shutdown_function([$app, 'removeProcessInstance']);
	}

	// Check if logging to database
	if ($key = array_search('--record', $argv)) {
		$app->setRecording(TRUE);
	}

	// Check if debug mode
	if ($key = array_search('--debug', $argv)) {
		$app->setDebug(TRUE);
		// Ensure debug display even on fatal error	
		register_shutdown_function([new Events\Cli\Debug(FALSE), 'display'], $app);
	}

	$app->setArgs($argv, $argc);

	//  Run
	$app->run();

} catch(Exception $e) {
	echo $e;
	exit(255);
}

?>
