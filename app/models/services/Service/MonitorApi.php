<?php
namespace Models\Services\Service;

use Models\Repositories\Repositories;
use Common\Array2XML;
use Models\Xmltrace;
use Models\Monitor;

class MonitorApi
{

    /**
     * 发起任务
     *
     * @param string $mid
     * @param int $action 1开始，0停止
     * @return array
     */
    public function creatTask($mid,$action,$taskid=null)
    {
        //判断监控频道ID是否存在
        $monitor = Monitor::findFirstBymid($mid);
        if(!$monitor){
            return false;
        }
        //创建任务
        switch($action){
            case 1:
                $taskname = 'perpareTaskStrat';
                $responsename = 'startResponse';
                break;
            case 0:
                $taskname = 'perpareTaskStop';
                $responsename = 'stopResponse';
                break;
            default:
                return false;
        }
        $starttask = Repositories::getRepository('MonitorApi')->$taskname($mid,$taskid);
        Repositories::getRepository('MonitorApi')->createTask($starttask);

        $xml = $this->ArraytoXML($starttask,'request');
        $this -> saveXMLtoDB($xml);
        //发起任务
        $sliceurl = Repositories::getRepository('MonitorApi')->getSliceUrl($mid);
        $curlxml = $this->curlPost($sliceurl,$xml);
        $this -> saveXMLtoDB($curlxml,'slice');
        $slice = $this->XMLtoArray($curlxml,'');

        //更新任务
        $res = Repositories::getRepository('MonitorApi')->$responsename($slice,$mid,$starttask['taskid']);



        return array('status'=>$res);

    }

    public function reponseTask($data)
    {
        $this -> saveXMLtoDB($data,'slice');
        $slice = $this->XMLtoArray($data,'');

        $res = Repositories::getRepository('MonitorApi')->reponseTaskUpdate($slice);
        if($res)
        {
            $retask['resultcode'] = 0;
            $retask['resultmsg'] = 'ok';
        }else{
            $retask['resultcode'] = -1;
            $retask['resultmsg'] = '任务更新失败';
        }

        $xml = $this->ArraytoXML($retask,'reponse');
        $this -> saveXMLtoDB($xml);

        return $xml;
    }
    
    private function XMLtoArray($xmlstr, $elementName)
    {
        $res = @simplexml_load_string($xmlstr,NULL,LIBXML_NOCDATA);
        $res = json_decode(json_encode((array)$res),true);
        if($elementName != null && $elementName != '')
            $res = $res[$elementName];
        return $res;
    }

    private function ArraytoXML($data, $rootNodeName)
    {
        $array2xml =new Array2XML();
        $xml =$array2xml::createXML($rootNodeName,$data);
        return $xml->saveXML();
    }

    private function saveXMLtoDB($xml,$type='monitor')
    {
        $xmltrace = new Xmltrace();
        $data['content'] = $xml;
        $data['ctime'] = date('Y-m-d H:i:s',time());
        $data['type'] = $type;
        $xmltrace->create($data);
    }

    private function curlPost($url, $data = '', $method = 'Post',$headers=array('Accept-Charset: utf-8','Content-type: text/xml'))
    {
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
}
