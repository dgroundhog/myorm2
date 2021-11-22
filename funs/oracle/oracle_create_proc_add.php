<?php

if (!defined("ORACLE_PROC")) {
    define('ORACLE_PROC', realpath(dirname(__FILE__)));
}
include_once(ORACLE_PROC . "/oracle_base.ini.php");


/**
 * oracle存储过程--添加
 *
 * @param $model
 */
function oracle_create_proc_add($model)
{

    if (!_oracle_proc_header($model, "add")) {
        return;
    }

    echo "(";

    $ii = 0;
    foreach ($model['table_fields'] as $key => $field) {

        if (!in_array($key, $model['add_keys'])) {
            continue;
        }
        $ii++;
        $_prefix = _oracle_proc_warp($ii);
        _oracle_proc_param($key, $field['type'], $field['size'], $_prefix);

    }

    if ($model['add_will_return_new_id'] == "1") {
        $ii++;
        $_prefix = _oracle_proc_warp($ii);
        echo "{$_prefix} INOUT v_new_id INT";
    }

    echo "\n) AS\n";
    if ($model['add_will_return_new_id'] == "1") {
        echo "m_new_id INT;\n";
    }
    echo "BEGIN\n";
    echo "_NOW := 0 ;\n";
    echo "SELECT sysdate as _NOW from dual ;\n";
    echo "INSERT INTO t_{$model['table_name']} (";


    $ii = 0;
    foreach ($model['table_fields'] as $key => $field) {
        if ($key == "id") {
            continue;
        }
        $ii++;
        $_prefix = _oracle_proc_warp($ii);
        echo "{$_prefix}{$key}";
    }

    echo "\n) \nVALUES(";

    $ii = 0;
    foreach ($model['table_fields'] as $key => $field) {
        if ($key == "id") {
            continue;
        }
        $ii++;
        $_prefix = _oracle_proc_warp($ii);

        if (!in_array($key, $model['add_keys'])) {
            switch ($key) {
                case "flag":
                    echo "{$_prefix} 'n' ";
                    break;

                case "state":
                    echo "{$_prefix} 'n' ";
                    break;

                case "ctime":
                case "utime":
                    echo "{$_prefix} _NOW ";
                    break;

                default:
                    echo "{$_prefix} '' ";
                    break;
            }
        } else {
            echo "{$_prefix} v_{$key} ";
        }

    }
    echo "\n);\n";
    echo "COMMIT;\n";

    if ($model['add_will_return_new_id'] == "1") {
        echo "SET m_new_id = LAST_INSERT_ID();\n";
        echo "SELECT m_new_id INTO v_new_id;\n";
        echo "SELECT m_new_id AS new_id;\n";
    }

    echo "\nEND;\n";

    _oracle_proc_footer($model, "add");

}