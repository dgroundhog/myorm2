<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_delete.php");


/**
 * java模型类--删除
 *
 * @param $model
 */
function java_model_delete($model)
{

    if (!_java_db_header($model, "delete")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("删除数据");
    _java_abs_delete_param_comment($model);
    _java_comment_footer();
    echo "public static int delete(";
    $i_param = _java_abs_delete_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    echo _tab(2) . "int iRet = DBFactory.get{$uc_table}().delete(";
    _java_abs_delete_param4use($model,true);
    echo ");\n";

    echo _tab(2) . "return iRet;\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "delete");
}