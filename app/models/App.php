<?php
//webspp
namespace Models;

class App extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $app_id;
    /**
     *
     * @var string
     */
    public $username;

    /**
     *
     * @var string
     */
    public $private_key;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     *
     * @var string
     */
    public $adminname;
     /**
     *
     * @var string
     */
    public $addtime;
     /**
     *
     * @var string
     */
    public $info;
  /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
    public function columnMap()
    {
        return array(
            'app_id' => 'app_id',
            'username' => 'username',
            'private_key' => 'private_key',
            'status' => 'status',
            'adminname' => 'adminname',
            'addtime' => 'addtime',
            'info' => 'info'
        );
    }
}
