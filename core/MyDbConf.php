<?php
if (!defined("CC_ROOT")) {
    define('CC_ROOT', realpath(dirname(__FILE__)));
}
include_once(CC_ROOT . "/_util.inc.php");
include_once(CC_ROOT . "/_cc.inc.php");
include_once(CC_ROOT . "/MyStruct.php");

/**
 * 数据库配置结构
 * Class MyDbConf
 *
 */
class MyDbConf extends MyStruct
{

    /**
     * 驱动
     * @var string
     */
    public $driver = "";

    /**
     * 主机
     * @var string
     */
    public $host = "";

    /**
     * 数据库（文件）
     * database / service name / sid /sqlite 数据库路径
     *
     * @var string
     */
    public $database = "";//

    /**
     * 主机端口
     * @var int
     */
    public $port = 0;


    /**
     * 用户名
     * @var string
     */
    public $user = "";

    /**
     * 密码
     * @var string
     */
    public $password = "";


    /**
     * 编码格式
     * @var string
     */
    public $charset = "utf8";


    /**
     * 配置来源, 来自环境，或者配置文件
     * env / ini
     * @var string
     */
    public $source = "env";

    /**
     * 直接的链接字符串，可以带host-port-db
     * @var string
     */
    public $uri = "";


    public $basic_keys = array(
        "driver",
        "host",
        "port",
        "database",
        "user",
        "password",
        "charset",
        "uri",
        "source"
    );

    public function __construct()
    {
        $this->scope = "DB_CONF";
    }

    /**
     * 输出字段结构为数组
     * @return array
     */
    public function getAsArray()
    {
        return $this->getBasicAsArray();
    }


    function init($v1)
    {
        // TODO: Implement init() method.
    }

    function parseToObj($a_data)
    {
        $this->parseToBasicObj($a_data);
        return $this;
    }
}