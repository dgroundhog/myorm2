<?php

if (!defined("PHP_BASE")) {
    define('PHP_BASE', realpath(dirname(__FILE__)));
}
include_once(PHP_BASE . "/php_base.ini.php");


/**
 * 方法 list-1 构造可变参数备注部分
 *
 * @param array $model 模型
 * @param MyList $my_list 查询结构
 * @param boolean $count_only 仅计数
 * @return void
 */
function _php_list_param_comment($model, $my_list, $count_only)
{
    if (count($my_list->list_by) > 0) {
        foreach ($my_list->list_by as $key) {
            $field = $model['table_fields'][$key];
            $type = _php_get_key_type($field['type']);
            $prefix = _php_get_key_prefix($field['type']);
            echo _tab(1) . " * @param {$type} \${$prefix}_{$key} {$field['name']}\n";
        }
    }

    $a_has_key = _php_list_get_conds();
    foreach ($a_has_key as $cond) {

        $bv = "list_has_{$cond}";
        $kv = "list_{$cond}_key";
        if ($my_list->$bv) {
            switch ($cond) {
                case "kw":
                    echo _tab(1) . " * @param string \$s_kw 搜索关键字\n";
                    break;

                case "date":
                    echo _tab(1) . " * @param string \$s_date_from 开始日期\n";
                    echo _tab(1) . " * @param string \$s_date_to 结束日期\n";
                    break;

                case "time":
                    echo _tab(1) . " * @param string \$s_time_from 开始时间\n";
                    echo _tab(1) . " * @param string \$s_time_to 结束时间\n";
                    break;

                case "between":
                    echo _tab(1) . " * @param int|mixed \$i_between_from 开始值\n";
                    echo _tab(1) . " * @param int|mixed \$i_between_to 结束值\n";
                    break;

                case "notbetween":
                    echo _tab(1) . " * @param int|mixed \$i_between_before 小值之前\n";
                    echo _tab(1) . " * @param int|mixed \$i_between_after 大值之前\n";
                    break;

                case "in":
                case "notin":
                    $key = $my_list->$kv;
                    $name = $model['table_fields'][$key]['name'];
                    echo _tab(1) . " * @param array \$a_{$cond}_{$key} 一组{$name}\n";
                    break;

                case "gt":
                case "gte":
                case "lt":
                case "lte":
                    $key = $my_list->$kv;
                    $name = $model['table_fields'][$key]['name'];
                    echo _tab(1) . " * @param int \$i_{$cond}_{$key} {$name}\n";
                    break;

                default:
                    break;

            }
        }
    }


    /**
     * 仅查询数量
     */
    if ($count_only) {
        echo _tab(1) . " * @return int \n";
        return;
    }

    /**
     * 查询列表
     */
    if ($my_list->list_has_order) {

        if (!is_array($my_list->list_order_by)) {
            if ($my_list->list_order_by == "") {
                echo _tab(1) . " * @param string \$s_order_by 排序字段,可组合使用\n";
                echo _tab(1) . " * @param string \$s_order_dir 排序方式ASC/DESC\n";
            } else {
                if ($my_list->list_order_dir == "") {
                    echo _tab(1) . " * @param string \$s_order_dir 排序方式ASC/DESC\n";
                }
            }


        }

    }
    if (!$my_list->list_has_group) {
        if ($my_list->list_has_pager) {

            if ($my_list->list_pager_type == "normal") {
                echo _tab(1) . " * @param int \$i_page 页码\n";
                echo _tab(1) . " * @param int \$i_page_size 分页大小\n";
            } else {
                //cursor
                echo _tab(1) . " * @param int \$i_cursor_from 最后一个偏移量起点\n";
                echo _tab(1) . " * @param int \$i_cursor_offset 偏移量\n";
            }
        }
    }

    echo _tab(1) . " * @return array \n";


}

/**
 * 方法 list-2 构造可变参数
 *
 * @param array $model 模型
 * @param MyList $my_list 查询结构
 * @param boolean $count_only 仅计数
 * @param int $i_tab 缩进
 * @param boolean $for_proc 如果是true，则array数据为字符串
 * @return int 实际使用参数个数
 */
