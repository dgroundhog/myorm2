<?php
/**
 * Created by IntelliJ IDEA.
 * User: dengqianzhong
 * Date: 2019-02-22
 * Time: 20:09
 */

error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/ShangHai');
//需要定义上级目录为APP_PATH

include_once APP_PATH . "/funs/_cc.inc.php";
include_once APP_PATH . "/funs/init_db.php";
include_once APP_PATH . "/funs/mysql/mysql_create_table.php";
include_once APP_PATH . "/funs/mysql/mysql_reset_table.php";
include_once APP_PATH . "/funs/mysql/mysql_create_proc.php";
include_once APP_PATH . "/funs/java/java_create_bean.php";
include_once APP_PATH . "/funs/java/java_db_abs.php";
include_once APP_PATH . "/funs/java/java_db_factory.php";
include_once APP_PATH . "/funs/java/java_create_model.php";
include_once APP_PATH . "/funs/java/java_create_mysql_model.php";
include_once APP_PATH . "/funs/java/java_create_servlet.php";
include_once APP_PATH . "/funs/java/java_html_index.php";
include_once APP_PATH . "/funs/java/java_html_list.php";
include_once APP_PATH . "/funs/java/java_html_new.php";
include_once APP_PATH . "/funs/java/java_html_edit.php";
include_once APP_PATH . "/funs/java/java_html_detail.php";
include_once APP_PATH . "/funs/java/java_html_menu.php";
include_once APP_PATH . "/funs/java/java_xml_web.php";


