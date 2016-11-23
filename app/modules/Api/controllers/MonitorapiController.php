<?php
/**
 * Created by PhpStorm.
 * User: new
 * Date: 2016/10/24
 * Time: 10:01
 */

namespace Controllers;

use Models\Services\Services as Services;

class MonitorapiController extends \Phalcon\Mvc\Controller
{

    public function receiveTask()
    {
        $xml = file_get_contents('php://input');
        $monitor = Services::getService('MonitorApi')->reponseTask($xml);

        return $monitor;
    }
}