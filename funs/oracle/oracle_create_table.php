<?php
/**
 * 生产oracle 表格
 *
 * @param $model
 */
function oracle_create_table($model)
{

    echo "-- ----------------------------\n";
    echo "-- Table structure for t_{$model['table_name']} \n";
    echo "-- both table and sql \n";
    echo "-- ----------------------------\n";

    echo "CREATE SEQUENCE seq_{$model['table_name']} MINVALUE 1 MAXVALUE 9999999 START WITH 1 INCREMENT BY 1 NOCACHE";

    echo "CREATE TABLE t_{$model['table_name']} (";

    $ii = 0;
    $i_field_size = count($model['table_fields']);
    $_prefix = "\n";
    foreach ($model['table_fields'] as $key => $field) {
        $ii++;
        $_prefix = _oracle_proc_warp($ii);
        switch ($field["type"]) {
            case "int":
                $size = $field['size'];
                if ($size < 1 || $size > 255) {
                    $size = 11;
                }
                if (isset($field["auto_increment"]) && $field["auto_increment"] == "1") {
                    echo "{$_prefix}{$key} int({$size}) NOT NULL AUTO_INCREMENT COMMENT '{$field['name']}'";
                } else {
                    if (isset($field["not_null"]) && $field["not_null"] == "1") {
                        echo "{$_prefix}{$key} int({$size}) NOT NULL  COMMENT '{$field['name']}'";
                    } else {
                        echo "{$_prefix}{$key} int({$size}) DEFAULT NULL  COMMENT '{$field['name']}'";
                    }
                }
                break;

            case "char":
                $size = $field['size'];
                if ($size < 1 || $size > 255) {
                    $size = 1;
                }
                if (isset($field["not_null"]) && $field["not_null"] == "1") {
                    echo "{$_prefix}{$key} char({$size}) NOT NULL  COMMENT '{$field['name']}'";
                } else {
                    echo "{$_prefix}{$key} char({$size}) DEFAULT NULL  COMMENT '{$field['name']}'";
                }

                break;

            case "varchar":
                $size = $field['size'];
                if ($size < 1 || $size > 9999) {
                    $size = 255;
                }
                if (isset($field["not_null"]) && $field["not_null"] == "1") {
                    echo "{$_prefix}{$key} varchar({$size}) NOT NULL  COMMENT '{$field['name']}'";
                } else {
                    echo "{$_prefix}{$key} varchar({$size}) DEFAULT NULL  COMMENT '{$field['name']}'";
                }
                break;

            case "text":
            case "blob":
            case "longblob":
            case "date":
            case "time":
            case "datetime":
                if (isset($field["not_null"]) && $field["not_null"] == "1") {
                    echo "{$_prefix}{$key} {$field["type"]} NOT NULL  COMMENT '{$field['name']}'";
                } else {
                    echo "{$_prefix}{$key} {$field["type"]} DEFAULT NULL  COMMENT '{$field['name']}'";
                }
                break;

            default :
                echo "{$_prefix}{$key} varchar(32) DEFAULT NULL  COMMENT '{$field['name']}'";
                break;
        }
    }
    if (isset($model['primary_key']) && isset($model['table_fields'][$model['primary_key']])) {
        echo "{$_prefix}PRIMARY KEY ({$model['primary_key']})";
    }

    if (isset($model['unique_key']) && count($model['unique_key']) > 0) {
        foreach ($model['unique_key'] as $index_name => $a_index) {
            if (!is_array($a_index)) {
                continue;
            }
            echo "{$_prefix}UNIQUE uk_{$model['table_name']}_{$index_name} (";
            $jj = 0;
            foreach ($a_index as $key) {
                if (isset($model['table_fields'][$key])) {
                    $jj++;
                    $_prefix2 = _oracle_proc_warp($jj, "inline");
                    echo "{$_prefix2}{$key}";
                }
            }
            echo ")";
        }
    }

    if (isset($model['index_key']) && count($model['index_key']) > 0) {
        foreach ($model['index_key'] as $index_name => $a_index) {
            echo "{$_prefix}KEY ik_{$model['table_name']}_{$index_name} (";
            $jj = 0;
            foreach ($a_index as $key) {
                if (isset($model['table_fields'][$key])) {
                    $jj++;
                    $_prefix2 = _oracle_proc_warp($jj, "inline");
                    echo "{$_prefix2}{$key}";
                }
            }
            echo ")";
        }
    }


    echo "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='{$model['table_title']}表';\n";

}