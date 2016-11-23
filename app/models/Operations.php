<?php
//webspp
namespace Models;

class Operations extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $operationid;
    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var integer
     */
    public $resourceid;

    /**
     *
     * @var string
     */
    public $operationinfo;
}
