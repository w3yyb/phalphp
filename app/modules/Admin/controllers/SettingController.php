<?php
/**
 * webapp
 * 系统配置
 */
namespace Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\Setting;

class SettingController extends ControllerBase
{
    public function initialize(){
        parent::initialize();
        $this->view->setVar('activeinfo','setting');
    }
    /**
     * Displays the creation form
     */
    public function indexAction()
    {
        $setting = Setting::find();

        /*foreach ($address as $address) {
            $result = [
                'reportaddr' => $address->reportaddr,
                'monitoraddr' => $address->monitoraddr,
            ];
        }
        $this->view->setVar("reportaddr", $result['reportaddr']);
        $this->view->setVar("monitoraddr", $result['monitoraddr']);*/
        $this->view->setVar("setting", $setting);
    }

    /*添加*/
    public function newAction()
    {
    }

    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "setting",
                "action" => "index"
            ));
        }

        $setting = new Setting();

        $setting->name = $this->request->getPost("name");
        $setting->value = $this->request->getPost("value");
        $setting->info = $this->request->getPost("info");

        $info = Setting::findFirst(array(
            "name = :name:",
            "bind" => array('name' => $setting->name)
        ));

        if (!$setting->save()) {
            foreach ($setting->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "setting",
                "action" => "new"
            ));
        }

        $this::logAction('添加系统变量', "添加了系统变量:$setting->name");
        $this->flash->success("设置成功");
        $this->response->redirect("setting/index");
    }

    /*编辑*/
    public function editAction($id)
    {

        if (!$this->request->isPost()) {
            $setting = Setting::findFirstByid($id);
            if (!$setting) {
                $this->flash->error("没有找到该数据");
                return $this->dispatcher->forward(array(
                    "controller" => "setting",
                    "action" => "index"
                ));
            }

            $this->view->id = $setting->id;
            $this->tag->setDefault("id", $setting->id);
            $this->tag->setDefault("name", $setting->name);
            $this->tag->setDefault("value", $setting->value);
            $this->tag->setDefault("info", $setting->info);
        }
    }

    /*修改*/
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "setting",
                "action" => "index"
            ));
        }
        $id = $this->request->getPost("id");

        $setting = Setting::findFirstByid($id);
        $setting->value = $this->request->getPost("value");
        $setting->info = $this->request->getPost("info");

        if (!$setting->save()) {
            foreach ($setting->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "setting",
                "action" => "edit",
                "params" => array($setting->id)
            ));
        }
        $this->tag->setDefault("format", $this->request->getPost("format"));

        $this::logAction('修改系统变量', "修改了系统变量:$setting->name");

        $this->flash->success("修改成功");
        $this->response->redirect("setting/index");
    }
}
