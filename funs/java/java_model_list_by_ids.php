<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_list_by_ids.php");


/**
 * java抽象类--按照ID查询列表
 *
 * @param $model
 */
function java_model_list_by_ids($model)
{
    if (!$model['list_by_ids_enable'] || $model['list_by_ids_key'] == "" || !isset($model['table_fields'][$model['list_by_ids_key']])) {

        return;
    }

    $key = $model['list_by_ids_key'];
    $type = $model['table_fields'][$key]['type'];
    if ($type != "int" && $type != "char" && $type != "varchar") {
        return;
    }
    
    if (!_java_db_header($model, "list_by_ids")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);


    _java_comment_header("根据一组ID查询列表，结构为hash map");
    _java_abs_list_by_ids_param_comment($model);
    _java_comment_footer();
    echo "public static Vector<HashMap> listByIds(";
    $i_param = _java_abs_list_by_ids_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    echo _tab(2) . "Vector<HashMap> vList = DBFactory.get{$uc_table}().listByIds(";
    _java_abs_list_by_ids_param4use($model, true);
    echo ");\n";
    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";


    _java_comment_header("根据一组ID查询列表 结构为bean");
    _java_abs_list_by_ids_param_comment($model);
    _java_comment_footer();
    echo "public static Vector<{$uc_table}Bean> listBeanByIds(";
    _java_abs_list_by_ids_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";


    echo _tab(2) . "Vector<{$uc_table}Bean> vList = DBFactory.get{$uc_table}().listBeanByIds(";
    _java_abs_list_by_ids_param4use($model, true);
    echo ");\n";
    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "list_by_ids");
}