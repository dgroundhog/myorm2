<?php

if (!defined("ORACLE_PROC")) {
    define('ORACLE_PROC', realpath(dirname(__FILE__)));
}
include_once(ORACLE_PROC . "/oracle_base.ini.php");


/**
 * oracle存储过程--获取计数，和list一致
 *
 * @param $model
 */
function oracle_create_proc_count($model)
{

    if (!_oracle_proc_header($model, "count")) {
        return;
    }
    echo "(";

    $ii = 0;
    foreach ($model['list_by'] as $key) {
        $ii++;
        $_prefix = _oracle_proc_warp($ii);
        _oracle_proc_param($key, $model['table_fields'][$key]['type'], $model['table_fields'][$key]['size'], $_prefix);
    }

    if (isset($model["list_kw"]) && count($model["list_kw"]) > 0) {
        $ii++;
        $_prefix = _oracle_proc_warp($ii);
        echo "{$_prefix} IN `v_kw` VARCHAR ( 255 )";
    }

    if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {
        $ii++;
        $_prefix = _oracle_proc_warp($ii);
        echo "{$_prefix} IN `v_date_from` VARCHAR ( 10 )";
        $ii++;
        $_prefix = _oracle_proc_warp($ii);
        echo "{$_prefix} IN `v_date_to` VARCHAR ( 10 )";
    }

    echo "\n)\n";
    echo "BEGIN\n";


    echo "SET @sql_query = 'SELECT count(`{$model['count_key']}`) AS i_count FROM `t_{$model['table_name']}` WHERE 1=1 ';\n";

    foreach ($model['list_by'] as $key) {

        echo "IF v_{$key} != '' THEN\n";
        echo "\tSET @sql_query = CONCAT( @sql_query, ' AND `{$key}` =  \'', v_{$key}, '\' ' );\n";
        echo "END IF;\n";
    }

    if (isset($model["list_kw"]) && count($model["list_kw"]) > 0) {

        echo "IF v_kw != '' THEN\n";

        echo "\tSET @sql_query = CONCAT( @sql_query, ' AND (\n";
        $jj = 0;
        foreach ($model["list_kw"] as $key) {
            $jj++;
            $_prefix3 = _oracle_proc_warp($jj, "OR");
            echo "{$_prefix3}LOCATE(\'',v_kw,'\',`{$key}`) > 0  ";
        }
        echo " )');\n";

        echo "END IF;\n";
    }

    if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {

        echo "IF v_date_from != '' AND  v_date_to != ''THEN\n";

        echo "\tSET @sql_query = CONCAT( @sql_query, ' AND (\n";
        echo "{$model["list_date_from_to"]} BETWEEN \'', v_date_from, '\' AND \'', v_date_to,'\')'";
        echo " );\n";

        echo "END IF;\n";
    }

    if (isset($model['table_fields']['flag'])) {

        echo "SET @sql_query = CONCAT( @sql_query, ' AND `flag` = \'n\'');\n";
    }

    echo "call p_debug(@sql_query);\n";

    echo "PREPARE stmt FROM @sql_query;\n";
    echo "EXECUTE stmt;\n";
    echo "COMMIT;\n";

    echo "END;\n";

    _oracle_proc_footer($model, "count");

}