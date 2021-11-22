<?php

if (!defined("MYSQL_PROC")) {
    define('MYSQL_PROC', realpath(dirname(__FILE__)));
}
include_once(MYSQL_PROC . "/mysql_base.ini.php");


/**
 * mysql存储过程--获取计数，和list一致
 *
 * @param $model
 */
function mysql_create_proc_count($model)
{

    if (!_mysql_proc_header($model, "count")) {
        return;
    }
    echo "(";

    $ii = 0;
    foreach ($model['list_by'] as $key) {
        $ii++;
        $_prefix = _mysql_proc_warp($ii);
        _mysql_proc_param($key, $model['table_fields'][$key]['type'], $model['table_fields'][$key]['size'], $_prefix);
    }

    if (isset($model["list_kw"]) && count($model["list_kw"]) > 0) {
        $ii++;
        $_prefix = _mysql_proc_warp($ii);
        echo "{$_prefix} IN `v_kw` VARCHAR ( 255 )";
    }

    if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {
        $ii++;
        $_prefix = _mysql_proc_warp($ii);
        echo "{$_prefix} IN `v_date_from` VARCHAR ( 10 )";
        $ii++;
        $_prefix = _mysql_proc_warp($ii);
        echo "{$_prefix} IN `v_date_to` VARCHAR ( 10 )";
    }

    echo "\n)\n";
    echo "BEGIN\n";


    echo "SET @sql_query = 'SELECT COUNT(`{$model['count_key']}`) AS i_count FROM `t_{$model['table_name']}` WHERE 1=1 ';\n";


    foreach ($model['list_by'] as $key) {
        if ($model['table_fields'][$key]['type'] == "int") {
            echo "IF v_{$key} >= 0 THEN\n";
            echo "\tSET @sql_query = CONCAT( @sql_query, ' AND `{$key}` =  \'', v_{$key}, '\' ' );\n";
            echo "END IF;\n";
        } else {
            echo "IF v_{$key} != '' THEN\n";
            echo "\tSET @sql_query = CONCAT( @sql_query, ' AND `{$key}` =  \'', v_{$key}, '\' ' );\n";
            echo "END IF;\n";
        }
    }


    if (isset($model["list_kw"]) && count($model["list_kw"]) > 0) {

        echo "IF v_kw != '' THEN\n";

        echo "\tSET @sql_query = CONCAT( @sql_query, ' AND (\n";
        $jj = 0;
        foreach ($model["list_kw"] as $key) {
            $jj++;
            $_prefix3 = _mysql_proc_warp($jj, "OR");
            echo "{$_prefix3}LOCATE(\'',v_kw,'\',`{$key}`) > 0  ";
        }
        echo " )');\n";

        echo "END IF;\n";
    }

    if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {

        echo "IF v_date_from != '' AND  v_date_to != ''THEN\n";

        echo "\tSET @sql_query = CONCAT( @sql_query, ' AND (";
        $date_key = $model["list_date_from_to"];
        if ($model['table_fields'][$date_key]['type'] == "datetime") {
            echo "{$date_key} BETWEEN \'', v_date_from, ' 00:00:00\' AND \'', v_date_to,' 23:59:59\')'";
        } else {
            echo "{$date_key} BETWEEN \'', v_date_from, '\' AND \'', v_date_to,'\')'";
        }
        echo " );\n";

        echo "END IF;\n";
    }

    if (isset($model['table_fields']['flag'])) {

        echo "SET @sql_query = CONCAT( @sql_query, ' AND `flag` = \'n\'');\n";
    }

    echo "CALL p_debug('p_{$model['table_name']}_count', @sql_query);\n";

    echo "PREPARE stmt FROM @sql_query;\n";
    echo "EXECUTE stmt;\n";
    echo "COMMIT;\n";

    echo "END;\n";

    _mysql_proc_footer($model, "count");

}