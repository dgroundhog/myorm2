<?php
if (!defined("PHP_BASE")) {
    define('PHP_BASE', realpath(dirname(__FILE__)));
}

include_once PHP_BASE . "/php_base.ini.php";

include_once PHP_BASE . "/php_create_bean.php";
include_once PHP_BASE . "/php_model_delete.php";
include_once PHP_BASE . "/php_model_update.php";
include_once PHP_BASE . "/php_model_list.php";
include_once PHP_BASE . "/php_model_add.php";
include_once PHP_BASE . "/php_model_fetch.php";

/**
 * 建立php模型类
 * @param $package
 * @param $model
 */
function php_create_modelx($package, $model)
{

    $uc_table = ucfirst($model['table_name']);
    _php_header();
    echo "use Phalcon\Db as Db;\n";
    //_php_comment("php mysql 扩展操作模型类--{$model['table_title']}");
    _php_comment(array("php  扩展操作模型类", $model['table_title']));
    echo "class {$uc_table}x extends {$uc_table} {\n";


    _php_comment_header("TODO 自定义code写在这里", 1);
    echo _tab(1) . " * @return mixed\n";
    _php_comment_footer(1);
    echo _tab(1) . "public static function todo(){ \n";
    echo _tab(2) . "return null;\n";
    echo _tab(1) . "}\n\n";

    echo "}";
}