<?php

/**
 * @param $msg
 * @return void
 */
function _mysql_comment($msg)
{
    if (is_array($msg)) {
        echo "-- ----------------------------\n";
        foreach ($msg as $s) {
            echo "-- {$s} \n";
        }
        echo "-- ----------------------------\n";
    } else {
        echo "-- {$msg}\n";
    }
}


/**
 * 公用存储过程头头
 * @param array $model
 * @param string $fun_name
 * @param string $fun_title
 * @param string $base_fun
 * @return string
 */
function _mysql_proc_header($model, $fun_name, $fun_title, $base_fun)
{

    if (!isset($model["{$base_fun}_enable"]) || !$model["{$base_fun}_enable"]) {
        echo "-- ----------------------------\n";
        echo "-- no define for procedure p_{$model['table_name']}_{$base_fun}::{$fun_name} \n";
        echo "-- ----------------------------\n";
        return null;
    }

    if ($fun_name != "" && $fun_name != "default" && $fun_name != $base_fun) {
        $fun = "{$base_fun}_{$fun_name}";
    } else {
        $fun = $base_fun;
    }

    echo "-- ----------------------------\n";
    echo "-- procedure structure for p_{$model['table_name']}_{$fun} \n";
    echo "-- desc : {$fun_title} \n";
    echo "-- ----------------------------\n";

    $user = $model['db_conf']["user"];
    $host = $model['db_conf']["host"];

    echo "DROP PROCEDURE IF EXISTS `p_{$model['table_name']}_{$fun}`;\n";
    echo "delimiter ;;\n";
    echo "CREATE DEFINER=`{$user}`@`{$host}` PROCEDURE `p_{$model['table_name']}_{$fun}`";
    echo "(";
    return "p_{$model['table_name']}_{$fun}";
}

/**
 * 存储过程的参数
 * @param array $a_temp
 */
function _mysql_proc_begin($a_temp)
{
    if (count($a_temp) > 1) {
        echo "\n";
        echo _tab(1);
        echo implode(",\n" . _tab(1), $a_temp);
        echo "\n";
    }
    if (count($a_temp) == 1) {
        echo $a_temp[0];
    }
    echo ")\n";
    echo "BEGIN\n";
}


/**
 * 公共存储过程尾巴
 * @param array $model
 * @param string $proc_name
 * @return void
 */
function _mysql_proc_footer($model, $proc_name)
{
    echo "END;\n";
    echo ";;\n";
    echo "delimiter ;\n";

    echo "-- ----------------------------\n";
    echo "-- end structure for {$proc_name} \n";
    echo "-- ----------------------------\n";
    echo "\n";
}

function _mysql_proc_warp2($ii, $join = ",")
{

    $s = "";
    switch ($join) {
        case "AND":
            $s = ($ii == 1) ? "\n  " : "\n   AND ";
            break;
        case "OR":
            $s = ($ii == 1) ? "\n  " : "\n   OR ";
            break;
        case "inline":
            $s = ($ii == 1) ? " " : " , ";
            break;
        case ",":
        default:
            $s = ($ii == 1) ? "\n  " : ",\n  ";

    }
    return $s;
}


/**
 * 获取php中参数的前缀
 *
 * @param $field_type
 * @return string
 */
function _mysql_proc_get_key_prefix($field_type)
{
    switch ($field_type) {
        //整型
        case "int":
            return "i";

        //字符字符串
        case "char":
        case "varchar":
        case "text":
        case "longtext":
            return "s";

        //大二进制
        case "blob":
        case "longblob":
            return "lb";

        //时间类型
        case "date":
        case "time":
        case "datetime":
            return "dt";

        //默认字符串
        default :
            return "s";
            break;
    }

}

/**
 * 公用存储过程头头
 * XXX 不考虑小数,如果是金钱，用分做单位
 *
 * @param array $model
 * @param string $key
 * @param string $append_for_update u/w
 *
 * @return string
 */
function _mysql_proc_param($model, $key, $append_for_update = "")
{

    $charset = $model['db_conf']["charset"];
    if (null == $charset) {
        $charset = "utf8mb4";
    }
    $p_type = $model['table_fields'][$key]['type'];
    $p_size = $model['table_fields'][$key]['size'];

    $prefix = _mysql_proc_get_key_prefix($p_type);

    if ($append_for_update != "") {
        $prefix = "{$prefix}_{$append_for_update}";
    }

    switch ($p_type) {
        case "text":
            return "IN `{$prefix}_{$key}` TEXT  CHARSET {$charset}";

        case "longtext":
            return "IN `{$prefix}_{$key}` LONGTEXT  CHARSET {$charset}";


        case "blob":
            return "IN `{$prefix}_{$key}` BLOB ";


        case "longblob":
            return "IN `{$prefix}_{$key}` LONGBLOB ";


        case "varchar":
            $size = $p_size;
            if ($size < 1 || $size > 9999) {
                $size = 255;
            }
            return "IN `{$prefix}_{$key}` VARCHAR ( {$size} )  CHARSET {$charset}";


        case "char":
            $size = $p_size;
            if ($size < 1 || $size > 255) {
                $size = 1;
            }
            return "IN `{$prefix}_{$key}` CHAR ( {$size} )  CHARSET {$charset}";


        case "date":
            return "IN `{$prefix}_{$key}` VARCHAR ( 10 )  CHARSET {$charset}";


        case "time":
        case "datetime":
            return "IN `{$prefix}_{$key}` VARCHAR ( 19 )  CHARSET {$charset}";


        case "int":
            return "IN `{$prefix}_{$key}` INT ";


        default:
            return "IN `{$prefix}_{$key}` VARCHAR ( 255 )  CHARSET {$charset}";

    }
}

/**
 * 获取大于小于的操作符号
 * @param string $ge_lt
 * @return string
 */
function _mysql_proc_gtelt($ge_lt)
{
    switch ($ge_lt) {
        case "gt":
            return ">";
        case "gte":
            return ">=";
        case "lt":
            return "<";
        case "lte":
            return "<=";
        default:
            return "=";
    }
}
