<?php

/**
 * 主程序模型
 * Class MyApp
 */
class MyApp
{

    /**
     * 单例模式的实例
     * @var MyApp
     */
    private static $instance = null;

    /**
     * 构造器私有化:禁止从类外部实例化
     * MyApp constructor.
     */
    private function __construct()
    {
    }

    /**
     * 克隆方法私有化:禁止从外部克隆对象
     */
    private function __clone()
    {
    }

    /**
     * @return MyApp
     */
    public static function getInstance()
    {
        //检测当前类属性$instance是否已经保存了当前类的实例
        if (self::$instance == null) {
            //如果没有,则创建当前类的实例
            self::$instance = new self();
        }
        //如果已经有了当前类实例,就直接返回,不要重复创建类实例
        return self::$instance;
    }

    /**
     * 支持的开发语言
     */
    const   LANG_PHP = "php";
    const   LANG_JAVA = "java";
    const   LANG_CPP = "cpp";

    /**
     * 允许的mvc方案，目前先默认一下，后续再扩展
     * @var string[][]
     */
    public static $a_allow_mvc = array(
        self::LANG_PHP => array("phalcon"),
        self::LANG_JAVA => array("servlet"),
        self::LANG_CPP => array("qhttp")
    );

    /**
     * 开发语言
     * @var string
     */
    public $lang = "";

    /**
     * 开发框架
     * @var string
     */
    public $mvc = "";

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return string
     */
    public function getMvc()
    {
        return $this->mvc;
    }

    /**
     * @param string $mvc
     */
    public function setMvc($mvc)
    {
        $this->mvc = $mvc;
    }

    /**
     * @return MyDb
     */
    public function getDbConf()
    {
        return $this->db_conf;
    }

    /**
     * @param MyDb $db_conf
     */
    public function setDbConf($db_conf)
    {
        $this->db_conf = $db_conf;
    }

    /**
     * @return array
     */
    public function getModelList()
    {
        return $this->model_list;
    }

    /**
     * @param array $model_list
     */
    public function setModelList($model_list)
    {
        $this->model_list = $model_list;
    }

    /**
     * @return string
     */
    public function getPathOutput()
    {
        return $this->path_output;
    }

    /**
     * 设置输出目录
     * @param string $path_output
     */
    public function setPathOutput($path_output)
    {
        $this->path_output = $path_output;
    }


    /**
     * 数据库配置
     * @var MyDb
     */
    public $db_conf = null;

    /**
     * 包含的模型，key => MyModel
     * @var array
     */
    public $model_list = array();


    /**
     * 输出目录
     * @var string
     */
    public $path_output = "";

    /**
     * app数据ok
     * @var boolean
     */
    public $checked_app_data_is_good = false;


    /**
     * 解析模型
     * @param array $a_app_data
     * @return bool|void
     */
    public function parse($a_app_data = array())
    {

        /**
         * 判断语言
         */
        if (!isset($a_db_conf['lang'])) {
            echo "NO database defined!!!";
            return null;
        }


        //db conf ,默认就是mysql
        $a_db_conf = $a_app_data['mvc'];

        //db conf ,默认就是mysql
        $a_db_conf = $a_app_data['db_conf'];

        if (!isset($a_db_conf['database'])) {
            echo "NO database defined!!!";
            return null;
        }

        if (isset($a_db_conf['source']) && $a_db_conf['source'] == "env") {
            //从环境读取的数据

        } else {
            //从ini读取的数据

        }

        //数据库名或者
        $database = trim($a_db_conf['database']);


        $driver = isset($a_db_conf['driver']) ? trim($a_db_conf['driver']) : MyDb::MYSQL;
        $host = isset($a_db_conf['host']) ? trim($a_db_conf['host']) : "localhost";
        $port = isset($a_db_conf['port']) ? trim($a_db_conf['port']) : "3306";
        $user = isset($a_db_conf['user']) ? trim($a_db_conf['user']) : "root";
        $password = isset($a_db_conf['password']) ? trim($a_db_conf['password']) : "123456";
        //$database
        $charset = isset($a_db_conf['charset']) ? trim($a_db_conf['charset']) : "utf8";
        $version = isset($a_db_conf['version']) ? trim($a_db_conf['version']) : "";

        $version = isset($a_db_conf['version']) ? trim($a_db_conf['version']) : "";

        $source = "ini";

        $this->db_conf = new MyDb($driver, $host, $port, $user, $password, $database, $charset, $version, $source);

        //model_list
        $a_model_list = $a_app_data['model_list'];

        foreach ($a_model_list as $a_model) {
            $this->model_list[] = new MyModel($a_model);
        }


    }

    /**
     * 从json解析系统模型
     * @param string $json_path
     * @return bool|void
     */
    public function parseByJson($json_path)
    {
        $s_json_data = file_get_contents($json_path);
        $a_json_data = json_decode($s_json_data, true);

        if (null != $a_json_data) {
            return $this->parse($a_json_data);

        }
        return false;
    }


    /**
     * 导出到json
     * @param string $json_path
     */
    public function saveToJson($json_path = "")
    {


    }


    /**
     * 构建
     */
    public function buildAll()
    {


    }

    /**
     * 构建模版
     */
    public function buildTmpl()
    {


    }

    /**
     * 构建模型
     */
    public function buildModel()
    {


    }


    /**
     * 构建数据库
     */
    public function buildDb()
    {


    }


}