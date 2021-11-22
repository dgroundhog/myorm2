<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_update_state.php");


/**
 * java抽象类--更新状态
 *
 * @param $model
 */
function java_model_update_state($model)
{

    if (!_java_db_header($model, "update_state")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("更新状态");
    _java_abs_update_state_param_comment($model);
    _java_comment_footer();
    echo "public static int updateState(";
    $i_param = _java_abs_update_state_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    echo _tab(2) . "int rRet = DBFactory.get{$uc_table}().updateState(";
    _java_abs_update_state_param4use($model,true);
    echo ");\n";
    echo _tab(2) . "return rRet;\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "update_state");
}