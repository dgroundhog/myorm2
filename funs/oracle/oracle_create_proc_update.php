<?php

if (!defined("ORACLE_PROC")) {
    define('ORACLE_PROC', realpath(dirname(__FILE__)));
}
include_once(ORACLE_PROC . "/oracle_base.ini.php");


/**
 * oracle存储过程--更新
 *
 * @param $model
 */
function oracle_create_proc_update($model)
{

    if (!_oracle_proc_header($model, "update")) {
        return;
    }

    echo "(";

    $ii = 0;
    foreach ($model['update_keys'] as $key) {
        $ii++;
        $_prefix = _oracle_proc_warp($ii);
        _oracle_proc_param($key, $model['table_fields'][$key]['type'], $model['table_fields'][$key]['size'], $_prefix);
    }
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

    echo "UPDATE `t_{$model['table_name']}` SET ";

    $ii = 0;
    foreach ($model['update_keys'] as $key) {
        $ii++;
        $_prefix = _oracle_proc_warp($ii);
        echo "{$_prefix} `{$key}`= v_{$key}";
    }

    if (in_array("utime", $model['table_fields']) && !in_array("utime", $model['update_keys'])) {
        $ii++;
        $_prefix = _oracle_proc_warp($ii);
        echo "{$_prefix} `utime`= NOW()";
    }

    echo " \nWHERE";

    $ii = 0;
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        $_prefix2 = _oracle_proc_warp($ii, "AND");
        echo "{$_prefix2} `{$key}`= v_{$key}";
    }
    //只有正常数据才能更新
    if (isset($model['table_fields']['flag'])) {
        $ii++;
        $_prefix2 = _oracle_proc_warp($ii, "AND");
        echo "{$_prefix2} `flag`='n'";
    }

    echo "\nLIMIT 1;";
    echo "\nCOMMIT;";

    echo "\nSELECT 1 INTO v_ret;";

    echo "\nEND;\n";
    _oracle_proc_footer($model, "update");

}