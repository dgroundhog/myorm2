<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");


function _java_abs_add_param_comment($model)
{
    foreach ($model['table_fields'] as $key => $field) {
        if (!in_array($key, $model['add_keys'])) {
            continue;
        }
        echo "* @param v_{$key} {$field['name']}\n";
    }
    echo "* @return int\n";

}

function _java_abs_add_param($model)
{
    $ii = 0;
    foreach ($model['table_fields'] as $key => $field) {
        if (!in_array($key, $model['add_keys'])) {
            continue;
        }
        $ii++;
        $_prefix = _java_db_warp($ii);
        _java_db_param($key, $field['type'], $_prefix);
    }
    return $ii;
}

function _java_abs_add_param4use($model, $in_model = false)
{

    $ii = 0;

    foreach ($model['table_fields'] as $key => $field) {
        if (!in_array($key, $model['add_keys'])) {
            continue;
        }
        $ii++;
        $_prefix = _java_param_join($ii);
        echo _java_req2db_param($key, $field['type'], $_prefix, $in_model);
    }
}

/**
 * java抽象类--添加
 *
 * @param $model
 */
function java_abs_add($model)
{

    if (!_java_db_header($model, "add")) {
        return;
    }
    $uc_table = ucfirst($model['table_name']);

    _java_comment_header("插入数据");
    _java_abs_add_param_comment($model);
    _java_comment_footer();
    echo "public int add(";
    _java_abs_add_param($model);
    echo ") { return 0; }\n";


    _java_comment_header("插入数据通过bean");
    echo "* @param v_{$model['table_name']}Bean\n";
    echo "* @return int\n";
    _java_comment_footer();
    echo "public int addBean({$uc_table}Bean v_{$model['table_name']}Bean) \n";
    echo "{ return 0; }\n";

    _java_db_footer($model, "add");

}