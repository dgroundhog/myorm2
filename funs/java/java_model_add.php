<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_add.php");
include_once(JAVA_BASE . "/java_abs_fetch.php");

/**
 * java模型类--添加
 *
 * @param $model
 */
function java_model_add($model)
{

    if (!_java_db_header($model, "add")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("插入数据");
    _java_abs_add_param_comment($model);
    _java_comment_footer();
    echo "public static int add(";
    $i_param = _java_abs_add_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";


    echo _tab(2) . "int iRet = DBFactory.get{$uc_table}().add(";
    _java_abs_add_param4use($model,true);
    echo ");\n";
    echo _tab(2) . "return iRet;\n";
    echo _tab(1) . "}";


    $can_touch = true;

    foreach ($model['fetch_by'] as $key) {
        if (!in_array($key, $model['add_keys'])) {
            $can_touch = false;
            break;
        }
    }
    if($can_touch){
        _java_comment_header("尝试插入数据");
        _java_abs_add_param_comment($model);
        _java_comment_footer();
        echo "public static int touch(";
        $i_param = _java_abs_add_param($model);
        echo _tab(1) . ")\n";
        echo _tab(1) . "{\n";
        echo _tab(2) . "int iRet = 0;\n";
        echo _tab(2) . "HashMap<String,String> mInfo = DBFactory.get{$uc_table}().fetch(";
        _java_abs_fetch_param4use($model,true);
        echo ");\n";

        echo _tab(2) . "if(null != mInfo.get(\"id\")){\n";
        echo _tab(3) . "iRet = 1;\n";
        echo _tab(2) . "}else{\n";
        echo _tab(3) . "iRet = DBFactory.get{$uc_table}().add(";
        _java_abs_add_param4use($model,true);
        echo ");\n";
        echo _tab(2) . "}";
        echo _tab(2) . "return iRet;\n";
        echo _tab(1) . "}";
    }


    _java_comment_header("插入数据--通过bean");
    echo "* @param v_{$model['table_name']}Bean\n";
    echo "* @return int\n";
    _java_comment_footer();
    echo "public static int addBean({$uc_table}Bean v_{$table_name}Bean) \n";
    echo _tab(1) . "{\n";
    echo _tab(2) . "int iRet = DBFactory.get{$uc_table}().addBean(v_{$table_name}Bean);\n";
    echo _tab(2) . "return iRet;\n";
    echo _tab(1) . "}";
    _java_db_footer($model, "add");


}