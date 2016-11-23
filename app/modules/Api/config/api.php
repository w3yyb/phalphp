<?php
/**
 * Created by PhpStorm.
 * User: new
 * Date: 2016/10/17
 * Time: 11:16
 */
return [
    'Monitor' => [
        'encryptionkey' => '0x39393636383433333035366134643632',
        'encryptioniv' => '0x39663932633836323866363163323832',
        'replyaddr' => 'http://'.$_SERVER['HTTP_HOST'].'/receiveTask',
    ],
    'webserver' => [
        'waitout' => 60,//秒
    ],
];