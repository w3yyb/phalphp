<?php
namespace Models;

use \Phalcon\Mvc\Model;
use \Phalcon\Validation;
use \Phalcon\Validation\Validator\Uniqueness;
use \Phalcon\Validation\Validator\PresenceOf;

class Monitor extends Model{
	public $mid;
	public $mname;
	public $msid;
	public $mcid;
	public $mac;
	public $cid;
	public $soucetype;
	public $sourcepath;
	public $cameraip;
	public $createtime;
	public $creator;
	//添加时数据验证
	public function validation(){
		$validation = new Validation;
		$validation->add(
			'mname',
			new PresenceOf([
				'message'=>'监控点名称不能为空'
				])
			);		
		$validation->add(
			'mname',
			new Uniqueness([
				'message'=>'监控点名称已经存在',
				])
			);
		$validation->add(
			'sourcepath',
			new PresenceOf([
				'message'=>'源地址不能为空'
			])
		);
		return $this->validate($validation);
	}

}