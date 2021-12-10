<?php
if (!defined("CC_ROOT")) {
    define('CC_ROOT', realpath(dirname(__FILE__)));
}
include_once(CC_ROOT . "/_util.inc.php");
include_once(CC_ROOT . "/_cc.inc.php");
include_once(CC_ROOT . "/MyStruct.php");
include_once(CC_ROOT . "/MyArch.php");
include_once(CC_ROOT . "/MyDb.php");
include_once(CC_ROOT . "/MyField.php");
include_once(CC_ROOT . "/MyModel.php");
include_once(CC_ROOT . "/MyIndex.php");
include_once(CC_ROOT . "/MyFun.php");
include_once(CC_ROOT . "/MyWhere.php");
include_once(CC_ROOT . "/MyCond.php");

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
     * @var MyArch
     */
    public $arch_list = null;
    /**
     * 数据库配置
     * @var MyDb
     */
    public $db_list = null;

    /**
     * 全局字段
     * @var array
     */
    public $field_list = array();

    /**
     * 包含的模型，key => MyModel
     * @var array
     */
    public $model_list = array();

    /**
     * app数据ok
     * @var boolean
     */
    public $checked_app_data_is_good = false;
    public $basic_keys = array("project_id", "img_icon_id", "img_logo_id");
    public $data_root = "";
    public $build_root = "";
    /**
     * 输出目录
     * 每次构建都有一个独立的输出目录
     * @var string
     */
    public $path_output = "";

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
        $now_str2 = date("mdH", $now);
        $this->uuid = uuid();
        $this->project_id = $project_id;
        $this->name = "app_{$now_str2}";
        $this->title = "{$project_id}的新应用{$now_str2}";
        $this->ctime = $now_str;
        $this->utime = $now_str;

        $this->model_list = array();
        $this->db_list = array();
        $this->arch_list = array();
        $this->field_list = array();

        //cc一个默认的db和默认的arch
        $o_arch = new MyArch();
        $o_arch->uuid = uuid();
        $o_arch->mvc = Constant::MVC_JAVA_SERVLET;
        $o_arch->ui = Constant::UI_NULL;
        $o_arch->has_restful = 0;
        $o_arch->has_test = 0;
        $o_arch->has_doc = 0;
        $o_arch->ctime = $now_str;
        $o_arch->utime = $now_str;

        $this->arch_list[$o_arch->uuid] = $o_arch;
        //cc一个默认的db和默认的db
        $o_db = new MyDb();
        $o_db->uuid = uuid();
        $o_db->driver = Constant::DB_MYSQL_56;
        $o_db->source = Constant::DB_SOURCE_EMBED;
        $o_db->host = "127.0.0.1";
        $o_db->port = 3306;
        $o_db->database = "mydb";
        $o_db->user = "root";
        $o_db->password = "passwd2change";
        $o_db->charset =Constant::DB_CHARSET_UTF8;
        $o_db->ctime = $now_str;
        $o_db->utime = $now_str;
        $this->db_list[$o_db->uuid] = $o_db;

        /**
         * 创建数据目录
         */
        $this->touchSomeDirs();

        return $this;
    }

    /**
     * 尝试建立基本目录
     * @return void
     */
    public function touchSomeDirs()
    {

        //project 目标已经生成了
        $project_id = $this->project_id;
        $app_id = $this->uuid;

        $data_root0 = CC_ROOT . DS . ".." . DS . "data" . DS . $project_id;
        $data_root = $data_root0 . DS . $app_id;
        if (!file_exists($data_root) || !is_dir($data_root)) {
            SeasLog::debug("new app--({$app_id})---data--mkdir--{$data_root0}");
            @mkdir($data_root);
        }
        $this->data_root = $data_root;

        $build_root0 = CC_ROOT . DS . ".." . DS . "build" . DS . $project_id;
        $build_root = $build_root0 . DS . $app_id;
        if (!file_exists($build_root) || !is_dir($build_root)) {
            SeasLog::debug("new app--({$app_id})---build--mkdir--{$build_root}");
            @mkdir($build_root);
        }
        $this->build_root = $build_root;
        //
    }

    public function copy($new_version)
    {
        $a_app_info = $this->getAsArray();
        $utime = date("Y-m-d H:i:s", time());
        $app_root = $this->data_root;

        $_uuid = uuid();
        $o_app2 = new MyApp();
        $o_app2->parseToObj($a_app_info);
        $o_app2->uuid = $_uuid;
        $o_app2->name = $new_version;
        $o_app2->ctime = $utime;
        $o_app2->utime = $utime;

        $o_app2->touchSomeDirs();
        $app_root2 = $o_app2->data_root;
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
        $a_data['arch_list'] = array();
        foreach ($this->arch_list as $key => $arch) {
            /* @var MyArch $arch */
            $a_data['arch_list'][$key] = $arch->getAsArray();
        }

        $a_data['db_list'] = array();
        foreach ($this->db_list as $key => $db) {
            /* @var MyDb $db */
            $a_data['db_list'][$key] = $db->getAsArray();
        }

        $a_data['field_list'] = array();
        foreach ($this->field_list as $key => $o_field) {
            /* @var MyField $o_field */
            $a_data['field_list'][$key] = $o_field->getAsArray();
        }

        $a_data['model_list'] = array();
        foreach ($this->model_list as $key => $o_model) {
            /* @var MyModel $o_model */
            $a_data['model_list'][$key] = $o_model->getAsArray();
        }
        return $a_data;

    }

    function parseToObj($a_data)
    {
        $this->parseToBasicObj($a_data);
        $this->touchSomeDirs();
        $this->arch_list = array();
        $this->db_list = array();
        $this->model_list = array();
        if (isset($a_data['arch_list']) && is_array($a_data['arch_list'])) {
            foreach ($a_data['arch_list'] as $key => $conf) {
                $o_obj = new MyArch();
                $o_obj->parseToObj($conf);
                $this->arch_list[$key] = $o_obj;
            }

        }

        if (isset($a_data['db_list']) && is_array($a_data['db_list'])) {
            foreach ($a_data['db_list'] as $key => $db) {
                $o_obj = new MyDb();
                $o_obj->parseToObj($db);
                $this->db_list[$key] = $o_obj;
            }
        }

        if (isset($a_data['field_list']) && is_array($a_data['field_list'])) {
            foreach ($a_data['field_list'] as $key => $field) {
                $o_obj = new MyField();
                $o_obj->parseToObj($field);
                $this->field_list[$key] = $o_obj;
            }
        }

        if (isset($a_data['model_list']) && is_array($a_data['model_list'])) {
            foreach ($a_data['model_list'] as $key => $field) {
                $o_obj = new MyModel();
                $o_obj->parseToObj($field);
                $this->model_list[$key] = $o_obj;
            }
        }
        return $this;
    }


    /**
     * 构建生成代码
     * @param $a_tags
     * @return void
     */
    public function build($a_tags)
    {
        //TODO 先生成基本目录
        SeasLog::info("生成基本目录");
        $new_build_id = date("YmdHis", time());
        $this->path_output = $this->build_root . DS . $new_build_id;

        SeasLog::debug("build app--({$this->name})--mkdir--{$this->path_output}");
        @mkdir($this->path_output);

        if (!is_dir($this->path_output)) {
            SeasLog::error("构建目录生成失败，请检查权限！！！");
            return;
        }

        if (in_array("db", $a_tags)) {
            //数据库
            $this->buildDbConf();
            $this->buildDb(null);
        }

        if (in_array("model", $a_tags)) {
            //模型
            $this->buildModel(null);
        }

        if (in_array("ui", $a_tags)) {
            //UI
            //$this->buildModel(null);
        }

        if (in_array("doc", $a_tags)) {
            //文档
            //->buildModel(null);
        }

        if (in_array("api", $a_tags)) {
            //接口
            //$this->buildModel(null);
        }

    }

    /**
     * 构建数据库的初始化配置
     */
    public function buildDbConf()
    {
        $dbc = null;
        switch ($this->db_list->driver) {
            case Constant::DB_MYSQL_56:
            case Constant::DB_MYSQL_57:
            case Constant::DB_MYSQL_80:
                $dbc = new DbMysql($this->db_list, $this->path_output);
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
        switch ($this->db_list->driver) {
            case Constant::DB_MYSQL_56:
            case Constant::DB_MYSQL_57:
            case Constant::DB_MYSQL_80:
                $dbc = new DbMysql($this->db_list, $this->path_output);
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
        switch ($this->arch_list->lang) {
            case Constant::LANG_JAVA:
                switch ($this->arch_list->mvc) {
                    case Constant::MVC_JAVA_SERVLET:

                        $mm = new JavaServletModel($this->arch_list, $this->db_list, $this->path_output);
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