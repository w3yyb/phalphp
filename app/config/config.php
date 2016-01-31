<?php

/**
 * api & cli & webapp
 * Settings to be stored in dependency injector
 */

return [
	'database' => array(
		'adapter' => 'Mysql', /* Possible Values: Mysql, Postgres, Sqlite */
		'host' => 'localhost',
		'username' => 'root',
		'password' => '39552041',
		'name' => 'phalconframework',
		'port' => 3306
    ),
    'app' => array(
       'debug' => FALSE
    )
  
];

