<?php

if (!defined("MYSQL_PROC")) {
    define('MYSQL_PROC', realpath(dirname(__FILE__)));
}
include_once(MYSQL_PROC . "/mysql_base.ini.php");


/**
 * mysql存储过程--删除1
 * @param array $model
 * @param string $delete_name
 * @param string $delete_title
 * @param array $a_delete_by
 * @param int $limit
 */
function _mysql_create_proc_delete($model, $delete_name, $delete_title, $a_delete_by, $limit)
{

    $proc_name = _mysql_proc_header($model, $delete_name, $delete_title, "delete");
    if (null == $proc_name) {
        return;
    }

    $jj = 0;
    foreach ($a_delete_by as $key) {
        $jj++;
        $a_temp[] = _mysql_proc_param($model, $key, "w");
    }
    $a_temp[] = "INOUT `v_affected_rows` INT";

    _mysql_proc_begin($a_temp);

    _mysql_comment("input  w count {$jj}");

    echo "DECLARE m_affected_rows INT;\n";
    echo "DECLARE s_affected_rows VARCHAR(12);\n";

    echo "DELETE FROM `t_{$model['table_name']}` WHERE ";


    $jj = 0;
    $a_temp = array();
    $a_temp[] = " 1 = 1";
    foreach ($a_delete_by as $key) {
        $jj++;

        $p_type = $model['table_fields'][$key]['type'];
        $prefix = _mysql_proc_get_key_prefix($p_type);
        $key2 = "{$prefix}_w_$key";
        $a_temp[] = "`{$key}` = {$key2}";
    }


    echo implode("\n" . _tab(1) . "AND ", $a_temp);

    if ($limit > 0) {
        echo "\n";
        echo "LIMIT {$limit};\n";
    }
    _mysql_comment("query w count {$jj}");


    echo "SET m_affected_rows = ROW_COUNT();\n";
    //echo "COMMIT;\n";
    echo "SET s_affected_rows = CONCAT( '' , m_affected_rows);\n";
    echo "CALL p_debug('{$proc_name}', s_affected_rows);\n";

    echo "SELECT m_affected_rows INTO v_affected_rows;\n";
    echo "SELECT m_affected_rows AS i_affected_rows;\n";

    _mysql_proc_footer($model, $proc_name);
}


/**
 * mysql存储过程--删除1
 *
 * @param $model
 */
function mysql_create_proc_delete($model)
{

    $a_delete_confs = _model_get_ok_delete($model);
    foreach ($a_delete_confs as $delete_name => $a_delete_conf) {
        $a_delete_by = $a_delete_conf['delete_by'];// 删除依据
        $limit = $a_delete_conf['limit'];// 删除依据
        _mysql_create_proc_delete($model, $delete_name, $a_delete_conf['delete_title'], $a_delete_by, $limit);
    }
}