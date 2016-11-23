<?php
/**
 * Created by PhpStorm.
 * User: new
 * Date: 2016/10/20
 * Time: 10:02
 */

namespace Models\Repositories\Repository;

use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\User\Component;
use Phalcon\Config;
use Models\Midlog;

class WebApi extends Component
{
    public function userStart($mid,$uid){

            $midlog = new Midlog();
            $midlog->mid = $mid;
            $midlog->uid = $uid;
            $midlog->ctime = date('Y-m-d H:i:s');
        return $midlog->create();
    }

    public function getMidByCom($communityid)
    {
        $sql = "SELECT mid,mname FROM \Models\Monitorcategory AS ms INNER JOIN \Models\Monitor As m ON ms.mcid = m.mcid WHERE communityid= '$communityid'";
        return  $this->modelsManager->executeQuery($sql);
    }

}