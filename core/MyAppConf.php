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

    public $basic_keys = array(
        "mvc",
        "ui",
        "has_restful",
        "has_test",
        "has_doc"
    );

    public function __construct()
    {
        $this->scope = "APP_CONF";
    }

    function getAsArray()
    {
        $a_data = $this->getBasicAsArray();
        return $a_data;
    }

    function parseToObj($a_data)
    {
        $this->parseToBasicObj($a_data);
        return $this;
    }

    function init($v1)
    {
        //可能外部生成
//        $now = time();
//        $now_str = date("Y-m-d H:i:s", $now);
//        $now_str2 = date("YmdHi", $now);
//        $_uid = uuid();
//        $this->name = $now_str2;
//        $this->title = $now_str2;
//        $this->uuid = $_uid;
//        $this->ctime = $now_str;
//        $this->utime = $now_str;
    }
}