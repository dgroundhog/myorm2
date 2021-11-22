<?php

if (!defined("ORACLE_PROC")) {
    define('ORACLE_PROC', realpath(dirname(__FILE__)));
}
include_once(ORACLE_PROC . "/oracle_base.ini.php");


/**
 * oracle存储过程--获取一组id的数据
 *
 * @param $model
 */
function oracle_create_proc_list_by_ids($model)
{
    if (!$model['list_by_ids_enable'] || $model['list_by_ids_key'] == "" || !in_array($model['list_by_ids_key'], $model['table_fields'])) {
        return;
    }

    if (!_oracle_proc_header($model, "list_by_ids")) {
        return;
    }

    echo "(\n";
    echo "IN `v_values` VARCHAR ( 9999 )";
    echo "\n)\n";
    echo "BEGIN\n";

    echo "SET @sql_query = 'SELECT * FROM`t_{$model['table_name']}` WHERE '; \n";

    echo "IF v_values=\'\' THEN\n";
    echo "\tSET @sql_query = CONCAT( @sql_query, '1=1 LIMIT 10' );\n";
    echo "ELSE\n";
    echo "\tSET @sql_query = CONCAT( @sql_query, ' {$model['list_by_ids_key']} IN(',v_values,') ');\n";
    echo "END IF;\n";

    echo "call p_debug(@sql_query);\n";
    echo "PREPARE stmt FROM @sql_query;\n";
    echo "EXECUTE stmt;\n";
    echo "COMMIT;\n";
    echo "END;\n";

    _oracle_proc_footer($model, "list_by_ids");


}