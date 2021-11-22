<?php

if (!defined("PHP_BASE")) {
    define('PHP_BASE', realpath(dirname(__FILE__)));
}
include_once(PHP_BASE . "/php_base.ini.php");


/**
 * 方法 update-1 参数的注释
 * TODO 值从bean来
 * @param array $model
 * @param array $update_keys
 * @param array $update_by
 * @param boolean $update_keys_from_bean
 * @return  void
 */
function _php_update_param_comment($model, $update_keys, $update_by, $update_keys_from_bean = false)
{
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);
    if ($update_keys_from_bean) {
        echo _tab(1) . " * @param {$uc_table}Bean \$o_bean\n";
    } else {
        foreach ($update_keys as $key) {
            $field = $model['table_fields'][$key];
            $type = _php_get_key_type($field['type']);
            $prefix = _php_get_key_prefix($field['type']);
            echo _tab(1) . " * @param {$type} \${$prefix}_u_{$key} {$field['name']}\n";
        }
    }


    //xxx 需要避免key重复
    if (count($update_by) >= 0) {
        //当$update_by==0 时，更新全部
        foreach ($update_by as $key) {
            $field = $model['table_fields'][$key];
            $type = _php_get_key_type($field['type']);
            $prefix = _php_get_key_prefix($field['type']);
            echo _tab(1) . " * @param {$type} \${$prefix}_w_{$key} {$field['name']}\n";
        }
    }

    echo _tab(1) . " * @return int \n";


}

/**
 * 方法 update-2 参数
 *
 * @param array $model
 * @param array $update_keys
 * @param array $update_by
 * @param boolean $update_keys_from_bean
 * @param int $i_tab
 * @param boolean $for_proc
 * @return int
 */
function _php_update_param($model, $update_keys, $update_by, $update_keys_from_bean = false, $i_tab = 0, $for_proc = false)
{
    //TODO 计数出错了，用非bean的参数
    $ii = 0;
    $a_temp = array();
    if (!$update_keys_from_bean) {
        foreach ($update_keys as $key) {
            $field = $model['table_fields'][$key];
            $prefix = _php_get_key_prefix($field['type']);
            $a_temp[] = "\${$prefix}_u_{$key}";
            $ii++;
        }
    } else {
        if ($for_proc) {


            foreach ($update_keys as $key) {
                $a_temp[] = "\$o_bean->{$key}";
                $ii++;
            }
        } else {
            $a_temp[] = "\$o_bean";
            $ii++;
        }
    }

    if (count($update_by) >= 0) {
        foreach ($update_by as $key) {
            $field = $model['table_fields'][$key];
            $prefix = _php_get_key_prefix($field['type']);
            $a_temp[] = "\${$prefix}_w_{$key}";
            $ii++;
        }
    }
    _php_param_footer($a_temp, $i_tab, $for_proc);
    return $ii;

}

/**
 * 方法 update-3 构造bind参数
 *
 * @param array $model
 * @param array $update_keys
 * @param array $update_by
 * @param int $i_tab
 * @return int
 */
function _php_update_param_bind($model, $update_keys, $update_by, $i_tab = 0)
{
    $ii = 0;
    $a_temp = array();

    foreach ($update_keys as $key) {
        $field = $model['table_fields'][$key];
        $bind = _php_get_key_bind($field['type']);
        $a_temp[] = _tab($i_tab) . "{$bind}";
        $ii++;
    }

    if (count($update_by) >= 0) {
        foreach ($update_by as $key) {
            $field = $model['table_fields'][$key];
            $bind = _php_get_key_bind($field['type']);
            $a_temp[] = _tab($i_tab) . "{$bind}";
            $ii++;
        }
    }
    echo implode(",\n", $a_temp);
    echo "\n";
    return $ii;
}


/**
 * 集成的获取函数
 * @param array $model
 * @param string $update_name
 * @param string $update_title
 * @param array $update_keys
 * @param array $update_by
 */
function _php_model_update($model, $update_name = "default", $update_title, $update_keys, $update_by)
{

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    $lc_update_name = strtolower($update_name);
    $uc_update_name = ucfirst($lc_update_name);

    $fun_suffix = "";
    $proc_suffix = "";
    if ($update_name != "default" && $update_name != "") {
        $fun_suffix = "By{$uc_update_name}";
        $proc_suffix = "_{$lc_update_name}";
    }

    _php_comment_header("{$update_title} 输入为所需参数", 1);
    _php_update_param_comment($model, $update_keys, $update_by, false);
    _php_comment_footer(1);
    echo _tab(1) . "public static function modify{$fun_suffix}(";
    $i_param = _php_update_param($model, $update_keys, $update_by, false, 2, false);
    echo ")\n";
    echo _tab(1) . "{\n";

    $s_qm = _question_marks($i_param);
    echo _tab(2) . "\$i_affected_rows = 0;\n";
    echo _tab(2) . "\$sql = \"{CALL `p_{$table_name}_update{$proc_suffix}`({$s_qm})}\";\n";

    _php_before_query();
    _php_update_param($model, $update_keys, $update_by, false, 4, true);
    _php_on_query();
    _php_update_param_bind($model, $update_keys, $update_by, 4);
    _php_after_query();

    _php_before_result_loop();
    echo _tab(4) . "\$i_affected_rows = \$a_ret['i_affected_rows'];\n";
    echo _tab(4) . "break;\n";
    _php_after_result_loop();

    echo _tab(2) . "self::logDebug(__METHOD__, __LINE__, \"call p_{$table_name}_update{$proc_suffix}--done--(\$i_affected_rows)\");\n";
    echo _tab(2) . "return \$i_affected_rows;\n";


    echo _tab(1) . "}";

    echo "\n";

    _php_comment_header("{$update_title},输入为一个bean", 1);
    _php_update_param_comment($model, $update_keys, $update_by, true);
    _php_comment_footer(1);
    echo _tab(1) . "public static function modifyBean{$fun_suffix}(";
    _php_update_param($model, $update_keys, $update_by, true, 0, false);
    echo ")\n";
    echo _tab(1) . "{\n";
    $s_qm = _question_marks($i_param);
    echo _tab(2) . "\$i_affected_rows = 0;\n";
    echo _tab(2) . "\$sql = \"{CALL `p_{$table_name}_update{$proc_suffix}`({$s_qm})}\";\n";

    _php_before_query();
    _php_update_param($model, $update_keys, $update_by, true, 4, true);
    _php_on_query();
    _php_update_param_bind($model, $update_keys, $update_by, 4);
    _php_after_query();

    _php_before_result_loop();
    echo _tab(4) . "\$i_affected_rows = \$a_ret['i_affected_rows'];\n";
    echo _tab(4) . "break;\n";
    _php_after_result_loop();

    echo _tab(2) . "self::logDebug(__METHOD__, __LINE__, \"call p_{$table_name}_update{$proc_suffix}--done--(\$i_affected_rows)\");\n";
    echo _tab(2) . "return \$i_affected_rows;\n";

    echo _tab(1) . "}";
}

/**
 * php 模型类--获取一条数据
 *
 * @param $model
 */
function php_model_update($model)
{

    if (!_php_db_header($model, "update")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);


    $a_update_confs = _model_get_ok_update($model);
    foreach ($a_update_confs as $update_name => $a_update_conf) {
        $s_update_title = $a_update_conf['update_title'];//更新的标题
        $a_update_keys = $a_update_conf['update_keys'];//更新的内容
        $a_update_by = $a_update_conf['update_by'];//更新依据
        _php_model_update($model, $update_name, $s_update_title, $a_update_keys, $a_update_by);
    }

    _php_db_footer($model, "update");
}