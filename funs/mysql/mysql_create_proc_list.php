<?php

if (!defined("MYSQL_PROC")) {
    define('MYSQL_PROC', realpath(dirname(__FILE__)));
}
include_once(MYSQL_PROC . "/mysql_base.ini.php");


/**
 * 方法 list-1 构造可变参数sql
 *
 * @param array $model 模型
 * @param MyList $my_list 查询结构
 * @param boolean $count_only 仅计数
 * @return void
 */
function _mysql_proc_list_sql($model, $my_list, $count_only)
{

    $ii = 0;

    if (count($my_list->list_by) > 0) {
        foreach ($my_list->list_by as $key) {
            //$a_temp[] = _mysql_proc_param($model, $key);
            $ii++;
            $p_type = $model['table_fields'][$key]['type'];
            $prefix = _mysql_proc_get_key_prefix($p_type);

            if ($p_type == "int") {
                echo "IF {$prefix}_{$key} >= 0 THEN\n";
                echo _tab(1) . "SET @sql = CONCAT( @sql, ' AND `{$key}` =  \'', {$prefix}_{$key}, '\' ' );\n";
                echo "END IF;\n";
            } else {
                echo "IF {$prefix}_{$key} != '' THEN\n";
                echo _tab(1) . "SET @sql = CONCAT( @sql, ' AND `{$key}` =  \'', {$prefix}_{$key}, '\' ' );\n";
                echo "END IF;\n";
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
                    //$a_temp[] = "IN `s_kw` VARCHAR ( 255 )";
                    echo "IF v_kw != '' THEN\n";
                    echo _tab(1) . "SET @sql = CONCAT( @sql, ' AND (\n";
                    $a_temp0 = array();
                    foreach ($my_list->list_kw as $key) {
                        $a_temp0[] = "LOCATE(\'',s_kw,'\',`{$key}`) > 0  ";
                    }
                    echo implode("\n" . _tab(2) . "OR", $a_temp0);
                    echo "\n";
                    echo _tab(1) . " )');\n";
                    echo "END IF;\n";
                    $ii++;
                    break;
                //开始日期--结束日期
                case "date":
                    //$a_temp[] = "IN `s_date_from` VARCHAR ( 10 )";
                    //$a_temp[] = "IN `s_date_to` VARCHAR ( 10 )";

                    echo "IF s_date_from != '' AND  s_date_to != '' THEN\n";
                    echo _tab(1) . "SET @sql = CONCAT( @sql, ' AND (";
                    $date_key = $my_list->$kv;
                    $p_type = $model['table_fields'][$date_key]['type'];
                    if ($p_type == "datetime") {
                        echo "{$date_key} BETWEEN \'', s_date_from, ' 00:00:00\' AND \'', s_date_to,' 23:59:59\')'";
                    } else {
                        echo "{$date_key} BETWEEN \'', s_date_from, '\' AND \'', s_date_to,'\')'";
                    }
                    echo " );\n";
                    echo "END IF;\n";

                    $ii++;
                    $ii++;
                    break;
                //开始时间--结束时间
                case "time":
                    //$a_temp[] = "IN `s_time_from` VARCHAR ( 19 )";
                    //$a_temp[] = "IN `s_time_to` VARCHAR ( 19 )";
                    echo "IF s_time_from != '' AND  s_time_to != '' THEN\n";
                    echo _tab(1) . "SET @sql = CONCAT( @sql, ' AND (";
                    $time_key = $my_list->$kv;
                    echo "{$time_key} BETWEEN \'', s_time_from, '\' AND \'', s_time_to,'\')'";
                    echo " );\n";
                    echo "END IF;\n";

                    $ii++;
                    $ii++;
                    break;

                //
                case "between":
                    //$a_temp[] = "IN `i_between_from` INT";
                    //$a_temp[] = "IN `i_between_to` INT";
                    $ii++;
                    $ii++;
                    echo "SET @sql = CONCAT( @sql, ' AND (";
                    $between_key = $my_list->$kv;
                    echo "{$between_key} BETWEEN \'', i_between_from, '\' AND \'', i_between_to,'\')'";
                    echo " );\n";

                    break;

                //
                case "notbetween":
                    //$a_temp[] = "IN `i_between_before` INT";
                    //$a_temp[] = "IN `i_between_after` INT";
                    $ii++;
                    $ii++;
                    echo "SET @sql = CONCAT( @sql, ' AND (";
                    $between_key = $my_list->$kv;
                    echo "{$between_key} NOT BETWEEN \'', i_between_before, '\' AND \'', i_between_after,'\')'";
                    echo " );\n";
                    break;

                //
                case "in":
                    $key = $my_list->$kv;
                    //$a_temp[] = "IN `s_{$cond}_{$key}` VARCHAR ( 9999 )";
                    $ii++;
                    echo "IF s_{$cond}_{$key} != '' THEN\n";
                    if ($key == "int") {
                        echo _tab(1) . "SET @sql = CONCAT( @sql, ' {$key} IN(', s_{$cond}_{$key}, ') ');\n";
                    } else {
                        echo _tab(1) . "SET @sql_v = REPLACE(s_{$cond}_{$key}, '|', '\',\'');\n";
                        echo _tab(1) . "SET @sql = CONCAT( @sql, ' {$key} IN(\'',@sql_v,'\') ');\n";
                    }
                    echo "END IF;\n";
                    break;

                case "notin":
                    $key = $my_list->$kv;
                    //$a_temp[] = "IN `s_{$cond}_{$key}` VARCHAR ( 9999 )";
                    $ii++;
                    echo "IF s_{$cond}_{$key} != '' THEN\n";
                    if ($key == "int") {
                        echo _tab(1) . "SET @sql = CONCAT( @sql, ' {$key} NOT IN(', s_{$cond}_{$key}, ') ');\n";
                    } else {
                        echo _tab(1) . "SET @sql_v = REPLACE(s_{$cond}_{$key}, '|', '\',\'');\n";
                        echo _tab(1) . "SET @sql = CONCAT( @sql, ' {$key} NOT IN(\'',@sql_v,'\') ');\n";
                    }
                    echo "END IF;\n";
                    break;

                //
                case "gt":
                case "gte":
                case "lt":
                case "lte":
                    $key = $my_list->$kv;
                    //$a_temp[] = "IN `i_{$cond}_{$key}` INT";
                    $ii++;
                    $real_cond = _mysql_proc_gtelt($cond);
                    echo "SET @sql = CONCAT( @sql, ' AND (";
                    $gtelt_key = $my_list->$kv;
                    echo "{$gtelt_key}  {$real_cond}  \'', i_{$cond}_{$key},'\')'";
                    echo " );\n";
                    break;

                default:
                    break;

            }
        }
    }

    if (isset($model['table_fields']['flag'])) {
        echo "SET @sql = CONCAT( @sql, ' AND `flag` = \'n\'');\n";
    }

    //仅查询数量
    if ($count_only) {
        return;
    }


    if (!$my_list->list_has_group && $my_list->list_has_pager) {
        if ($my_list->list_pager_type != "normal") {
            $kkk = $my_list->cursor_offset_key;
            if ($my_list->list_order_dir == "" || $my_list->list_order_dir == "DESC") {
                echo "SET @sql = CONCAT( @sql, ' AND {$kkk} < ',i_cursor_from,' ');\n";
            } else {
                echo "SET @sql = CONCAT( @sql, ' AND {$kkk} > ',i_cursor_from,' ');\n";
            }
        }
    }

    //排序
    if ($my_list->list_has_order) {
        //        if ($my_list->list_order_by == "") {
        //            $a_temp[] = "IN `s_order_by` VARCHAR ( 255 )";
        //            $ii++;
        //        }
        //        $a_temp[] = "IN `s_order_dir` VARCHAR ( 255 )";
        //        $ii++;
        if (!is_array($my_list->list_order_by)) {
            if ($my_list->list_order_by == "" && $my_list->list_order_dir == "") {
                $ii++;
                echo "SET @sql = CONCAT( @sql, ' ORDER BY ',s_order_by,' ',s_order_dir);\n";
            } else {
                if ($my_list->list_order_dir == "") {
                    echo "SET @sql = CONCAT( @sql, ' ORDER BY {$my_list->list_order_by} ',s_order_dir);\n";
                    //echo "SET @sql = CONCAT( @sql, ' ORDER BY ',s_order_by,' {$my_list->list_order_dir}');\n";
                } else {
                    //不允许 list_order_by为空，而s_order_dir 不为空
                }
            }
        } else {
            $a_temp = array();
            foreach ($my_list->list_order_by as $order_by => $order_dir) {
                $a_temp[] = "{$order_by} {$order_dir}";
            }
            if (count($a_temp) > 0) {
                $s_temp = implode(", ", $a_temp);
                echo "SET @sql = CONCAT( @sql, ' ORDER BY {$s_temp}');\n";
            }
        }
    }

    if (!$my_list->list_has_group) {
        if ($my_list->list_has_pager) {
            //$a_temp[] = "IN `i_page` INT";
            //$a_temp[] = "IN `i_page_size` INT";
            $ii++;
            $ii++;
            if ($my_list->list_pager_type == "normal") {
                echo "SET @sql = CONCAT( @sql, ' LIMIT  ', m_offset, ',',m_length);\n";
            } else {
                //页码,TODO 偏移量  大于数id
                echo "SET @sql = CONCAT( @sql, ' LIMIT  ', i_cursor_offset);\n";
            }
        }
    }
    return;
}


