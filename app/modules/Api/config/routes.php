<?php

/**
 * api
 * @author Itv
 * @version 1.0
 * @link http://docs.phalconphp.com/en/latest/reference/micro.html#defining-routes
 * @eg.

$routes[] = [
 	'method' => 'post', 
	'route' => '/api/update', 
	'handler' => 'myFunction'
];

 */
 
 
 
 

//测试
$routes[] = [
	'method' => 'get',
	'route' => '/test',
	'handler' => ['Controllers\ExampleController', 'test'],
	'authentication' => FALSE
];
$routes[] = [
	'method' => 'get',
	'route' => '/apitest',
	'handler' => ['Controllers\ExampleController', 'apitest'],
	'authentication' => FALSE
];
return $routes;
