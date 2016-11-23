<?php
namespace Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\App as App;

class AppController extends ControllerBase
{
    public function initialize(){
        parent::initialize();
        $this->view->setVar('activeinfo','app');
    }
    /**
     * app列表
     */
    public function indexAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "Models\App", $_POST);
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
        if ($this->request->getPost("username")) {
            //$parameters["conditions"] .= "and status = 0"; //status:0正常,1删除
            $username = $this->request->getPost("username");
            $parameters["conditions"] .= " and status = 0 and  username like '%".$username."%'   ";
        }

        if (isset($parameters["conditions"])) {
            $builder = $this->modelsManager->createBuilder()
            ->columns('*')
            ->from('Models\App')
            ->where($parameters['conditions'], $parameters['bind'])
            ->orderBy('app_id');
        } else {
                 $builder = $this->modelsManager->createBuilder()
            ->columns('*')
            ->from('Models\App')
            ->orderBy('app_id');
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
     * Creates a new app
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "app",
                "action" => "index"
            ));
        }

        $app = new App();

        $app->username = $this->request->getPost("username");
        $app->info = $this->request->getPost("info");
        $publicKey = $this->config->app->publicKey;
        $app->private_key =  hash_hmac('sha256', $app->username, $publicKey);
        $app->status = 0;
        $app->adminname = $this->auth['name'];

        $apps = App::findFirst(array(
            "username = :username:",
            "bind" => array('username' => $app->username)
        ));

        if ($apps != false) {
            $this->flash->error('该用户名已被注册');
            return $this->dispatcher->forward(array(
                "controller" => "app",
                "action" => "new"
            ));
        }

        if (!$app->save()) {
            foreach ($app->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "app",
                "action" => "new"
            ));
        }
        $this::logAction('添加APP', "添加APP:$app->username");

        $this->flash->success("设置成功");

        $this->response->redirect("app/index");
    }

    /**
     * 删除
     *
     * @param string $app_id
     */
    public function deleteAction($app_id)
    {
        $app = App::findFirstByapp_id($app_id);
        if (!$app) {
            $this->flash->error("该app不存在");
            return $this->dispatcher->forward(array(
                "controller" => "app",
                "action" => "index"
            ));
        }
        if (!$app->delete()) {
            foreach ($app->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward(array(
                "controller" => "app",
                "action" => "index"
            ));
        }
        $this::logAction('删除APP', "删除APP:$app->username");

        $this->flash->success("删除成功");
        $this->response->redirect("app/index");
    }

}
