<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_sum.php");


/**
 * java模型--计数器
 *
 * @param $model
 */
function java_model_sum($model)
{

    if (!_java_db_header($model, "sum")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("计数");
    _java_abs_sum_param_comment($model);
    _java_comment_footer();

    echo _tab(1) . "public static Vector<HashMap> sum(";
    $i_param = _java_abs_sum_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    echo _tab(2) . "Vector<HashMap> vList = DBFactory.get{$uc_table}().sum(";
    _java_abs_sum_param4use($model,true);
    echo ");\n";
    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "sum");

}