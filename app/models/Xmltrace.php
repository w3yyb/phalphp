<?php
/**
 * Created by PhpStorm.
 * User: new
 * Date: 2016/10/17
 * Time: 14:05
 */

namespace Models;

use \Phalcon\Mvc\Model;

class Xmltrace extends Model
{
    public $id;
    public $content;
    public $ctime;
    public $type;

    public function initialize()
    {
        $this->setConnectionService('logdb');
    }
}