<?php
//chirw
namespace Controllers;
use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
	public $auth;
	public function initialize(){
		if($this->session->has('auth')){
			$this->auth = $this->session->get('auth');
			$this->view->setVar("username", $this->auth['name']); 
			$this->view->setVar("uid", $this->auth['id']);
		}else{
			$this->flash->error('please login');

			$this->response->redirect("http://localhost/phalcon-framework/public/index.php?_url=/login");
		}
	}
}
