<?php

/**
 *
 * webapp
 * @package Controllers
 */

namespace Controllers;

use Phalcon\Mvc\Controller;

class IndexController extends ControllerBase
{
    public function initialize(){
        parent::initialize();
        $this->view->setVar('activeinfo','index');
    }
    public function indexAction()
    {
        $this->view->setVar('name', "PHP Web Application using Phalcon MVC");
    }
}
