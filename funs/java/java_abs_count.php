<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");

function _java_abs_count_param_comment($model)
{
    foreach ($model['list_by'] as $key) {
        echo "* @param v_{$key} {$model['table_fields'][$key]['name']}\n";
    }

    if (isset($model["list_kw"]) && count($model["list_kw"]) > 0) {
        echo "* @param v_kw 搜索关键字\n";
    }

    if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {
        echo "* @param v_date_from 开始日期\n";
        echo "* @param v_date_to 结束日期\n";
    }
    echo "* @return int\n";
}

function _java_abs_count_param($model)
{
    $ii = 0;
    foreach ($model['list_by'] as $key) {
        $ii++;
        $_prefix = _java_db_warp($ii);
        _java_db_param($key, $model['table_fields'][$key]['type'], $_prefix);
    }

    if (isset($model["list_kw"]) && count($model["list_kw"]) > 0) {
        $ii++;
        $_prefix = _java_db_warp($ii);
        echo "{$_prefix} String v_kw";
    }

    if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {
        $ii++;
        $_prefix = _java_db_warp($ii);
        echo "{$_prefix} String v_date_from";
        $ii++;
        $_prefix = _java_db_warp($ii);
        echo "{$_prefix} String v_date_to";
    }
    return $ii;
}

function _java_abs_count_param4use($model, $in_model = false)
{
    $ii = 0;

    foreach ($model['list_by'] as $key) {
        $ii++;
        $_prefix = _java_param_join($ii);
        echo _java_req2db_param($key, $model['table_fields'][$key]['type'], $_prefix, $in_model);
    }

    if (isset($model["list_kw"]) && count($model["list_kw"]) > 0) {


        $ii++;
        $_prefix = _java_param_join($ii);
        echo "{$_prefix} v_kw";
    }

    if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {


        $ii++;
        $_prefix = _java_param_join($ii);
        echo "{$_prefix} v_date_from";

        $ii++;
        $_prefix = _java_param_join($ii);
        echo "{$_prefix} v_date_to";

    }
}

/**
 * java抽象类--计数器
 *
 * @param $model
 */
function java_abs_count($model)
{

    if (!_java_db_header($model, "count")) {
        return;
    }

    _java_comment_header("计数");
    _java_abs_count_param_comment($model);
    _java_comment_footer();

    echo "public int count(";
    _java_abs_count_param($model);
    echo ") { return 0; }\n";

    _java_db_footer($model, "count");

}