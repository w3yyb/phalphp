<?php
namespace Models;
class Admin extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $username;

    /**
     *
     * @var string
     */
    public $password;

   
  public function initialize() {
		$this->setSource("admin");
	}
   

    /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
    /*
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'username' => 'username', 
            'password' => 'password', 
            'logintime' => 'logintime', 
            'regtime' => 'regtime',
            'adminname' => 'adminname',
            'info' => 'info'
        );
    }*/
}
