<?php

/**
 * 生产mysql 表格
 *
 * @param array $model
 */
function mysql_create_table($model) {
    $ii = 0;
    $i_field_size = count($model['table_fields']);

    echo "-- ----------------------------\n";
    echo "-- table structure for t_{$model['table_name']} [{$model['table_title']}] \n";
    echo "-- field count ({$i_field_size}) \n";
    echo "-- ----------------------------\n";
    echo "CREATE TABLE `t_{$model['table_name']}`\n(\n";

    $a_temp = array();
    foreach ($model['table_fields'] as $key => $field) {
        $ii++;
        switch ($field["type"]) {
            //整型
            case "int":
                $size = $field['size'];
                if ($size < 1 || $size > 255) {
                    $size = 11;
                }
                if (isset($field["auto_increment"]) && $field["auto_increment"] == "1") {
                    $a_temp[] = "`{$key}` int({$size}) NOT NULL AUTO_INCREMENT COMMENT '{$field['name']}'";
                } else {
                    if (isset($field["not_null"]) && $field["not_null"] == "1") {
                        $a_temp[] = "`{$key}` int({$size}) NOT NULL  COMMENT '{$field['name']}'";
                    } else {
                        $a_temp[] = "`{$key}` int({$size}) DEFAULT NULL  COMMENT '{$field['name']}'";
                    }
                }
                break;

            //单个字符
            case "char":
                $size = $field['size'];
                if ($size < 1 || $size > 255) {
                    $size = 1;
                }
                if (isset($field["not_null"]) && $field["not_null"] == "1") {
                    $a_temp[] = "`{$key}` char({$size}) NOT NULL  COMMENT '{$field['name']}'";
                } else {
                    $a_temp[] = "`{$key}` char({$size}) DEFAULT NULL  COMMENT '{$field['name']}'";
                }
                break;

            //字符串
            case "varchar":
                $size = $field['size'];
                if ($size < 1 || $size > 9999) {
                    $size = 255;
                }
                if (isset($field["not_null"]) && $field["not_null"] == "1") {
                    $a_temp[] = "`{$key}` varchar({$size}) NOT NULL  COMMENT '{$field['name']}'";
                } else {
                    $a_temp[] = "`{$key}` varchar({$size}) DEFAULT NULL  COMMENT '{$field['name']}'";
                }
                break;

            //其他丰富字段
            case "text":
            case "longtext":
            case "blob":
            case "longblob":
            case "date":
            case "time":
            case "datetime":
                if (isset($field["not_null"]) && $field["not_null"] == "1") {
                    $a_temp[] = "`{$key}` {$field["type"]} NOT NULL  COMMENT '{$field['name']}'";
                } else {
                    $a_temp[] = "`{$key}` {$field["type"]} DEFAULT NULL  COMMENT '{$field['name']}'";
                }
                break;

            //默认为255的字符串
            default :
                $a_temp[] = "`{$key}` varchar(255) DEFAULT NULL  COMMENT '{$field['name']}'";
                break;
        }
    }
    if (isset($model['primary_key']) && isset($model['table_fields'][$model['primary_key']])) {
        $a_temp[] = "PRIMARY KEY (`{$model['primary_key']}`)";
    }

    if (isset($model['unique_key']) && count($model['unique_key']) > 0) {
        foreach ($model['unique_key'] as $index_name => $a_index) {
            if (!is_array($a_index)) {
                continue;
            }
            $a_temp0 = array();
            $jj = 0;
          
            foreach ($a_index as $key) {
             
                if (isset($model['table_fields'][$key])) {
                    $jj++;
                    $a_temp0[] = "`{$key}`";
                }
            }
            $s_temp0 = implode(", ", $a_temp0);
            $a_temp[] = "UNIQUE uk_{$model['table_name']}_{$index_name} ({$s_temp0})";
        }
    }

    if (isset($model['index_key']) && count($model['index_key']) > 0) {
        foreach ($model['index_key'] as $index_name => $a_index) {
            if (!is_array($a_index)) {
                continue;
            }
            $a_temp0 = array();
            $jj = 0;
            foreach ($a_index as $key) {
                if (isset($model['table_fields'][$key])) {
                    $jj++;
                    $a_temp0[] = "`{$key}`";
                }
            }
            $s_temp0 = implode(", ", $a_temp0);
            $a_temp[] = "UNIQUE ik_{$model['table_name']}_{$index_name} ({$s_temp0})";
        }
    }

    echo _tab(1);
    echo implode(",\n" . _tab(1), $a_temp);
    echo "\n)";

    $charset = $model['db_conf']["charset"];
    if (null == $charset) {
        $charset = "utf8mb4";
    }

    echo "ENGINE=InnoDB\n";
    echo "DEFAULT CHARSET={$charset}\n";
    echo "COLLATE {$charset}_general_ci\n";
    echo "COMMENT='{$model['table_title']}表';";
}
