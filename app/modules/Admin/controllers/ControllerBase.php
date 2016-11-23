<?php
//webapp
namespace Controllers;

use Phalcon\Mvc\Controller;
use \Models\Log;
use \Models\Admin;
use \Models\Role;
use \Security\Access;
use Phalcon\Config;

class ControllerBase extends Controller
{
    public $auth;
    public function initialize()
    {

        if ($this->session->has('auth')) {
            $this->auth = $this->session->get('auth');
            $this->view->setVar("username", $this->auth['name']);
            $this->view->setVar("uid", $this->auth['id']);
            $_SESSION['username']=$this->auth['name'];
            $user = Admin::findFirstByusername($this->auth['name']);
            $role = Role::findFirstByroleid($user->roleid);
            $rolename=$role->rolename;
           // var_dump($rolename);

            $ControllerName = \Phalcon\DI::getDefault()->get('dispatcher')->getControllerName();
            if ($ControllerName != 'index') {
                $access = new Access\Access();
                $accessstatus = $access->check($rolename);  //这里要多数据库中查出用户的角色
                if (!$accessstatus) {
                    // $this->flash->error('您没有权限执行此操作');
                    $response = new \Phalcon\Http\Response();
                    $response->setStatusCode(200, "OK");
                    $response->setContent("<script>alert('您没有权限执行此操作');window.history.back();</script>");
                    $response->send();
                    exit;
                }
            }

        } else {
            $this->flash->error('请登录');
            $this->response->redirect("/login");
        }
        $settingcon = new Config(require(dirname(__DIR__) . '/../../config/config.php'));
        $this->view->setVar('communityset', $settingcon->communityset);
    }

    /**
    记录操作日志
     *  @param string $title
     *  @param string $content
     */
    public static function logAction($title, $content)
    {
        $log = new Log();
        $log->username=$_SESSION['username'];
        $log->ip= $_SERVER["REMOTE_ADDR"];
        $log->addtime=date("Y-m-d H:i:s");
        $log->title=$title;
        $log->content=$content;
        $log->url='';
        $log->save();
    }
}
