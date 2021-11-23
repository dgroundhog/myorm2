<?php
if (!defined("CC_ROOT")) {
    define('CC_ROOT', realpath(dirname(__FILE__)));
}
include_once(CC_ROOT . "/_util.inc.php");
include_once(CC_ROOT . "/_cc.inc.php");
include_once(CC_ROOT . "/MyStruct.php");

class MyProject extends MyStruct
{
    //和js对应的数据结构
    //仅目录有用


    /**
     * 包含的应用，version => MyApp
     * @var array
     */
    public $version_list = array();

    public function __construct()
    {

    }

    /**
     * @param $new_name
     */
    public function init($new_name)
    {
        //创建一个默认版本
        //先创建目录
        $project_root = MyProject::getDataRoot($new_name);
        SeasLog::debug("new prj--({$new_name})--mkdir--{$project_root}");
        if (!file_exists($project_root)) {
            mkdir($project_root);
        }

        $o_app = new MyApp();
        $o_app->init($new_name);
        $new_app_name = $o_app->uuid;

        $now = time();
        $now_str = date("Y-m-d H:i:s", $now);
        $now_str2 = date("YmdHi", $now);
        $this->name = $new_name;
        $this->title = "新项目{$now_str2}";
        $this->uuid = uuid();
        $this->ctime = $now_str;
        $this->utime = $now_str;
        $this->version_list[$new_app_name] = $o_app;

    }

    public static function getDataRoot($new_name)
    {

        $data_root = CC_ROOT . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . $new_name;

        if (file_exists($data_root)) {
            @mkdir($data_root);
        }

        return $data_root;

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
        $this->version_list = array();
        foreach ($a_data['version_list'] as $key => $item) {
            $o_app = new MyApp();
            $o_app->parseToObj($item);
            $this->version_list[$key] = $o_app;
        }
        return $this;
    }
}