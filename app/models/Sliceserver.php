<?php
namespace Models;

use \Phalcon\Mvc\Model;
use \Phalcon\Validation;
use \Phalcon\Validation\Validator\Uniqueness;
use \Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex;


class Sliceserver extends Model {


	//添加时数据验证
	public function validation(){
		$validation = new Validation;
		$validation->add(
			'ssname',
			new PresenceOf([
				'message'=>'服务器名称不能为空'
				])
			);
		$validation->add(
			'serverip',
			new PresenceOf([
				'message'=>'服务器ip地址不能为空',
				])
			);
		$validation->add(
			'ssname',
			new Uniqueness([
				'message'=>'服务器名称已经存在',
				])
			);
		$validation->add(
			'serverip',
			new Uniqueness([
				'message'=>'服务器ip地址已经存在',
				])
			);
		$validation->add(
			'serverip',
			new Regex([
				'pattern'=>'/((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d))))/',
				'message'=>'服务器ip地址无效'
				])
			);

		return $this->validate($validation);
	}
	


}
