<?php

if (!defined("MYSQL_PROC")) {
    define('MYSQL_PROC', realpath(dirname(__FILE__)));
}
include_once(MYSQL_PROC . "/mysql_base.ini.php");

/**
 * mysql存储过程--获取一条数据
 *
 * @param array $model
 * @param string $fetch_name
 * @param string $fetch_title
 * @param array $fetch_by
 */
function _mysql_create_proc_fetch($model, $fetch_name, $fetch_title, $fetch_by)
{
    $proc_name = _mysql_proc_header($model, $fetch_name, $fetch_title, "fetch");
    if (null == $proc_name) {
        return;
    }

    $ii = 0;
    $a_temp = array();
    foreach ($fetch_by as $key) {
        $ii++;
        $a_temp[] = _mysql_proc_param($model, $key);
    }
    _mysql_proc_begin($a_temp);

    echo "SELECT * FROM `t_{$model['table_name']}` WHERE ";

    $ii = 0;
    $a_temp = array();
    $a_temp[] = " 1 = 1";
    foreach ($fetch_by as $key) {
        $ii++;
        $prefix = _mysql_proc_get_key_prefix($model["table_fields"][$key]['type']);
        $a_temp[] = "`{$key}` = `{$prefix}_{$key}`";
    }

    if (isset($model['table_fields']['flag'])) {
        $ii++;
        $a_temp[] = "`flag`='n'";
    }

    echo implode("\n" . _tab(1) . "AND ", $a_temp);
    echo "\n";
    echo "LIMIT 1;\n";
    //echo "CALL p_debug('{$proc_name}', '1');\n";

    _mysql_proc_footer($model, $proc_name);
}

/**
 *  获取一条数据
 *
 * @param $model
 */
function mysql_create_proc_fetch($model)
{
    /**
     * 默认主键查询
     */
    $fetch_by = _model_get_ok_fetch_by($model);
    if (count($fetch_by) > 0) {
        _mysql_create_proc_fetch($model, "default", "默认主键查询", $fetch_by);
        /**
         * 其他可能的主键查询单条语句
         * fetch_by_other=>
         *      ----fetch_name  => fetch_title
         *                      => fetch_by
         */
        $a_fetch_by_other = _model_get_ok_other_fetch_by($model);

        foreach ($a_fetch_by_other as $fetch_name => $a_vv) {
            $fetch_title = $a_vv['fetch_title'];
            $fetch_by = $a_vv['fetch_by'];
            _mysql_create_proc_fetch($model, $fetch_name, $fetch_title, $fetch_by);
        }
    }
}