function _php_list_param($model, $my_list, $count_only, $i_tab = 0, $for_proc = false)
{

    $ii = 0;
    $a_temp = array();

    if (count($my_list->list_by) > 0) {
        foreach ($my_list->list_by as $key) {

            $field = $model['table_fields'][$key];
            //$type = _php_get_key_type($field['type']);
            $prefix = _php_get_key_prefix($field['type']);
            $a_temp[] = "\${$prefix}_{$key}";
            $ii++;
        }
    }


    $a_has_key = _php_list_get_conds();
    foreach ($a_has_key as $cond) {

        $bv = "list_has_{$cond}";
        $kv = "list_{$cond}_key";
        if ($my_list->$bv) {
            switch ($cond) {
                //搜索关键字
                case "kw":
                    $a_temp[] = "\$s_kw";
                    $ii++;
                    break;
                //开始日期--结束日期
                case "date":
                    $a_temp[] = "\$s_date_from";
                    $a_temp[] = "\$s_date_to";
                    $ii++;
                    $ii++;
                    break;
                //开始时间--结束时间
                case "time":
                    $a_temp[] = "\$s_time_from";
                    $a_temp[] = "\$s_time_to";
                    $ii++;
                    $ii++;
                    break;

                //
                case "between":
                    $a_temp[] = "\$i_between_from";
                    $a_temp[] = "\$i_between_to";
                    $ii++;
                    $ii++;
                    break;

                //
                case "notbetween":
                    $a_temp[] = "\$i_between_before";
                    $a_temp[] = "\$i_between_after";
                    $ii++;
                    $ii++;
                    break;

                //
                case "in":
                case "notin":
                    $key = $my_list->$kv;
                    if ($for_proc) {
                        $a_temp[] = "\$s_{$cond}_{$key}";
                    } else {
                        $a_temp[] = "\$a_{$cond}_{$key}";
                    }
                    $ii++;
                    break;

                //
                case "gt":
                case "gte":
                case "lt":
                case "lte":
                    $key = $my_list->$kv;
                    $a_temp[] = "\$i_{$cond}_{$key}";
                    $ii++;
                    break;

                default:
                    break;

            }
        }
    }

    //仅查询数量
    if ($count_only) {
        _php_param_footer($a_temp, $i_tab, $for_proc);
        return $ii;
    }

    //排序
    if ($my_list->list_has_order) {
        if (!is_array($my_list->list_order_by)) {
            if ($my_list->list_order_by == "") {
                $a_temp[] = "\$s_order_by";
                $ii++;
                $a_temp[] = "\$s_order_dir";
                $ii++;
            } else {
                if ($my_list->list_order_dir == "") {
                    $a_temp[] = "\$s_order_dir";
                    $ii++;
                }
            }
        }
    }


    if (!$my_list->list_has_group) {

        if ($my_list->list_has_pager) {

            if ($my_list->list_pager_type == "normal") {
                $a_temp[] = "\$i_page";
                $a_temp[] = "\$i_page_size";
            } else {
                //页码,  偏移量  大于数id
                $a_temp[] = "\$i_cursor_from";
                $a_temp[] = "\$i_cursor_offset";
            }

            $ii++;
            $ii++;
        }
    }

    _php_param_footer($a_temp, $i_tab, $for_proc);


    return $ii;

}


/**
 * 方法 list-3 构造bind参数
 *
 * @param array $model 模型
 * @param MyList $my_list 查询结构
 * @param boolean $count_only 仅计数
 * @param int $i_tab
 * @return int
 */
