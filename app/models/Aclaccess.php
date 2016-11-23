<?php
//webspp
namespace Models;

class Aclaccess extends \Phalcon\Mvc\Model
{
    /**
     *
     * @var integer
     */
    public $id;
    /**
     *
     * @var integer
     */
    public $resourceid;
    /**
     *
     * @var integer
     */
    public $roleid;




//    public function initialize()
//    {
//        $this->hasMany(
//            "resourceid",
//            "Resources",
//            "resourceid"
//        );
//
//        $this->hasMany(
//            "roleid",
//            "Role",
//            "roleid"
//        );
//    }


  
}
