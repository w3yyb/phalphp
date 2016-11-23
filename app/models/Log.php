<?php
//webspp
namespace Models;

class Log extends \Phalcon\Mvc\Model
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
    public $ip;

    /**
     *
     * @var string
     */
    public $addtime;

    /**
     *
     * @var string
     */
    public $title;
     /**
     *
     * @var string
     */
    public $content;
     /**
     *
     * @var string
     */
    public $url;
  /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
  
}
