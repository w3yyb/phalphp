<?php
/**
 * webapp
 * acl_资源管理模块
 */
namespace Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\Resources as Resources;
use \Models\Operations as Operations;
use \Models\Role;
use \Models\Aclaccess as Aclaccess;

class ResourceController extends ControllerBase
{
    public function initialize(){
        parent::initialize();
        $this->view->setVar('activeinfo','resource');
    }
    /**
     * 资源and 操作列表
     */
    public function indexAction()
    {
        $resources = Resources::find();
        $result = [];
        $k=0;
        foreach ($resources as $resource) {
            $operations = Operations::findByresourceid($resource->resourceid);
            $result[] = [
                'resourceid' => $resource->resourceid,
                'name' => $resource->name,
                'resourceinfo' => $resource->resourceinfo,
            ];
            foreach ($operations as $operation) {
                $result[$k]['opera'][] = [
                    'operationid' => $operation->operationid,
                    'name' => $operation->name,
                    'operationinfo' => $operation->operationinfo,
                ];
            }
            $k++;
        }
        $this->view->setVar("allresource", $result);
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
    }

    /**
     * Displays the creation form
     */
    public function addoperaAction($id)
    {
        $this->view->resourceid = $id;
        $this->tag->setDefault("resourceid", $id);
    }

    /**
     * 编辑
     *
     * @param string $id
     */
    public function editAction($id)
    {

        if (!$this->request->isPost()) {
            $resources = Resources::findFirstByresourceid($id);
            if (!$resources) {
                $this->flash->error("resource was not found");
                return $this->dispatcher->forward(array(
                    "controller" => "resource",
                    "action" => "index"
                ));
            }

            $this->view->resourceid = $resources->resourceid;
            $this->tag->setDefault("resourceid", $resources->resourceid);
            $this->tag->setDefault("name", $resources->name);
            $this->tag->setDefault("resourceinfo", $resources->resourceinfo);
           // $this->view->setVar("roleid", $roleid);


        }
    }

    /**
     * 编辑操作
     *
     * @param string $id
     */
    public function editoperaAction($id)
    {

        if (!$this->request->isPost()) {
            $operation = Operations::findFirstByoperationid($id);
            if (!$operation) {
                $this->flash->error("operation was not found");
                return $this->dispatcher->forward(array(
                    "controller" => "resource",
                    "action" => "index"
                ));
            }

            $this->view->operationid = $operation->operationid;
            $this->tag->setDefault("operationid", $operation->operationid);
            $this->tag->setDefault("name", $operation->name);
            $this->tag->setDefault("operationinfo", $operation->operationinfo);
            // $this->view->setVar("roleid", $roleid);
        }
    }

    /**
     * 添加资源
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "index"
            ));
        }

        $resource = new Resources();
        $resource->name = $this->request->getPost("name");
        $resource->resourceinfo = $this->request->getPost("resourceinfo");

        $resourcename = Resources::findFirst(array(
            "name = :name:",
            "bind" => array('name' => $resource->name)
        ));

        if ($resourcename != false) {
            $this->flash->error('该资源名已存在');
            return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "new"
            ));
        }

        if (!$resource->save()) {
            foreach ($resource->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "new"
            ));
        }

        $this::logAction('添加资源', "添加了资源:$resource->name");
        $this->flash->success("设置成功");
        /*return $this->dispatcher->forward(array(
            "controller" => "resource",
            "action" => "index"
        ));*/
        $this->response->redirect("resource/index");
    }

    /**
     * 添加资源
     */
    public function createoperaAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "index"
            ));
        }

        $operation = new Operations();
        $operation->name = $this->request->getPost("name");
        $operation->operationinfo = $this->request->getPost("operationinfo");
        $operation->resourceid = $this->request->getPost("resourceid");

        $operationname = Operations::findFirst(array(
            "name = :name:",
            "bind" => array('name' => $operation->name)
        ));

       

        if (!$operation->save()) {
            foreach ($operation->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "addopera",
                "params" => [$operation->resourceid],
            ));
        }

        $this::logAction('添加操作', "添加了操作:$operation->name");
        $this->flash->success("设置成功");
        /*return $this->dispatcher->forward(array(
            "controller" => "resource",
            "action" => "index"
        ));*/
        $this->response->redirect("resource/index");
    }
    /**
     * Saves a resource edited
     *
     */
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("resourceid");

        $resource = Resources::findFirstByresourceid($id);
        if (!$resource) {
            $this->flash->error("该资源不存在");

            /*return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "index"
            ));*/
            $this->response->redirect("resource/index");
        }

        $resource->name = $this->request->getPost("name");
        $resource->resourceid = $this->request->getPost("resourceid");
        $resource->resourceinfo = $this->request->getPost("resourceinfo");

        if (!$resource->save()) {
            foreach ($resource->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "edit",
                "params" => array($resource->resourceid)
            ));
        }
        $this::logAction('修改资源', "修改了资源:$resource->name");

        $this->flash->success("修改成功");

        /*return $this->dispatcher->forward(array(
            "controller" => "resource",
            "action" => "index"
        ));*/
        $this->response->redirect("resource/index");
    }

    /**
     * Saves a operation edited
     *
     */
    public function saveoperaAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("operationid");

        $operation = Operations::findFirstByoperationid($id);
        if (!$operation) {
            $this->flash->error("该操作不存在");

            /*return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "index"
            ));*/
            $this->response->redirect("resource/index");
        }

        $operation->name = $this->request->getPost("name");
        $operation->operationid = $this->request->getPost("operationid");
        $operation->operationinfo = $this->request->getPost("operationinfo");

        if (!$operation->save()) {
            foreach ($operation->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "editopera",
                "params" => array($operation->operationid)
            ));
        }
        $this::logAction('修改操作', "修改了操作:$operation->name");

        $this->flash->success("修改成功");

        /*return $this->dispatcher->forward(array(
            "controller" => "resource",
            "action" => "index"
        ));*/
        $this->response->redirect("resource/index");
    }
    /**
     * Deletes a resrouce
     *
     * @param string $id
     */
    public function deleteAction($id)
    {

        $resource = Resources::findFirstByresourceid($id);
        if (!$resource) {
            $this->flash->error("resource was not found");

            return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "index"
            ));
        }

        if (!$resource->delete()) {
            foreach ($resource->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "search"
            ));
        }
        $access=Aclaccess::findByresourceid($resource->resourceid);
        $access->delete();
        $opra=Operations::findByresourceid($resource->resourceid);
        $opra->delete();

        $this::logAction('删除资源', "删除了资源:$resource->name");

        $this->flash->success("删除成功");

        /*return $this->dispatcher->forward(array(
            "controller" => "resource",
            "action" => "index"
        ));*/
        $this->response->redirect("resource/index");
    }

    /**
     * Deletes a operation
     *
     * @param string $id
     */
    public function deleteoperaAction($id)
    {

        $operation = Operations::findFirstByoperationid($id);
        if (!$operation) {
            $this->flash->error("operation was not found");

            return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "index"
            ));
        }

        if (!$operation->delete()) {
            foreach ($operation->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "resource",
                "action" => "index"
            ));
        }
        $this::logAction('删除操作', "删除了操作:$operation->name");

        $this->flash->success("删除成功");

        /*return $this->dispatcher->forward(array(
            "controller" => "resource",
            "action" => "index"
        ));*/
        $this->response->redirect("resource/index");
    }
}
