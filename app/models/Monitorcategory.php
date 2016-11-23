<?php
/**
 * 监控分类Model.
 */
namespace Models;
use \Phalcon\Mvc\Model;
use \Phalcon\Validation;
use \Phalcon\Validation\Validator\Uniqueness;
use \Phalcon\Validation\Validator\PresenceOf;
use \Phalcon\Validation\Validator\StringLength;

class Monitorcategory extends \Phalcon\Mvc\Model
{
    public function validation()
    {
        $validation = new Validation;
        $validation->add(
            'categoryname',
            new PresenceOf([
                'message' => '分类名称不能为空'
            ])
        );
        $validation->add('categoryname', new StringLength(array(
            'max' => 50,
            'min' => 2,
            'messageMaximum' => '分类名称最多为50个汉字',
            'messageMinimum' => '分类名称最少为2个汉字'
        )));
        $validation->add('categoryname', new Uniqueness(array(
            'model' => 'Monitorcategory',
            'message' => '分类名称已经存在'
        )));
        return $this->validate($validation);
    }
}