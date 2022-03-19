<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/ShangHai');
if (!defined("DS")) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined("WT_ROOT")) {
    define('WT_ROOT', realpath(dirname(__FILE__)));
}
define('MEM_DISK_SPEED_UP', "G:");
//可以删除掉
if (!defined("MEM_DISK_SPEED_UP")) {
    SeasLog::setBasePath(WT_ROOT . DS . ".." . DS . "logs");
}
else{
    SeasLog::setBasePath( MEM_DISK_SPEED_UP . DS ."logs");
}
//
//TODO myapp line-338

include_once(WT_ROOT . "/../core/Constant.php");
include_once(WT_ROOT . "/../core/MyProject.php");
include_once(WT_ROOT . "/../core/MyApp.php");
//接受参数
$_act = (!isset($_POST['act'])) ? @$_GET['act'] : @$_POST['act'];
$_project = (!isset($_POST['project'])) ? @$_GET['project'] : @$_POST['project'];
$_version1 = (!isset($_POST['version'])) ? @$_GET['version'] : @$_POST['version'];

$_version2 = @$_POST['version2'];
$_data = @$_POST['data'];
//下面几个适合增量更新
$_uuid = @$_POST['uuid'];
$_name = @$_POST['name'];
$_title = @$_POST['title'];
$_memo = @$_POST['memo'];
$_arch = @$_POST['arch'];
$_db = @$_POST['db'];
$_all = @$_POST['all'];//是否构建全部

$img_id = @$_GET['img_id'];

//仅保存
if (is_null($_act)) {
    return;
}

SeasLog::info("act--{$_act}");
SeasLog::info("req--{$_data}");
/**
 * 数据目录
 */
$g_data_root_path = WT_ROOT . DS . ".." . DS . "data";

/**
 * 初始化一个项目目录
 */
function ajax_init_project($name)
{
    global $g_data_root_path;
    $a_return = array();
    $a_return['code'] = "ok";
    $a_return['msg'] = "done";
    $a_return['data'] = array();

    if (!is_ok_project_name($name)) {
        $a_return['code'] = "name_not_ok";
        $a_return['msg'] = "项目不符合要求";
    } else {
        //如果项目存在就返回那个项目的信息
        $project_path = $g_data_root_path . DS . $name . ".json";
        if (file_exists($project_path)) {
            $str = file_get_contents($project_path);
            $a_return['data']['project_info'] = json_decode($str, true);
        } else {
            $o_project = new MyProject();
            $o_project->init($name);
            $a_project_info = $o_project->getAsArray();
            file_put_contents($project_path, json_encode($a_project_info));
            $a_return['data']['project_info'] = $a_project_info;
        }
    }
    echo json_encode($a_return);
}


/**
 * 初始化一个项目目录
 */
function ajax_update_project($_project, $_title, $_memo)
{
    global $g_data_root_path;
    $a_return = array();
    $a_return['code'] = "ok";
    $a_return['msg'] = "done";
    $a_return['data'] = array();

    //如果项目存在就返回那个项目的信息
    $project_path = $g_data_root_path . DIRECTORY_SEPARATOR . $_project . ".json";
    if (file_exists($project_path)) {
        $str = file_get_contents($project_path);
        $a_project_info = json_decode($str, true);

        $o_project = new MyProject();
        $o_project->parseToObj($a_project_info);
        $o_project->title = $_title;
        $o_project->memo = $_memo;
        $o_project->utime = date("Y-m-d H:i:s", time());
        $a_project_info = $o_project->getAsArray();

        file_put_contents($project_path, json_encode($a_project_info));
        $a_return['data']['project_info'] = $a_project_info;
    } else {
        $a_return['code'] = "project_not_exist";
        $a_return['msg'] = "项目不存在1";
    }
    echo json_encode($a_return);
}


/**
 * 读取索引
 * @param $data_root_path
 * @return array
 */
