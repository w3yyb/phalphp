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
//前台调用摄像头启动
$routes[] = [
	'method' => 'get', 
	'route' => '/monitorStart/{mid}/{uid}',
	'handler' => ['Controllers\WebapiController', 'monitorStart'],
    'authentication' => FALSE
];
//前台调用观看心跳
$routes[] = [
	'method' => 'get',
	'route' => '/monitorBeat/{mid}',
	'handler' => ['Controllers\MonitorbeatController', 'indexAction'],
	'authentication' => FALSE
];
//切片服务器调用汇报接口
$routes[] = [
	'method' => 'post',
	'route' => '/receiveTask',
	'handler' => ['Controllers\MonitorapiController', 'receiveTask'],
	'authentication' => FALSE
];
//获取社区下的监控点
$routes[] = [
    'method' => 'get',
    'route' => '/getMonitors/{id}',
    'handler' => ['Controllers\WebapiController', 'getMonitors'],
    'authentication' => FALSE
];

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
