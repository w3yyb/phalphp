<?php
namespace Controllers;
//接收push客户端 注册 privateKey
use \Models\Api as Api;
class PushregController extends \Phalcon\Mvc\Controller
{
    public function indexAction($username,$privateKey)
    {
        $api = Api::findFirst("private_key = '".$privateKey."'")->private_key;
        if($api) {
            echo '{"Errcode":"500","Errmsg":"privateKey already exist"}';
        }else {
            $api       = new Api();
            $api->private_key = $privateKey;
            $api->username = $username;
            $api->status = 0;
            if ($api->create() == false) {
                echo '{"Errcode":"500","Errmsg":"error,can not reg"}';
            } else {
                echo '{"Errcode":"200","Errmsg":"reg success"}';
            } 
        }
    }

}
