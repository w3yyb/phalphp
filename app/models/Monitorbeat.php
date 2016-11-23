<?php
namespace Models;

use \Phalcon\Mvc\Model;
use \Phalcon\Validation;
use \Phalcon\Validation\Validator\Uniqueness;
use \Phalcon\Validation\Validator\PresenceOf;

class Monitorbeat extends Model{
    public $mid;
    public $ctime;
    public $status;
    //添加时数据验证
    public function validation(){
        $validation = new Validation;
        $validation->add(
            'mid',
            new PresenceOf([
                'message'=>'摄像头不能为空'
            ])
        );

        return $this->validate($validation);
    }

}