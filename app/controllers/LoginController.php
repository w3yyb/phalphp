<?php
namespace Controllers;
use Phalcon\Mvc\Controller;
use \Models\Admin as Admin;
class LoginController extends Controller
{
	//reg login user
    private function _registerSession($user)
    {
        $this->session->set('auth', array(
            'id' => $user->id,
            'name' => $user->username
        ));
        //$this->view->setVar("username", $user->username); 
    }
    
	//login
	public function indexAction()
    {
        if ($this->request->isPost()) {

            $username = $this->request->getPost('username');
            $password = sha1($this->request->getPost('password'));

            $user = Admin::findFirst(array(
                "username = :username: AND password = :password:",
                "bind" => array('username' => $username, 'password' => $password)
            ));
            
            if ($user != false) {
                $this->_registerSession($user);

                $this->flash->success('Welcome ' . $user->username);

                //Forward to the 'invoices' controller if the user is valid
                /*
                return $this->dispatcher->forward(array(
                    'controller' => 'index',
                    'action' => 'index'
                ));*/
                $this->response->redirect("http://localhost/phalcon-framework/public/index.php?_url=/admin");
            }else{
            	$this->flash->error('username or password error');
            }
        }
    }
    
    //logout
    public function logoutAction(){
    	$this->session->destroy();
    	/*return $this->dispatcher->forward(array(
                    'controller' => 'login',
                    'action' => 'index'
                ));*/
    	$this->response->redirect("http://localhost/phalcon-framework/public/index.php?_url=/login");
    }
}
