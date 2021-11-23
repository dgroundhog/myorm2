<?php
if (!defined("CC_ROOT")) {
    define('CC_ROOT', realpath(dirname(__FILE__)));
}
include_once(CC_ROOT . "/_util.inc.php");
include_once(CC_ROOT . "/_cc.inc.php");
include_once(CC_ROOT . "/MyStruct.php");
include_once(CC_ROOT . "/MyAppConf.php");
include_once(CC_ROOT . "/MyDbConf.php");

/**
 * 主程序模型
 * Class MyApp
 */
class MyApp extends MyStruct
{
    /**
     * 名字就是版本号
     * 创建时就用uuid作为主建
     */

    const DEFAULT_NAME = "default";


    /**
     * 图片和图标ID
     * @var string
     */
    public $img_logo_id = "";
    public $img_icon_id = "";

    /**
     * app配置
     * @var MyAppConf
     */
    public $conf_list = null;
    /**
     * 数据库配置
     * @var MyDbConf
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
    public $basic_keys = array(
        "project_id",
        "img_icon_id",
        "img_logo_id"
    );

    public function __construct()
    {
        $this->scope = "APP";
    }

    /**
     *
     * 初始化默认
     * @param $project_id
     */
    public function init($project_id)
    {


        $now = time();
        $now_str = date("Y-m-d H:i:s", $now);
        $now_str2 = date("YmdHi", $now);
        $this->uuid = uuid();
        $this->project_id = $project_id;
        $this->name = self::DEFAULT_NAME;
        $this->title = "新版本{$now_str2}-";
        $this->ctime = $now_str;
        $this->utime = $now_str;

        $this->model_list = array();
        $this->db_conf = array();
        $this->conf_list = array();

        /**
         * 创建目录
         */
        $project_root = MyProject::getDataRoot($project_id);
        $app_root = $project_root . DS . $this->uuid;
        if (!is_dir($app_root)) {
            mkdir($app_root);
        }
        //是否需要生成其他文件

        return $this;
    }

    public function copy($new_version)
    {
        $a_app_info = $this->getAsArray();

        $utime = date("Y-m-d H:i:s", time());
        $project_root = MyProject::getDataRoot($this->project_id);
        $app_root = $project_root . DS . $this->uuid;
        if (!is_dir($app_root)) {
            SeasLog::debug("App文件夹不存在{$app_root}");
            return null;
        }

        $_uuid = uuid();
        $o_app2 = new MyApp();
        $o_app2->parseToObj($a_app_info);
        $o_app2->uuid = $_uuid;
        $o_app2->name = $new_version;
        $o_app2->ctime = $utime;
        $o_app2->utime = $utime;
        $app_root2 = $project_root . DS . $_uuid;
        if (dir_copy($app_root, $app_root2)) {
            SeasLog::info("App文件夹复制成功--{$app_root}---{$app_root2}");
            return $o_app2;
        } else {
            SeasLog::error("App文件夹复制失败--{$app_root}---{$app_root2}");
            return null;
        }

    }

    function getAsArray()
    {
        $a_data = $this->getBasicAsArray();
        $a_data['conf_list'] = array();
        foreach ($this->conf_list as $key => $conf) {
            /* @var MyAppConf $conf */
            $a_data['conf_list'][$key] = $conf->getAsArray();
        }

        $a_data['db_list'] = array();
        foreach ($this->db_list as $key => $db) {
            /* @var MyDbConf $db */
            $a_data['db_list'][$key] = $db->getAsArray();
        }

        //TODO model
        $a_data['model_list'] = array();

        return $a_data;


    }

    function parseToObj($a_data)
    {
        $this->parseToBasicObj($a_data);
        $this->conf_list = array();
        $this->db_list = array();
        $this->model_list = array();
        if (isset($a_data['conf_list']) && is_array($a_data['conf_list'])) {
            foreach ($a_data['conf_list'] as $key => $conf) {
                $o_conf = new MyAppConf();
                $o_conf->parseToObj($conf);
                $this->conf_list[$key] = $o_conf;
            }

        }

        if (isset($a_data['db_list']) && is_array($a_data['db_list'])) {
            foreach ($a_data['db_list'] as $key => $db) {
                $o_db = new MyDbConf();
                $o_db->parseToObj($db);
                $this->db_list[$key] = $o_db;
            }
        }


        //TODO model
        return $this;
    }

