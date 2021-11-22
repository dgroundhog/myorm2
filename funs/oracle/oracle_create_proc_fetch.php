<?php

if (!defined("ORACLE_PROC")) {
    define('ORACLE_PROC', realpath(dirname(__FILE__)));
}
include_once(ORACLE_PROC . "/oracle_base.ini.php");


/**
 * oracle存储过程--获取一条数据
 *
 * @param $model
 */
function oracle_create_proc_fetch($model)
{

    if (!_oracle_proc_header($model, "fetch")) {
        return;
    }

    echo "(";

    $ii = 0;
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        $_prefix = _oracle_proc_warp($ii);
        _oracle_proc_param($key, $model['table_fields'][$key]['type'], $model['table_fields'][$key]['size'], $_prefix);
    }

    echo "\n)\n";
    echo "BEGIN\n";

    echo "SELECT * FROM `t_{$model['table_name']}` WHERE ";

    $ii = 0;
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        $_prefix2 = _oracle_proc_warp($ii, "AND");
        echo "{$_prefix2} `{$key}`= v_{$key}";
    }

    if (isset($model['table_fields']['flag'])) {
        $ii++;
        $_prefix2 = _oracle_proc_warp($ii, "AND");
        echo "{$_prefix2} `flag`='n'";
    }
    echo " \nLIMIT 1;\n";
    echo " END;\n";

    _oracle_proc_footer($model, "fetch");
}