<?php

/**
 *
 * webapp
 * @package Controllers
 */

namespace Controllers;
use Phalcon\Mvc\Controller;
class IndexController extends Controller {

	public function indexAction() {
       		$this->view->setVar('name', "PHP Web Application using Phalcon MVC");
	}
}
