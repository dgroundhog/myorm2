<?php

if (!defined("PHP_BASE")) {
    define('PHP_BASE', realpath(dirname(__FILE__)));
}
include_once(PHP_BASE . "/php_base.ini.php");


/**
 * 方法 fetch-1 参数的注释
 *
 * @param $model
 * @param $fetch_by
 * @param $return_format
 */
function _php_fetch_param_comment($model, $fetch_by, $return_format)
{
    if (count($fetch_by) > 0) {
        foreach ($fetch_by as $key) {
            if (!isset($model['table_fields'][$key])) {
                continue;
            }
            $field = $model['table_fields'][$key];
            $type = _php_get_key_type($field['type']);
            $prefix = _php_get_key_prefix($field['type']);
            echo _tab(1) . " * @param {$type} \${$prefix}_{$key} {$field['name']}\n";
        }
    }

    echo _tab(1) . " * @return {$return_format} \n";


}

/**
 * 方法 fetch-2 参数
 *
 * @param $model
 * @param $fetch_by
 * @param int $i_tab
 * @param boolean $for_proc
 * @return int
 */
function _php_fetch_param($model, $fetch_by, $i_tab = 0, $for_proc = false)
{

    $ii = 0;
    $a_temp = array();
    if (count($fetch_by) > 0) {
        foreach ($fetch_by as $key) {
            if (!isset($model['table_fields'][$key])) {
                continue;
            }
            $field = $model['table_fields'][$key];
            $prefix = _php_get_key_prefix($field['type']);
            $a_temp[] = "\${$prefix}_{$key}";
            $ii++;
        }
    }
    _php_param_footer($a_temp, $i_tab, $for_proc);
    return $ii;

}

/**
 * 方法 fetch-3 构造bind参数
 *
 * @param $model
 * @param $fetch_by
 * @param int $i_tab
 * @return int
 */
function _php_fetch_param_bind($model, $fetch_by, $i_tab = 0)
{
    $ii = 0;
    $a_temp = array();
    if (count($fetch_by) > 0) {
        foreach ($fetch_by as $key) {
            if (!isset($model['table_fields'][$key])) {
                continue;
            }
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
 * @param string $fetch_name
 * @param string $fetch_title
 * @param array $fetch_by
 */
function _php_model_fetch($model, $fetch_name = "default", $fetch_title, $fetch_by)
{

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    $lc_fetch_name = strtolower($fetch_name);
    $uc_fetch_name = ucfirst($lc_fetch_name);

    $fun_suffix = "";
    $proc_suffix = "";
    if ($fetch_name != "default" && $fetch_name != "") {
        $fun_suffix = "By{$uc_fetch_name}";
        $proc_suffix = "_{$lc_fetch_name}";
    }

    _php_comment_header("{$fetch_title},取出一条数据,结构为array", 1);
    _php_fetch_param_comment($model, $fetch_by, "array|mixed");
    _php_comment_footer(1);
    echo _tab(1) . "public static function fetch{$fun_suffix}(";
    $i_param = _php_fetch_param($model, $fetch_by, 2);
    echo ")\n";
    echo _tab(1) . "{\n";


    $s_qm = _question_marks($i_param);
    echo _tab(2) . "\$a_info = array();\n";
    echo _tab(2) . "\$sql = \"{CALL `p_{$table_name}_fetch{$proc_suffix}`({$s_qm})}\";\n";

    _php_before_query();
    _php_fetch_param($model, $fetch_by, 4, true);
    _php_on_query();
    _php_fetch_param_bind($model, $fetch_by, 4);
    _php_after_query();

    echo _tab(2) . "\$b_found = false;\n";
    _php_before_result_loop();
    echo _tab(4) . "foreach (\$a_ret as \$kk => \$vv) {\n";
    echo _tab(5) . "if(isset(self::\$m_row_map[\$kk])){\n";
    echo _tab(6) . "\$a_info[\$kk] = \$vv;\n";
    echo _tab(5) . "}\n";
    echo _tab(5) . "\$b_found = true;\n";
    echo _tab(5) . "break;\n";
    echo _tab(4) . "}\n";
    _php_after_result_loop();

    echo _tab(2) . "self::logDebug(__METHOD__, __LINE__, \"call p_{$table_name}_fetch{$proc_suffix}--done--(\$b_found)\");\n";
    echo _tab(2) . "return \$a_info;\n";

    echo _tab(1) . "}";


    _php_comment_header("{$fetch_title},取出一条数据,结构为bean", 1);
    _php_fetch_param_comment($model, $fetch_by, "{$uc_table}Bean");
    _php_comment_footer(1);
    echo _tab(1) . "public static function fetchBean{$fun_suffix}(";
    $i_param = _php_fetch_param($model, $fetch_by, 2);
    echo ")\n";
    echo _tab(1) . "{\n";


    $s_qm = _question_marks($i_param);
    echo _tab(2) . "\$o_bean = new {$uc_table}Bean();\n";
    echo _tab(2) . "\$sql = \"{CALL `p_{$table_name}_fetch{$proc_suffix}`({$s_qm})}\";\n";

    _php_before_query();
    _php_fetch_param($model, $fetch_by, 4, true);
    _php_on_query();
    _php_fetch_param_bind($model, $fetch_by, 4);
    _php_after_query();

    echo _tab(2) . "\$b_found = false;\n";
    _php_before_result_loop();
    echo _tab(4) . "foreach (\$a_ret as \$kk => \$vv) {\n";
    echo _tab(5) . "if(isset(self::\$m_row_map[\$kk])){\n";
    echo _tab(6) . "\$o_bean->\$kk = \$vv;\n";
    echo _tab(5) . "}\n";
    echo _tab(5) . "\$b_found = true;\n";
    echo _tab(5) . "break;\n";
    echo _tab(4) . "}\n";
    _php_after_result_loop();

    echo _tab(2) . "self::logDebug(__METHOD__, __LINE__, \"call p_{$table_name}_fetch{$proc_suffix}--done--(\$b_found)\");\n";
    echo _tab(2) . "return \$o_bean;\n";

    echo _tab(1) . "}";
}

/**
 * php 模型类--获取一条数据
 *
 * @param $model
 */
function php_model_fetch($model)
{

    if (!_php_db_header($model, "fetch")) {
        return;
    }

    /**
     * 默认主键查询
     */
    $fetch_by = _model_get_ok_fetch_by($model);
    if (count($fetch_by) > 0) {
        _php_model_fetch($model, "default", "默认主键查询", $fetch_by);
        /**
         * 其他可能的主键查询单条语句
         * fetch_by_other=>
         *      ----fetch_name  => fetch_title
         *                      => fetch_by
         */
        $a_fetch_by_other = _model_get_ok_other_fetch_by($model);

        foreach ($a_fetch_by_other as $fetch_name => $a_vv) {
            $fetch_title = $a_vv['fetch_title'];
            $fetch_by = $a_vv['fetch_by'];
            _php_model_fetch($model, $fetch_name, $fetch_title, $fetch_by);
        }
    }
    _php_db_footer($model, "fetch");
}