<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");

function _java_abs_fetch_param_comment($model)
{
    foreach ($model['fetch_by'] as $key) {
        echo "* @param v_{$key} {$model['table_fields'][$key]['name']}\n";
    }
    echo "* @return HashMap\n";
}

function _java_abs_fetch_param($model)
{
    $ii = 0;
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        $_prefix = _java_db_warp($ii);
        _java_db_param($key, $model['table_fields'][$key]['type'], $_prefix);
    }
    return $ii;
}

function _java_abs_fetch_param4use($model, $in_model = false)
{

    $ii = 0;
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        $_prefix = _java_param_join($ii);
        echo _java_req2db_param($key, $model['table_fields'][$key]['type'], $_prefix, $in_model);
    }
}

/**
 * java抽象类--获取一条数据
 *
 * @param $model
 */
function java_abs_fetch($model)
{

    if (!_java_db_header($model, "fetch")) {
        return;
    }

    _java_comment_header("取出数据为map");
    _java_abs_fetch_param_comment($model);
    _java_comment_footer();
    echo "public HashMap fetch(";
    _java_abs_fetch_param($model);
    echo ") { return null; }\n";


    $uc_table = ucfirst($model['table_name']);
    _java_comment_header("取出数据为Bean");
    _java_abs_fetch_param_comment($model);
    _java_comment_footer();
    echo "public {$uc_table}Bean fetchBean(";
    _java_abs_fetch_param($model);
    echo ") { return null; }\n";


    _java_db_footer($model, "fetch");
}