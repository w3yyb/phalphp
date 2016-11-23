<?php

/**
 * MonitorTask
 * Example of a Task/Cli application
 */

namespace Tasks;

use Phalcon\Config;
use \Cli\Output as Output;
use Models\Monitorbeat;
use Models\services\Services;


class MonitorbeatTask extends \Phalcon\Cli\Task {

    public function mainAction() {
        Output::stdout("Main Action");
    }

    public function testAction()
    {
        $configs = new Config(require(dirname(__DIR__) . '/config/config.php'));
        $tasktime = time() - $configs['tasktime'] * 60 * 1000; //任务时间
        $monitorbeat = new Monitorbeat();
        $infolist = $monitorbeat->find(array("conditions" => "ctime <= $tasktime"));
        foreach($infolist as $key => $val){
            $monitorbeat->mid = $val['mid'];
            $monitorbeat->status = 0;
            $monitorbeat->save();
            Services::getService('Web')->StopTask($val['mid']);
        }
    }
}
