<?php
//webspp
namespace Models;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class Monitorsolution extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer 自增id
     */
    public $msid;
    /**
     *
     * @var string 方案名称
     */
    public $name;

    /**
     *
     * @var integer 文件传输类型：1为file   即内存保存,2为remote远程传输
     */
    public $transtype;

    /**
     *
     * @var string 远程传输路径target：remote    【当选remote 时写远程传输服务器接受地址，当先file时，写本地地址】
     */
    public $targetpath;

    /**
     *
     * @var string 本地存储路径storepath：【当选remote 时使用,文件转存实际地址】
     */
    public $storepath;
    /**
     *
     * @var string 切片信息汇报地址：notify
     */
    public $slicereportpath;
    /**
     *
     * @var string m3u8列表切片地址前缀：
     */
    public $httpprefix;

    /**
     *
     * @var integer 本地存储切片数量：0代表有保存，有回看，非0代表内存保存片数
     */
    public $slicenumber;
    /**
     *
     * @var integer 片长：单位秒
     */
    public $cliptime;
    /**
     *
     * @var integer 传输缓存时间：（秒）
     */
    public $cachetime;

    /**
     *
     * @var string 远程传输速度因子：至少1.0以上  1.0--10之间。
     */
    public $speedfactor;

    /**
     *
     * @var integer 是否有声(1有声 0无声)
     */
    public $isvoice;

    /**
     *
     * @var integer 切片服务器id：选择那个切片服务器⁑
     */
    public $ssid;

    /**
     *
     * @var string 备注
     */
    public $remark;
    /**
     *
     * @var datetime 创建时间
     */
    public $createtime;
    /**
     *
     * @var datetime 更新时间
     */
    public $updatetime;
    /**
     *
     * @var integer 方案状态 1为启用  0为禁用
     */
    public $status;

    public function initialize(){
       /* $this->hasMany(
            'msid',
            'Monitor',
            'msid'
            );*/
        $this->hasOne(
            "msid",
            "Monitor",
            "msid"
            );
    }

    public function validation()
    {
        $validator = new Validation();
        $validator->add(
            'name',
            new PresenceOf(
                [
                    'message' => '名称不能为空',
                ]
            )
        );

        return $this->validate($validator);
    }

}
