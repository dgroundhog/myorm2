<?php
if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");


include_once JAVA_BASE . "/java_abs_add.php";
include_once JAVA_BASE . "/java_abs_count.php";
include_once JAVA_BASE . "/java_abs_delete.php";
include_once JAVA_BASE . "/java_abs_drop.php";
include_once JAVA_BASE . "/java_abs_fetch.php";
include_once JAVA_BASE . "/java_abs_list.php";
include_once JAVA_BASE . "/java_abs_list_all.php";
include_once JAVA_BASE . "/java_abs_list_by_ids.php";
include_once JAVA_BASE . "/java_abs_sum.php";
include_once JAVA_BASE . "/java_abs_update.php";
include_once JAVA_BASE . "/java_abs_update_state.php";


/**
 * 建立java抽象类
 * @param $package
 * @param $model
 */
function java_db_abs($package, $model)
{

    $uc_table = ucfirst($model['table_name']);

    echo "package  {$package}.db.base;\n";

    echo "import {$package}.bean.{$uc_table}Bean;\n";

    echo "import java.util.HashMap;\n";
    echo "import java.util.Map;\n";
    echo "import java.util.Vector;\n";

    _java_comment("java db 操作抽象类--{$model['table_title']}");
    echo "abstract public class Db{$uc_table} {\n";

    _java_comment("基本数据结构,其中blob、longtext字段不参与map获取");
    echo "public static final Map<String, String> mRowMap = new HashMap<String, String>() {{\n";
    foreach ($model['table_fields'] as $key => $field) {
        if ($field['type'] == "longblob" || $field['type'] == "blob" || $field['type'] == "longtext") {
            continue;
        }
        echo _tab(2) . "put(\"{$key}\", \"{$key}\");\n";
    }
    echo "}};\n";


    java_abs_add($model);
    java_abs_count($model);
    java_abs_delete($model);
    java_abs_drop($model);
    java_abs_fetch($model);
    java_abs_list($model);
    java_abs_list_all($model);
    java_abs_list_basic($model);
    java_abs_list_by_ids($model);
    java_abs_sum($model);
    java_abs_update($model);
    java_abs_update_state($model);

    echo "}";
}