<?php
/**
 * 摄像头类型管理
 */
namespace Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\Cameratype as Cameratype;
use \Models\Monitor as Monitor;

class CameratypeController extends ControllerBase
{
    public $formats; //格式
    public function initialize(){
        parent::initialize();
        $this->formats = array('1'=>'rtsp');
        $this->view->setVar('activeinfo','cameratype');
    }

    /*列表*/
    public function indexAction(){
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "Models\Cameratype", $_POST);
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
        if ($this->request->getPost("name")) {
            $name = $this->request->getPost("name");
            $parameters["conditions"] .= " and  name like '%".$name."%'   ";
        }
        if (isset($parameters["conditions"])) {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Cameratype')
                ->where($parameters['conditions'], $parameters['bind'])
                ->orderBy('cid desc');
        } else {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Cameratype')
                ->orderBy('cid desc');
        }

        $paginator = new PaginatorQueryBuilder(
            array(
                "builder" => $builder,
                "limit"   => 10,
                "page"    => $numberPage
            )
        );

        $infolist = Cameratype::find();

        $this->view->page = $paginator->getPaginate();
        $this->view->setVar("infolist", $infolist);
        $this->view->setVar('format',$this->formats);
    }

    /*添加*/
    public function newAction()
    {
        $format = $this->tag->select(
            [
                "format",
                $this->formats,
            ]
        );
        $this->view->setVar("format", $format);
    }

    /**
     * 添加分类
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "cameratype",
                "action" => "index"
            ));
        }

        $cameratype = new Cameratype();

        $cameratype->name = $this->request->getPost("name");
        $cameratype->format = $this->request->getPost("format");
        $cameratype->mainsource = $this->request->getPost("mainsource");
        $cameratype->childsource = $this->request->getPost("childsource");
        $cameratype->manufactor = $this->request->getPost("manufactor");
        $cameratype->tel = $this->request->getPost("tel");
        $cameratype->remark = $this->request->getPost("remark");
        $cameratype->createtime = date('Y-m-d H:i:s', time());
        $cameratype->status = 1;
        $cameratype->creator = $this->auth['id'];//

        $info = Cameratype::findFirst(array(
            "name = :name:",
            "bind" => array('name' => $cameratype->name)
        ));

        if (!$cameratype->save()) {
            foreach ($cameratype->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "cameratype",
                "action" => "new"
            ));
        }

        $this::logAction('添加摄像头类型', "添加了摄像头类型:$cameratype->name");
        $this->flash->success("设置成功");
        $this->response->redirect("cameratype/index");
    }

    /*编辑*/
    public function editAction($id)
    {

        if (!$this->request->isPost()) {
            $cameratype = Cameratype::findFirstBycid($id);
            if (!$cameratype) {
                $this->flash->error("没有找到该数据");
                return $this->dispatcher->forward(array(
                    "controller" => "cameratype",
                    "action" => "index"
                ));
            }

            $this->view->mid = $cameratype->cid;
            $this->tag->setDefault("cid", $cameratype->cid);
            $this->tag->setDefault("name", $cameratype->name);
            $this->tag->setDefault("mainsource", $cameratype->mainsource);
            $this->tag->setDefault("childsource", $cameratype->childsource);
            $this->tag->setDefault("manufactor", $cameratype->manufactor);
            $this->tag->setDefault("tel", $cameratype->tel);
            $this->tag->setDefault("remark", $cameratype->remark);

            $cateinfo = Cameratype::find();

            $this->tag->setDefault("format", "$cameratype->format");
            $selectarr=   [
                "format",
                $this->formats,
            ];

            $typeid= $this->tag->selectStatic($selectarr);
            $this->view->setVar("format", $typeid);


        }
    }

    /*修改*/
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "cameratype",
                "action" => "index"
            ));
        }

        $selectarr=   [
            "format",
            $this->formats,
        ];
        $typeid= $this->tag->selectStatic($selectarr);
        $this->view->setVar("format", $typeid);
        $id = $this->request->getPost("cid");

        $cameratype = Cameratype::findFirstBycid($id);
        $cameratype->name = $this->request->getPost("name");
        $cameratype->format = $this->request->getPost("format");
        $cameratype->mainsource = $this->request->getPost("mainsource");
        $cameratype->childsource = $this->request->getPost("childsource");
        $cameratype->manufactor = $this->request->getPost("manufactor");
        $cameratype->tel = $this->request->getPost("tel");
        $cameratype->remark = $this->request->getPost("remark");
        $cameratype->status = 1;

        if (!$cameratype->save()) {
            foreach ($cameratype->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "cameratype",
                "action" => "edit",
                "params" => array($cameratype->cid)
            ));
        }
        $this->tag->setDefault("format", $this->request->getPost("format"));

        $this::logAction('修改摄像头类型', "修改了摄像头类型:$cameratype->name");

        $this->flash->success("修改成功");
        $this->response->redirect("cameratype/index");
    }

    /*更新状态*/
    public function statusAction($id)
    {
        $cameratype = Cameratype::findFirstBycid($id);
        if (!$cameratype) {
            $this->flash->error("该摄像头类型不存在");

            return $this->dispatcher->forward(array(
                "controller" => "cameratype",
                "action" => "index"
            ));
        }

        if($cameratype->status == 1){
            if(Monitor::findFirstBycid($id)){
                $this->flash->error("型号已被使用，禁用失败");

                return $this->dispatcher->forward(array(
                    "controller" => "cameratype",
                    "action" => "index"
                ));
            }
            $cameratype->status = 0;
        }else{
            $cameratype->status = 1;
        }

        if (!$cameratype->save()) {
            foreach ($cameratype->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "community",
                "action" => "index"
            ));
        }
        $this::logAction('修改状态', "编辑状态:$cameratype->name");

        $this->flash->success("编辑成功");
        $this->response->redirect("cameratype/index");
    }
}