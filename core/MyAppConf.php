<?php

if (!defined("CC_ROOT")) {
    define('CC_ROOT', realpath(dirname(__FILE__)));
}
include_once(CC_ROOT . "/_util.inc.php");
include_once(CC_ROOT . "/_cc.inc.php");
include_once(CC_ROOT . "/MyStruct.php");


class MyAppConf extends MyStruct
{


    /**
     * 开发框架
     * @var string
     */
    public $mvc = "";

    /**
     * 前端UI
     * @var string
     */
    public $ui = "";

    /**
     *是否启用restful
     * 0/1
     * @var string
     */
    public $has_restful = 0;

    /**
     * 是否需要测试用例
     * 0/1
     * @var string
     */
    public $has_test = 0;

    /**
     * 是否需要启用文档
     * 0/1
     * @var string
     */
    public $has_doc = 0;


    function getAsArray()
    {
        $a_data = $this->getBasicAsArray();
        $a_data["mvc"] = $this->mvc;
        $a_data["ui"] = $this->ui;
        $a_data["has_doc"] = $this->has_doc;
        $a_data["has_restful"] = $this->has_restful;
        $a_data["has_test"] = $this->has_test;
        // TODO: Implement getAsArray() method.
    }

    function parseToObj($a_data)
    {
        // TODO: Implement parseToObj() method.
    }

    function init($v1)
    {
        $now = time();
        $now_str = date("Y-m-d H:i:s", $now);
        $now_str2 = date("YmdHi", $now);
        $_uid = uuid();
        $this->name = $now_str2;
        $this->title = $now_str2;
        $this->uuid = $_uid;
        $this->ctime = $now_str;
        $this->utime = $now_str;
    }
}