<?php
/**
 * webapp
 * 监控方案管理模块
 */
namespace Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\Monitorsolution as Monitorsolution;
use \Models\Sliceserver as Sliceserver;

class MonitorsolutionController extends ControllerBase
{
    public function initialize(){
        parent::initialize();
        $this->view->setVar('activeinfo','monitorsolution');
    }
    /**
     * 监控方案列表
     */
    public function indexAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            if($_POST['status'] == 'all'){unset($_POST['status']);}
            $query = Criteria::fromInput($this->di, "Models\Monitorsolution", $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }
        if ($numberPage ==null) {
            $this->persistent->parameters = null;
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }

        //名称
        $name = "";
        if ($this->request->getPost("name")) {
            $name = $this->request->getPost("name");
            if(isset($parameters["conditions"])){
                $parameters["conditions"] .= " and  name like '%".$name."%'   ";
            }else{
                $parameters["conditions"] .= " name like '%".$name."%'   ";
            }
        }
        $this->view->setVar('name',$name);

        //状态
        $status = '';
        if($this->request->getPost("status") || $this->request->getPost("status") == '0'){
            $status = $this->request->getPost("status");
            if(isset($parameters["conditions"])){
                $parameters["conditions"] .= ' and status='.$status;
            }else{
                $parameters["conditions"] = ' status='.$status;
            }
        }
        $this->view->setVar('status',$status);
        if (isset($parameters["conditions"])) {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Monitorsolution')
                ->where($parameters['conditions'], $parameters['bind'])
                ->orderBy('msid DESC');
        } else {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Monitorsolution')
                ->orderBy('msid  DESC');
        }

