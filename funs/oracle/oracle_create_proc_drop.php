<?php

if (!defined("ORACLE_PROC")) {
    define('ORACLE_PROC', realpath(dirname(__FILE__)));
}
include_once(ORACLE_PROC . "/oracle_base.ini.php");


/**
 *oracle存储过程--删除2
 *
 * @param $model
 */
function oracle_create_proc_drop($model)
{

    if (!_oracle_proc_header($model, "drop")) {
        return;
    }

    echo "(";

    $ii = 0;
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        $_prefix = _oracle_proc_warp($ii);
        _oracle_proc_param($key, $model['table_fields'][$key]['type'], $model['table_fields'][$key]['size'], $_prefix);
    }

    $ii++;
    $_prefix = _oracle_proc_warp($ii);
    echo "{$_prefix} INOUT `v_ret` INT";

    echo "\n)\n";
    echo "BEGIN\n";


    echo "DELETE FROM `t_{$model['table_name']}` WHERE ";

    $ii = 0;
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        $_prefix2 = _oracle_proc_warp($ii, "AND");
        echo "{$_prefix2} `{$key}`= v_{$key}";
    }

    echo "\nLIMIT 1;";
    echo "\nCOMMIT;";

    echo "\nSELECT 1 INTO v_ret;";

    echo "\nEND;\n";


    _oracle_proc_footer($model, "drop");


}