    /**
     * @return MyDbConf
     */
    public function getDbConf()
    {
        return $this->db_conf;
    }

    /**
     * @param MyDbConf $db_conf
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
     * 解析模型
     * @param array $a_app_data
     * @return bool|void
     */
    public function parse($a_app_data = array())
    {

        /**
         * 判断语言
         */
        if (!isset($a_app_data['db_conf']) || !isset($a_app_data['conf_list'])) {
            echo "NO conf defined!!!";
            return null;
        }


        //db conf ,默认就是java
        $a_conf_list = $a_app_data['conf_list'];

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

        $driver = isset($a_db_conf['driver']) ? trim($a_db_conf['driver']) : Constant::DB_MYSQL;
        $host = isset($a_db_conf['host']) ? trim($a_db_conf['host']) : "localhost";
        $port = isset($a_db_conf['port']) ? trim($a_db_conf['port']) : "3306";
        $user = isset($a_db_conf['user']) ? trim($a_db_conf['user']) : "root";
        $password = isset($a_db_conf['password']) ? trim($a_db_conf['password']) : "123456";
        //$database
        $charset = isset($a_db_conf['charset']) ? trim($a_db_conf['charset']) : "utf8";
        $version = isset($a_db_conf['version']) ? trim($a_db_conf['version']) : "";

        $version = isset($a_db_conf['version']) ? trim($a_db_conf['version']) : "";

        $source = "ini";

        $this->db_conf = new MyDbConf($driver, $host, $port, $user, $password, $database, $charset, $version, $source);

        //model_list
        $a_model_list = $a_app_data['model_list'];

        foreach ($a_model_list as $a_model) {
            $this->model_list[] = new MyModel($a_model);
        }
    }

    /**
     * 导出到json
     * @param string $json_path
     */
    public function saveToJson($json_path = "")
    {

    }

    /**
     * 构建生成代码
     */
    public function buildAll()
    {
        //数据库
        $this->buildDbConf();
        $this->buildDb(null);
        //模型
        $this->buildModel(null);
    }

    /**
     * 构建数据库的初始化配置
     */
    public function buildDbConf()
    {
        $dbc = null;
        switch ($this->db_conf->driver) {
            case Constant::DB_MYSQL:
                $dbc = new DbMysql($this->db_conf, $this->path_output);
                break;
            default:

        }
        if ($dbc == null) {
            return;
        }
        $dbc->ccInitDb();
    }

    /**
     * 构建数据库
     */
    public function buildDb(MyModel $model_to_be = null)
    {
        $dbc = null;
        switch ($this->db_conf->driver) {
            case Constant::DB_MYSQL:
                $dbc = new DbMysql($this->db_conf, $this->path_output);
                break;
            default:

        }
        if ($dbc == null) {
            return;
        }

        if ($model_to_be != null) {
            $dbc->ccTable($model_to_be);
            $dbc->ccTable_reset($model_to_be);
            $dbc->ccProc($model_to_be);
        } else {
            foreach ($this->model_list as $o_model) {
                $dbc->ccTable($o_model);
                $dbc->ccTable_reset($o_model);
                $dbc->ccProc($o_model);
            }
        }


    }

    /**
     * 构建模型
     */
    public function buildModel(MyModel $model_to_be = null)
    {
        $mm = null;
        switch ($this->conf_list->lang) {
            case Constant::LANG_JAVA:
                switch ($this->conf_list->mvc) {
                    case Constant::MVC_JAVA_SERVLET:

                        $mm = new JavaServletModel($this->conf_list, $this->db_conf, $this->path_output);
                        break;
                    default:

                }
                break;
            default:

        }
        if ($mm == null) {
            return;
        }

        if ($model_to_be != null) {
            $mm->ccModel($model_to_be);
            $mm->ccWeb($model_to_be);
            $mm->ccTmpl($model_to_be);
            $mm->ccApi($model_to_be);
            $mm->ccDoc($model_to_be);

        } else {
            foreach ($this->model_list as $o_model) {

                $mm->ccModel($o_model);
                $mm->ccWeb($o_model);
                $mm->ccTmpl($o_model);
                $mm->ccApi($o_model);
                $mm->ccDoc($o_model);
            }
        }


    }
}