<?php

if (!defined("PHP_BASE")) {
    define('PHP_BASE', realpath(dirname(__FILE__)));
}
include_once(PHP_BASE . "/php_base.ini.php");
include_once(PHP_BASE . "/php_model_fetch.php");


/**
 * 方法请求参数的
 * @param array $model
 * @param int $i_tab
 * @return int
 */
function _php_add_req_param($model, $i_tab = 0)
{
    $ii = 0;
    foreach ($model['table_fields'] as $key => $field) {
        if (!in_array($key, $model['add_keys'])) {
            continue;
        }
        _php_req_param($model, $key, "post", $i_tab);
        $ii++;
    }
    return $ii;
}


/**
 * php的控制器
 *
 * @param $model
 */
function php_controller_add($model)
{

    if (!_php_db_header($model, "add")) {
        return;
    }
    $table_name = $model['table_name'];
    $table_title = $model['table_title'];
    $lc_table = strtolower($table_name);
    $uc_table = ucfirst($table_name);

    _php_comment("创建数据的表单", 1);
    echo _tab(1) . "protected function newAction() {\n";
    echo _tab(2) . "\$this->assign(\"page_title\",\"新建{$table_title}\");\n";
    echo _tab(2) . "\$a_info = \$this->_beforeFormEdit(\"{$lc_table}_edit\");\n";
    echo _tab(2) . "\n";
    echo _tab(2) . "//TODO 获取上一个记录剩余的数据\n";

    echo _tab(2) . "\$this->assign(\"a_info\",\$a_info);\n";
    echo _tab(2) . "//TODO其他需要预先输出的参数\n";
    echo _tab(1) . "}\n";

    _php_comment("保存插入数据", 1);
    echo _tab(1) . "protected function saveAction() {\n";
    echo _tab(2) . "if (!\$this->_beforeFormSave(\"{$lc_table}_edit\")){\n";
    echo _tab(3) . "return \$this->response->redirect(\$this->_pool['url_{$lc_table}_list']);\n";
    echo _tab(2) . "}\n";

    echo _tab(2) . "//接收请求参数\n";
    echo _tab(2) . "\$a_input_org = array();\n";
    echo _tab(2) . "\$a_input_error = array();\n";

    _php_comment("清洗输入参数", 1);
    _php_add_req_param($model, 2);
    echo _tab(2) . "//TODO 检查需要进一步处理的参数\n";

    echo _tab(2) . "\$iRet = {$uc_table}::add(";
    _php_add_param($model, 3);
    echo ");\n";
    //TODO 判断返回值的大小
    echo _tab(2) . "if (\$iRet > 0){\n";
    echo _tab(3) . "\$succSuffix = \"?op=succ\"";
    echo _tab(3) . "return \$this->response->redirect(\$this->_pool['url_{$lc_table}_list'].\$succSuffix);\n";
    echo _tab(2) . "}\n";
    echo _tab(2) . "else {\n";
    //TODO 把原始输入和错误写入session
    echo _tab(3) . "return \$this->response->redirect(\$this->_pool['url_{$lc_table}_add']);\n";
    echo _tab(2) . "}\n";
    echo _tab(1) . "}\n";

    _php_comment("ajax保存插入数据", 1);
    echo _tab(1) . "protected function ajax_saveAction() {\n";
    echo _tab(2) . "//TODO\n";
    echo _tab(1) . "}\n";


    _php_comment_header("插入数据", 1);
    _php_add_param_comment($model);
    _php_comment_footer(1);
    echo _tab(1) . "public static function add(";
    $i_param = _php_add_param($model, 2);
    echo ")\n";
    echo _tab(1) . "{\n";

    if (count($model['add_keys']) == 0) {
        echo _tab(2) . "return -1;//TODO\n";
    } else {

        $s_qm = _question_marks($i_param);
        echo _tab(2) . "\$i_new_id = 0;\n";
        echo _tab(2) . "\$sql = \"{CALL `p_{$table_name}_add`({$s_qm},@_new_id)}\";\n";

        _php_before_query();
        _php_add_param($model, 4, true);
        _php_on_query();
        _php_add_param_bind($model, 4);
        _php_after_query();

        _php_before_result_loop();
        echo _tab(4) . "\$i_new_id = \$a_ret['i_new_id'];\n";
        echo _tab(4) . "break;\n";
        _php_after_result_loop();

        echo _tab(2) . "self::logDebug(__METHOD__, __LINE__, \"call p_{$table_name}_add--(\$i_new_id)\");\n";
        echo _tab(2) . "return \$i_new_id;\n";

    }
    echo _tab(1) . "}";


    /**
     * 检查key是否存在，如果主键存在，可保存
     */
    $can_touch = true;
    $a_temp = array();
    foreach ($model['fetch_by'] as $key) {
        if (!isset($model['table_fields'][$key])) {
            continue;
        }
        $a_temp[] = $key;

        if (!in_array($key, $model['add_keys'])) {
            $can_touch = false;
            break;
        }
    }

    if (count($a_temp) > 0 && $can_touch) {
        /**
         * 默认主键查询
         */
        $fetch_by = $a_temp;

        _php_comment_header("如果包含主键的数据不存在，尝试插入数据", 1);
        _php_add_param_comment($model);
        _php_comment_footer(1);

        echo _tab(1) . "public static function touch(";
        $i_param = _php_add_param($model, 2);
        echo ")\n";
        echo _tab(1) . "{\n";
        echo _tab(2) . "\$i_ret = 0;\n";
        echo _tab(2) . "\$a_info = self::fetch(";
        _php_fetch_param($model, $fetch_by, 4);
        echo _tab(2) . ");\n";
        echo _tab(2) . "if(null != \$a_info && is_array(\$a_info) && isset(\$a_info[\"id\"])){\n";
        echo _tab(3) . "\$i_ret = 1;\n";
        echo _tab(2) . "}else{\n";
        echo _tab(3) . "\$i_ret = self::add(";
        _php_add_param($model, 4);
        echo ");\n";
        echo _tab(2) . "}\n";
        echo _tab(2) . "return \$i_ret;\n";
        echo _tab(1) . "}";
    }

    _php_comment_header("通过bean插入数据", 1);
    echo _tab(1) . " * @param {$uc_table}Bean \$v_bean\n";
    echo _tab(1) . " * @return int\n";
    _php_comment_footer(1);

    echo _tab(1) . "public static function addBean(\$v_bean) \n";
    echo _tab(1) . "{\n";
    if (count($model['add_keys']) == 0) {
        echo _tab(2) . "return -1;\n";
    } else {

        $s_qm = _question_marks($i_param);
        echo _tab(2) . "\$i_new_id = 0;\n";
        echo _tab(2) . "\$sql = \"{CALL `p_{$table_name}_add`({$s_qm},@_new_id)}\";\n";

        _php_before_query();

        $a_temp = array();
        foreach ($model['table_fields'] as $key => $field) {
            if (!in_array($key, $model['add_keys'])) {
                continue;
            }
            $a_temp[] = _tab(4) . "\$v_bean->{$key}";
        }
        echo implode(",\n", $a_temp);

        _php_on_query();
        _php_add_param_bind($model, 4);
        _php_after_query();

        _php_before_result_loop();
        echo _tab(4) . "\$i_new_id = \$a_ret['i_new_id'];\n";
        echo _tab(4) . "break;\n";
        _php_after_result_loop();

        echo _tab(2) . "self::logDebug(__METHOD__, __LINE__, \"CALL p_{$table_name}_add--(\$i_new_id)\");\n";
        echo _tab(2) . "return \$i_new_id;\n";

    }
    echo _tab(1) . "}";
    _php_db_footer($model, "add");


}