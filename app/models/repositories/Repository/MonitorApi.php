<?php

namespace Models\Repositories\Repository;

use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\User\Component;
use Models\Monitortask;
use \Models\Setting;

class MonitorApi extends Component
{

    private $configs;

    public function __construct()
    {
        $settings = Setting::find();
        foreach($settings as $setting){
            $this->configs[$setting->name] = $setting->value;
        }
    }

    /**
     * 获取任务开始接口数据
     *
     * @param string $mid
     * @return array
     */
    public function perpareTaskStrat($mid, $taskid=null)
    {

        $sql = "SELECT mid,sourcepath,isvoice,cliptime,slicereportpath,targetpath,storepath,cachetime,httpprefix,speedfactor,slicenumber
FROM \Models\Monitor AS m INNER JOIN \Models\Sliceserver AS s ON m.ssid=s.ssid INNER JOIN \Models\Monitorsolution AS ms ON s.msid=ms.msid WHERE mid = '$mid' LIMIT 1";
        $monitor = $this->modelsManager->executeQuery($sql);
        $monitortask = array();
        $monitortask['type'] = 'task';
        $monitortask['action'] = 'start';
        $monitortask['taskid'] = $taskid?$taskid:$this->guid();
        $monitortask['guid'] = $monitor[0]->mid;
        $monitortask['isvoice'] = $monitor[0]->isvoice;
        $monitortask['encryptionkey'] = $this->configs['encryptionkey'];
        $monitortask['encryptioniv'] = $this->configs['encryptioniv'];
        $monitortask['source'] = $monitor[0]->sourcepath;
        $monitortask['cliptime'] = $monitor[0]->cliptime;
//        $monitortask['replyaddr'] = 'http://192.168.6.134:8088/receiveTask';
        $monitortask['replyaddr'] = $this->configs['replyaddr'];
        $monitortask['notifyaddr'] = $monitor[0]->slicereportpath;
        $monitortask['target'] = $monitor[0]->targetpath.'/'.$monitor[0]->mid;
        $monitortask['storepath'] = $monitor[0]->storepath;
        $monitortask['cachetime'] = $monitor[0]->cachetime;
        $monitortask['httpprefix'] = $monitor[0]->httpprefix.$monitor[0]->mid;
        $monitortask['speedfactor'] = $monitor[0]->speedfactor;
        $monitortask['slicenumber'] = $monitor[0]->slicenumber;
        return $monitortask;
    }

    /**
     * 获取任务停止接口数据
     *
     * @param string $mid
     * @return array
     */
    public function perpareTaskStop($mid,$taskid)
    {
        $monitortask = array();
        $monitortask['type'] = 'task';
        $monitortask['action'] = 'stop';
        $monitortask['taskid'] = $taskid;
        $monitortask['guid'] = $mid;
        return $monitortask;
    }

    /**
     * 创建任务记录
     *
     * @param array $data
     * @return boolean
     */
    public function createTask($data)
    {
        $monitortask = new Monitortask();
        switch($data['action'])
        {
            case 'start':
                $data['action'] = 1;
                break;
            case 'stop':
                $data['action'] = 0;
                break;
        }
        $data['mid'] = $data['guid'];
        $data['createtime'] = date('Y-m-d H:i:s');
        return $monitortask->save($data);
    }

    /**
     * 及时响应更新任务记录
     *
     * @param array $data
     * @return boolean
     */
    public function startResponse($data,$mid,$taskid)
    {
        if($data['returncode'] != '0'){
            $monitortask = Monitortask::findFirst(
                "taskid = '".$taskid."'"
            );
            if($data['returncode'] == 102){
                $monitortask->action = 1;
                $monitortask->status = 2;
                $monitortask->code = $data['returncode'];
                $resmt = $monitortask->update();

                $sql = "UPDATE \Models\Monitor SET mstatus = :mstatus: WHERE mid = :mid:";
                $bind = [
                    "mstatus" => 1,
                    "mid" => $mid,
                ];
                $resm = $this->modelsManager->executeQuery($sql,$bind);

                return $resm&&$resmt;
            }else{
                $monitortask->action = 1;
                $monitortask->status = -1;
                $monitortask->code = $data['returncode'];
                return $monitortask->update();
            }
        }else{
            return true;
        }
    }

    public function stopResponse($data,$mid,$taskid)
    {
        $monitortask = Monitortask::findFirst(
            "taskid = '".$taskid."'"
        );
        if( $data['returncode'] == '0' )
        {
            $monitortask->action = 0;
            $monitortask->status = 0;
            $monitortask->updatetime = date('Y-m-d H:i:s');
            $resmt = $monitortask->update();
            $sql = "UPDATE \Models\Monitor SET mstatus = :mstatus: WHERE mid = :mid:";
            $bind = [
                "mstatus" => 0,
                "mid" => $mid,
            ];
            $resm = $this->modelsManager->executeQuery($sql,$bind);
            return $resm&&$resmt;

        }else{
            return false;
        }
    }
    
    /**
     * 切片服务器主动汇报更新任务记录
     *
     * @param array $data
     * @return boolean
     */
    public function reponseTaskUpdate($data)
    {
        $monitortask = Monitortask::findFirst(
                "taskid = '".$data['taskid']."'"
        );

        switch($data['action'])
        {
            case 'start':
                if($data['resultcode'] == '0')
                {
                    $monitortask->action = 1;
                    $monitortask->status = 2;
                    $time=explode('-',$data['excutetime']);
                    $time=strtotime($time[0].' '.$time[1]);
                    $monitortask->taskstarttime =  date('Y-m-d H:i:s',$time);
                    $mstatus = 1;

                }else{
                    $monitortask->action = 1;
                    $monitortask->status = -1;
                    $monitortask->code = $data['resultcode'];
                    $mstatus = 0;
                }
                $monitortask->updatetime = date('Y-m-d H:i:s');
                $resmt = $monitortask->update();

                $sql = "UPDATE \Models\Monitor SET mstatus = :mstatus: WHERE mid = :mid:";
                $bind = [
                    "mstatus" => $mstatus,
                    "mid" => $data['guid'],
                ];
                $resm = $this->modelsManager->executeQuery($sql,$bind);
                return $resm&&$resmt;
                break;
            case 'run':
                $monitortask->action = 1;
                $monitortask->code = $data['errorcode'];
                $monitortask->updatetime = date('Y-m-d H:i:s');
                $resmt = $monitortask->update();
                return $resmt;
                break;
        }

    }

    public function midHasStart($mid)
    {
        return Monitortask::findFirst([
            "mid = :mid: AND status = :status:",
            "bind" => [
                "status" => 2,
                "mid" => $mid,
            ],
        ]);
    }

    public function midHasWait($mid)
    {
        return Monitortask::findFirst([
            "mid = :mid: AND status = :status:",
            "bind" => [
                "status" => 1,
                "mid" => $mid,
            ],
        ]);
    }

    public function getSliceUrl($mid)
    {
        $sql = "SELECT apiurl FROM \Models\Monitor AS m INNER JOIN \Models\Sliceserver AS ms ON m.ssid=ms.ssid WHERE mid = '$mid' LIMIT 1";
        $monitor = $this->modelsManager->executeQuery($sql);
        return $monitor[0]->apiurl;
    }

    private  function guid(){
        if (function_exists('com_create_guid')){
            return trim(trim(com_create_guid(),'{'),'}');
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = //chr(123) "{"
                substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
                //.chr(125); "}"
            return $uuid;
        }
    }
}
