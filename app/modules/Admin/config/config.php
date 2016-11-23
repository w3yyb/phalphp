<?php

/**
 * api & cli & webapp
 * Settings to be stored in dependency injector
 */
//目前无用
return [
	'database' => array(
		'adapter' => 'Mysql', /* Possible Values: Mysql, Postgres, Sqlite */
		'host' => '192.168.6.76',
		'username' => 'root',
		'password' => '111111',
		'name' => 'database',
		'port' => 3306
    ),
    'app' => array(
       'debug' => false
    )
  
];

