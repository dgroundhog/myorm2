<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_count.php");


/**
 * java模型--计数器
 *
 * @param $model
 */
function java_model_count($model)
{

    if (!_java_db_header($model, "count")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("计数");
    _java_abs_count_param_comment($model);
    _java_comment_footer();

    echo "public static int count(";
    $i_param = _java_abs_count_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";


    echo _tab(2) . "int iTotal = DBFactory.get{$uc_table}().count(";
    _java_abs_count_param4use($model,true);
    echo ");\n";
    echo _tab(2) . "return iTotal;\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "count");

}