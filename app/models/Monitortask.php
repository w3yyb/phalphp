<?php
namespace Models;

use \Phalcon\Mvc\Model;/*
use \Phalcon\Validation;
use \Phalcon\Validation\Validator\Uniqueness;
use \Phalcon\Validation\Validator\PresenceOf;*/
use \Models\monitor as monitor;

class Monitortask extends Model {
	
	public $taskid;
	public $action;
	public $mid;
	public $createtime;
	public $status;
	public $code;
	public $updatetime;
	public $taskstarttime;

}
