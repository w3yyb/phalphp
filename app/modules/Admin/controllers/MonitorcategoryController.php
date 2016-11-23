<?php
/**
 * 监控分类.
 */
namespace Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\Monitorcategory as Monitorcategory;
use \Models\Community as Community;
use \Models\Monitor as Monitor;

class MonitorcategoryController extends ControllerBase
{
    public function initialize(){
        parent::initialize();
        $this->view->setVar('activeinfo','monitorcategory');
    }
    /*分类列表*/
    public function indexAction(){
        $numberPage = 1;
        if ($this->request->isPost()) {
            if($_POST['parentid'] == 'all'){unset($_POST['parentid']);}
            $query = Criteria::fromInput($this->di, "Models\Monitorcategory", $_POST);
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
        if ($this->request->getPost("categoryname")) {
            $categoryname = $this->request->getPost("categoryname");
            $parameters["conditions"] .= " and  categoryname like '%".$categoryname."%'   ";
        }
        $communityid = '';
        if($this->request->getPost("communityid")){
            $communityid = $this->request->getPost("communityid");
            if(isset($parameters["conditions"])){
                $parameters["conditions"] .= ' AND communityid='.$communityid;
            }else{
                $parameters["conditions"] = ' communityid='.$communityid;
            }

        }
        $parentid = 'all';
        if($this->request->getPost("parentid") || $this->request->getPost("parentid") == '0'){
            $parentid = $this->request->getPost("parentid");
            if(isset($parameters["conditions"])){
                $parameters["conditions"] .= ' AND parentid='.$parentid;
            }else{
                $parameters["conditions"] = ' parentid='.$parentid;
            }

        }
        $this->view->setVar('communityid',$communityid);
        $this->view->setVar('parentid',$parentid);
        if (isset($parameters["conditions"])) {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Monitorcategory')
                ->where($parameters['conditions'], $parameters['bind'])
                ->orderBy('mcid');
        } else {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Monitorcategory')
                ->orderBy('mcid');
        }

        $paginator = new PaginatorQueryBuilder(
            array(
                "builder" => $builder,
                "limit"   => 10,
                "page"    => $numberPage
            )
        );

        $infolist = Monitorcategory::find();

        $parents = array();
        foreach($infolist as $info)
        {
            $parents['0'] = '顶级分类';
            if(count(Monitorcategory::find(['parentid = '.$info->mcid])) > 0){
                $parents[$info->mcid] = $this->menutypeAction($info->idpath) . $info->categoryname;
            }




            /*if($info->parentid == 0){
                $parents[0] = '顶级分类';
            }else{
                $id = intval($info->mcid);
                $parents[$id] = $info->categoryname;
            }*/
        }

        $monitorcategory = Monitorcategory::find();
        $monitorcategorys=[];
        foreach ($monitorcategory as $cateinfo) {
            $monitorcategorys[$cateinfo->mcid] = $this->menutypeAction($cateinfo->idpath) . $cateinfo->categoryname;
        }

        $this->view->page = $paginator->getPaginate();
        $this->view->setVar("infolist", $infolist);
        $community = Community::find(['status=1']);
        $this->view->setVar("community", $community);
        $this->view->setVar("parents", $parents);
    }

    /*添加*/
    public function newAction()
    {
        $monitorcategory = Monitorcategory::find();
        $monitorcategorys=[];
        foreach ($monitorcategory as $cateinfo) {
            $monitorcategorys[$cateinfo->mcid] = $this->menutypeAction($cateinfo->idpath) . $cateinfo->categoryname;
        }
        $monitorcategorylist = $this->tag->select(
            [
                "mcid",
                $monitorcategorys,
                "using" => [
                    "mcid",
                    "categoryname",
                ],
                "useEmpty" => true,
                "emptyText"  => "顶级分类",
                "emptyValue" => "0",
            ]
        );

        $community = Community::find(array("conditions" => "status = 1"));

        $this->view->setVar("monitorcategory", $monitorcategorylist);
        $this->view->setVar("community", $community);
    }

    /**
     * 添加分类
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "monitorcategory",
                "action" => "index"
            ));
        }

        $monitorcategory = new Monitorcategory();

        $monitorcategory->categoryname = $this->request->getPost("categoryname");
        $monitorcategory->parentid = $this->request->getPost("mcid");
        $monitorcategory->communityid = $this->request->getPost("communityid");
        $monitorcategory->remark = $this->request->getPost("remark");
        $monitorcategory->createtime = date('Y-m-d H:i:s', time());
        $monitorcategory->creator = $this->auth['id'];//

        if($monitorcategory->parentid > 0){
            $cateinfo = Monitorcategory::findFirstBymcid($monitorcategory->parentid);
            if($cateinfo->idpath != ''){
                $monitorcategory->idpath = $cateinfo->idpath . ',' . $monitorcategory->parentid;
            }
        }

        $info = Monitorcategory::findFirst(array(
            "categoryname = :categoryname:",
            "bind" => array('categoryname' => $monitorcategory->categoryname)
        ));

        if (!$monitorcategory->save()) {
            foreach ($monitorcategory->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "monitorcategory",
                "action" => "new"
            ));
        }

        $this::logAction('添加监控分类', "添加了监控分类:$monitorcategory->categoryname");
        $this->flash->success("设置成功");
        $this->response->redirect("monitorcategory/index");
    }
    /*编辑*/
    public function editAction($id)
    {

        if (!$this->request->isPost()) {
            $monitorcategory = Monitorcategory::findFirstBymcid($id);
            if (!$monitorcategory) {
                $this->flash->error("没有找到该数据");
                return $this->dispatcher->forward(array(
                    "controller" => "monitorcategory",
                    "action" => "index"
                ));
            }

            $this->view->mcid = $monitorcategory->mcid;
            $this->tag->setDefault("mcid", $monitorcategory->mcid);
            $this->tag->setDefault("categoryname", $monitorcategory->categoryname);
            $this->tag->setDefault("parentid", $monitorcategory->parentid);
            $this->tag->setDefault("remark", $monitorcategory->remark);

            $cateinfo = Monitorcategory::find();
            $cateinfos=[];
            foreach ($cateinfo as $cateinfo) {
                if($cateinfo->mcid != $id){
                    $cateinfos[$cateinfo->mcid] = $this->menutypeAction($cateinfo->idpath) . $cateinfo->categoryname;
                }
            }

            $this->tag->setDefault("cateinfo", "$monitorcategory->parentid");
            $selectarr=   [
                "cateinfo",
                $cateinfos,
                "using" => [
                "mcid",
                "categoryname",
            ],
                "useEmpty" => true,
                "emptyText"  => "顶级分类",
                "emptyValue" => "0",
            ];
            $community = Community::find(array("conditions" => "status = 1"));
            $this->view->setVar("community", $community);

            $typeid= $this->tag->selectStatic($selectarr);
            $this->view->setVar("parentid", $typeid);
            $this->view->setVar("communityid", $monitorcategory->communityid);


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

        $id = $this->request->getPost("mcid");

        $monitorcategory = Monitorcategory::findFirstBymcid($id);

        $monitorcategory->parentid = $this->request->getPost("cateinfo");
        $monitorcategory->communityid = $this->request->getPost("communityid");
        $monitorcategory->remark = $this->request->getPost("remark");


        if (!$monitorcategory->save()) {
            foreach ($monitorcategory->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "monitorcategory",
                "action" => "edit",
                "params" => array($monitorcategory->mcid)
            ));
        }
        $this::logAction('修改监控分类', "修改了监控分类:$monitorcategory->categoryname");

        $this->flash->success("修改成功");
        $this->response->redirect("monitorcategory/index");
    }

    /*删除*/
    public function deleteAction($id)
    {

        $monitorcategory = Monitorcategory::findFirstBymcid($id);
        if (!$monitorcategory) {
            $this->flash->error("该监控分类不存在");

            return $this->dispatcher->forward(array(
                "controller" => "monitorcategory",
                "action" => "index"
            ));
        }
        if(Monitorcategory::findFirstByparentid($id) || Monitor::findFirstBymcid($id)){
            $this->flash->error("监控分类已被使用，删除失败");

            return $this->dispatcher->forward(array(
                "controller" => "monitorcategory",
                "action" => "index"
            ));
        }

        if (!$monitorcategory->delete()) {
            foreach ($monitorcategory->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "admin",
                "action" => "search"
            ));
        }
        $this::logAction('删除监控分类', "删除了监控分类:$monitorcategory->categoryname");

        $this->flash->success("删除成功");
        $this->response->redirect("monitorcategory/index");
    }

    /*格式化菜单*/
    public function menutypeAction($idpath){
        $info = '|';
        if($idpath != '') {
            $array = explode(",", $idpath);
            foreach ($array as $key) {
                $info .= '|';
            }
        }
        return $info.'—';
    }
}
