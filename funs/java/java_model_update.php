<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_update.php");


/**
 * java模型类--更新
 *
 * @param $model
 */
function java_model_update($model)
{

    if (!_java_db_header($model, "update")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("更新数据");
    _java_abs_update_param_comment($model);
    _java_comment_footer();
    echo "public static int update(";
    $i_param = _java_abs_update_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    echo _tab(2) . "return DBFactory.get{$uc_table}().update(";
    _java_abs_update_param4use($model,true);
    echo ");\n";

    echo _tab(1) . "}";


    _java_comment_header("通过bean更新数据");
    echo "* @param v_{$model['table_name']}Bean\n";
    echo "* @return int\n";
    _java_comment_footer();
    echo "public int updateBean({$uc_table}Bean v_{$model['table_name']}Bean) \n";
    echo _tab(1) . "{\n";
    echo _tab(2) . "return DBFactory.get{$uc_table}().updateBean(v_{$model['table_name']}Bean);\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "update");
}