function project_load_index($data_root_path)
{
    $a_projects = array();
    //1、首先先读取文件夹
    $temp = scandir($data_root_path);
    //遍历文件夹
    foreach ($temp as $v) {
        $aa = $data_root_path . DIRECTORY_SEPARATOR . $v;
        if (is_dir($aa)) {//如果是文件夹则执行
            continue;
        } else {
            //echo
            if (str_ends_with($aa, ".json")) {
                $str_project = file_get_contents($aa);
                $a_project = json_decode($str_project, true);
                $o_project = new MyProject();
                $o_project->parseToObj($a_project);
                $a_projects[] = $o_project->getAsArray();
            }
        }
    }
    return $a_projects;
}

switch (trim($_act)) {

    case "load":
        //加载全部项目索引
        ajax_load_projects();
        break;
        
    case "init":
        //初始化项目
        ajax_init_project($_project);
        break;

    case "update":
        //保存项目基本信息
        ajax_update_project($_project, $_title, $_memo);
        break;

    case "app_img":
        //保存项目基本信息
        $app_root = WT_ROOT . DS . ".." . DS . "data" . DS . $_project . DS . $_version1 . DS;
        $app_url = "../data/{$_project}/{$_version1}/";
        $target_path = $app_root . $img_id;
        $target_url = $app_url . $img_id;
        if (file_exists($target_path)) {
            header("Location: {$target_url}");

        } else {
            header("Location: img/mustang.png");
        }
        exit();
        break;

    case "add":
        //保存项目
        ajax_create_app($_project, $_version1);
        break;

    case "save":
        //保存项目
        ajax_save_app($_project, $_version1, $_data);
        break;

    case "copy":
        //复制一个版本
        ajax_copy_app($_project, $_version1, $_version2);
        break;
        
    case "drop":
        //删除某个项目的某个版本
        ajax_drop_app($_project, $_version1);
        break;

    case "build":
        ajax_build_app($_project, $_version1, $_arch, $_db,$_all);
        break;

    default:
    case "test":
        var_dump($_data);
        var_dump(base64_decode($_data));
        break;
}

//加载全部项目索引
function ajax_load_projects()
{
    global $g_data_root_path;
    $a_return = array();
    $a_return['code'] = "ok";
    $a_return['msg'] = "done";
    $a_return['data'] = array();
    $a_projects = project_load_index($g_data_root_path);
    $a_return['data']['projects'] = $a_projects;

    echo json_encode($a_return);

}



/**
 * 仅更新其中一个版本
 * @param $project
 * @param $version
 * @param $data
 */
function ajax_save_app($project, $version, $data)
{
    global $g_data_root_path;
    $a_return = array();
    $a_return['code'] = "ok";
    $a_return['msg'] = "done";
    $a_return['data'] = array();

    //如果项目存在就返回那个项目的信息
    $project_path = $g_data_root_path . DIRECTORY_SEPARATOR . $project . ".json";
    if (file_exists($project_path)) {
        $str_project = file_get_contents($project_path);
        $a_project_info = json_decode($str_project, true);

        $o_project = new MyProject();
        $o_project->parseToObj($a_project_info);
        $utime = date("Y-m-d H:i:s", time());
        $o_project->utime = $utime;

        $a_app_info = json_decode($data, true);
        $o_app = new MyApp();
        $o_app->parseToObj($a_app_info);
        $o_app->utime = $utime;
        //SeasLog::debug($o_app);
        $o_project->version_list[$version] = $o_app;

        $a_project_info = $o_project->getAsArray();

        file_put_contents($project_path, json_encode($a_project_info));
        $a_return['data']['project_info'] = $a_project_info;
    } else {
        $a_return['code'] = "project_not_exist";
        $a_return['msg'] = "项目不存在2";
    }


    echo json_encode($a_return);
}

