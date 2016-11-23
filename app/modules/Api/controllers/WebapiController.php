<?php
/**
 * Created by PhpStorm.
 * User: new
 * Date: 2016/10/24
 * Time: 11:41
 */

namespace Controllers;

use Models\Services\Services as Services;

class WebapiController extends \Phalcon\Mvc\Controller
{
    public function monitorStart($mid,$uid)
    {
        $monitor = Services::getService('WebApi')->StartTask($mid,$uid);

        return $monitor;
    }

    public function getMonitors($id)
    {
        $monitor = Services::getService('WebApi')->getMonitors($id);

        return $monitor;
    }
}