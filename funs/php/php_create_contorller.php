<?php
if (!defined("PHP_BASE")) {
    define('PHP_BASE', realpath(dirname(__FILE__)));
}

include_once PHP_BASE . "/php_base.ini.php";

include_once PHP_BASE . "/php_controller_add.php";
//include_once PHP_BASE . "/php_model_delete.php";
//include_once PHP_BASE . "/php_model_update.php";
//include_once PHP_BASE . "/php_model_list.php";
//include_once PHP_BASE . "/php_model_add.php";
//include_once PHP_BASE . "/php_model_fetch.php";

/**
 * 建立php模型类
 * @param $package
 * @param $model
 */
function php_create_contorller($package, $model)
{

    $uc_table = ucfirst($model['table_name']);
    _php_header();

    echo "use Phalcon\Mvc\Controller;\n";

    _php_comment(array("php  控制器", $model['table_title']));
    echo "class {$uc_table} extends ControllerBase {\n";


    _php_comment("最终控制器初始化 for local init level3", 1);
    echo _tab(1) . "protected function _beforeAction() {\n";
    echo _tab(2) . "parent::_beforeAction();\n";
    echo _tab(2) . "//TODO 私有在这里定义\n";
    //TODO 私有操作连接在这里定义
    echo _tab(1) . "}\n";

    _php_comment("一般不使用的入口路由", 1);
    echo _tab(1) . "public function indexAction()\n";
    echo _tab(1) . "{\n";
    echo _tab(2) . "//TODO nothing to do here\n";
    echo _tab(1) . "}\n";


    //CURD
    php_controller_add($model);
//    php_model_delete($model);
//    php_model_update($model);
//    php_model_fetch($model);
//    php_model_list($model);

    echo "}";
}