//新建一个app
function ajax_create_app($project, $new_version_name)
{
    global $g_data_root_path;
    $a_return = array();
    $a_return['code'] = "ok";
    $a_return['msg'] = "done";
    $a_return['data'] = array();

    //如果项目存在就返回那个项目的信息
    $project_path = $g_data_root_path . DIRECTORY_SEPARATOR . $project . ".json";
    if (file_exists($project_path)) {
        $str_project = file_get_contents($project_path);
        $a_project_info = json_decode($str_project, true);
        $o_project = new MyProject();
        $o_project->parseToObj($a_project_info);

        $utime = date("Y-m-d H:i:s", time());
        $o_project->utime = $utime;

        $o_app = new MyApp();
        $o_app->init($o_project->name);
        $_uuid = $o_app->uuid;
        $o_app->name = $new_version_name;
        $o_project->version_list[$_uuid] = $o_app;
        $a_project_info = $o_project->getAsArray();
        file_put_contents($project_path, json_encode($a_project_info));
        $a_return['data']['project_info'] = $a_project_info;
        $a_return['data']['new_app_version'] = $_uuid;
    } else {
        $a_return['code'] = "project_not_exist";
        $a_return['msg'] = "项目不存在3";
    }


    echo json_encode($a_return);
}

function ajax_copy_app($project, $version, $new_version)
{
    global $g_data_root_path;
    $a_return = array();
    $a_return['code'] = "ok";
    $a_return['msg'] = "done";
    $a_return['data'] = array();

    //如果项目存在就返回那个项目的信息
    $project_path = $g_data_root_path . DIRECTORY_SEPARATOR . $project . ".json";
    if (file_exists($project_path)) {
        $str_project = file_get_contents($project_path);
        $a_project_info = json_decode($str_project, true);
        $o_project = new MyProject();
        $o_project->parseToObj($a_project_info);

        $utime = date("Y-m-d H:i:s", time());
        $o_project->utime = $utime;

        $o_app = $o_project->version_list[$version];

        /* @var MyApp $o_app */
        $o_app2 = $o_app->copy($new_version);
        if (null != $o_app2) {
            $_uuid = $o_app2->uuid;
            $o_project->version_list[$_uuid] = $o_app2;
            $a_project_info = $o_project->getAsArray();
            file_put_contents($project_path, json_encode($a_project_info));
            $a_return['data']['project_info'] = $a_project_info;
            $a_return['data']['new_app_version'] = $_uuid;
        } else {
            $a_return['code'] = "clone_error";
            $a_return['msg'] = "复制失败";
        }
    } else {
        $a_return['code'] = "project_not_exist";
        $a_return['msg'] = "项目不存在3";
    }


    echo json_encode($a_return);
}

function ajax_drop_app($project, $version)
{
    global $g_data_root_path;
    $a_return = array();
    $a_return['code'] = "ok";
    $a_return['msg'] = "done";
    $a_return['data'] = array();

    //如果项目存在就返回那个项目的信息
    $project_path = $g_data_root_path . DIRECTORY_SEPARATOR . $project . ".json";
    if (file_exists($project_path)) {
        $str_project = file_get_contents($project_path);
        $a_project_info = json_decode($str_project, true);
        $o_project = new MyProject();
        $o_project->parseToObj($a_project_info);
        $utime = date("Y-m-d H:i:s", time());
        $o_project->utime = $utime;
        unset($o_project->version_list[$version]);
        $a_project_info = $o_project->getAsArray();
        file_put_contents($project_path, json_encode($a_project_info));
        $a_return['data']['project_info'] = $a_project_info;
        $a_return['msg'] = "删除成功";
    } else {
        $a_return['code'] = "project_not_exist";
        $a_return['msg'] = "项目不存在4";
    }
    echo json_encode($a_return);
}

/**
 * 构建app
 * @param $project
 * @param $version
 * @param $arch
 * @param $db
 * @param $_all
 * @return void
 */
function ajax_build_app($project, $version, $arch, $db,$_all)
{
    global $g_data_root_path;
    $a_return = array();
    $a_return['code'] = "ok";
    $a_return['msg'] = "done";
    $a_return['data'] = array();

    //如果项目存在就返回那个项目的信息
    $project_path = $g_data_root_path . DIRECTORY_SEPARATOR . $project . ".json";
    if (file_exists($project_path)) {
        $str_project = file_get_contents($project_path);
        $a_project_info = json_decode($str_project, true);
        $o_project = new MyProject();
        $o_project->parseToObj($a_project_info);
        $o_project->build($version, $arch, $db,$_all);

    } else {
        $a_return['code'] = "project_not_exist";
        $a_return['msg'] = "项目不存在4";
    }
    echo json_encode($a_return);
}