/**
 * 方法 list-2 构造可变参数
 *
 * @param array $model 模型
 * @param MyList $my_list 查询结构
 * @param boolean $count_only 仅计数
 * @return array 参数组合
 */
function _mysql_proc_list_param($model, $my_list, $count_only)
{

    $ii = 0;
    $a_temp = array();
    //$a_temp[] = "1 = 1";

    if (count($my_list->list_by) > 0) {
        foreach ($my_list->list_by as $key) {
            $a_temp[] = _mysql_proc_param($model, $key);
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
                    $a_temp[] = "IN `s_kw` VARCHAR ( 255 )";
                    $ii++;
                    break;
                //开始日期--结束日期
                case "date":
                    $a_temp[] = "IN `s_date_from` VARCHAR ( 10 )";
                    $a_temp[] = "IN `s_date_to` VARCHAR ( 10 )";
                    $ii++;
                    $ii++;
                    break;
                //开始时间--结束时间
                case "time":
                    $a_temp[] = "IN `s_time_from` VARCHAR ( 19 )";
                    $a_temp[] = "IN `s_time_to` VARCHAR ( 19 )";
                    $ii++;
                    $ii++;
                    break;

                //
                case "between":
                    $a_temp[] = "IN `i_between_from` INT";
                    $a_temp[] = "IN `i_between_to` INT";
                    $ii++;
                    $ii++;
                    break;

                //
                case "notbetween":
                    $a_temp[] = "IN `i_between_before` INT";
                    $a_temp[] = "IN `i_between_after` INT";
                    $ii++;
                    $ii++;
                    break;

                //
                case "in":
                case "notin":
                    $key = $my_list->$kv;
                    $a_temp[] = "IN `s_{$cond}_{$key}` VARCHAR ( 9999 )";
                    $ii++;
                    break;

                //
                case "gt":
                case "gte":
                case "lt":
                case "lte":
                    $key = $my_list->$kv;
                    $a_temp[] = "IN `i_{$cond}_{$key}` INT";
                    $ii++;
                    break;

                default:
                    break;

            }
        }
    }

    //仅查询数量
    if ($count_only) {
        return $a_temp;
    }

    //排序
    if ($my_list->list_has_order) {
        if (!is_array($my_list->list_order_by)) {
            //TODO
            if ($my_list->list_order_by == "") {
                $a_temp[] = "IN `s_order_by` VARCHAR ( 255 )";
                $ii++;
                $a_temp[] = "IN `s_order_dir` VARCHAR ( 255 )";
                $ii++;
            } else {
                if ($my_list->list_order_dir == "") {
                    $a_temp[] = "IN `s_order_dir` VARCHAR ( 255 )";
                    $ii++;
                }
            }
        }
    }
    
    if (!$my_list->list_has_group) {
        if ($my_list->list_has_pager) {

            if ($my_list->list_pager_type == "normal") {
                $a_temp[] = "IN `i_page` INT";
                $a_temp[] = "IN `i_page_size` INT";
                $ii++;
                $ii++;
            } else {
                //cursor
                $a_temp[] = "IN `i_cursor_from` INT";
                $a_temp[] = "IN `i_cursor_offset` INT";
                $ii++;
                $ii++;
            }
        }
    }

    return $a_temp;
}

