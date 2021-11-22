<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_fetch.php");


/**
 * java模型类--获取一条数据
 *
 * @param $model
 */
function java_model_fetch($model)
{

    if (!_java_db_header($model, "fetch")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("取出数据为map");
    _java_abs_fetch_param_comment($model);
    _java_comment_footer();


    echo "public static HashMap fetch(";
    $i_param = _java_abs_fetch_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    echo _tab(2) . "HashMap<String,String> mInfo = DBFactory.get{$uc_table}().fetch(";
    _java_abs_fetch_param4use($model,true);
    echo ");\n";
    echo _tab(2) . "return mInfo;\n";
    echo _tab(1) . "}";


    $uc_table = ucfirst($model['table_name']);
    _java_comment_header("取出数据为Bean");
    _java_abs_fetch_param_comment($model);
    _java_comment_footer();
    echo "public static {$uc_table}Bean fetchBean(";
    _java_abs_fetch_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";
    echo _tab(2) . "{$uc_table}Bean {$table_name}Bean = DBFactory.get{$uc_table}().fetchBean(\n";
    _java_abs_fetch_param4use($model,true);
    echo ");\n";
    echo _tab(2) . "return {$table_name}Bean;\n";

    echo _tab(1) . "}";


    _java_db_footer($model, "fetch");
}