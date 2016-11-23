<?php

/**
 * webapp
 * @version 1.0
 * @link http://docs.phalconphp.com/en/latest/reference/routing.html
 * @eg.

$routes = [
 	'/uri' => [
		'controller' => 'index',
		'action' => 'cheese'
	],
];

 */

return [
	'/' => [
		'controller' => 'index',
		'action' => 'index'
	],
	'/index' => [
		'controller' => 'index',
		'action' => 'index'
    ],
    '/login' => [
		'controller' => 'login',
		'action' => 'index'
	]

];
