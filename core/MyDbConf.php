<?php

/**
 * 数据库配置结构
 * Class MyDbConf
 *
 */
class MyDbConf
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
     * 数据库（文件）
     * database / service name / sid /sqlite 数据库路径
     *
     * @var string
     */
    public $database = "";//

    /**
     * 数据库版本
     * @var string
     */
    public $version = "";

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
     * MyDbConf constructor.
     * @param string $driver
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $password
     * @param string $charset
     * @param string $database
     * @param string $version
     * @param string $source
     */
    public function __construct($driver, $host, $port, $user, $password, $database, $charset = "utf8", $version = "", $source = "ini")
    {
        $this->driver = $driver;
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->charset = $charset;
        $this->version = $version;
        $this->source = $source;
    }


    /**
     * 输出字段结构为数组
     * @return array
     */
    public function getAsArray()
    {
        return array(

            "driver" => $this->driver,
            "host" => $this->host,
            "port" => $this->port,
            "user" => $this->user,
            "password" => $this->password,
            "database" => $this->database,
            "charset " => $this->charset,
            "version" => $this->version,
            "source" => $this->source
        );
    }


}