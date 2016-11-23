<?php
/**
 * webapp
 * 管理员管理模块
 */
namespace Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\Admin as Admin;
use \Models\Role;

class AdminController extends ControllerBase
{
    public function initialize(){
        parent::initialize();
        $this->view->setVar('activeinfo','admin');
    }
    /**
     * 管理员列表
     */
    public function indexAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "Models\Admin", $_POST);
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
            $username = $this->request->getPost("username");
            $parameters["conditions"] .= " and  username like '%".$username."%'   ";
        }
        if (isset($parameters["conditions"])) {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Admin')
                ->where($parameters['conditions'], $parameters['bind'])
                ->orderBy('id');
        } else {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Admin')
                ->orderBy('id');
        }

        $paginator = new PaginatorQueryBuilder(
            array(
                "builder" => $builder,
                "limit"   => 10,
                "page"    => $numberPage
            )
        );
        $role = Role::find();
        $this->view->setVar("roleid", $role);
        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
        $role = Role::find();
        $roleid= $this->tag->select(
            [
                "roleid",
                $role,
                "using" => [
                    "roleid",
                    "roleinfo",
                ],
                "useEmpty" => true,
                "emptyText"  => "请选择",
                "emptyValue" => "",
                "required" => "",
            ]
        );
        $this->view->setVar("roleid", $roleid);
    }

    /**
     * 编辑
     *
     * @param string $id
     */
    public function editAction($id)
    {

        if (!$this->request->isPost()) {
            $admin = Admin::findFirstByid($id);
            if (!$admin) {
                $this->flash->error("admin was not found");
                return $this->dispatcher->forward(array(
                    "controller" => "admin",
                    "action" => "index"
                ));
            }

            $this->view->id = $admin->id;
            $this->tag->setDefault("id", $admin->id);
            $this->tag->setDefault("username", $admin->username);
            $this->tag->setDefault("roleid", $admin->roleid);

            $role = Role::find();
            $roles=[];
            foreach ($role as $role) {
                 $roles[$role->roleid] =$role->roleinfo;
            }

            $selectarr=   [
                "roleid",
                $roles
            ];

            $roleid= $this->tag->selectStatic($selectarr);
            $this->view->setVar("roleid", $roleid);


        }
    }

    /**
     * 添加管理员
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "admin",
                "action" => "index"
            ));
        }

        $admin = new Admin();

        $admin->username = $this->request->getPost("username");
        $admin->roleid = $this->request->getPost("roleid");
        $admin->password = sha1($this->request->getPost("password"));
        $admin->regtime = time();
        $admin->adminname = $this->auth['name'];
        //echo $this->request->getPost("username");exit;

        $user = Admin::findFirst(array(
            "username = :username:",
            "bind" => array('username' => $admin->username)
        ));

        if ($user != false) {
            $this->flash->error('该用户名已被注册');
            return $this->dispatcher->forward(array(
                "controller" => "admin",
                "action" => "new"
            ));
        }

        if (!$admin->save()) {
            foreach ($admin->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "admin",
                "action" => "new"
            ));
        }

        $this::logAction('添加管理员', "添加了管理员:$admin->username");
        $this->flash->success("设置成功");
        /*return $this->dispatcher->forward(array(
            "controller" => "admin",
            "action" => "index"
        ));*/
        $this->response->redirect("admin/index");
    }

    /**
     * Saves a admin edited
     *
     */
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "admin",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("id");

        $admin = Admin::findFirstByid($id);
        if (!$admin) {
            $this->flash->error("该管理员不存在");

            /*return $this->dispatcher->forward(array(
                "controller" => "admin",
                "action" => "index"
            ));*/
            $this->response->redirect("admin/index");
        }

        //$admin->username = $this->request->getPost("username");
        $admin->password = sha1($this->request->getPost("password"));
        $admin->roleid = $this->request->getPost("roleid");
        /*$admin->logintime = $this->request->getPost("logintime");
        $admin->regtime = $this->request->getPost("regtime");*/


        if (!$admin->save()) {
            foreach ($admin->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "admin",
                "action" => "edit",
                "params" => array($admin->id)
            ));
        }
        $this::logAction('修改管理员', "修改了管理员:$admin->username");

        $this->flash->success("修改成功");

        /*return $this->dispatcher->forward(array(
            "controller" => "admin",
            "action" => "index"
        ));*/
        $this->response->redirect("admin/index");
    }

    /**
     * Deletes a admin
     *
     * @param string $id
     */
    public function deleteAction($id)
    {

        $admin = Admin::findFirstByid($id);
        if (!$admin) {
            $this->flash->error("admin was not found");

            return $this->dispatcher->forward(array(
                "controller" => "admin",
                "action" => "index"
            ));
        }

        if (!$admin->delete()) {
            foreach ($admin->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "admin",
                "action" => "search"
            ));
        }
        $this::logAction('删除管理员', "删除了管理员:$admin->username");

        $this->flash->success("删除成功");

        /*return $this->dispatcher->forward(array(
            "controller" => "admin",
            "action" => "index"
        ));*/
        $this->response->redirect("admin/index");
    }
}
