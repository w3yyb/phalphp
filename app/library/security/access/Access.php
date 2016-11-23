<?php
//webapp
//acl 访问权限控制
namespace Security\Access;

use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\User\Component;
use Phalcon\Acl;
use \Models\Role;
use \Models\Operations;
use \Models\Resources;
use \Models\Aclaccess as Aclaccess;

class Access extends Component
{
    public $acl;
    public $roles;
    public $privateResources;

    public function __construct()
    {
        //$allowList=[];
        $this->acl = new \Phalcon\Acl\Adapter\Memory();
        $this->acl->setDefaultAction(\Phalcon\Acl::DENY);
        $roles = Role::find();
        foreach ($roles as $roles) {
         //   $allowList[$roles->rolename]=[];
            $this->roles[] = $roles->rolename;//角色
            //var_dump($roles->rolename);
            $this->acl->addRole(new \Phalcon\Acl\Role($roles->rolename));
            //$resources = Resources::findByroleid($roles->roleid); //goodyuan
            //$resources = Aclaccess::findByroleid($roles->roleid);
            $phql = "SELECT c.*,b.* FROM \Models\Aclaccess c  left join  \Models\Resources b WHERE c.roleid = $roles->roleid and b.resourceid=c.resourceid ";
            $resources = $this->modelsManager->executeQuery($phql);
              //var_dump($resources);
            foreach ($resources as $resources) {
             //   $allowList[$roles->rolename][$resources->name]=[];
               //  var_dump($resources->b->name);
                $oprations = Operations::findByresourceid($resources->c->resourceid);
                //var_dump($resources->resourceid);
                $optarr=$oprations->toArray();
                    $oprationsarr=[];
                foreach ($optarr as $optarr) {
                    if ($optarr['name']) {
                         $oprationsarr[] = $optarr['name'];
                        //var_dump($optarr['name']);
                    }
                }
                 $this->privateResources[$resources->b->name] = $oprationsarr;
                 $this->acl->addResource(new \Phalcon\Acl\Resource($resources->b->name), $oprationsarr);
                foreach ($oprations as $oprations) {
                 //   $allowList[$roles->rolename][$resources->name][] =$oprations->name;
                    // var_dump($roles->rolename);
                     $this->acl->allow($roles->rolename, $resources->b->name, $oprations->name);
                }
            }
        }
    }

    public function check($role = null)
    {
        if ($role!=null) {
            $filter = new \Phalcon\Filter();
            $role = $filter->sanitize($role, "string");
            $ControllerName = \Phalcon\DI::getDefault()->get('dispatcher')->getControllerName();
            $ActionName = \Phalcon\DI::getDefault()->get('dispatcher')->getActionName();
            if ($this->acl->isAllowed($role, $ControllerName, $ActionName)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
