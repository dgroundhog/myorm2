<?php

if (!defined("PHP_BASE")) {
    define('PHP_BASE', realpath(dirname(__FILE__)));
}
include_once(PHP_BASE . "/php_base.ini.php");
include_once(PHP_BASE . "/php_model_fetch.php");

/**
 * 方法 add-1 构造参数注释
 * @param $model
 */
function _php_add_param_comment($model)
{
    foreach ($model['table_fields'] as $key => $field) {
        if (!in_array($key, $model['add_keys'])) {
            continue;
        }
        $type = _php_get_key_type($field['type']);
        $prefix = _php_get_key_prefix($field['type']);
        echo _tab(1) . " * @param {$type} \${$prefix}_{$key} {$field['name']}\n";
    }
    echo _tab(1) . " * @return int\n";
}

/**
 * 方法 add-2 构造参数
 * @param array $model
 * @param int $i_tab
 * @param boolean $for_proc
 * @return int
 */
function _php_add_param($model, $i_tab = 0, $for_proc = false)
{
    $ii = 0;
    $a_temp = array();
    foreach ($model['table_fields'] as $key => $field) {
        if (!in_array($key, $model['add_keys'])) {
            continue;
        }
        $prefix = _php_get_key_prefix($field['type']);
        $a_temp[] = "\${$prefix}_{$key}";
        $ii++;
    }
    _php_param_footer($a_temp, $i_tab, $for_proc);

    return $ii;
}

/**
 * 方法 add-3 构造bind参数
 * @param $model
 * @param int $i_tab
 * @return int
 */
function _php_add_param_bind($model, $i_tab = 0)
{
    $ii = 0;
    $a_temp = array();
    foreach ($model['table_fields'] as $key => $field) {
        if (!in_array($key, $model['add_keys'])) {
            continue;
        }
        $bind = _php_get_key_bind($field['type']);
        $a_temp[] = _tab($i_tab) . "{$bind}";
        $ii++;
    }
    echo implode(",\n", $a_temp);
    echo "\n";
    return $ii;
}

/**
 * php模型类--添加,按照key顺序自然排序
 *
 * @param $model
 */
function php_model_add($model)
{

    if (!_php_db_header($model, "add")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

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