<?php
/**
 * 社区信息model.
 */
namespace Models;

class Community extends \Phalcon\Mvc\Model
{
    public function initialize()
    {
        $this->setConnectionService('db2');
    }
    public function getSource() {
        return "smt_community";
    }
}