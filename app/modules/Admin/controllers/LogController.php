<?php
namespace Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\Log;

class LogController extends ControllerBase
{
    public function initialize(){
        parent::initialize();
        $this->view->setVar('activeinfo','log');
    }
    /**
     * log列表
     */
    public function indexAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "Models\Log", $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }
        if ($numberPage ==null) {
            $this->persistent->parameters = null;
            $_SESSION['conditions']=null;
        }
        if ($this->request->getPost("username") && (!$this->request->getPost("stime") || !$this->request->getPost("etime"))) {
            $_SESSION['conditions']=null;
        }
         $parameters = $this->persistent->parameters;

        if (!is_array($parameters)) {
            $parameters = array();
        }

        if ($this->request->getPost("stime") && !$this->request->getPost("etime")) {
            $this->flash->success("请输入结束时间");
        } elseif (!$this->request->getPost("stime") && $this->request->getPost("etime")) {
            $this->flash->success("请输入开始时间");
        }


        if ($this->request->getPost("username") || ($this->request->getPost("stime")&&$this->request->getPost("etime"))) {
            $sql="";
            $username = $this->request->getPost("username");
            if ($username) {
                $sql="    username like '%".$username."%'   and ";
            }
            $stime = $this->request->getPost("stime");
            $etime = $this->request->getPost("etime");
            //$parameters["conditions"] .= "   and  username like '%".$username."%'   ";
            //$parameters['bind']=  array ( "username" =>"%$username%");
            if ($stime && $etime) {
                $parameters["conditions"] = "  $sql  addtime > '" . $stime . "' and addtime <'" . $etime . "'";//这里的条件不会存到 $this->persistent->parameters里
                $parameters['bind']=null;
                $_SESSION['conditions']=$parameters["conditions"];
            }
        }
        if (!empty($_SESSION['conditions'])) {
             $parameters["conditions"]=$_SESSION['conditions'];
             $parameters['bind']=null;
        }

        if (isset($parameters["conditions"])) {
            $builder = $this->modelsManager->createBuilder()
            ->columns('*')
            ->from('Models\Log')
            ->where($parameters['conditions'], $parameters['bind'])
            ->orderBy('id desc');
        } else {
                 $builder = $this->modelsManager->createBuilder()
            ->columns('*')
            ->from('Models\Log')
            ->orderBy('id desc');
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
     * 删除
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $log = Log::findFirstByid($id);
        if (!$log) {
            $this->flash->error("该log不存在");
            return $this->dispatcher->forward(array(
                "controller" => "log",
                "action" => "index"
            ));
        }
        if (!$log->delete()) {
            foreach ($app->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward(array(
                "controller" => "log",
                "action" => "index"
            ));
        }
       // $this::logAction('删除Log', "删除Log");

        $this->flash->success("删除成功");
        $this->response->redirect("log/index");
    }

}
