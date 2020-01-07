<?php
//webspp
namespace Models;

class Resources extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $resourceid;
    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $resourceinfo;

//    public function initialize()
//    {
//        $this->belongsTo(
//            "resourceid",
//            "Aclaccess",
//            "resourceid"
//        );
//
//    }

  
}
