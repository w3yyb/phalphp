<?php
/**
 * Created by PhpStorm.
 * User: new
 * Date: 2016/10/20
 * Time: 9:59
 */

namespace Models;

use \Phalcon\Mvc\Model;

class Midlog extends Model
{
    public $uid;
    public $mid;
    public $ctime;

    public function initialize()
    {
        $this->setConnectionService('logdb');
    }

}