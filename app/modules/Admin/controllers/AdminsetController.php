<?php
/**
 * admin设置密码
 */
namespace Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\Admin as Admin;
class AdminsetController extends ControllerBase
{
    public function initialize(){
        parent::initialize();
        $this->view->setVar('activeinfo','admin');
    }
    /*设置密码*/
    public function setAction($id)
    {

        if (!$this->request->isPost()) {
            $admin = Admin::findFirstByid($id);
            if (!$admin) {
                $this->flash->error("admin was not found");
                return $this->dispatcher->forward(array(
                    "controller" => "index",
                    "action" => "index"
                ));
            }

            $this->view->id = $admin->id;
            $this->tag->setDefault("id", $admin->id);
            $this->tag->setDefault("username", $admin->username);



        }
    }

    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "index",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("id");

        $admin = Admin::findFirstByid($id);
        if (!$admin) {
            $this->flash->error("该管理员不存在");
            $this->response->redirect("index/index");
        }
        $admin->password = sha1($this->request->getPost("password"));


        if (!$admin->save()) {
            foreach ($admin->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "adminset",
                "action" => "edit",
                "params" => array($admin->id)
            ));
        }
        $this::logAction('修改管理员', "修改了管理员:$admin->username");

        $this->flash->success("修改成功");
        $this->response->redirect("index/index");
    }
}