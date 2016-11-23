<?php
/**
 * Monitorbeat.
 */
namespace Controllers;
use \Models\Monitorbeat as Monitorbeat;
class MonitorbeatController extends \Phalcon\Mvc\Controller
{
    public function indexAction($mid)
    {
        $monitorbeat = Monitorbeat::findFirstBymid($mid);
        if($monitorbeat) {
            $monitorbeat->ctime = time();
            $monitorbeat->status = 1;
        }else {
            $monitorbeat = new Api();
            $monitorbeat->mid = $mid;
            $monitorbeat->ctime = time();
            $monitorbeat->status = 1;
        }
        if(!$monitorbeat->save()){
            echo '0';
        }else{
            echo '1';
        }
    }
}