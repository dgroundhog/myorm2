<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");

function _java_abs_update_state_param_comment($model)
{

    echo "* @param v_state {$model['table_fields']['state']['name']}\n";

    foreach ($model['fetch_by'] as $key) {
        echo "* @param v_{$key} {$model['table_fields'][$key]['name']}\n";
    }

    if (isset($model['table_fields']['op_id2'])) {
        echo "* @param v_op_id2 更新操作员\n";
    }

    echo "* @return int\n";
}

function _java_abs_update_state_param($model)
{
    $ii = 0;
    $ii++;
    $_prefix = _java_db_warp($ii);
    if ($model['table_fields']['state']['type'] == "int") {
        echo "{$_prefix} int v_state";
    } else {
        echo "{$_prefix} String v_state";
    }


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

function _java_abs_update_state_param4use($model, $in_model = false)
{
    $ii = 0;
    $ii++;
    $_prefix = _java_param_join($ii);
    echo _java_req2db_param("state", $model['table_fields']["state"]['type'], $_prefix, $in_model);
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
 * java抽象类--更新状态
 *
 * @param $model
 */
function java_abs_update_state($model)
{

    if (!_java_db_header($model, "update_state")) {
        return;
    }

    _java_comment_header("更新状态");
    _java_abs_update_state_param_comment($model);
    _java_comment_footer();
    echo "public int updateState(";
    _java_abs_update_state_param($model);
    echo ") { return 0; }\n";


    _java_db_footer($model, "update_state");
}