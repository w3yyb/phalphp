<?php
/**
 * 社区信息.
 */
namespace Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\Community as Community;

class CommunityController extends ControllerBase
{
    public function initialize(){
        parent::initialize();
        $this->view->setVar('activeinfo','community');
    }
    /*列表*/
    public function indexAction(){
        $numberPage = 1;
        if ($this->request->isPost()) {
            if($_POST['status'] == 'all'){unset($_POST['status']);}
            $query = Criteria::fromInput($this->di, "Models\Community", $_POST);
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

        $communityname = "";
        if ($this->request->getPost("communityname")) {
            $communityname = $this->request->getPost("communityname");
            if(isset($parameters["conditions"])){
                $parameters["conditions"] .= " and  communityname like '%".$communityname."%'   ";
            }else{
                $parameters["conditions"] .= " communityname like '%".$communityname."%'   ";
            }
        }
        $this->view->setVar('communityname',$communityname);

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
                ->from('Models\Community')
                ->where($parameters['conditions'], $parameters['bind'])
                ->orderBy('communityid');
        } else {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Community')
                ->orderBy('communityid');
        }

        $paginator = new PaginatorQueryBuilder(
            array(
                "builder" => $builder,
                "limit"   => 10,
                "page"    => $numberPage
            )
        );

        //$infolist = Community::find();

        $this->view->page = $paginator->getPaginate();
        //$this->view->setVar("infolist", $infolist);
    }

    /*更新状态*/
    public function statusAction($id)
    {

        $community = Community::findFirstBycommunityid($id);
        if (!$community) {
            $this->flash->error("该社区不存在");

            return $this->dispatcher->forward(array(
                "controller" => "community",
                "action" => "index"
            ));
        }

        if($community->status == 1){
            $community->status = 0;
        }else{
            $community->status = 1;
        }

        if (!$community->save()) {
            foreach ($community->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "community",
                "action" => "index"
            ));
        }
        $this::logAction('修改社区信息', "编辑社区:$community->name");

        $this->flash->success("编辑成功");
        $this->response->redirect("community/index");
    }
}
