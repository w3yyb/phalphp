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
		'host' => '192.168.41.20',
		'username' => 'smart',
		'password' => '1234qwer!',
		'name' => 'smartmonitor',
		'port' => 3306
    ),
	'db2' => array(
		'adapter'     => 'Mysql',
		'host'        => '192.168.41.20',
		'username'    => 'smart',
		'password'    => '1234qwer!',
		'name'      => 'smartcommunity',
		'port' => 3306,
		'tablePrefix' => 'smt_'
	),
	'logdb' => array(
		'adapter' => 'Mysql', /* Possible Values: Mysql, Postgres, Sqlite */
		'host' => '192.168.41.20',
		'username' => 'smart',
		'password' => '1234qwer!',
		'name' => 'smartmonitor',
		'port' => 3306
	),
    'app' => array(
        'debug' => false,
        'publicKey' => 'uitv'
    ),
	'tasktime' => 5,
	'communityset' => 1, //1开启，0关闭
   'serverowner'=>array('1'=>'滨州','2'=>'泰国','3'=>'QA环境')
  
];
  
