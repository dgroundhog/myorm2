<?php
/**
 * 一些基本函数
 */


/**
 * 输出空格
 * 用空格替代缩进
 * @param int $size
 */
function _tab($size)
{
    $space = "";
    for ($ii = 0; $ii < $size; $ii++) {
        $space .= "    ";
    }
    return $space;
}


/**
 * sql注释头
 * @return void
 */
function _db_comment_begin()
{
    echo "-- ---------- begin ----------\n";
}

/**
 * sql注释尾
 * @return void
 */
function _db_comment_end()
{
    echo "-- ----------- end -----------\n";
}

/**
 * sql 注释
 * @param mixed $msg 消息体
 * @param boolean $with_head 是否包含头尾
 * @return void
 */
function _db_comment($msg, $with_head = false)
{
    if ($with_head) {
        _db_comment_begin();
    }
    if (is_array($msg)) {
        foreach ($msg as $s) {
            echo "-- {$s} \n";
        }
    } else {
        echo "-- {$msg}\n";
    }
    if ($with_head) {
        _db_comment_end();
    }
}

/**
 * 用于生成存储过程的问号
 * @param $i_param
 * @return string
 */
function _db_question_marks($i_param)
{
    $a_qm = array();
    for ($jj = 0; $jj < $i_param; $jj++) {
        $a_qm[$jj] = "?";
    }
    return implode(",", $a_qm);
}

/**
 * 获取存储过程的名字
 * @param $table_name
 * @param $fun_name
 * @param $base_fun
 * @return string
 */
function _db_find_proc_name($table_name,$fun_name,$base_fun){
    switch ($fun_name) {
        case $base_fun:
        case "default":
        case "":
            $fun = $base_fun;
            break;
        case "default_c":
            $fun = "{$base_fun}_c";
            break;
        default:
            $fun = "{$base_fun}_{$fun_name}";
            break;
    }

    return "p_{$table_name}__{$fun}";
}


/**
 * 获取模型中函数的名字
 * @param string $fun_name
 * @param string $base_fun
 * @param string $return_bean 针对基本查询，返回bean
 * @return string
 */
function _db_find_model_fun_name($fun_name, $base_fun, $return_bean = false)
{
    switch ($fun_name) {
        case $base_fun:
        case "default":
        case "":
            $fun = $base_fun;
            break;
        default:
            $fun = "{$base_fun}_{$fun_name}";
            break;
    }

    $real_fun = "{$fun}";
    if ($return_bean) {
        $real_fun = "{$fun}_vBean";
    }
    return $real_fun;
}

///////////////////////////////////////////////////////////////////////////////////////////

/**
 *  添加评论
 * @param $msg
 * @param int $i_tab
 */
function _java_comment($msg, $i_tab = 0)
{
    echo "\n";
    echo _tab($i_tab) . "/**\n";
    if (is_array($msg)) {
        foreach ($msg as $v) {
            echo _tab($i_tab) . " * {$v}\n";
        }
    } else {
        echo _tab($i_tab) . " * {$msg}\n";
    }
    echo _tab($i_tab) . " */\n";
}

/**
 * 符合格式化的备注开始
 * @param $msg
 * @param $i
 * @return void
 */
function _java_comment_header($msg,$i=0)
{
    echo "\n"._tab($i)."/**\n";
    if (is_array($msg)) {
        foreach ($msg as $v) {
            echo _tab($i)."* {$v}\n";
        }
    } else {
        echo _tab($i)." * {$msg}\n";
    }
}

/**
 * 符合格式化的备注结尾
 * @param $i
 * @return void
 */
function _java_comment_footer($i=0)
{
    echo  _tab($i)." */\n";
}



function _java_statement_param_bean($key, $p_type, $ii, $bean)
{

    switch ($p_type) {
        case "blob":
        case "longblob":
            echo "if(v_{$bean}Bean.{$key} == null) {\n";
            echo _tab(5) . "v_{$bean}Bean.{$key} = new byte[0];\n";
            echo _tab(4) . "}\n";
            echo _tab(4) . "ByteArrayInputStream bis_{$key} = new ByteArrayInputStream(v_{$bean}Bean.{$key}); \n";
            echo _tab(4) . "st.setBinaryStream({$ii}, bis_{$key}, bis_{$key}.available());\n";
            break;

        case "int":
            echo "st.setInt({$ii}, v_{$bean}Bean.{$key}); \n";
            break;

        case "varchar":
        default:
            echo "st.setString({$ii}, v_{$bean}Bean.{$key}); \n";
            break;
    }
}




////////////////////////////////////////////////////////////////////////////////////////////////

function _php_header()
{
    echo "<?php";
    echo "\n//auto gen via orm2php";
    echo "\n";
}

/**
 *  添加评论
 * @param $msg
 * @param int $i_tab
 */
function _php_comment($msg, $i_tab = 0)
{
    echo "\n";
    echo _tab($i_tab) . "/**\n";
    if (is_array($msg)) {
        foreach ($msg as $v) {
            echo _tab($i_tab) . " * {$v}\n";
        }
    } else {
        echo _tab($i_tab) . " * {$msg}\n";
    }
    echo _tab($i_tab) . " */\n";
}

/**
 * 渲染评论头
 * @param mixed $msg
 * @param int $i_tab
 */
function _php_comment_header($msg, $i_tab = 0)
{
    echo "\n";
    echo "\n";
    echo _tab($i_tab) . "/**\n";
    if (is_array($msg)) {
        foreach ($msg as $v) {
            echo _tab($i_tab) . " * {$v}\n";
        }
    } else {
        echo _tab($i_tab) . " * {$msg}\n";
    }
    echo _tab($i_tab) . " * \n";
}

/**
 * 渲染评论尾巴
 * @param int $i_tab
 */
function _php_comment_footer($i_tab = 0)
{
    echo _tab($i_tab) . " */\n";
}

/**
 * 获取基本过滤器
 */
 function _app_get_default_filter()
{
    return array(
        "int",
        "trim",
        "string",
        "email",
        "alphanum",
    );
}

/**
 * 换行链接符号
 * @param $ii
 * @return string
 */
function _warp2join($ii)
{
    return ($ii == 0) ? "\n" : ",\n";
}

/**
 * 获取参数的前缀
 *
 * @param string $field_type
 * @return string
 */
function _get_field_param_prefix($field_type)
{

    switch ($field_type) {
        //bool
        case Constant::DB_FIELD_TYPE_BOOL :
            return "b";

        //整型
        case Constant::DB_FIELD_TYPE_INT:
        case Constant::DB_FIELD_TYPE_LONGINT:
            return "i";

        case Constant::DB_FIELD_TYPE_BLOB :
        case Constant::DB_FIELD_TYPE_LONGBLOB :
            return "lb";

        //日期时间
        case Constant::DB_FIELD_TYPE_DATE :
        case Constant::DB_FIELD_TYPE_TIME :
        case Constant::DB_FIELD_TYPE_DATETIME :
            return "dt";

        //字符串
        case Constant::DB_FIELD_TYPE_CHAR:
        case Constant::DB_FIELD_TYPE_VARCHAR:
        case Constant::DB_FIELD_TYPE_TEXT :
        case Constant::DB_FIELD_TYPE_LONGTEXT :
        default :
            return "s";
    }

}
