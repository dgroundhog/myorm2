<?php
if (!defined("PHP_BASE")) {
    define('PHP_BASE', realpath(dirname(__FILE__)));
}
include_once(PHP_BASE . "/php_base.ini.php");


/**
 * 建立php BEAN类
 * @param $package
 * @param $model
 */
function php_create_bean($package, $model)
{

    $uc_table = ucfirst($model['table_name']);
    _php_header();
    _php_comment("数据bean-{$model['table_title']}[{$model['model_name']}]");
    echo "class {$uc_table}Bean\n{\n";

    foreach ($model['table_fields'] as $key => $field) {
        _php_comment_header("{$field['name']}", 1);
        switch ($field['type']) {
            case "blob":
            case "longblob":
                echo _tab(1) . " * @var string|object\n";
                _php_comment_footer(1);
                echo _tab(1) . "public \${$key} = null;\n";
                break;
            case "int":
                echo _tab(1) . " * @var int\n";
                _php_comment_footer(1);
                echo _tab(1) . "public \${$key} = 0;\n";
                break;
            default:
                echo _tab(1) . " * @var string\n";
                _php_comment_footer(1);
                echo _tab(1) . "public \${$key} = \"\";\n";
                break;
        }
    }
    echo "}";
}