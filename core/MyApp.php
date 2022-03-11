<?php
if (!defined("CC_ROOT")) {
    define('CC_ROOT', realpath(dirname(__FILE__)));
}
include_once(CC_ROOT . "/_util.inc.php");
include_once(CC_ROOT . "/_cc.inc.php");
include_once(CC_ROOT . "/CcBase.php");
include_once(CC_ROOT . "/MyStruct.php");
include_once(CC_ROOT . "/MyArch.php");
include_once(CC_ROOT . "/MyDb.php");
include_once(CC_ROOT . "/MyField.php");
include_once(CC_ROOT . "/MyModel.php");
include_once(CC_ROOT . "/MyIndex.php");
include_once(CC_ROOT . "/MyFun.php");
include_once(CC_ROOT . "/MyWhere.php");
include_once(CC_ROOT . "/MyCond.php");

include_once(CC_ROOT . "/db/DbMysql.php");
include_once(CC_ROOT . "/mvc/JavaServletMvc.php");
include_once(CC_ROOT . "/mvc/PhpPhalconMvc.php");

/**
 * 主程序模型
 * Class MyApp
 */
class MyApp extends MyStruct
{


    /**
     * 图片和图标ID
     * @var string
     */
    public $img_logo_id = "";
    public $img_icon_id = "";

    //包名
    public $package = "";

    //当前的架构
    public $curr_arch = "";

    //当前的数据配置
    public $curr_db = "";

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
    public $basic_keys = array("project_id", "package", "img_icon_id", "img_logo_id");
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
     * 获取当前的框架配置
     * @return MyArch
     */
    public function getCurrArch()
    {
        if ($this->curr_arch != "") {
            if (isset($this->arch_list[$this->curr_arch])) {
                return $this->arch_list[$this->curr_arch];
            }
        }
        /* @var MyArch $first_arch */
        $first_arch = null;
        foreach ($this->arch_list as $kk => $db) {
            $this->curr_arch = $kk;
            $first_arch = $db;
            break;
        }
        return $first_arch;
    }

    /**
     * 获取当前的db配置
     * @return MyDb
     */
    public function getCurrDb()
    {
        if ($this->curr_db != "") {
            if (isset($this->db_list[$this->curr_db])) {
                return $this->db_list[$this->curr_db];
            }
        }


        /* @var MyDb $first_db */
        $first_db = null;
        foreach ($this->db_list as $kk => $db) {
            $this->curr_db = $kk;
            $first_db = $db;
            break;
        }

        return $first_db;
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
        $this->package = "";

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
        $o_db->charset = Constant::DB_CHARSET_UTF8;
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
     * @param $arch   目标结构配置
     * @param $db     数据库配置
     * @return void
     */
    public function build($arch, $db)
    {
        if (count($this->arch_list) == 0 || count($this->db_list) == 0) {
            SeasLog::error("没有有效的架构和数据库配置！！！");
            return null;
        }
        //
        SeasLog::info("生成基本目录");
        $new_build_id = date("YmdHis", time());
        if (!defined("MEM_DISK_SPEED_UP")) {
            $this->path_output = $this->build_root . DS . $new_build_id;
        }
        else{
            $this->path_output = MEM_DISK_SPEED_UP .DS."build" . DS . $new_build_id;
        }

        SeasLog::debug("build app--({$this->name})--mkdir--{$this->path_output}");
        @mkdir($this->path_output);

        if (!is_dir($this->path_output)) {
            SeasLog::error("构建目录生成失败，请检查权限！！！");
            return;
        }

        $this->curr_db = $db;//这是UUID
        $this->curr_arch = $arch;//这也是UUID
        //数据库
        $this->buildDb();
        //数据库配套模型
        $this->buildModel();

        $a_tags = array();
        //TODO
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
     * 构建数据库
     * @return void
     */
    public function buildDb()
    {
        $dbc = DbBase::findCc($this);
        if ($dbc == null) {
            SeasLog::error("找不到对应的数据库构建器");
            return;
        }
        $path_dbcc = $dbc->db_output;
        $_target = $path_dbcc . DS . "init_db.sql";
        ob_start();
        $dbc->ccInitDb();
        $cc_data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $cc_data);

        foreach ($this->model_list as $o_model) {
            /* @var MyModel $o_model */
            $model_name = $o_model->name;
            SeasLog::info("建表语句--{$model_name}");
            $_target = $path_dbcc . DS . "{$model_name}_cc_table.sql";
            ob_start();
            $dbc->ccTable($o_model);
            $cc_data = ob_get_contents();
            ob_end_clean();
            file_put_contents($_target, $cc_data);

            SeasLog::info("删表语句--{$model_name}");
            $_target = $path_dbcc . DS . "{$model_name}_reset_table.sql";
            ob_start();
            $dbc->ccTable_reset($o_model);
            $cc_data = ob_get_contents();
            ob_end_clean();
            file_put_contents($_target, $cc_data);

            SeasLog::info("存储过程--{$model_name}");
            $_target = $path_dbcc . DS . "{$model_name}_proc.sql";
            ob_start();
            $dbc->ccProc($o_model);
            $cc_data = ob_get_contents();
            ob_end_clean();
            file_put_contents($_target, $cc_data);
        }


        //TODO 合并代码？
    }

    /**
     * 构建模型
     */
    public function buildModel()
    {
        $mm = MvcBase::findCc($this);
        if ($mm == null) {
            return;
        }
        //TODO 全局资源的
        foreach ($this->model_list as $o_model) {

            /* @var MyModel $o_model */
            $mm->ccBean($o_model);//创建bean 文件
            $mm->ccModel($o_model);//创建bean 文件

        }

    }
}