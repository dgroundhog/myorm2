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
function php_create_model($package, $model)
{

    $uc_table = ucfirst($model['table_name']);
    _php_header();

    echo "use Phalcon\Db as Db;\n";

    _php_comment(array("php  操作模型类", $model['table_title']));
    echo "class {$uc_table} extends MvcBase {\n";

    _php_comment("基本数据结构,定义参看bean", 1);
    echo _tab(1) . "public static \$m_row_map = array(\n";
    $a_temp = array();
    foreach ($model['table_fields'] as $key => $field) {
        $a_temp[] = _tab(2) . "\"{$key}\" => \"{$field['name']}\"";
    }
    echo implode(",\n", $a_temp) . "\n";
    echo _tab(1) . ");\n";

    /**
     * 基本数据实体
     */
    _php_comment_header("数据实体", 1);
    echo _tab(1) . " * @var {$uc_table}Bean\n";
    _php_comment_footer(1);
    echo _tab(1) . "public \$bean;\n";


    _php_comment_header("获取数据实体", 1);
    echo _tab(1) . " * @return {$uc_table}Bean\n";
    _php_comment_footer(1);
    echo _tab(1) . "public function getBean() {\n";
    echo _tab(2) . "return \$this->bean;\n";
    echo _tab(1) . "}\n";


    _php_comment_header("设置数据实体", 1);
    echo _tab(1) . " * @param {$uc_table}Bean \$bean0\n";
    echo _tab(1) . " * @return void\n";
    _php_comment_footer(1);
    echo _tab(1) . "public function setBean(\$bean0) {\n";
    echo _tab(2) . "\$this->bean = \$bean0;\n";
    echo _tab(1) . "}\n";

    //if (isset($model['kv_list']) && count($model['keys_by_select']) > 0) {

    /**
     * 获得基本的状态值hash
     * kv_list => [key => {k1=>v1,k2=>v2}]
     */
    if (isset($model['kv_list']) && count($model['kv_list']) > 0) {
        foreach ($model['kv_list'] as $key => $a_v1) {
            if (isset($model['table_fields'][$key])) {
                $uc_key = ucfirst($key);
                $f_name = $model['table_fields'][$key]['name'];
                _php_comment_header("获取[{$f_name}] kv列表", 1);
                echo _tab(1) . " * @return array\n";
                _php_comment_footer(1);
                echo _tab(1) . "public static function get{$uc_key}KV(){ \n";
                $a_temp2 = array();
                foreach ($a_v1 as $k2 => $v2) {
                    $a_temp2[] = "\"{$k2}\" => \"{$v2}\"";
                }
                echo _tab(2) . "return array(\n";
                if (count($a_temp2) > 0) {
                    echo _tab(3);
                    echo implode(",\n" . _tab(3), $a_temp2);
                    echo "\n";
                }
                echo _tab(2) . ");\n";
                echo _tab(1) . "}\n";
            }
        }
    }

    //CURD
    php_model_add($model);
    php_model_delete($model);
    php_model_update($model);
    php_model_fetch($model);
    php_model_list($model);

    echo "}";
}