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


///////////////////////////////////////////////////////////////////////////////////////////



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
function _fun_comment($msg, $i_tab = 0)
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
function _fun_comment_header($msg, $i_tab = 0)
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
 * 渲染中间的注释
 * @param mixed $msg
 * @param int $i_tab
 */
function _fun_comment_mid($msg, $i_tab = 0)
{
    if (is_array($msg)) {
        foreach ($msg as $v) {
            echo _tab($i_tab) . " * {$v}\n";
        }
    } else {
        echo _tab($i_tab) . " * {$msg}\n";
    }
}

/**
 * 渲染评论尾巴
 * @param int $i_tab
 */
function _fun_comment_footer($i_tab = 0)
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