/**
 * mysql存储过程--获取列表的计数器
 *
 * @param array $model
 * @param MyList $my_list 查询结构
 */
function _mysql_create_proc_count($model, $my_list)
{
    $proc_name = _mysql_proc_header($model, $my_list->list_name, $my_list->fetch_title, "count");
    if (null == $proc_name) {
        return;
    }
    $a_temp = _mysql_proc_list_param($model, $my_list, true);
    _mysql_proc_begin($a_temp);

    echo "SET @sql = 'SELECT COUNT(`{$my_list->list_count_key}`) AS i_count FROM`t_{$model['table_name']}` WHERE 1=1 '; \n";

    _mysql_proc_list_sql($model, $my_list, true);
    echo "CALL p_debug('{$proc_name}', @sql);\n";
    echo "PREPARE stmt FROM @sql;\n";
    echo "EXECUTE stmt;\n";
    //echo "COMMIT;\n";
    _mysql_proc_footer($model, $proc_name);
}

/**
 * mysql存储过程--获取列表
 *
 * @param array $model
 * @param MyList $my_list 查询结构
 */
function _mysql_create_proc_list($model, $my_list)
{
    $proc_name = _mysql_proc_header($model, $my_list->list_name, $my_list->list_title, "list");
    if (null == $proc_name) {
        return;
    }
    $a_temp = _mysql_proc_list_param($model, $my_list, false);
    _mysql_proc_begin($a_temp);

    if (!$my_list->list_has_group && $my_list->list_has_pager) {
        if ($my_list->list_pager_type == "normal") {
            echo "DECLARE m_offset INT;\n";
            echo "DECLARE m_length INT;\n";
            echo "SET m_length = i_page_size;\n";
            echo "SET m_offset = ( i_page - 1 ) * i_page_size;\n\n";
        }
    }


    echo "SET @sql = 'SELECT ";

    if (!$my_list->list_has_group) {
        if (count($my_list->list_keys) == 0) {
            echo "*";
        } else {
            if ($my_list->is_distinct) {
                echo "DISTINCT ";
            }
            $a_temp = array();
            foreach ($my_list->list_keys as $skey) {
                $a_temp[] = "`{$skey}`";
            }
            echo implode(",", $a_temp);
        }
    } else {
        $list_group_type = $my_list->list_group_type;
        $list_group_key = $my_list->list_group_key;
        $a_temp = array();
        $a_temp[] = strtoupper($list_group_type) . "(`{$list_group_key}`) AS i_" . strtolower($list_group_type);
        foreach ($my_list->list_group_by as $skey) {
            $a_temp[] = "`{$skey}`";
        }
        echo implode(",", $a_temp);
    }
    echo " FROM `t_{$model['table_name']}` WHERE 1=1 '; \n";

    _mysql_proc_list_sql($model, $my_list, false);
    echo "CALL p_debug('{$proc_name}', @sql);\n";
    echo "PREPARE stmt FROM @sql;\n";
    echo "EXECUTE stmt;\n";
    //echo "COMMIT;\n";
    _mysql_proc_footer($model, $proc_name);

}

/**
 * mysql存储过程--获取列表
 *
 * @param $model
 */
function mysql_create_proc_list($model)
{
    $a_list_confs = _model_get_ok_list($model);
    foreach ($a_list_confs as $list_key => $my_list) {

        /**
         * @var MyList $my_list
         */
        if (!$my_list->list_has_group) {
            //普通查询
            if ($my_list->count_only == true) {
                _mysql_create_proc_count($model, $my_list);
            } else {
                _mysql_create_proc_count($model, $my_list);
                _mysql_create_proc_list($model, $my_list);
            }
        } else {
            //聚合的只有列表
            _mysql_create_proc_list($model, $my_list);
        }

    }
}