<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_drop.php");


/**
 * java模型类--删除2 清除数据
 *
 * @param $model
 */
function java_model_drop($model)
{

    if (!_java_db_header($model, "drop")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("清除数据");
    _java_abs_drop_param_comment($model);
    _java_comment_footer();
    echo "public static int drop(";
    $i_param = _java_abs_drop_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    echo _tab(2) . "int iRet = DBFactory.get{$uc_table}().drop(";
    _java_abs_drop_param4use($model,true);
    echo ");\n";
    echo _tab(2) . "return iRet;\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "drop");
}