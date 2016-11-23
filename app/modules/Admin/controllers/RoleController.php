<?php
/**
 * webapp
 * acl_角色管理模块
 */
namespace Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\Resources as Resources;
use \Models\Operations as Operations;
use \Models\Role as Role;
use \Models\Aclaccess as Aclaccess;

class RoleController extends ControllerBase
{
    public function initialize(){
        parent::initialize();
        $this->view->setVar('activeinfo','role');
    }
    /**
     * 角色列表
     */
    public function indexAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "Models\Role", $_POST);
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

        if (isset($parameters["conditions"])) {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Role')
                ->where($parameters['conditions'], $parameters['bind'])
                ->orderBy('roleid');
        } else {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Role')
                ->orderBy('roleid');
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
            $role = Role::findFirstByroleid($id);
            if (!$role) {
                $this->flash->error("role was not found");
                return $this->dispatcher->forward(array(
                    "controller" => "role",
                    "action" => "index"
                ));
            }

            $this->view->roleid = $role->roleid;
            $this->tag->setDefault("roleid", $role->roleid);
            $this->tag->setDefault("rolename", $role->rolename);
            $this->tag->setDefault("roleinfo", $role->roleinfo);
            //$this->view->setVar("roleid", $roleid);
        }
    }

    /**
     * 添加角色
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "index"
            ));
        }

        $role = new Role();

        $role->rolename = $this->request->getPost("rolename");
        $role->roleinfo = $this->request->getPost("roleinfo");

        $rolename = Role::findFirst(array(
            "rolename = :rolename:",
            "bind" => array('rolename' => $role->rolename)
        ));

        if ($rolename != false) {
            $this->flash->error('该名称已存在');
            return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "new"
            ));
        }

        if (!$role->save()) {
            foreach ($role->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "new"
            ));
        }

        $this::logAction('添加角色', "添加了角色:$role->rolename");
        $this->flash->success("设置成功");
        /*return $this->dispatcher->forward(array(
            "controller" => "role",
            "action" => "index"
        ));*/
        $this->response->redirect("role/index");
    }

    /**
     * Saves a role edited
     *
     */
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "index"
            ));
        }

        $roleid = $this->request->getPost("roleid");

        $role = Role::findFirstByroleid($roleid);
        if (!$role) {
            $this->flash->error("该角色不存在");

            /*return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "index"
            ));*/
            $this->response->redirect("role/index");
        }
        $role->rolename = $this->request->getPost("rolename");
        $role->roleinfo = $this->request->getPost("roleinfo");
        $role->roleid = $this->request->getPost("roleid");
        /*$role->logintime = $this->request->getPost("logintime");
        $role->regtime = $this->request->getPost("regtime");*/


        if (!$role->save()) {
            foreach ($role->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "edit",
                "params" => array($role->roleid)
            ));
        }
        $this::logAction('修改角色', "修改了角色:$role->rolename");

        $this->flash->success("修改成功");

        /*return $this->dispatcher->forward(array(
            "controller" => "role",
            "action" => "index"
        ));*/
        $this->response->redirect("role/index");
    }

    /**
     * Deletes a role
     *
     * @param string $id
     */
    public function deleteAction($id)
    {

        $role = Role::findFirstByroleid($id);
        if (!$role) {
            $this->flash->error("role was not found");

            return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "index"
            ));
        }
        $access=Aclaccess::findByroleid($role->roleid);

        if (!$role->delete()) {
            foreach ($role->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "index"
            ));
        }
        $access->delete();
        $this::logAction('删除角色', "删除了角色:$role->rolename");

        $this->flash->success("删除成功");

        /*return $this->dispatcher->forward(array(
            "controller" => "role",
            "action" => "index"
        ));*/
        $this->response->redirect("role/index");
    }

    /*
	* 权限设置页*/
    public function rolesetAction($id)
    {
        $phql = "SELECT c.*,b.* FROM \Models\Aclaccess c  left join  \Models\Resources b WHERE c.roleid = $id and b.resourceid=c.resourceid ";
        $access = $this->modelsManager->executeQuery($phql);
        $resources = Resources::find();
        $result = [];
        $results = [];
        foreach ($access as $access) {
            //$operations = Operations::findByresourceid($resource->resourceid);
            $result[$access->b->resourceid][] = [
                'resourceid' => $access->b->resourceid,
                'name' => $access->b->name,
                'resourceinfo' => $access->b->resourceinfo,
            ];
        }

        foreach ($resources as $resource) {
            //$operations = Operations::findByresourceid($resource->resourceid);
            $results[$resource->resourceid][] = [
                'resourceid' => $resource->resourceid,
                'name' => $resource->name,
                'resourceinfo' => $resource->resourceinfo,
            ];
        }
        foreach ($results as $key => $val) {
            if (isset($result[$key]) && $result[$key] ===$results[$key]) {
                $results[$key]['selected']='1';
            }
        }
        $this->view->setVar("allresource", $results);

        $this->view->roleid = $id;
        $this->tag->setDefault("roleid", $id);
    }

    /**
     * Saves a role edited
     *
     */
    public function saveaccessAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "index"
            ));
        }

        $roleid = $this->request->getPost("roleid");
        $role = Role::findFirstByroleid($roleid);
        if (!$role) {
            $this->flash->error("该角色不存在");
            /*return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "index"
            ));*/
            $this->response->redirect("role/index");
        }

        $access = Aclaccess::findByroleid($roleid);
        if (!$access->delete()) {
            foreach ($access->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "index"
            ));
        }

        $acl = $this->request->getPost("access");
        foreach ($acl as $v) {
            $aclaccess = new Aclaccess();
            $aclaccess->roleid = $this->request->getPost("roleid");
            $aclaccess->resourceid = $v;
            $aclaccess->save();

            if (!$aclaccess->save()) {
                foreach ($aclaccess->getMessages() as $message) {
                    $this->flash->error($message);
                }

                return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "new"
                ));
            }
        }

        $this::logAction('修改权限', "修改了权限");
        $this->flash->success("修改成功");
        $this->response->redirect("role/index");
    }
}
