<?php
if (!defined("CC_ROOT")) {
    define('CC_ROOT', realpath(dirname(__FILE__)));
}
include_once(CC_ROOT . "/_util.inc.php");
include_once(CC_ROOT . "/_cc.inc.php");
include_once(CC_ROOT . "/MyBase.php");

/**
 * 项目元素
 */
abstract class MyStruct implements MyBase
{

    public $uuid;//隐形唯一
    public $name;//显性唯一
    public $title;//标题别称
    public $memo;//备注
    public $position = 255;//备注

    public $ctime;//创建
    public $utime;//最后更新时间

    public $basic_keys = array();

    /**
     * 冗余的项目名字，不可以该名字
     * @var string
     */
    public $project_id = null;//== project  的name
    public $app_id = null;//== app 的UUID

    /**
     * MyFunList constructor.
     */
    public function __construct()
    {

    }


    function getBasicAsArray()
    {
        $a_data = array();
        $a_data['name'] = $this->name;
        $a_data['title'] = $this->title;
        $a_data['uuid'] = $this->uuid;
        $a_data['ctime'] = $this->ctime;
        $a_data['utime'] = $this->utime;
        $a_data['memo'] = $this->memo;
        $a_data['position'] = $this->position;
        foreach ($this->basic_keys as $key) {
            $a_data[$key] = $this->$key;
        }
        return $a_data;
        
    }

    function parseToBasicObj($a_data)
    {

        $this->name = $a_data['name'];
        $this->title = $a_data['title'];
        $this->uuid = $a_data['uuid'];
        $this->ctime = $a_data['ctime'];
        $this->utime = $a_data['utime'];
        $this->memo = $a_data['memo'];
        $this->position = $a_data['position'];

        foreach ($this->basic_keys as $key) {
            $this->$key = $a_data[$key];
        }

        return $this;
    }


}