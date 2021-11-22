<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");

function _java_abs_update_param_comment($model)
{

    foreach ($model['update_keys'] as $key) {
        echo "* @param v_{$key} {$model['table_fields'][$key]['name']}\n";
    }
    foreach ($model['fetch_by'] as $key) {
        echo "* @param v_{$key} {$model['table_fields'][$key]['name']}\n";
    }

    echo "* @return int\n";
}

function _java_abs_update_param($model)
{
    $ii = 0;
    foreach ($model['update_keys'] as $key) {
        $ii++;
        $_prefix = _java_db_warp($ii);
        _java_db_param($key, $model['table_fields'][$key]['type'], $_prefix);
    }
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        $_prefix = _java_db_warp($ii);
        _java_db_param($key, $model['table_fields'][$key]['type'], $_prefix);
    }
    return $ii;

}

function _java_abs_update_param4use($model, $in_model = false)
{
    $ii = 0;
    foreach ($model['update_keys'] as $key) {
        $ii++;
        $_prefix = _java_param_join($ii);
        echo _java_req2db_param($key, $model['table_fields'][$key]['type'], $_prefix, $in_model);
    }
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        $_prefix = _java_param_join($ii);
        echo _java_req2db_param($key, $model['table_fields'][$key]['type'], $_prefix, $in_model);
    }
}

/**
 * java抽象类--更新
 *
 * @param $model
 */
function java_abs_update($model)
{

    if (!_java_db_header($model, "update")) {
        return;
    }
    $uc_table = ucfirst($model['table_name']);

    _java_comment_header("更新数据");
    _java_abs_update_param_comment($model);
    _java_comment_footer();
    echo "public int update(";
    _java_abs_update_param($model);
    echo ") { return 0; }\n";


    _java_comment_header("通过bean更新数据");
    echo "* @param v_{$model['table_name']}Bean\n";
    echo "* @return int\n";
    _java_comment_footer();
    echo "public int updateBean({$uc_table}Bean v_{$model['table_name']}Bean) \n";
    echo "{ return 0; }\n";

    _java_db_footer($model, "update");
}