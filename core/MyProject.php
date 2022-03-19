<?php
if (!defined("CC_ROOT")) {
    define('CC_ROOT', realpath(dirname(__FILE__)));
}
if (!defined("DS")) {
    define('DS', DIRECTORY_SEPARATOR);
}
include_once(CC_ROOT . "/_util.inc.php");
include_once(CC_ROOT . "/_cc.inc.php");
include_once(CC_ROOT . "/MyStruct.php");
include_once(CC_ROOT . "/MyApp.php");

class MyProject extends MyStruct
{
    //和js对应的数据结构
    //仅目录有用

    /**
     * 包含的应用，version => MyApp
     * @var array
     */
    public $version_list = array();
    public $data_root = "";
    public $build_root = "";

    public function __construct()
    {
        $this->scope = "PROJECT";
    }

    /**
     * 创建一个默认版本
     * @param $new_project_name
     */
    public function init($new_project_name)
    {
        $this->touchSomeDirs($new_project_name);

        $now = time();
        $now_str = date("Y-m-d H:i:s", $now);
        $now_str2 = date("YmdHi", $now);
        $this->name = $new_project_name;
        $this->title = "新项目{$now_str2}";
        $this->uuid = uuid();
        $this->ctime = $now_str;
        $this->utime = $now_str;

        SeasLog::debug("创建一个新项目{$new_project_name}");
        $o_app = new MyApp();
        $o_app->init($new_project_name);
        SeasLog::debug("创建对应的默认版本应用{$o_app->name}");

        $this->version_list[$o_app->uuid] = $o_app;
    }

    /**
     * 尝试建立基本目录
     * @param $project_name
     * @return void
     */
    public function touchSomeDirs($project_name)
    {

        $data_root = CC_ROOT . DS . ".." . DS . "data" . DS . $project_name;
        if (!file_exists($data_root) || !is_dir($data_root)) {
            SeasLog::debug("new prj--({$project_name})---data--mkdir--{$data_root}");
            @mkdir($data_root);
        }
        $this->data_root = $data_root;

        $build_root = CC_ROOT . DS . ".." . DS . "build" . DS . $project_name;
        if (!file_exists($build_root) || !is_dir($build_root)) {
            SeasLog::debug("new prj--({$project_name})---build--mkdir--{$build_root}");
            @mkdir($build_root);
        }
        $this->build_root = $build_root;

    }

    function getAsArray()
    {
        $a_data = $this->getBasicAsArray();
        $a_data['version_list'] = array();
        foreach ($this->version_list as $key => $item) {
            /*@var MyApp $item */
            $a_data['version_list'][$key] = $item->getAsArray();
        }

        return $a_data;

    }

    function parseToObj($a_data)
    {
        $this->parseToBasicObj($a_data);
        SeasLog::debug($a_data['name']);
        $this->touchSomeDirs($a_data['name']);
        SeasLog::debug(1111);
        $this->version_list = array();
        foreach ($a_data['version_list'] as $key => $item) {
            $o_app = new MyApp();
            $o_app->parseToObj($item);
            $o_app->touchSomeDirs();
            $this->version_list[$key] = $o_app;
        }
        return $this;
    }

    /**
     * 根基不同的版本构建
     * @param $version
     * @param $arch   目标结构配置
     * @param $db     数据库配置
     * @param $build_all  构建全部，false时只构建基本模型
     * @return void
     */
    function build($version, $arch, $db,$build_all)
    {

        if (!$this->version_list || count($this->version_list) == 0 || !isset($this->version_list[$version])) {
            SeasLog::error("no app defined");
            return;
        }

        $o_curr_app = $this->version_list[$version];
        /* @var MyApp $o_curr_app */

        $o_curr_app->build($arch, $db,$build_all);
    }
}