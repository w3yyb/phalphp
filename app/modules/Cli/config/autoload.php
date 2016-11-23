<?php

/**
 * cli
 * Autoload Class files by PHP namespacing support
 * 
 * @author Itv
 * @eg 
 	$autoload = [
 		'namespace' => '/path/to/dir',
		'namespace' => '/path/to/dir'
	];
	return $autoload;
 *
 * @note $appDir is the app directory (/path/to/php-cli-app-phalcon/app)
 */

$autoload = [
	'Utilities\Debugcli' => $appDir . '/library/utilities/debugcli/',
	'Application' => $appDir . '/library/application/',
	'Interfaces' => $appDir . '/library/interfaces/',
	'Controllers' => $appDir . '/controllers/',
	'Models' => $appDir . '/models/',
	'Tasks' => $appDir . '/tasks/',
	'Cli' => $appDir . '/library/cli/',
	'Events\Cli' => $appDir . '/library/events/cli/',
	'Events\Database' => $appDir . '/library/events/database/',
];

return $autoload;