function myorm($project, $package, $a_models, $a_dbs, $project_title, $is_first_time = false)
{


    $package_path = str_replace(".", "/", $package);


    if ($is_first_time) {

        $path_sql_mysql = APP_PATH . "/doc/{$project}/mysql";
        $path_sql_oracle = APP_PATH . "/doc/{$project}/oracle";
        if (!file_exists($path_sql_mysql)) {
            mkdir($path_sql_mysql);
        }
        if (!file_exists($path_sql_oracle)) {
            mkdir($path_sql_oracle);
        }

        $path_bean = APP_PATH . "/src/{$package_path}/bean";
        if (!file_exists($path_bean)) {
            mkdir($path_bean);
        }

        $path_db = APP_PATH . "/src/{$package_path}/db";
        if (!file_exists($path_db)) {
            mkdir($path_db);
        }
        $path_db_base = APP_PATH . "/src/{$package_path}/db/base";
        $path_db_mysql = APP_PATH . "/src/{$package_path}/db/mysql";
        $path_db_oracle = APP_PATH . "/src/{$package_path}/db/oracle";
        if (!file_exists($path_db_base)) {
            mkdir($path_db_base);
        }
        if (!file_exists($path_db_mysql)) {
            mkdir($path_db_mysql);
        }
        if (!file_exists($path_db_oracle)) {
            mkdir($path_db_oracle);
        }

        $path_model = APP_PATH . "/src/{$package_path}/model";
        $path_service = APP_PATH . "/src/{$package_path}/service";
        $path_servlet = APP_PATH . "/src/{$package_path}/servlet";
        $path_tmpl = APP_PATH . "/web/WEB-INF/tmpl_{$project}";

        if (!file_exists($path_model)) {
            mkdir($path_model);
        }

        if (!file_exists($path_service)) {
            mkdir($path_service);
        }

        if (!file_exists($path_servlet)) {
            mkdir($path_servlet);
        }

        if (!file_exists($path_tmpl)) {
            mkdir($path_tmpl);
        }

        $a_init_files = array(
            "bean/AjaxResult.java",
            "bean/Admin_logBean.java",
            "bean/AjaxBeanx.java",
            "bean/DateFromToBeanx.java",
            "bean/UploadBeanx.java",
            "db/base/UnormBase.java",
            "db/mysql/UnormMysql.java",
            "db/DbBase.java",
            "model/ModelBase.java",
            "model/AdminModel.java",
            "model/UnormModel.java",
            "model/Admin_logModel.java",
            "service/MyRes.java",
            "servlet/DefaultServletx.java",
            "servlet/ServletBase.java",
            "servlet/HtmlServletBase.java",
            "servlet/UploadServletBase.java",
            "servlet/RestServletBase.java",
            "Constants.java",
            "MyListener.java",
            "MyTemplate.java",
            "MyApp.java"
        );


        if ($a_dbs["mysql"]["source"] == "embed") {

            array_push($a_init_files, "db/DbEmbedPool.java");
            array_push($a_init_files, "db/DbMysql.java");

        } else {

            array_push($a_init_files, "db/DbEnvPool.java");
            array_push($a_init_files, "db/DbMysql.java");

        }

        foreach ($a_init_files as $value) {
            $from = APP_PATH . "/src/__orm__/{$value}";
            $to = APP_PATH . "/src/{$package_path}/{$value}";
            $tmp = file_get_contents($from);
            $tmp = str_replace("__orm__", $package, $tmp);
            $tmp = str_replace("__project_title__", $project_title, $tmp);
            file_put_contents($to, $tmp);
        }
    }

    $_target = APP_PATH . "/src/{$package_path}/db/DBFactory.java";
    ob_start();
    java_db_factory($package, $a_dbs, $a_models);
    $data = ob_get_contents();
    ob_end_clean();
    file_put_contents($_target, $data);

    $_target = APP_PATH . "/web/WEB-INF/tmpl_{$project}/menu_inc.html";
    ob_start();
    java_html_menu($a_models);
    $data = ob_get_contents();
    ob_end_clean();
    file_put_contents($_target, $data);


    $_target = APP_PATH . "/web/WEB-INF/web.xml";
    ob_start();
    java_xml_web($package, $a_models);
    $data = ob_get_contents();
    ob_end_clean();
    file_put_contents($_target, $data);


    foreach ($a_dbs as $db_type => $db_conf) {

        $_target = APP_PATH . "/doc/{$project}/{$db_type}/init_db.sql";
        ob_start();
        init_db($db_type, $db_conf);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);
    }


    foreach ($a_models as $table => $my_model) {

        $table_name = $my_model['table_name'];
        $uc_table = ucfirst($table_name);


        //TODO 过来添加和更新的字段，确保在集合内
        $a_temp = array();
        foreach ($my_model['add_keys'] as $v) {
            if (isset($my_model['table_fields'][$v])) {
                array_push($a_temp, $v);
            } else {
                echo "{$table_name}--add_keys-err--{$v}\n";
            }
        }
        $my_model['add_keys'] = $a_temp;

        $a_temp = array();
        foreach ($my_model['update_keys'] as $v) {
            if (isset($my_model['table_fields'][$v])) {
                array_push($a_temp, $v);
            } else {
                echo "{$table_name}--update_keys-err--{$v}\n";
            }
        }
        $my_model['update_keys'] = $a_temp;


        $_target = APP_PATH . "/src/{$package_path}/bean/{$uc_table}Bean.java";
        ob_start();
        java_create_bean($package, $my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

        $_target = APP_PATH . "/src/{$package_path}/db/base/Db{$uc_table}.java";
        ob_start();
        java_db_abs($package, $my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

        foreach ($a_dbs as $db_type => $db_conf) {

            if ($db_type == "mysql") {

                $_target = APP_PATH . "/doc/{$project}/mysql/{$table_name}_mysql_table.sql";
                ob_start();
                mysql_create_table($my_model);
                $data = ob_get_contents();
                ob_end_clean();
                file_put_contents($_target, $data);

                $_target = APP_PATH . "/doc/{$project}/mysql/{$table_name}_mysql_reset.sql";
                ob_start();
                mysql_reset_table($my_model);
                $data = ob_get_contents();
                ob_end_clean();
                file_put_contents($_target, $data);

                $_target = APP_PATH . "/doc/{$project}/mysql/{$table_name}_mysql_procedure.sql";
                ob_start();
                mysql_create_proc($my_model, $db_conf);
                $data = ob_get_contents();
                ob_end_clean();
                file_put_contents($_target, $data);

                $_target = APP_PATH . "/src/{$package_path}/db/mysql/DbMysql{$uc_table}.java";
                ob_start();
                java_create_mysql_model($package, $my_model);
                $data = ob_get_contents();
                ob_end_clean();
                file_put_contents($_target, $data);

                $_target = APP_PATH . "/src/{$package_path}/model/{$uc_table}Model.java";
                ob_start();
                java_create_model($package, $my_model);
                $data = ob_get_contents();
                ob_end_clean();
                file_put_contents($_target, $data);
            }
        }

        $_target = APP_PATH . "/src/{$package_path}/servlet/{$uc_table}Servlet.java";
        ob_start();
        java_create_servlet($package, $my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);


        $_target = APP_PATH . "/web/WEB-INF/tmpl_{$project}/{$table_name}_index.html";
        ob_start();
        java_html_index($my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

        $_target = APP_PATH . "/web/WEB-INF/tmpl_{$project}/{$table_name}_list.html";
        ob_start();
        java_html_list($my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

        $_target = APP_PATH . "/web/WEB-INF/tmpl_{$project}/{$table_name}_new.html";
        ob_start();
        java_html_new($my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

        $_target = APP_PATH . "/web/WEB-INF/tmpl_{$project}/{$table_name}_edit.html";
        ob_start();
        java_html_edit($my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);


        $_target = APP_PATH . "/web/WEB-INF/tmpl_{$project}/{$table_name}_detail.html";
        ob_start();
        java_html_detail($my_model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);


    }
}

