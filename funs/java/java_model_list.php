<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_list.php");


/**
 * java抽象类--查询列表
 *
 * @param $model
 */
function java_model_list($model)
{

    if (!_java_db_header($model, "list")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("查询列表，结构为hash map");
    _java_abs_list_param_comment($model);
    _java_comment_footer();

    echo _tab(1) . "public static Vector<HashMap> list(";
    $i_param = _java_abs_list_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";
    echo _tab(2) . "Vector<HashMap> vList = DBFactory.get{$uc_table}().list(";
    _java_abs_list_param4use($model,true);
    echo ");\n";
    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";


    _java_comment_header("查询列表 结构为bean");
    _java_abs_list_param_comment($model);
    _java_comment_footer();
    echo "public static Vector<{$uc_table}Bean> listBean(";
    _java_abs_list_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    echo _tab(2) . "Vector<{$uc_table}Bean> vList = DBFactory.get{$uc_table}().listBean(";
    _java_abs_list_param4use($model,true);
    echo ");\n";
    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "list");
}



/**
 * java抽象类--查询列表
 *
 * @param $model
 */
function java_model_list_basic($model)
{

    if (!_java_db_header($model, "list_basic")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("查询列表，结构为hash map");
    _java_abs_list_param_comment($model);
    _java_comment_footer();

    echo "public static Vector<HashMap> listBasic(";
    $i_param = _java_abs_list_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";
    echo _tab(2) . "Vector<HashMap> vList = DBFactory.get{$uc_table}().listBasic(";
    _java_abs_list_param4use($model,true);
    echo ");\n";
    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";


    _java_comment_header("查询列表 结构为bean");
    _java_abs_list_param_comment($model);
    _java_comment_footer();
    echo "public static Vector<{$uc_table}Bean> listBasicBean(";
    _java_abs_list_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    echo _tab(2) . "Vector<{$uc_table}Bean> vList = DBFactory.get{$uc_table}().listBasicBean(";
    _java_abs_list_param4use($model,true);
    echo ");\n";
    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "list");
}