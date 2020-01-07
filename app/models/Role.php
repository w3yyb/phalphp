<?php
//webspp
namespace Models;

class Role extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $roleid;
    /**
     *
     * @var string
     */
    public $rolename;

    /**
     *
     * @var string
     */
    public $roleinfo;

//    public function initialize()
//    {
//        $this->belongsTo(
//            "roleid",
//            "Aclaccess",
//            "roleid"
//        );
//    }


  
}
