<?php
/**
 * Created by PhpStorm.
 * User: new
 * Date: 2016/10/20
 * Time: 9:41
 */

namespace Models\Services\Service;

use Models\Repositories\Repositories;
use Models\Services\Services as Services;
use Models\Community as Community;
use Phalcon\Mvc\User\Component;
use \Models\Setting;

class WebApi extends Component
{
    private $configs;

    public function __construct()
    {
        $settings = Setting::find();
        foreach($settings as $setting){
            $this->configs[$setting->name] = $setting->value;
        }
    }

    public function StartTask($mid,$uid)
    {
        Repositories::getRepository('WebApi')->userStart($mid,$uid);
        if(Repositories::getRepository('MonitorApi')->midHasStart($mid))
        {
            return array('status'=>true);
        }else{
            if($monitortask = Repositories::getRepository('MonitorApi')->midHasWait($mid))
            {
                $waittime = time() - strtotime($monitortask->createtime);
                if($waittime > $this->configs['startwaittime'])
                {
                    return Services::getService('MonitorApi')->creatTask($monitortask->mid,1,$monitortask->taskid);
                }else{
                    return array('status'=>true);
                }
            }else{
                return Services::getService('MonitorApi')->creatTask($mid,1);
            }
        }

    }

    public function StopTask($mid)
    {
        if($monitortask = Repositories::getRepository('MonitorApi')->midHasStart($mid))
        {
            return Services::getService('MonitorApi')->creatTask($monitortask->mid,0,$monitortask->taskid);
        }else{
            return array('status'=>true);
        }
    }

    public function getMonitors($id)
    {
        if(Community::findFirstBycommunityid($id))
        {
            $mids = Repositories::getRepository('WebApi')->getMidByCom($id);
            foreach ($mids as $key => $value) {
                $datas[$key]['mid'] = $value['mid'];
                $datas[$key]['mname'] = $value['mname'];
                $datas[$key]['webplayurl'] = str_replace('{$monitorid}',$value['mid'],$this->configs['webplayurl']);
            }

            $return['resultcode'] = 1;
            $return['resultmsg'] = 'ok';
            $return['datas'] = $datas;

        }else{
            $return['resultcode'] = 0;
            $return['resultmsg'] = 'communityid error';
            $return['datas'] = '';

        }
        return json_encode($return);
    }
}