function _php_list_param_bind($model, $my_list, $count_only, $i_tab = 0)
{

    $ii = 0;
    $a_temp = array();

    if (count($my_list->list_by) > 0) {
        foreach ($my_list->list_by as $key) {
            $field = $model['table_fields'][$key];
            $bind = _php_get_key_bind($field['type']);
            $a_temp[] = _tab($i_tab) . "{$bind}";
            $ii++;
        }
    }

    $s_bind = _php_get_key_bind("string");
    $i_bind = _php_get_key_bind("int");


    $a_has_key = _php_list_get_conds();
    foreach ($a_has_key as $cond) {

        $bv = "list_has_{$cond}";
        $kv = "list_{$cond}_key";
        if ($my_list->$bv) {
            switch ($cond) {
                //搜索关键字
                case "kw":
                case "in":
                case "notin":
                    $a_temp[] = _tab($i_tab) . "{$s_bind}";
                    $ii++;
                    break;

                //开始日期--结束日期
                case "date":
                case "time":
                    $a_temp[] = _tab($i_tab) . "{$s_bind}";
                    $ii++;
                    $a_temp[] = _tab($i_tab) . "{$s_bind}";
                    $ii++;
                    break;

                //
                case "between":
                case "notbetween":
                    $a_temp[] = _tab($i_tab) . "{$i_bind}";
                    $ii++;
                    $a_temp[] = _tab($i_tab) . "{$i_bind}";
                    $ii++;
                    break;

                //
                case "gt":
                case "gte":
                case "lt":
                case "lte":
                    $a_temp[] = _tab($i_tab) . "{$i_bind}";
                    $ii++;
                    break;

                default:
                    break;

            }
        }
    }


    //仅查询数量
    if ($count_only) {
        echo implode(",\n", $a_temp);
        echo "\n";
        return $ii;
    }

    //排序
    if ($my_list->list_has_order) {
        if (!is_array($my_list->list_order_by)) {
            if ($my_list->list_order_by == "") {
                $a_temp[] = _tab($i_tab) . "{$s_bind}";
                $ii++;
                $a_temp[] = _tab($i_tab) . "{$s_bind}";
                $ii++;
            } else {
                if ($my_list->list_order_dir == "") {
                    $a_temp[] = _tab($i_tab) . "{$s_bind}";
                    $ii++;
                }
            }
        }
    }
    if (!$my_list->list_has_group) {
        //页码
        if ($my_list->list_has_pager) {
            $a_temp[] = _tab($i_tab) . "{$i_bind}";
            $a_temp[] = _tab($i_tab) . "{$i_bind}";
            $ii++;
            $ii++;
        }
    }

    echo implode(",\n", $a_temp);
    echo "\n";
    return $ii;


}


/**
 * 方法 list-4 片段
 *
 * @param array $model 模型
 * @param MyList $my_list 查询结构
 * @param int $i_tab
 * @return int
 */
function _php_list_param_array2string($model, $my_list, $i_tab = 0)
{
    if ($my_list->list_has_in) {
        $key = $my_list->list_in_key;
        if ($model["table_fields"][$key]["type"] == "int") {
            echo _tab($i_tab) . "\$s_in_{$key} =  implode(\",\", \$a_in_{$key});\n";
        } else {
            echo _tab($i_tab) . "\$s_in_{$key} =  implode(\"|\", \$a_in_{$key});\n";
        }
    }

    if ($my_list->list_has_notin) {
        $key = $my_list->list_notin_key;
        if ($model["table_fields"][$key]["type"] == "int") {
            echo _tab($i_tab) . "\$s_notin_{$key} =  implode(\",\", \$a_notin_{$key});\n";
        } else {
            echo _tab($i_tab) . "\$s_notin_{$key} =  implode(\"|\", \$a_notin_{$key});\n";
        }
    }
}


/**
 * 集成的获取计数的函数，MyList由外部传入
 * @param $model
 * @param MyList $my_list 查询结构
 * @return     void
 */
function _php_model_count($model, $my_list)
{

    $list_name = $my_list->list_name;
    $list_title = $my_list->list_title;


    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    $lc_list_name = strtolower($list_name);
    $uc_list_name = ucfirst($lc_list_name);

    $fun_suffix = "";
    $proc_suffix = "";
    if ($list_name != "default" && $list_name != "") {
        $fun_suffix = "By{$uc_list_name}";
        $proc_suffix = "_{$lc_list_name}";
    }

    _php_comment_header("{$list_title},获取总记录", 1);
    _php_list_param_comment($model, $my_list, true);
    _php_comment_footer(1);
    echo _tab(1) . "public static function getCount{$fun_suffix}(";
    $i_param = _php_list_param($model, $my_list, true, 2);
    echo ")\n";
    echo _tab(1) . "{\n";

    echo _tab(2) . "\$i_count = 0;\n";
    $s_qm = _question_marks($i_param);
    echo _tab(2) . "\$sql = \"{CALL `p_{$table_name}_count{$proc_suffix}`({$s_qm})}\";\n";

    _php_list_param_array2string($model, $my_list, 2);

    //处理查询
    _php_before_query();
    _php_list_param($model, $my_list, true, 4, true);
    _php_on_query();
    _php_list_param_bind($model, $my_list, true, 4);
    _php_after_query();
    //处理结果
    _php_before_result_loop();
    echo _tab(4) . "foreach (\$a_ret as \$kk => \$vv) {\n";
    echo _tab(5) . "if(\$kk==\"i_count\"){\n";
    echo _tab(6) . "\$i_count = \$vv;\n";
    echo _tab(5) . "}\n";
    echo _tab(5) . "break;\n";
    echo _tab(4) . "}\n";
    _php_after_result_loop();

    echo _tab(2) . "self::logDebug(__METHOD__, __LINE__, \"call p_{$table_name}_count{$proc_suffix}--done--(\$i_count)\");\n";
    echo _tab(2) . "return \$i_count;\n";
    echo _tab(1) . "}";
}

