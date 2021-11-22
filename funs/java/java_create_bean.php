<?php
if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");


/**
 * 建立java抽象类
 * @param $package
 * @param $model
 */
function java_create_bean($package, $model)
{

    $uc_table = ucfirst($model['table_name']);

    echo "package  {$package}.bean;\n";

    echo "import java.util.HashMap;\n";
    echo "import java.util.Map;\n";
    echo "import java.util.Vector;\n";
    echo "import java.io.Serializable;\n";

    _java_comment("数据bean-{$model['table_title']}-{$model['model_name']}");
    echo "public class {$uc_table}Bean implements Serializable {\n";

    foreach ($model['table_fields'] as $key => $field) {
        _java_comment("{$field['name']}", 1);
        switch ($field['type']) {
            case "blob":
            case "longblob":
                echo "    public  byte[] {$key} = null;\n";
                break;
            case "int":
                echo "    public int {$key} = 0;\n";
                break;
            default:
                echo "    public String {$key} = \"\";\n";
                break;
        }
    }
    echo "}";
}