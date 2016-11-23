<?php

/**
 * api & cli & webapp
 * Settings to be stored in dependency injector
 */
//define('APP_MODULE_LIST','Admin,Api');
//define('ADMIN_MODULE','Admin');
//define('API_MODULE','Admin');
//define('APP_MODULE_PATH','Modules');
//无用了
return [
	'database' => array(
		'adapter' => 'Mysql', /* Possible Values: Mysql, Postgres, Sqlite */
		'host' => '192.168.6.76',
		'username' => 'root',
		'password' => 'password111',
		'name' => 'smartmonitor',
		'port' => 3306
    ),
    'app' => array(
       'debug' => false
    )
  
];

