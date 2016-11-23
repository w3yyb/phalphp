<?php
/**
 * 监控分类.
 */
namespace Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\Monitorcategory as Monitorcategory;
use \Models\Monitortask as Monitortask;
use \Models\Sliceserver as Sliceserver;

class MonitortaskController extends ControllerBase
{
    public function initialize(){
        parent::initialize();
        $this->soucetype = array('1'=>'主源','2'=>'子源');
        $this->view->setVar('activeinfo','monitortask');
    }
    /*监控点任务列表*/
    public function indexAction($id){

        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "\Models\Monitortask", $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }
        if ($numberPage == null) {
            $this->persistent->parameters = null;
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
        $mname = '';
        if ($this->request->getPost("mname")) {
            $mname = $this->request->getPost("mname");
            $parameters["conditions"] = " m.mname like '%".$mname."%'   ";            
        }
        $mcid = '';
        if($this->request->getPost("mcid")){
            $mcid = $this->request->getPost("mcid");
            if(isset($parameters["conditions"])){
                $parameters["conditions"] .= ' AND m.mcid='.$mcid;
            }else{
                $parameters["conditions"] = ' m.mcid='.$mcid;
            }
             
        }
        $ssid = '';
        if($this->request->getPost("ssid")){
            $ssid = $this->request->getPost("ssid");
            if(isset($parameters["conditions"])){
                $parameters["conditions"] .= ' AND m.ssid='.$ssid;
            }else{
                $parameters["conditions"] = ' m.ssid='.$ssid;
            }

        }

        if($this->request->getPost("mtstatus")){
            $status = $this->request->getPost("mtstatus");
            if($status <100)
            {
                switch($status)
                {
                    case 1:
                        $statussql = ' mt.status =1';
                        break;
                    case 2:
                        $statussql = ' mt.status =2';
                        break;
                    case 3:
                        $statussql = ' mt.status < 1';
                        break;
                }
                if(isset($parameters["conditions"])){
                    $parameters["conditions"] .= ' AND'.$statussql;
                }else{
                    $parameters["conditions"] = $statussql;
                }
            }else{
                $status = 100;
            }
        }else{
            if($this->request->get("mtstatus")){
                $status = $this->request->get("mtstatus");
                if(isset($parameters["conditions"])){
                    $parameters["conditions"] .= ' AND mt.status =2';
                }else{
                    $parameters["conditions"] = ' mt.status =2';
                }
            }else{
                $status = 100;
            }
        }
        $this->view->setVar('ssid',$ssid);
        $this->view->setVar('mcid',$mcid);
        $this->view->setVar('mname',$mname);
        $this->view->setVar('status',$status);
        if(!empty($id)){
            if(isset($parameters["conditions"])){
                $parameters["conditions"] .= ' AND mt.mid="'.$id.'"';
            }else{
                $parameters["conditions"] = ' mt.mid="'.$id.'"';
            }
        }
        $this->view->setVar('mid',$id);
        if (isset($parameters["conditions"])) {
            /*$builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Monitortask')
                ->join()
                ->where($parameters['conditions'], $parameters['bind'])
                ->orderBy('mcid');*/
            $sql = 'SELECT m.mname,m.mac,m.soucetype,m.sourcepath,m.cid,m.mcid,m.ssid,m.cameraip,m.mstatus,ms.ssname as msolutioname,mc.categoryname, ct.name as cameraname,mt.taskid,mt.action,mt.mid,mt.createtime,mt.status,mt.updatetime,mt.taskstarttime,mt.code from \Models\Monitortask  mt  left join \Models\Monitor m on m.mid=mt.mid left join \Models\Monitorcategory mc on m.mcid=mc.mcid left join \Models\Sliceserver ms on ms.ssid=m.ssid left join \Models\Cameratype ct on m.cid=ct.cid where '. $parameters["conditions"].' order by mt.status desc,mt.taskid desc';
        } else {
           /* $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Monitortask')
                ->
                ->orderBy('mcid');*/
            $sql =  'SELECT m.mid,m.mname,m.mac,m.soucetype,m.sourcepath,m.cid,m.mcid,m.ssid,m.cameraip,m.mstatus,ms.ssname as msolutioname,mc.categoryname, ct.name as cameraname,mt.taskid,mt.action,mt.mid,mt.createtime,mt.status,mt.updatetime,mt.taskstarttime,mt.code from \Models\Monitortask  mt  left join \Models\Monitor m on m.mid=mt.mid left join \Models\Monitorcategory mc on m.mcid=mc.mcid left join \Models\Sliceserver ms on ms.ssid=m.ssid left join \Models\Cameratype ct on m.cid=ct.cid  order by mt.status desc,mt.taskid desc';
        }
        $data = $this->modelsManager->executeQuery($sql);
        /*$paginator = new PaginatorQueryBuilder(
            array(
                "builder" => $data,
                "limit"   => 10,
                "page"    => $numberPage
            )
        );*/
//        var_dump($data);exit;
        $paginator = new Paginator(
            array('data'=>$data,
            'limit'=>10,
            'page'=>$numberPage
            )
            );

        //监控分类
        $monitorcategory = Monitorcategory::find();
        $mcategory = array();
        foreach($monitorcategory as $mc){
            $mcategory[$mc->mcid] = $mc->categoryname;
        }
        $slicesevers = Sliceserver::find();
        $slicesever = array();
        foreach($slicesevers as $ss)
        {
            $slicesever[$ss->ssid] = $ss->ssname;
        }
        $this->view->page = $paginator->getPaginate();
        $this->view->setVar('soucetype',$this->soucetype);
        $this->view->setVar('monitorcategory',$mcategory);
        $this->view->setVar('slicesever',$slicesever);
    }

   
    
}