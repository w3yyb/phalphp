<?php
//api
namespace Controllers;

use Models\Services\Services as Services;
use \Common\Array2XML as Array2XML;
use Models\Usertask;

class ExampleController extends \Phalcon\Mvc\Controller {


	public function posttestAction() {

        $xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-1"?>
<reponse>
<type>task</type>
<action>start</action>
<taskid>3FE1DE4C-0372-4417-B1CE-884D31136B3D</taskid>
<guid>C62ED611-FDAE-4C96-982A-43D2C19621A0</guid>
<returncode>0</returncode>
<returnmsg>ok</returnmsg>
</reponse>
XML;
//        $xml = $this->XMLtoArray($xml,'');

        return $xml;


	}
    private function XMLtoArray($xmlstr, $elementName)
    {
       /* $xml2array =new \Common\XML2Array();
		$res =$xml2array::createArray($xmlstr);*/
        $res = @simplexml_load_string($xmlstr,NULL,LIBXML_NOCDATA);
        $res = json_decode(json_encode((array)$res),true);
        if($elementName != null && $elementName != '')
            $res = $res[$elementName];
        return $res;
    }
    public function gettestAction()
    {
        $data = <<<XML
<?xml version="1.0" encoding="ISO-8859-1"?>
<content>
<type>result</type>
<action>stop</action>
<taskid>3FE1DE4C-0372-4417-B1CE-884D31136B3D</taskid>
<guid>C62ED611-FDAE-4C96-982A-43D2C19621A0</guid>
<excutetime>2011-04-25 11:00:05.123</excutetime>
<resultcode>0</resultcode>
<resultmsg>ok</resultmsg>
</content>
XML;
/*        $data = <<<XML
<?xml version="1.0" encoding="ISO-8859-1"?>
<reponse>
<type>task</type>
<action>start</action>
<taskid>9EF15C92-A9C5-4BDE-9E17-F210A02D607C</taskid>
<guid>3f7a9128-9f6e-4fcb-9003-f7f1290dc463</guid>
<returncode>0</returncode>
<returnmsg>ok</returnmsg>
</reponse>
XML;*/
        $url = 'http://smartmonitor.cn/apitest';
        $method = 'post';
        $headers = array('Accept-Charset: utf-8','Content-type: text/xml');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible;MSIE 5.01;Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);
        return $temp;
    }

    public function test()
    {

        $monitor = Services::getService('WebApi')->StartTask(16,11);

        var_dump($monitor);exit;
    }

    public function getAction($id) {


        $monitor = Services::getService('MonitorApi')->creatTask($id,1);

        var_dump($monitor);exit;

    }

    public function apitestAction()
    {
        $xml = file_get_contents('php://input');
        $monitor = Services::getService('MonitorApi')->reponseTask($xml);
//        var_dump($monitor);exit;
        return $monitor;
    }

}
