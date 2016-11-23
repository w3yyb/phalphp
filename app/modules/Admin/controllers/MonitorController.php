<?php
/**
 * 监控分类.
 */
namespace Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\Monitor as Monitor;
use \Models\services\Services;
use \Models\Sliceserver as Sliceserver;
use \Models\Setting;
$dir = dirname(__DIR__);
$apiDir = $dir . '/../../modules/Api';
define('API_PATH',$apiDir);
class MonitorController extends ControllerBase
{   
    public function initialize(){
        parent::initialize();
        $this->soucetype = array('1'=>'主源','2'=>'子源');
        $this->monitorsolution = new \Models\Monitorsolution;
        $this->sliceserver = new \Models\Sliceserver;
        $this->monitorcategory = new \Models\Monitorcategory;
        $this->cameratype = new \Models\Cameratype;
        $this->view->setVar('activeinfo','monitor');
    }
    /*列表*/
    public function indexAction(){
        $numberPage = 1;

        if ($this->request->isPost()) {

            if(empty($_POST['ssid'])){unset($_POST['ssid']);}
            if(empty($_POST['mcid'])){unset($_POST['mcid']);}
            if($_POST['mstatus'] == 'all'){unset($_POST['mstatus']);}
            $query = Criteria::fromInput($this->di, "Models\Monitor", $_POST);
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
        //名称
        $mname = "";
        if ($this->request->getPost("mname")) {
            $mname = $this->request->getPost("mname");
            if(isset($parameters["conditions"])){
                $parameters["conditions"] .= " and  mname like '%".$mname."%'   ";
            }else{
                $parameters["conditions"] .= " mname like '%".$mname."%'   ";
            }
        }
        $this->view->setVar('mname',$mname);
        //监控分类
        $mcid = '';
        if($this->request->getPost("mcid")){
            $mcid = $this->request->getPost("mcid");
            if(isset($parameters["conditions"])){
                $parameters["conditions"] .= ' and mcid='.$mcid;
            }else{
                $parameters["conditions"] = ' mcid='.$mcid;
            }
        }
        $this->view->setVar('mcid',$mcid);
        //切片服务器
        $ssid = '';
        if($this->request->getPost("ssid")){
            $ssid = $this->request->getPost("ssid");
            if(isset($parameters["conditions"])){
                $parameters["conditions"] .= ' and ssid='.$ssid;
            }else{
                $parameters["conditions"] = ' ssid='.$ssid;
            }
        }
        $this->view->setVar('ssid',$ssid);
        //状态
        $mstatus = '';
        if($this->request->getPost("mstatus") || $this->request->getPost("mstatus") == '0'){
            $mstatus = $this->request->getPost("mstatus");
            if(isset($parameters["conditions"])){
                $parameters["conditions"] .= ' and mstatus='.$mstatus;
            }else{
                $parameters["conditions"] = ' mstatus='.$mstatus;
            }
        }
        $this->view->setVar('mstatus',$mstatus);
        if (isset($parameters["conditions"])) {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Monitor')
                ->where($parameters['conditions'], $parameters['bind'])
                ->orderBy('createtime desc');
        } else {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Monitor')
                ->orderBy('createtime desc');
        }

        $paginator = new PaginatorQueryBuilder(
            array(
                "builder" => $builder,
                "limit"   => 10,
                "page"    => $numberPage
            )
        );

       // $infolist = Monitor::find();
        //获取切片服务器
        $solutions = $this->sliceserver->find();
        $this->view->setVar("searchsliceserver", $solutions);
        $middle = array();
        foreach($solutions as $s){
            $middle[$s->ssid] = $s;
        }
        $solutions = $middle;
        $middle = array();
        $monitorcategory = $this->monitorcategory->find();

        $this->view->setVar("searchmonitorcategory", $monitorcategory);
        foreach($monitorcategory as $s){
            $middle[$s->mcid] = $s;
        }

        $monitorcategory = $middle;
        $middle = array();
        $cameratype = $this->cameratype->find();
        foreach($cameratype as $s){
            $middle[$s->cid] = $s;
        }
        $cameratype = $middle;
        $this->view->page = $paginator->getPaginate();
        //$this->view->setVar("infolist", $infolist);
        $this->view->setVar("solutions", $solutions);
        $this->view->setVar("monitorcategory", $monitorcategory);
        $this->view->setVar("cameratype", $cameratype);
        $this->view->setVar('soucetype',$this->soucetype);
    }

    /*添加*/
    public function newAction()
    {
        $solutions = $this->sliceserver->find(array("conditions" => "serverstatus = 1"));
        $middle = array();
        foreach($solutions as $s){
            $middle[$s->ssid] = $s->ssname;
        }
        $solutions = $middle;
        $middle = array();
        $monitorcategory = $this->monitorcategory->find();
        foreach($monitorcategory as $s){
            $middle[$s->mcid] = $s->categoryname;
        }

        $monitorcategory = $middle;
        $middle = array();
        $cameratype = $this->cameratype->find(array("conditions" => "status = 1"));
        foreach($cameratype as $s){
            $middle[$s->cid] = $s->name;
        }
        $cameratype = $middle;
        //$this->view->setVar("infolist", $infolist);
        $this->view->setVar("solutions", $solutions);
        $this->view->setVar("monitorcategory", $monitorcategory);
        $this->view->setVar("cameratype", $cameratype);
        $this->view->setVar('soucetype',$this->soucetype);
    }

    /**
     * 添加监控点
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "monitor",
                "action" => "index"
            ));
        }
        $ssid = $this->request->getPost("ssid");
        if(!empty($ssid)){
            $sliceserver = Sliceserver::findFirstByssid($ssid);

            $sql1 = 'SELECT count(*) as num from \Models\Monitor where ssid='.$ssid;
            $monitor_num =  $this->modelsManager->executeQuery($sql1)->getFirst();
            if($sliceserver->maxnum <= $monitor_num['num']){
                $this->flash->error('切片服务器'.$sliceserver->sname.'中切片服务器的监控点已达到最大值'.$sliceserver->maxnum.'，不能再创建监控点！');
                return $this->dispatcher->forward(array(
                    "controller" => "monitor",
                    "action" => "new"
                ));
            }
        }

        $monitor = new Monitor();
        $monitor->mid = $this->guid();
        $monitor->mname = $this->request->getPost("mname");
        $monitor->ssid = $this->request->getPost("ssid");
        $monitor->mcid = $this->request->getPost("mcid");
        $monitor->mac = $this->request->getPost("mac");
        $monitor->cid = $this->request->getPost("cid");
        $monitor->soucetype = $this->request->getPost("soucetype");
        $monitor->sourcepath = $this->request->getPost("sourcepath");
        $monitor->cameraip = $this->request->getPost("cameraip");
        $monitor->createtime = date('Y-m-d H:i:s', time());
        $monitor->creator = $this->auth['id'];
        $monitor->mstatus = 0;
        if (!$monitor->save()) {
            foreach ($monitor->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "monitor",
                "action" => "new"
            ));
        }

        $this::logAction('添加监控点', "添加了监控点:$monitor->mname");
        $this->flash->success("设置成功");
        $this->response->redirect("monitor/index");
    }
    /*编辑*/
    public function editAction($id)
    {

        if (!$this->request->isPost()) {
            $monitor = Monitor::findFirstBymid($id);
            if (!$monitor) {
                $this->flash->error("没有找到该数据");
                return $this->dispatcher->forward(array(
                    "controller" => "monitor",
                    "action" => "index"
                ));
            }
            $solutions = $this->sliceserver->find(array("conditions" => "serverstatus = 1"));
            $middle = array();
            foreach($solutions as $s){
                $middle[$s->ssid] = $s->ssname;
            }
            $solutions = $middle;
            $middle = array();
            $monitorcategory = $this->monitorcategory->find();
            foreach($monitorcategory as $s){
                $middle[$s->mcid] = $s->categoryname;
            }

            $monitorcategory = $middle;
            $middle = array();
            $cameratype = $this->cameratype->find(array("conditions" => "status = 1"));
            foreach($cameratype as $s){
                $middle[$s->cid] = $s->name;
            }
            $cameratype = $middle;
            //$this->view->setVar("infolist", $infolist);
            

            $this->view->mid = $monitor->mid;
            $this->tag->setDefault("mid", $monitor->mid);
            $this->tag->setDefault("mname", $monitor->mname);
            $this->tag->setDefault("ssid", $monitor->ssid);
            $this->tag->setDefault("mcid", $monitor->mcid);
            $this->tag->setDefault("mac", $monitor->mac);
            $this->tag->setDefault("cid", $monitor->cid);
            $this->tag->setDefault("soucetype", $monitor->soucetype);
            $this->tag->setDefault("sourcepath", $monitor->sourcepath);
            $this->tag->setDefault("cameraip", $monitor->cameraip);

            $this->view->setVar("solutions", $solutions);
            $this->view->setVar("monitorcategory", $monitorcategory);
            $this->view->setVar("cameratype", $cameratype);
            $this->view->setVar('soucetype',$this->soucetype);
        }
    }

    /*修改*/
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "monitorcategory",
                "action" => "index"
            ));
        }
        $solutions = $this->sliceserver->find();
        $middle = array();
        foreach($solutions as $s){
            $middle[$s->ssid] = $s->ssname;
        }
        $solutions = $middle;
        $middle = array();
        $monitorcategory = $this->monitorcategory->find();
        foreach($monitorcategory as $s){
            $middle[$s->mcid] = $s->categoryname;
        }

        $monitorcategory = $middle;
        $middle = array();
        $cameratype = $this->cameratype->find();
        foreach($cameratype as $s){
            $middle[$s->cid] = $s->name;
        }
        $cameratype = $middle;

        $this->view->setVar("solutions", $solutions);
        $this->view->setVar("monitorcategory", $monitorcategory);
        $this->view->setVar("cameratype", $cameratype);
        $this->view->setVar('soucetype',$this->soucetype);
        $id = $this->request->getPost("mid");
        $monitor = Monitor::findFirstBymid($id);
        $old_ssid = $monitor->ssid;
        $ssid = $this->request->getPost("ssid");
        if(!empty($ssid)){
            $sliceserver = Sliceserver::findFirstByssid($ssid);

            $sql1 = 'SELECT count(*) as num from \Models\Monitor where ssid='.$ssid;
            $monitor_num =  $this->modelsManager->executeQuery($sql1)->getFirst();
            if($old_ssid != $ssid && $sliceserver->maxnum <= $monitor_num['num']){
                $this->flash->error('切片服务器'.$sliceserver->sname.'中切片服务器的监控点已达到最大值'.$sliceserver->maxnum.'，不能再创建监控点！');
                return $this->dispatcher->forward(array(
                    "controller" => "monitor",
                    "action" => "new"
                ));
            }
        }

        
        $monitor->mname = $this->request->getPost("mname");
        $monitor->ssid = $this->request->getPost("ssid");
        $monitor->mcid = $this->request->getPost("mcid");
        $monitor->mac = $this->request->getPost("mac");
        $monitor->cid = $this->request->getPost("cid");
        $monitor->soucetype = $this->request->getPost("soucetype");
        $monitor->sourcepath = $this->request->getPost("sourcepath");
        $monitor->cameraip = $this->request->getPost("cameraip");


        if (!$monitor->save()) {
            foreach ($monitor->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "monitor",
                "action" => "edit",
                "params" => array($monitor->mid)
            ));
        }
        $this::logAction('修改监控点', "修改了监控点:$monitor->mname");

        $this->flash->success("修改成功");
        $this->response->redirect("monitor/index");
    }

    /*删除*/
    public function deleteAction($id)
    {

        $monitor = Monitor::findFirstBymid($id);
        if (!$monitor) {
            $this->flash->error("该监控点不存在");

            return $this->dispatcher->forward(array(
                "controller" => "monitor",
                "action" => "index"
            ));
        }

        if (!$monitor->delete()) {
            foreach ($monitor->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "monitor",
                "action" => "index"
            ));
        }
        $this::logAction('删除监控点', "删除了监控点:$monitor->mname");

        $this->flash->success("删除成功");
        $this->response->redirect("monitor/index");
    }
    /*启用或禁用:$status:1为启用;0为禁用*/
    public function updatestatusAction($id,$status){
        //$msg = array('0'=>'禁用','1'=>'启用');
        $monitor = Monitor::findFirstBymid($id);
        $uid = 'MonitorAdmin_'.$this->auth['id'];
        if($status == 1){
            Services::getService('WebApi')->StartTask($id,$uid);
        }else{
            Services::getService('WebApi')->StopTask($id);
        }

        /*if(!$monitor->save()){
            $this->flash->error($msg[$monitor->mstatus].'失败');
            foreach ($monitor->getMessages() as $message) {
                $this->flash->error($message);
            }
        }else{
            $this->flash->success($msg[$monitor->mstatus].'成功');
        }*/
        $this->response->redirect("monitor/index");
    }

    /*批量启用禁用*/
    public function updatestatusbatAction($chk_value, $status){
        $id = explode(",",$chk_value);
        $result =array();
        foreach($id as $val){
            $uid = 'MonitorAdmin_'.$this->auth['id'];
            if($status == 1){
                Services::getService('WebApi')->StartTask($val,$uid);
            }else{
                Services::getService('WebApi')->StopTask($val);
            }
        }
    }

    /*查看详情*/
   public function detailAction($id){
        $sql = 'SELECT m.mid, m.mname,m.ssid,m.mcid,m.mac,m.cid,m.soucetype,m.sourcepath,m.cameraip,m.createtime,m.creator,m.mstatus,ms.ssname as msname,mc.categoryname, c.name as cameraname,a.username from \Models\Monitor m left join \Models\Sliceserver ms on m.ssid=ms.ssid left join \Models\Monitorcategory mc on m.mcid= mc.mcid left join \Models\Cameratype c on m.cid=c.cid left join \Models\Admin a on a.id=m.creator where m.mid="'.$id.'" limit 1';
        $info = $this->modelsManager->executeQuery($sql)->getFirst();
        if (!$info) {
            $this->flash->error("没有找到该数据");
            return $this->dispatcher->forward(array(
                    "controller" => "monitor",
                    "action" => "index"
            ));
        }
        $soucetype = '';
        if($info->soucetype){
            $soucetype = $this->soucetype[$info->soucetype];
        }
        $mstatus = '禁用';
        if($info->mstatus == 1){
            $mstatus = '正常';
        }
        $this->tag->setDefault('mname',$info->mname);
        $this->tag->setDefault('categoryname',$info->categoryname);
        $this->tag->setDefault('cameraname',$info->cameraname);
        $this->tag->setDefault('mac',$info->mac);
        $this->tag->setDefault('soucetype',$soucetype);
        $this->tag->setDefault('sourcepath',$info->sourcepath);
        $this->tag->setDefault('cameraip',$info->cameraip);
        $this->tag->setDefault('creator',$info->username);
        $this->tag->setDefault('mstatus',$mstatus);
        $this->tag->setDefault('msname',$info->msname);
        $this->tag->setDefault('createtime',$info->createtime);

   }
   private  function guid(){
        if (function_exists('com_create_guid')){
            return trim(trim(com_create_guid(),'{'),'}');
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = //chr(123) "{"
                substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
                //.chr(125); "}"
            return $uuid;
        }
    }


    public function getSourcepathAction()
    {

        $cid = $this->request->getPost('cid');
        $soucetype = $this->request->getPost('soucetype');
        if (!$this->request->isPost() || $cid == 0 || $soucetype == 0)
        {
            return '';
        }
        switch($soucetype)
        {
            case 1:
                $sql = "SELECT mainsource FROM \Models\Cameratype WHERE cid = $cid";
                $res = $this->modelsManager->executeQuery($sql);
                return  json_encode($res[0]->mainsource);

            case 2:
                $sql = "SELECT childsource FROM \Models\Cameratype WHERE cid = $cid";
                $res = $this->modelsManager->executeQuery($sql);
                return  json_encode($res[0]->childsource) ;

        }

    }
    /*预览*/
    public function openVideoAction($id){
        $setinfo = Setting::findFirstByname('webplayurl');
        //监控点信息
        $monitorinfo = Monitor::findFirstBymid($id);
        $monitorinfo->playurl = str_replace('{$monitorid}', $id, $setinfo->value); //播放地址
        $this->view->setVar("monitorinfo", $monitorinfo);
    }
}