<?php

if (!defined("MYSQL_PROC")) {
    define('MYSQL_PROC', realpath(dirname(__FILE__)));
}
include_once(MYSQL_PROC . "/mysql_base.ini.php");

/**
 * mysql存储过程--添加
 *
 * @param array $model
 */
function mysql_create_proc_add($model)
{

    $proc_name = _mysql_proc_header($model, "add", "插入数据", "add");
    if (null == $proc_name) {
        return;
    }

    $ii = 0;
    $a_temp = array();
    foreach ($model['table_fields'] as $key => $field) {
        if (!in_array($key, $model['add_keys'])) {
            continue;
        }
        $ii++;
        $a_temp[] = _mysql_proc_param($model, $key);
    }

    if ($model['add_will_return_new_id'] == "1") {
        $ii++;
        $a_temp[] = "INOUT `v_new_id` INT";
    }
    _mysql_proc_begin($a_temp);

    echo "DECLARE m_new_id INT;\n";

    echo "INSERT INTO `t_{$model['table_name']}` \n(\n";

    $ii = 0;
    $a_temp = array();
    foreach ($model['table_fields'] as $key => $field) {
        if ($key == "id") {
            continue;
        }
        $ii++;
        $a_temp[] = "`{$key}`";
    }
    echo _tab(1);
    echo implode(",\n" . _tab(1), $a_temp);
    echo "\n) \nVALUES\n(\n";


    $ii = 0;
    $a_temp = array();
    foreach ($model['table_fields'] as $key => $field) {
        if ($key == "id") {
            continue;
        }
        $ii++;

        if (!in_array($key, $model['add_keys'])) {
            //预置值
            switch ($key) {
                case "flag":
                    $a_temp[] = "'n'";
                    break;

                case "state":
                    if (isset($field['default_value']) && $field['default_value'] != null) {
                        if ($field['type'] == "int") {
                            $a_temp[] = "{$field['default_value']}";
                        } else {
                            $a_temp[] = "'{$field['default_value']}'";
                        }
                    } else {
                        if ($field['type'] == "int") {
                            $a_temp[] = "0";
                        } else {
                            $a_temp[] = "'n'";
                        }
                    }
                    break;

                case "ctime":
                case "utime":
                    $a_temp[] = "NOW()";
                    break;

                default:
                    if (isset($field['default_value']) && $field['default_value'] != null) {
                        if ($field['type'] == "int") {
                            $a_temp[] = "{$field['default_value']}";
                        } else {
                            $a_temp[] = "'{$field['default_value']}'";
                        }
                    } else {
                        $a_temp[] = "''";
                    }
                    break;
            }
        } else {
            $prefix = _mysql_proc_get_key_prefix($field['type']);
            $a_temp[] = "`{$prefix}_{$key}`";
        }
    }
    echo _tab(1);
    echo implode(",\n" . _tab(1), $a_temp);

    echo "\n);\n";
    echo "SET m_new_id = LAST_INSERT_ID();\n";
    //echo "COMMIT;\n";
    echo "SET @s_new_id = CONCAT( '', m_new_id);\n";
    echo "CALL p_debug('{$proc_name}', @s_new_id);\n";

    if ($model['add_will_return_new_id'] == "1") {
        echo "SELECT m_new_id INTO v_new_id;\n";
        echo "SELECT m_new_id AS i_new_id;\n";
    }


    _mysql_proc_footer($model, $proc_name);
}