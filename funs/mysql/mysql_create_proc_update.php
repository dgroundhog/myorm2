<?php

if (!defined("MYSQL_PROC")) {
    define('MYSQL_PROC', realpath(dirname(__FILE__)));
}
include_once(MYSQL_PROC . "/mysql_base.ini.php");


/**
 * mysql存储过程--更新
 *
 * @param array $model
 * @param string $update_name
 * @param string $update_title
 * @param array $update_keys
 * @param array $update_by
 * @param int $limit
 */
function _mysql_create_proc_update($model, $update_name = "default", $update_title, $update_keys, $update_by, $limit)
{

    $proc_name = _mysql_proc_header($model, $update_name, $update_title, "update");
    if (null == $proc_name) {
        return;
    }

    $ii = 0;
    $a_temp = array();
    foreach ($update_keys as $key) {
        $ii++;
        $a_temp[] = _mysql_proc_param($model, $key, "u");
    }

    $jj = 0;
    foreach ($update_by as $key) {
        $jj++;
        $a_temp[] = _mysql_proc_param($model, $key, "w");
    }
    $a_temp[] = "INOUT `v_affected_rows` INT";

    _mysql_proc_begin($a_temp);

    _mysql_comment("input u count {$ii} w count {$jj}");

    echo "DECLARE m_affected_rows INT;\n";
    echo "DECLARE s_affected_rows VARCHAR(12);\n";
    echo "UPDATE `t_{$model['table_name']}` SET ";

    $ii = 0;
    $a_temp = array();
    foreach ($update_keys as $key) {
        $ii++;
        $p_type = $model['table_fields'][$key]['type'];
        $prefix = _mysql_proc_get_key_prefix($p_type);
        $key2 = "{$prefix}_u_$key";
        $a_temp[] = "`{$key}` = {$key2}";
    }

    if (isset($model['table_fields']["utime"]) && !in_array("utime", $update_keys)) {
        $a_temp[] = "`utime` = NOW()";
    }

    if (count($a_temp) > 1) {
        echo "\n";
        echo _tab(1);
        echo implode(",\n" . _tab(1), $a_temp);
    }
    if (count($a_temp) == 1) {
        echo $a_temp[0];
    }

    echo "\nWHERE ";

    $jj = 0;
    $a_temp = array();
    $a_temp[] = " 1 = 1";
    foreach ($update_by as $key) {
        $jj++;

        $p_type = $model['table_fields'][$key]['type'];
        $prefix = _mysql_proc_get_key_prefix($p_type);
        $key2 = "{$prefix}_w_$key";
        $a_temp[] = "`{$key}` = {$key2}";
    }

    //只有正常数据才能更新
    if (isset($model['table_fields']['flag'])) {
        $a_temp[] = "`flag` = 'n'";
    }

    echo implode("\n" . _tab(1) . "AND ", $a_temp);

    if ($limit > 0) {
        echo "\n";
        echo "LIMIT {$limit};\n";
    }
    _mysql_comment("query u count {$ii} w count {$jj}");


    echo "SET m_affected_rows = ROW_COUNT();\n";
    //echo "COMMIT;\n";
    echo "SET s_affected_rows = CONCAT( '' , m_affected_rows);\n";
    echo "CALL p_debug('{$proc_name}', s_affected_rows);\n";

    echo "SELECT m_affected_rows INTO v_affected_rows;\n";
    echo "SELECT m_affected_rows AS i_affected_rows;\n";


    _mysql_proc_footer($model, $proc_name);

}


/**
 * mysql存储过程--更新
 *
 * @param $model
 */
function mysql_create_proc_update($model)
{
    $a_update_confs = _model_get_ok_update($model);
    foreach ($a_update_confs as $update_name => $a_update_conf) {
        $s_update_title = $a_update_conf['update_title'];//更新的标题
        $a_update_keys = $a_update_conf['update_keys'];//更新的内容
        $a_update_by = $a_update_conf['update_by'];//更新依据
        $limit = $a_update_conf['limit'];//更新依据
        _mysql_create_proc_update($model, $update_name, $s_update_title, $a_update_keys, $a_update_by, $limit);
    }
}