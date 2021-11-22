<?php


function _oracle_proc_header($model, $fun)
{

    if (!isset($model["{$fun}_enable"]) || !$model["{$fun}_enable"]) {
        echo "-- ----------------------------\n";
        echo "-- PROCEDURE NO define for p_{$model['table_name']}_{$fun} \n";
        echo "-- ----------------------------\n";
        return false;
    }

    $datetime = date("YYYY-mm-dd HH:ii:ss", time());

    echo "-- ----------------------------\n";
    echo "-- BEGIN DDL for p_{$model['table_name']}_{$fun} \n";
    echo "-- auto generate at {$datetime} \n";
    echo "-- ----------------------------\n";

    $user = $model['db_conf']["user"];
    $host = $model['db_conf']["host"];


    echo "SET DEFINE OFF;\n";
    echo "CREATE OR REPLACE PROCEDURE \"p_{$model['table_name']}_{$fun}\"";

    return true;
}

function _oracle_proc_footer($model, $fun)
{

    echo " END p_{$model['table_name']}_{$fun};\n";
    echo " /\n";

    echo "-- ----------------------------\n";
    echo "-- END DDL for p_{$model['table_name']}_{$fun} \n";
    echo "-- ----------------------------\n";
}

function _oracle_proc_warp($ii, $join = ",")
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


function _oracle_proc_param($key, $p_type, $p_size, $_prefix)
{

    switch ($p_type) {
        case "text":
            echo "{$_prefix} IN v_{$key} CLOB ";
            break;

        case "blob":
        case "longblob":
            echo "{$_prefix} IN v_{$key} BLOB ";
            break;

        case "varchar":
            $size = $p_size;
            if ($size < 1 || $size > 4000) {
                $size = 255;
            }
            echo "{$_prefix} IN v_{$key} VARCHAR2 ( {$size} )";
            break;

        case "char":
            $size = $p_size;
            if ($size < 1 || $size > 255) {
                $size = 1;
            }
            echo "{$_prefix} IN v_{$key} CHAR ( {$size} ) ";
            break;

        case "date":
            echo "{$_prefix} IN v_{$key} DATE ";
            break;

        case "time":
        case "datetime":
            echo "{$_prefix} IN v_{$key} VARCHAR2 ( 19 ) ";
            break;

        case "int":
            echo "{$_prefix} IN v_{$key} NUMBER ";
            break;

        default:
            echo "{$_prefix} IN v_{$key} VARCHAR2 ( 255 ) ";
            break;
    }
}


