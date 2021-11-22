<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");

function _java_abs_list_by_ids_param_comment($model)
{

    echo "* @param v_values \n";
    echo "* @return Vector\n";
}

function _java_abs_list_by_ids_param($model)
{
    $ii = 0;
    echo "String v_values";
    $ii++;
    return $ii;
}

function _java_abs_list_by_ids_param4use($model, $in_model = false)
{
    $ii = 0;
    echo "v_values";
    $ii++;
    return $ii;
}


/**
 * java抽象类--按照ID查询列表
 *
 * @param $model
 */
function java_abs_list_by_ids($model)
{

    if (!_java_db_header($model, "list_by_ids")) {
        return;
    }

    $uc_table = ucfirst($model['table_name']);

    _java_comment_header("根据一组ID查询列表，结构为hash map");
    _java_abs_list_by_ids_param_comment($model);
    _java_comment_footer();
    echo "public Vector<HashMap> listByIds(";
    _java_abs_list_by_ids_param($model);
    echo ") { return null; }\n";


    _java_comment_header("根据一组ID查询列表 结构为bean");
    _java_abs_list_by_ids_param_comment($model);
    _java_comment_footer();
    echo "public Vector<{$uc_table}Bean> listBeanByIds(";
    _java_abs_list_by_ids_param($model);
    echo ") { return null; }\n";

    _java_db_footer($model, "list_by_ids");
}