        $paginator = new PaginatorQueryBuilder(
            array(
                "builder" => $builder,
                "limit"   => 10,
                "page"    => $numberPage
            )
        );
        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
    }

    /**
     * 编辑
     *
     * @param string $id
     */
    public function editAction($id)
    {

        if (!$this->request->isPost()) {
            $solution = Monitorsolution::findFirstBymsid($id);
            if (!$solution) {
                $this->flash->error("solution was not found");
                return $this->dispatcher->forward(array(
                    "controller" => "monitorsolution",
                    "action" => "index"
                ));
            }

            $this->view->msid = $solution->msid;
            $this->tag->setDefault("msid", $solution->msid);
            $this->tag->setDefault("name", $solution->name);
            $this->tag->setDefault("transtype", $solution->transtype);
            $this->tag->setDefault("targetpath", $solution->targetpath);
            $this->tag->setDefault("storepath", $solution->storepath);
            $this->tag->setDefault("slicereportpath", $solution->slicereportpath);
            $this->tag->setDefault("httpprefix", $solution->httpprefix);
            $this->tag->setDefault("slicenumber", $solution->slicenumber);
            $this->tag->setDefault("cliptime", $solution->cliptime);
            $this->tag->setDefault("cachetime", $solution->cachetime);
            $this->tag->setDefault("speedfactor", $solution->speedfactor);
            $this->tag->setDefault("isvoice", $solution->isvoice);
            $this->tag->setDefault("remark", $solution->remark);
            $this->tag->setDefault("status", $solution->status);
            $this->view->setVar("status", $solution->status);
        }
    }

    /**
     * 查看
     *
     * @param string $id
     */
    public function viewAction($id)
    {

        if (!$this->request->isPost()) {
            $solution = Monitorsolution::findFirstBymsid($id);
            if (!$solution) {
                $this->flash->error("solution was not found");
                return $this->dispatcher->forward(array(
                    "controller" => "monitorsolution",
                    "action" => "index"
                ));
            }

            $this->view->msid = $solution->msid;
            $this->tag->setDefault("msid", $solution->msid);
            $this->tag->setDefault("name", $solution->name);
            $this->tag->setDefault("transtype", $solution->transtype);
            $this->tag->setDefault("targetpath", $solution->targetpath);
            $this->tag->setDefault("storepath", $solution->storepath);
            $this->tag->setDefault("slicereportpath", $solution->slicereportpath);
            $this->tag->setDefault("httpprefix", $solution->httpprefix);
            $this->tag->setDefault("slicenumber", $solution->slicenumber);
            $this->tag->setDefault("cliptime", $solution->cliptime);
            $this->tag->setDefault("cachetime", $solution->cachetime);
            $this->tag->setDefault("speedfactor", $solution->speedfactor);
            $this->tag->setDefault("isvoice", $solution->isvoice);
            $this->tag->setDefault("remark", $solution->remark);
            $this->tag->setDefault("status", $solution->status);
            $this->tag->setDefault("createtime", $solution->createtime);
            $this->tag->setDefault("updatetime", $solution->updatetime);

            $this->view->setVar("status", $solution->status);
            $this->view->setVar("isvoice", $solution->isvoice);
            $this->view->setVar("transtype", $solution->transtype);
        }
    }

    /**
     * 添加监控方案
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "monitorsolution",
                "action" => "index"
            ));
        }
       // var_dump($this->request->getPost());exit;
        $solution = new Monitorsolution();
        $slicenumber = $this->request->getPost("slicenumber");
        $cliptime = $this->request->getPost("cliptime");
        $cachetime = $this->request->getPost("cachetime");
        $solution->name = $this->request->getPost("name");
        $solution->transtype = $this->request->getPost("transtype");
        $solution->targetpath = $this->request->getPost("targetpath");
        $solution->storepath = $this->request->getPost("storepath");
        $solution->slicereportpath = $this->request->getPost("slicereportpath");
        $solution->httpprefix = $this->request->getPost("httpprefix");
        if($slicenumber < 0){
            $slicenumber = 1;
        }
        $solution->slicenumber = $slicenumber;
        $solution->cliptime = empty($cliptime)?0:$cliptime;
        $solution->cachetime = empty($cachetime)?0:$cachetime;
        $solution->speedfactor = $this->request->getPost("speedfactor");
        $solution->isvoice = $this->request->getPost("isvoice");
        $solution->remark = $this->request->getPost("remark");
        $solution->createtime = date("Y-m-d H:i:s");
        $solution->updatetime = date("Y-m-d H:i:s");
        $solution->status = $this->request->getPost("status");

        //echo $this->request->getPost("username");exit;

        $sname = Monitorsolution::findFirst(array(
            "name = :name:",
            "bind" => array('name' => $solution->name)
        ));

        if ($sname != false) {
            $this->flash->error('该监控方案名已存在');
            return $this->dispatcher->forward(array(
                "controller" => "monitorsolution",
                "action" => "new"
            ));
        }

        if (!$solution->save()) {
            foreach ($solution->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "monitorsolution",
                "action" => "new"
            ));
        }

        $this::logAction('添加监控方案', "添加了监控方案:$solution->name");
        $this->flash->success("设置成功");
        /*return $this->dispatcher->forward(array(
            "controller" => "monitorsolution",
            "action" => "index"
        ));*/
        $this->response->redirect("monitorsolution/index");
    }

    /**
     * Saves a   edited
     *
     */
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "monitorsolution",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("msid");

        $solution = Monitorsolution::findFirstBymsid($id);
        if (!$solution) {
            $this->flash->error("该方案不存在");

            /*return $this->dispatcher->forward(array(
                "controller" => "monitorsolution",
                "action" => "index"
            ));*/
            $this->response->redirect("monitorsolution/index");
        }

        $solution->msid = $this->request->getPost("msid");
        $solution->name = $this->request->getPost("name");
        $solution->transtype = $this->request->getPost("transtype");
        $solution->targetpath = $this->request->getPost("targetpath");
        $solution->storepath = $this->request->getPost("storepath");
        $solution->slicereportpath = $this->request->getPost("slicereportpath");
        $solution->httpprefix = $this->request->getPost("httpprefix");
        $solution->slicenumber = $this->request->getPost("slicenumber");
        $solution->cliptime = $this->request->getPost("cliptime");
        $solution->cachetime = $this->request->getPost("cachetime");
        $solution->speedfactor = $this->request->getPost("speedfactor");
        $solution->isvoice = $this->request->getPost("isvoice");
        $solution->remark = $this->request->getPost("remark");
        $solution->updatetime = date("Y-m-d H:i:s");
        $solution->status = $this->request->getPost("status");


        if (!$solution->save()) {
            foreach ($solution->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "monitorsolution",
                "action" => "edit",
                "params" => array($solution->msid)
            ));
        }
        $this::logAction('修改监控方案', "修改了监控方案:$solution->name");

        $this->flash->success("修改成功");

        /*return $this->dispatcher->forward(array(
            "controller" => "monitorsolution",
            "action" => "index"
        ));*/
        $this->response->redirect("monitorsolution/index");
    }

    /**
     * Deletes a admin
     *
     * @param string $id
     */
    public function deleteAction($id)
    {

        $solution = Monitorsolution::findFirstBymsid($id);
        if (!$solution) {
            $this->flash->error("solution was not found");

            return $this->dispatcher->forward(array(
                "controller" => "monitorsolution",
                "action" => "index"
            ));
        }

        if(Sliceserver::findFirstBymsid($id)){
            $this->flash->error("监控方案已被使用，删除失败");
            return $this->dispatcher->forward(array(
                "controller" => "monitorsolution",
                "action" => "index"
            ));
        }

        if (!$solution->delete()) {
            foreach ($solution->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "monitorsolution",
                "action" => "index"
            ));
        }

        $this::logAction('删除监控方案', "删除了监控方案:$solution->name");

        $this->flash->success("删除成功");

        /*return $this->dispatcher->forward(array(
            "controller" => "monitorsolution",
            "action" => "index"
        ));*/
        $this->response->redirect("monitorsolution/index");
    }
}
