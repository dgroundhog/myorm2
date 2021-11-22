<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_list_all.php");


/**
 * java抽象类--查询列表
 *
 * @param $model
 */
function java_model_list_all($model)
{

    if (!_java_db_header($model, "list_all")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("查询列表，结构为hash map");
    _java_abs_list_all_param_comment($model);
    _java_comment_footer();

    echo "public static Vector<HashMap> listAll(";

    $i_param = _java_abs_list_all_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    echo _tab(2) . "Vector<HashMap> vList = DBFactory.get{$uc_table}().listAll(";
    _java_abs_list_all_param4use($model,true);
    echo ");\n";
    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";


    _java_comment_header("查询列表 结构为bean");
    _java_abs_list_all_param_comment($model);
    _java_comment_footer();
    echo "public static Vector<{$uc_table}Bean> listBeanAll(";
    _java_abs_list_all_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    echo _tab(2) . "Vector<{$uc_table}Bean> vList = DBFactory.get{$uc_table}().listBeanAll(";
    _java_abs_list_all_param4use($model,true);
    echo ");\n";

    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "list_all");
}