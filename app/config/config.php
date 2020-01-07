<?php

/**
 * api & cli & webapp
 * Settings to be stored in dependency injector
 */
//define('APP_MODULE_LIST','Admin,Api');
//define('ADMIN_MODULE','Admin');
//define('API_MODULE','Admin');
//define('APP_MODULE_PATH','Modules');
return [
	'database' => array(
		'adapter' => 'Mysql', /* Possible Values: Mysql, Postgres, Sqlite */
		'host' => 'localhost',
		'username' => 'root',
		'password' => '111111',
		'name' => 'database',
		'port' => 3306
    ),
	'db2' => array(
		'adapter'     => 'Mysql',
		'host'        => 'localhost',
		'username'    => 'root',
		'password'    => '111111!',
		'name'      => 'database',
		'port' => 3306,
		'tablePrefix' => 'smt_'
	),
	'logdb' => array(
		'adapter' => 'Mysql', /* Possible Values: Mysql, Postgres, Sqlite */
		'host' => 'localhost',
		'username' => 'root',
		'password' => '111111!',
		'name' => 'database',
		'port' => 3306
	),
    'app' => array(
        'debug' => false,
        'publicKey' => 'uitv'
    ),
	'tasktime' => 5,
	'communityset' => 1, //1开启，0关闭
   'serverowner'=>array('1'=>'a','2'=>'b','3'=>'c')
  
];
  
