<?php
/**
 * 摄像头类型Model
 */
namespace Models;
use \Phalcon\Mvc\Model;
use \Phalcon\Validation;
use \Phalcon\Validation\Validator\Uniqueness;
use \Phalcon\Validation\Validator\PresenceOf;
use \Phalcon\Validation\Validator\StringLength;
use \Phalcon\Validation\Validator\Regex;

class Cameratype extends \Phalcon\Mvc\Model
{
    public function validation()
    {
        $validation = new Validation;
        $validation->add(
            'name',
            new PresenceOf([
                'message' => '类型名称不能为空'
            ])
        );
        $validation->add('name', new StringLength(array(
            'max' => 100,
            'min' => 2,
            'messageMaximum' => '类型名称最多为100个汉字',
            'messageMinimum' => '类型名称最少为2个汉字'
        )));
        $validation->add('name', new Uniqueness(array(
            'model' => 'Monitorcategory',
            'message' => '类型名称已经存在'
        )));
        $validation->add(
            'mainsource',
            new PresenceOf([
                'message' => '主源地址不能为空'
            ])
        );
        $validation->add('tel', new Regex(array(
            'pattern' => '/^1(3|4|5|7|8)\d{9}$/',
            'message' => '手机号格式不正确'
        )));

        return $this->validate($validation);
    }
}