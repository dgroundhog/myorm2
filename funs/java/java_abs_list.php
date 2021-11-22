<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");

function _java_abs_list_param_comment($model)
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

    if (isset($model["list_has_order"]) && $model["list_has_order"] != false) {
        echo "* @param v_order_by 排序字段\n";
        echo "* @param v_order_dir 排序方式\n";
    }

    echo "* @param v_page 页码\n";
    echo "* @param v_page_size 分页大小\n";

    echo "* @return Vector\n";
}

function _java_abs_list_param($model)
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

    if (isset($model["list_has_order"]) && $model["list_has_order"] != false) {
        $ii++;
        $_prefix = _java_db_warp($ii);
        echo "{$_prefix} String v_order_by";
        $ii++;
        $_prefix = _java_db_warp($ii);
        echo "{$_prefix} String v_order_dir";
    }

    $ii++;
    $_prefix = _java_db_warp($ii);
    echo "{$_prefix} int v_page";
    $ii++;
    $_prefix = _java_db_warp($ii);
    echo "{$_prefix} int v_page_size";

    return $ii;
}

/**
 * 参数使用
 * @param $model
 * @return int
 */
function _java_abs_list_param4use($model, $in_model = false)
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

    if (isset($model["list_has_order"]) && $model["list_has_order"] != false) {

        $ii++;
        $_prefix = _java_param_join($ii);
        echo "{$_prefix} v_order_by";

        $ii++;
        $_prefix = _java_param_join($ii);
        echo "{$_prefix} v_order_dir";
    }

    if (!$in_model) {
        $ii++;
        $_prefix = _java_param_join($ii);
        echo "{$_prefix} i_page";

        $ii++;
        $_prefix = _java_param_join($ii);
        echo "{$_prefix} i_page_size";
    } else {
        $ii++;
        $_prefix = _java_param_join($ii);
        echo "{$_prefix} v_page";

        $ii++;
        $_prefix = _java_param_join($ii);
        echo "{$_prefix} v_page_size";
    }


    return $ii;
}

/**
 * java抽象类--查询列表
 *
 * @param $model
 */
function java_abs_list($model)
{

    if (!_java_db_header($model, "list")) {
        return;
    }

    $uc_table = ucfirst($model['table_name']);

    _java_comment_header("查询列表，结构为hash map");
    _java_abs_list_param_comment($model);
    _java_comment_footer();
    echo "public Vector<HashMap> list(";
    _java_abs_list_param($model);
    echo ") { return null; }\n";


    _java_comment_header("查询列表 结构为bean");
    _java_abs_list_param_comment($model);
    _java_comment_footer();
    echo "public Vector<{$uc_table}Bean> listBean(";
    _java_abs_list_param($model);
    echo ") { return null; }\n";

    _java_db_footer($model, "list");
}

/**
 * java抽象类--查询列表
 *
 * @param $model
 */
function java_abs_list_basic($model)
{

    if (!_java_db_header($model, "list_basic")) {
        return;
    }

    $uc_table = ucfirst($model['table_name']);

    _java_comment_header("查询列表，结构为hash map");
    _java_abs_list_param_comment($model);
    _java_comment_footer();
    echo "public Vector<HashMap> listBasic(";
    _java_abs_list_param($model);
    echo ") { return null; }\n";


    _java_comment_header("查询列表 结构为bean");
    _java_abs_list_param_comment($model);
    _java_comment_footer();
    echo "public Vector<{$uc_table}Bean> listBasicBean(";
    _java_abs_list_param($model);
    echo ") { return null; }\n";

    _java_db_footer($model, "list");
}