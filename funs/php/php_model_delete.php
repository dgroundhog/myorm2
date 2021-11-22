<?php

if (!defined("PHP_BASE")) {
    define('PHP_BASE', realpath(dirname(__FILE__)));
}
include_once(PHP_BASE . "/php_base.ini.php");


/**
 * 方法 delete-1 参数的注释
 * TODO 值从bean来
 * @param array $model
 * @param array $delete_by
 * @return  void
 */
function _php_delete_param_comment($model, $delete_by)
{
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    //xxx 需要避免key重复
    if (count($delete_by) > 0) {
        //当$delete_by==0 时， 删除全部
        foreach ($delete_by as $key) {
            $field = $model['table_fields'][$key];
            $type = _php_get_key_type($field['type']);
            $prefix = _php_get_key_prefix($field['type']);
            echo _tab(1) . " * @param {$type} \${$prefix}_w_{$key} {$field['name']}\n";
        }
    }

    echo _tab(1) . " * @return int \n";


}

/**
 * 方法 delete-2 参数
 *
 * @param array $model
 * @param array $delete_by
 * @param int $i_tab
 * @param boolean $for_proc
 * @return int
 */
function _php_delete_param($model, $delete_by, $i_tab = 0, $for_proc = false)
{
    $ii = 0;
    $a_temp = array();

    if (count($delete_by) > 0) {
        foreach ($delete_by as $key) {
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
 * 方法 delete-3 构造bind参数
 *
 * @param array $model
 * @param array $delete_by
 * @param int $i_tab
 * @return int
 */
function _php_delete_param_bind($model, $delete_by, $i_tab = 0)
{
    $ii = 0;
    $a_temp = array();


    if (count($delete_by) > 0) {
        foreach ($delete_by as $key) {
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
 * @param string $delete_name
 * @param string $delete_title
 * @param array $delete_by
 */
function _php_model_delete($model, $delete_name = "default", $delete_title, $delete_by)
{

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    $lc_delete_name = strtolower($delete_name);
    $uc_delete_name = ucfirst($lc_delete_name);

    $fun_suffix = "";
    $proc_suffix = "";
    if ($delete_name != "default" && $delete_name != "") {
        $fun_suffix = "By{$uc_delete_name}";
        $proc_suffix = "_{$lc_delete_name}";
    }

    _php_comment_header("{$delete_title} 输入为所需参数", 1);
    _php_delete_param_comment($model, $delete_by);
    _php_comment_footer(1);
    echo _tab(1) . "public static function drop{$fun_suffix}(";
    $i_param = _php_delete_param($model, $delete_by, 0);
    echo ")\n";
    echo _tab(1) . "{\n";

    $s_qm = _question_marks($i_param);
    echo _tab(2) . "\$i_affected_rows = 0;\n";
    echo _tab(2) . "\$sql = \"{CALL `p_{$table_name}_delete{$proc_suffix}`({$s_qm})}\";\n";

    _php_before_query();
    _php_delete_param($model, $delete_by, 4, true);
    _php_on_query();
    _php_delete_param_bind($model, $delete_by, 4);
    _php_after_query();

    _php_before_result_loop();
    echo _tab(4) . "\$i_affected_rows = \$a_ret['i_affected_rows'];\n";
    echo _tab(4) . "break;\n";
    _php_after_result_loop();

    echo _tab(2) . "self::logDebug(__METHOD__, __LINE__, \"call p_{$table_name}_delete{$proc_suffix}--done--(\$i_affected_rows)\");\n";
    echo _tab(2) . "return \$i_affected_rows;\n";

    echo _tab(1) . "}";

}

/**
 * php 模型类--获取一条数据
 *
 * @param $model
 */
function php_model_delete($model)
{

    if (!_php_db_header($model, "delete")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    $delete_by = $model['fetch_by'];
    $a_temp = array();
    foreach ($delete_by as $pk_key) {
        if (!isset($model['table_fields'][$pk_key])) {
            continue;
        }
        $a_temp[] = $pk_key;
    }
    $delete_by = $a_temp;

    _php_comment_header("删除或者移除", 1);
    _php_delete_param_comment($model, $delete_by);
    _php_comment_footer(1);
    echo _tab(1) . "public static function deleteTobe(";
    $i_param = _php_delete_param($model, $delete_by, 0);
    echo ")\n";
    echo _tab(1) . "{\n";
    //flag就是用来删除的
    if (isset($model["table_fields"]["flag"])) {
        echo _tab(2) . "return self::modifyByFlag(\"d\",";
        _php_delete_param($model, $delete_by, 0);
        echo ");\n";
    } else {
        echo _tab(2) . "return self::drop(";
        _php_delete_param($model, $delete_by, 0);
        echo _tab(4) . ");\n";
    }
    echo _tab(1) . "}";

    /**
     * 其他可能的主键查询单条语句
     * delete_confs=>
     *  ----delete_name  => title
     *                  => keys
     */
    $a_delete_confs = _model_get_ok_delete($model);
    foreach ($a_delete_confs as $delete_name => $a_delete_conf) {
        $a_delete_by = $a_delete_conf['delete_by'];// 删除依据
        _php_model_delete($model, $delete_name, $a_delete_conf['delete_title'], $a_delete_by);
    }

    _php_db_footer($model, "delete");
}