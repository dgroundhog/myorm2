<?php
/**
 * Created by IntelliJ IDEA.
 * User: dengqianzhong
 * Date: 2019-02-22
 * Time: 20:09
 */
//error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/ShangHai');
//需要定义上级目录为APP_PATH

include_once APP_PATH . "/funs/_cc.inc.php";
include_once APP_PATH . "/funs/init_db.php";
//include_once APP_PATH . "/funs/init_db.php";
include_once APP_PATH . "/funs/php/php_create_bean.php";
include_once APP_PATH . "/funs/php/php_create_contorller.php";
include_once APP_PATH . "/funs/php/php_create_model.php";
include_once APP_PATH . "/funs/php/php_create_modelx.php";


include_once APP_PATH . "/funs/mysql/mysql_create_table.php";
include_once APP_PATH . "/funs/mysql/mysql_reset_table.php";
include_once APP_PATH . "/funs/mysql/mysql_create_proc.php";


/**
 * php版本构建系统
 * @param string $project_name 项目名字
 * @param array $a_project_conf 项目配置
 * @param bool $is_first_time 第一次提交
 */
function myorm2php($project_name, $a_project_conf, $is_first_time = false)
{

    $package = $a_project_conf['package'];//php 没有包的概念
    $project_title = $a_project_conf['title'];
    $a_models = $a_project_conf['a_models'];
    $db_conf = $a_project_conf['db_conf'];

    $a_urlx = $a_project_conf['a_urlx'];

    $package_path = str_replace(".", "/", $package);

    $project_target_root = APP_PATH . "/build/php/{$project_name}";

    if (!file_exists($project_target_root) || $is_first_time) {
        mkdir($project_target_root);
        //创建第一层目录
        $a_path_level_all = array(
            "app" => array(
                "beans",
                "config",
                "controllers",
                "library",
                "logs",
                "models",
                "views"
            ),

            "doc" => array(
                "pm",
                "sql",
                "test"
            ),

            "public" => array(
                "css",
                "js",
                "img",
                "fonts",
                "vendors"
            )
        );
        foreach ($a_path_level_all as $k1 => $a_v1) {
            $a_path_level1 = "{$project_target_root}/{$k1}";
            mkdir($a_path_level1);
            foreach ($a_v1 as $k2) {
                $a_path_level2 = "{$a_path_level1}/{$k2}";
                mkdir($a_path_level2);
            }
        }
    }

    $project_sql_path = $project_target_root . "/doc/sql";
    $project_bean_path = $project_target_root . "/app/beans";
    $project_model_path = $project_target_root . "/app/models";
    $project_controller_path = $project_target_root . "/app/controllers";

    $a_file_from_to = array(
        "/invo/app/controllers/ControllerBase.php" => "{$project_controller_path}/ControllerBase.php",
        "/invo/app/models/ModelBase.php" => "{$project_model_path}/ModelBase.php"
    );
    //复制基本的重复的文件
    foreach ($a_file_from_to as $from => $to) {
        $tmp = file_get_contents(APP_PATH . $from);
        file_put_contents($to, $tmp);
    }


    $_target_sql = "{$project_sql_path}/init_db.sql";
    ob_start();
    init_db($db_conf);
    $data = ob_get_contents();
    ob_end_clean();
    file_put_contents($_target_sql, $data);

    $db_type = $db_conf["db"];
    $user = $db_conf["user"];
    $host = $db_conf["host"];

    $_src_debug = APP_PATH . "/funs/{$db_type}/db_run_once.sql";
    $_target_debug = "{$project_sql_path}/db_run_once.sql";

    $DEFINER = "`{$user}`@`{$host}`";
    $CHARSET = $db_conf["charset"];
    $data2 = file_get_contents($_src_debug);
    $data2 = str_replace("__DEFINER__", $DEFINER, $data2);
    $data2 = str_replace("__CHARSET__", $CHARSET, $data2);
    file_put_contents($_target_debug, $data2);


    foreach ($a_models as $table => $my_model) {

        $table_name = $my_model['table_name'];
        //var_dump($table_name);
        if (!$table_name || $table_name == null || $table_name == "null" || strlen($table_name) < 1) {
            continue;
        }

        $my_model['db_conf'] = $db_conf;


        $uc_table = ucfirst($table_name);

        $_target = "{$project_sql_path}/{$table_name}_mysql_table.sql";
        ob_start();
        mysql_create_table($my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

        $_target = "{$project_sql_path}/{$table_name}_mysql_reset.sql";
        ob_start();
        mysql_reset_table($my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

        $_target = "{$project_sql_path}/{$table_name}_mysql_procedure.sql";
        ob_start();
        mysql_create_proc($my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);


        $_target = "{$project_bean_path}/{$uc_table}Bean.php";
        ob_start();
        php_create_bean($package, $my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);


        $_target = "{$project_model_path}/{$uc_table}.php";
        ob_start();
        php_create_model($package, $my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);


        $_target = "{$project_model_path}/{$uc_table}x.php";
        ob_start();
        php_create_modelx($package, $my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);


        $_target = "{$project_controller_path}/{$uc_table}Controller.php";
        ob_start();
        php_create_contorller($package, $my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

    }
}