/**
 * 集成的获取列表函数，MyList由外部传入
 * @param $model
 * @param MyList $my_list 查询结构
 * @return     void
 */
function _php_model_list($model, $my_list)
{

    $list_name = $my_list->list_name;
    $list_title = $my_list->list_title;


    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    $lc_list_name = strtolower($list_name);
    $uc_list_name = ucfirst($lc_list_name);

    $fun_suffix = "";
    $proc_suffix = "";
    if ($list_name != "default" && $list_name != "" && $list_name != "list") {
        $fun_suffix = "By{$uc_list_name}";
        $proc_suffix = "_{$lc_list_name}";
    }

    if (!$my_list->list_has_group) {
        /**
         * 先渲染记录查询
         */
        _php_model_count($model, $my_list);
    }
    /**
     * 渲染基本列表
     */
    _php_comment_header("{$list_title},结构为array", 1);
    _php_list_param_comment($model, $my_list, false);
    _php_comment_footer(1);
    echo _tab(1) . "public static function getList{$fun_suffix}(";
    $i_param = _php_list_param($model, $my_list, false, 2);
    echo ")\n";
    echo _tab(1) . "{\n";
    echo _tab(2) . "\$a_list = array();\n";
    $s_qm = _question_marks($i_param);
    echo _tab(2) . "\$sql = \"{CALL `p_{$table_name}_list{$proc_suffix}`({$s_qm})}\";\n";

    _php_list_param_array2string($model, $my_list, 2);

    //处理查询
    _php_before_query();
    _php_list_param($model, $my_list, false, 4, true);
    _php_on_query();
    _php_list_param_bind($model, $my_list, false, 4);
    _php_after_query();
    //处理结果


    _php_before_result_loop();
    echo _tab(4) . "\$a_info = array();\n";
    if ($my_list->list_has_group) {
        $list_group_type = $my_list->list_group_type;
        echo _tab(4) . "\$a_info[\"i_{$list_group_type}\"] = \$a_ret[\"i_{$list_group_type}\"];\n";
        foreach ($my_list->list_group_by as $group_by) {
            echo _tab(4) . "\$a_info[\"{$group_by}\"] = \$a_ret[\"{$group_by}\"];\n";
        }
    } else {
        echo _tab(4) . "foreach (\$a_ret as \$kk => \$vv) {\n";
        echo _tab(5) . "if(isset(self::\$m_row_map[\$kk])){\n";
        echo _tab(6) . "\$a_info[\$kk] = \$vv;\n";
        echo _tab(5) . "}\n";
        echo _tab(4) . "}\n";
    }
    echo _tab(4) . "\$a_list[] = \$a_info;\n";
    _php_after_result_loop();
    echo _tab(2) . "self::logDebug(__METHOD__, __LINE__, \"call p_{$table_name}_list{$proc_suffix}--done\");\n";
    echo _tab(2) . "return \$a_list;\n";
    echo _tab(1) . "}";


    if (!$my_list->list_has_group) {
        /**
         * 渲染bean
         */
        _php_comment_header("{$list_title},取出一组数据,结构为bean", 1);
        _php_list_param_comment($model, $my_list, false);
        _php_comment_footer(1);
        echo _tab(1) . "public static function getBeanList{$fun_suffix}(";
        $i_param = _php_list_param($model, $my_list, false, 2);
        echo ")\n";
        echo _tab(1) . "{\n";
        $s_qm = _question_marks($i_param);
        echo _tab(2) . "\$a_list = array();\n";
        echo _tab(2) . "\$sql = \"{CALL `p_{$table_name}_list{$proc_suffix}`({$s_qm})}\";\n";

        _php_list_param_array2string($model, $my_list, 2);
        //处理查询
        _php_before_query();
        _php_list_param($model, $my_list, false, 4, true);
        _php_on_query();
        _php_list_param_bind($model, $my_list, false, 4);
        _php_after_query();
        //处理结果
        _php_before_result_loop();
        echo _tab(4) . "\$o_bean = new {$uc_table}Bean();\n";
        echo _tab(4) . "foreach (\$a_ret as \$kk => \$vv) {\n";
        echo _tab(5) . "if(isset(self::\$m_row_map[\$kk])){\n";
        echo _tab(6) . "\$o_bean->\$kk = \$vv;\n";
        echo _tab(5) . "}\n";
        echo _tab(4) . "}\n";
        echo _tab(4) . "\$a_list[] = \$o_bean;\n";
        _php_after_result_loop();

        echo _tab(2) . "self::logDebug(__METHOD__, __LINE__, \"call p_{$table_name}_list{$proc_suffix}--for bean done\");\n";
        echo _tab(2) . "return \$a_list;\n";
        echo _tab(1) . "}";
    }

    //关联查询

    /**
     * 基础数据
     */
    foreach ($my_list->sub_list as $sub_list_name => $sub_list_conf) {


        if (isset($sub_list_conf['list_title']) && $sub_list_conf['list_title'] != "") {
            $sub_list_title = $sub_list_conf['list_title'];
        } else {
            $sub_list_title = $my_list->list_title . "-" . $sub_list_name;
        }

        $sub_lc_list_name = strtolower($sub_list_name);
        $sub_uc_list_name = ucfirst($sub_lc_list_name);

        //后缀名扩展，避免冲突
        $sub_fun_suffix = "{$fun_suffix}_{$sub_uc_list_name}";

        $my_sub_list = clone $my_list;
        $my_sub_list->list_name = $sub_list_name;
        $my_sub_list->list_title = $sub_list_title;
        //$my_sub_list->list_has_page = $list_has_page;

        $my_sub_list->list_by = array();
        if (isset($sub_list_conf['list_by']) && is_array($sub_list_conf['list_by'])) {
            $a_temp = array();
            foreach ($sub_list_conf['list_by'] as $kw_key) {
                if (!isset($model['table_fields'][$kw_key])) {
                    continue;
                }
                $a_temp[] = $kw_key;
            }
            if (count($a_temp) > 0) {
                $my_sub_list->list_by = $a_temp;
            }
        }

        if (isset($sub_list_conf['list_has_kw'])) {
            $my_sub_list->list_has_kw = !!$sub_list_conf['list_has_kw'];
        }

        if (isset($sub_list_conf['list_has_date'])) {
            $my_sub_list->list_has_date = !!$sub_list_conf['list_has_date'];
        }

        if (isset($sub_list_conf['list_has_time'])) {
            $my_sub_list->list_has_time = !!$sub_list_conf['list_has_time'];
        }


        //用默认值替换缺失的参数，string 用空，int 用（-1），blob 用 null


        $a_temp = array();
        if (count($my_list->list_by) > 0) {
            foreach ($my_list->list_by as $key) {
                $field = $model['table_fields'][$key];
                //$type = _php_get_key_type($field['type']);
                if (in_array($key, $my_sub_list->list_by)) {
                    $prefix = _php_get_key_prefix($field['type']);
                    $a_temp[] = "\${$prefix}_{$key}";
                } else {
                    $default_val = _php_get_key_default_value($field['type']);
                    $a_temp[] = "$default_val";
                }
            }
        }

        $a_has_key = _php_list_get_conds();
        foreach ($a_has_key as $cond) {

            $bv = "list_has_{$cond}";
            $kv = "list_{$cond}_key";
            if ($my_list->$bv) {
                switch ($cond) {
                    //搜索关键字
                    case "kw":
                        if ($my_sub_list->list_has_kw) {
                            $a_temp[] = "\$s_kw";
                        } else {
                            $a_temp[] = "\"\"";
                        }
                        break;
                    //开始日期--结束日期
                    case "date":
                        if ($my_sub_list->list_has_date) {
                            $a_temp[] = "\$s_date_from";
                            $a_temp[] = "\$s_date_to";
                        } else {
                            $a_temp[] = "\"\"";
                            $a_temp[] = "\"\"";
                        }
                        break;
                    //开始时间--结束时间
                    case "time":
                        if ($my_sub_list->list_has_time) {
                            $a_temp[] = "\$s_time_from";
                            $a_temp[] = "\$s_time_to";
                        } else {
                            $a_temp[] = "\"\"";
                            $a_temp[] = "\"\"";
                        }
                        break;

                    //约定下面八组只能继承
                    //
                    case "between":
                        $a_temp[] = "\$i_between_from";
                        $a_temp[] = "\$i_between_to";

                        break;

                    //
                    case "notbetween":
                        $a_temp[] = "\$i_between_before";
                        $a_temp[] = "\$i_between_after";

                        break;

                    //
                    case "in":
                    case "notin":
                        $key = $my_list->$kv;
                        $a_temp[] = "\$a_{$cond}_{$key}";

                        break;

                    //
                    case "gt":
                    case "gte":
                    case "lt":
                    case "lte":
                        $key = $my_list->$kv;
                        $a_temp[] = "\$i_{$cond}_{$key}";

                        break;

                    default:
                        break;

                }
            }
        }

        //计数的到此结束
        $sub_params_count = _php_param_footer2($a_temp, 4);


        //排序
        if ($my_list->list_has_order) {
            if ($my_sub_list->list_has_order) {
                if ($my_list->list_order_by == "") {
                    $a_temp[] = "\$s_order_by";
                }
                $a_temp[] = "\$s_order_dir";
            } else {
                if ($my_list->list_order_by == "") {
                    $a_temp[] = "\"\"";
                }
                $a_temp[] = "\"\"";
            }
        }

        //页码，如果主列表需要分页，关联列表也需要

        if ($my_list->list_has_pager) {
            if ($my_list->list_pager_type == "normal") {
                $a_temp[] = "\$i_page";
                $a_temp[] = "\$i_page_size";
            } else {
                $a_temp[] = "\$i_cursor_from";
                $a_temp[] = "\$i_cursor_offset";
            }
        }

        $sub_params_list = _php_param_footer2($a_temp, 4);


        if (!$my_list->list_has_group) {
            //非聚合才有独立的计数
            _php_comment_header("{$sub_list_title},获取记录数", 1);
            _php_list_param_comment($model, $my_sub_list, true);
            _php_comment_footer(1);
            echo _tab(1) . "public static function getCount{$sub_fun_suffix}(";
            _php_list_param($model, $my_sub_list, true, 2);
            echo ")\n";
            echo _tab(1) . "{\n";
            echo _tab(2) . "return self::getCount{$fun_suffix}({$sub_params_count});\n";
            echo _tab(1) . "}\n";
        }

        _php_comment_header("{$sub_list_title},取出一组数据,结构为array", 1);
        _php_list_param_comment($model, $my_sub_list, false);
        _php_comment_footer(1);
        echo _tab(1) . "public static function getList{$sub_fun_suffix}(";
        _php_list_param($model, $my_sub_list, false, 2);
        echo ")\n";
        echo _tab(1) . "{\n";
        echo _tab(2) . "return self::getList{$fun_suffix}({$sub_params_list});\n";
        echo _tab(1) . "}\n";

        if (!$my_list->list_has_group) {
//非聚合才有独立的计数
            _php_comment_header("{$sub_list_title},取出一组数据,结构为{$uc_table}Bean", 1);
            _php_list_param_comment($model, $my_sub_list, false);
            _php_comment_footer(1);
            echo _tab(1) . "public static function getBeanList{$sub_fun_suffix}(";
            _php_list_param($model, $my_sub_list, false, 2);
            echo ")\n";
            echo _tab(1) . "{\n";
            echo _tab(2) . "return self::getBeanList{$fun_suffix}({$sub_params_list});\n";
            echo _tab(1) . "}\n";
        }
    }
}

/**
 * java抽象类--查询列表
 *
 * @param array $model
 */
function php_model_list($model)
{
    if (!_php_db_header($model, "list")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    /**
     * 查询列表是一个大数组
     * list_confs =>
     *  ----list_name  => title
     *                  => keys
     */

    $a_list_confs = _model_get_ok_list($model);

    foreach ($a_list_confs as $list_key => $my_list) {

        /**
         * @var MyList $my_list
         */
        //$my_list = new MyList();
        //仅计数
        if (!$my_list->list_has_group) {
            //普通查询
            if ($my_list->count_only == true) {
                _php_model_count($model, $my_list);
            } else {
                _php_model_list($model, $my_list);
            }
        } else {
            //聚合的只有列表
            _php_model_list($model, $my_list);
        }

    }

    _php_db_footer($model, "list");
}
