<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");

function _java_abs_delete_param_comment($model)
{

    foreach ($model['fetch_by'] as $key) {
        echo "* @param v_{$key} {$model['table_fields'][$key]['name']}\n";
    }

    if (isset($model['table_fields']['op_id2'])) {
        echo "* @param v_op_id2 根性操作员\n";
    }

    echo "* @return int\n";
}

function _java_abs_delete_param($model)
{
    $ii = 0;
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        $_prefix = _java_db_warp($ii);
        _java_db_param($key, $model['table_fields'][$key]['type'], $_prefix);
    }

    if (isset($model['table_fields']['op_id2'])) {
        $ii++;
        $_prefix = _java_db_warp($ii);
        _java_db_param("op_id2", "varchar", $_prefix);
    }

    return $ii;

}

function _java_abs_delete_param4use($model, $in_model = false)
{

    $ii = 0;
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        $_prefix = _java_param_join($ii);
        echo _java_req2db_param($key, $model['table_fields'][$key]['type'], $_prefix, $in_model);
    }

    if (isset($model['table_fields']['op_id2'])) {
        $ii++;
        $_prefix = _java_param_join($ii);
        echo _java_req2db_param("op_id2", "varchar", $_prefix, $in_model);
    }
}

/**
 * java抽象类--删除
 *
 * @param $model
 */
function java_abs_delete($model)
{

    if (!_java_db_header($model, "delete")) {
        return;
    }

    _java_comment_header("删除数据");
    _java_abs_delete_param_comment($model);
    _java_comment_footer();
    echo "public int delete(";
    _java_abs_delete_param($model);
    echo ") { return 0; }\n";

    _java_db_footer($model, "delete");
}