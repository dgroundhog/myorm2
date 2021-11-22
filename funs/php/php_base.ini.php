<?php


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


function _php_db_header($model, $fun)
{

    echo "\n\n";
    if ($fun != "list_basic") {
        if (!isset($model["{$fun}_enable"]) || !$model["{$fun}_enable"]) {
            echo "// ----------------------------\n";
            echo "// NO define for {$model['table_name']} {$fun} \n";
            echo "// ----------------------------\n";
            return false;
        }
    }

    echo "//----------------------------\n";
    echo "// Define {$model['table_name']} {$fun} \n";
    echo "//----------------------------\n";

    return true;
}

function _php_db_footer($model, $fun)
{
    echo "\n";
    echo "//----------------------------\n";
    echo "// END define {$model['table_name']} {$fun} \n";
    echo "//----------------------------\n";
}


function _php_servlet_header($model, $fun)
{

    echo "\n\n";
    if (!isset($model["{$fun}_enable"]) || !$model["{$fun}_enable"]) {
        echo "// ----------------------------\n";
        echo "// NO servlet for {$model['table_name']} {$fun} \n";
        echo "// ----------------------------\n";
        return false;
    }

    echo "//----------------------------\n";
    echo "//servlet {$model['table_name']} {$fun} \n";
    echo "//----------------------------\n";

    return true;
}

function _php_servlet_footer($model, $fun)
{

    echo "\n";
    echo "//----------------------------\n";
    echo "//END servlet {$model['table_name']} {$fun} \n";
    echo "//----------------------------\n";
}

function _question_marks($i_param)
{
    $a_qm = array();
    for ($jj = 0; $jj < $i_param; $jj++) {
        $a_qm[$jj] = "?";
    }
    echo _tab(2) . "//bind param count--($i_param)\n";
    return implode(",", $a_qm);
}

function _php_param_join($ii)
{

    return ($ii == 1) ? "" : ", ";

}

function _php_url_join($ii)
{

    return ($ii == 1) ? "" : "&";

}

function _php_db_warp($ii, $join = ",")
{

    return ($ii == 1) ? "\n  " : ",\n  ";

}

function _php_statement_param($key, $p_type, $ii)
{

    switch ($p_type) {
        case "longblob":
            echo "if(v_{$key} == null) {\n";
            echo _tab(5) . "v_{$key} = new byte[0];\n";
            echo _tab(4) . "}\n";
            echo _tab(4) . "ByteArrayInputStream bis_{$key} = new ByteArrayInputStream(v_{$key}); \n";
            echo _tab(4) . "st.setBinaryStream({$ii}, bis_{$key}, bis_{$key}.available());\n";
            break;

        case "int":
            echo "st.setInt({$ii}, v_{$key}); \n";
            break;

        case "varchar":
        default:
            echo "st.setString({$ii}, v_{$key}); \n";
            break;
    }
}


function _php_statement_param_bean($key, $p_type, $ii, $bean)
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

function _php_result_bean($key, $p_type, $bean)
{

    switch ($p_type) {
        case "blob":
        case "longblob":
            echo "is = rs.getBinaryStream(\"{$key}\");\n";
            echo _tab(5) . "if (is != null) {\n";
            echo _tab(6) . "buf = new byte[is.available()];\n";
            echo _tab(6) . "is.read(buf);\n";
            echo _tab(6) . "{$bean}Bean.{$key} = buf ;\n";
            echo _tab(5) . "}\n";
            break;

        case "int":
            echo "{$bean}Bean.{$key} = rs.getInt(\"{$key}\");\n";
            break;

        case "varchar":
        default:
            echo "{$bean}Bean.{$key} = rs.getString(\"{$key}\");\n";
            break;
    }
}


function _php_query_header($db_type, $op_type = "write")
{


    $db_type1 = ucfirst($db_type);

    if ($op_type == "read") {
        echo _tab(2) . "InputStream is=null;\n";
        echo _tab(2) . "byte[] buf = null;\n";
    }

    echo _tab(2) . "Connection conn = null;\n";
    echo _tab(2) . "CallableStatement st = null;\n";
    echo _tab(2) . "ResultSet rs = null;\n";
    echo _tab(2) . "try {\n";
    echo _tab(3) . "conn = Db{$db_type1}.getConnection();\n";
    echo _tab(3) . "if (!conn.isClosed()) {\n";
}

function _php_query_footer($db_type, $op_type = "write")
{

    $db_type1 = ucfirst($db_type);
    echo _tab(3) . "}\n";
    echo _tab(2) . "} catch (SQLException e0) {\n";
    echo _tab(3) . "logger.error(\"SQLException-e0\",e0);\n";
    echo _tab(2) . "} catch (ClassNotFoundException e1) {\n";
    echo _tab(3) . "logger.error(\"ClassNotFoundException\",e1);\n";
    echo _tab(2) . "} catch (Exception e2) {\n";
    echo _tab(3) . "logger.error(\"FinalException\",e2);\n";
    echo _tab(2) . "} finally {\n";
    echo _tab(3) . "Db{$db_type1}.release(conn, st, rs);\n";
    if ($op_type == "read") {


        echo _tab(3) . "try {\n";
        echo _tab(4) . "if(null != is){\n";
        echo _tab(5) . "is.close();\n";
        echo _tab(4) . "}\n";
        echo _tab(3) . "} catch (Exception e3) {\n";
        echo _tab(4) . "e3.printStackTrace();\n";
        echo _tab(3) . "}\n";


        echo _tab(3) . "is=null;\n";
        echo _tab(3) . "buf = null;\n";
    }
    echo _tab(2) . "}\n";
}


function _php_db_param($key, $p_type, $_prefix)
{
    switch ($p_type) {
        case "blob":
        case "longblob":
            echo "{$_prefix} byte[] v_{$key}";
            break;
        case "int":
            echo "{$_prefix} int v_{$key}";
            break;
        default:
            echo "{$_prefix} String v_{$key}";
            break;
    }
}

function _php_req2db_param($key, $p_type, $_prefix, $in_model = false)
{
    if ($in_model) {
        echo "{$_prefix}v_{$key}";
        return;
    }
    switch ($p_type) {
        case "int":
            echo "{$_prefix}i_{$key}";
            break;
        default:
            echo "{$_prefix}v_{$key}";
            break;
    }
}

function _php_tmpl_param($key, $p_type)
{
    switch ($p_type) {

        case "int":
            echo _tab(2) . "ctx.setVariable(\"curr_{$key}\", i_{$key});\n";
            break;
        default:
            echo _tab(2) . "ctx.setVariable(\"curr_{$key}\", v_{$key});\n";
            break;
    }
}


function _php_url_param($key, $p_type, $_prefix)
{
    switch ($p_type) {

        case "int":
            echo "+ \"{$_prefix}{$key}=\" + i_{$key}\n";
            break;
        default:
            echo "+ \"{$_prefix}{$key}=\" + v_{$key}\n";
            break;
    }
}

function _php_url2_param($key, $p_type, $_prefix)
{
    switch ($p_type) {

        case "int":
            echo "+ \"{$_prefix}{$key}=\" + i_{$key}\n";
            break;
        default:
            echo "+ \"{$_prefix}{$key}=\" + v_{$key}\n";
            break;
    }
}


/**
 * 网页传入的参数
 * @param $key
 * @param $p_type
 * @param $d_value
 */
function _php_req_param2($key, $p_type, $d_value)
{
    if ($key == "op_id1" || $key == "op_id2") {
        echo _tab(2) . "String v_{$key} = currAdmin.getId();\n";
        return;
    }
    switch ($p_type) {
        case "blob":
        case "longblob":
            echo _tab(2) . "byte[] v_{$key} = null;\n";
            echo _tab(2) . "//TODO add bin data here;\n";
            break;
        case "int":
            echo _tab(2) . "String v_{$key} = req.getParameter(\"{$key}\");\n";
            if ($d_value == "") {
                echo _tab(2) . "int i_{$key}_default = 0;\n";
            } else {
                echo _tab(2) . "int i_{$key}_default = {$d_value};\n";
            }
            echo _tab(2) . "int i_{$key} = tidyIntParam(v_{$key},i_{$key}_default); \n";
            echo _tab(2) . "logger.debug(\"{$key}-----\" + i_{$key});\n";
            break;
        default:
            echo _tab(2) . "String v_{$key} = req.getParameter(\"{$key}\");\n";
            echo _tab(2) . "v_{$key} = tidyStrParam(v_{$key},\"{$d_value}\");\n";
            echo _tab(2) . "logger.debug(\"GetParameter---{$key}---\" + v_{$key});\n";
            break;
    }
}


/**
 * 输入参数构建
 * @param array $model
 * @param string $key
 * @param string $method
 * @param int $i_tab
 * @return void
 */
function _php_req_param($model, $key, $method = "get", $i_tab = 0)
{

    $lu_method = strtoupper($method);

    $field = $model['table_fields'][$key];

    echo _tab($i_tab) . "//获取 {$field["name"]}\n";


    $prefix = _php_get_key_prefix($field['type']);
    $vvv = "\${$prefix}_{$key}";

    if ($key == "op_id1" || $key == "op_id2") {
        echo _tab($i_tab) . "{$vvv} = \$this->_visitor->getId();\n";
        return;
    }

    $_filter = "trim";
    //默认仅去空白
    $a_filter = _get_default_filter();
    if (isset($field["filter"]) && $field["filter"] != "") {
        if (in_array($field["filter"], $a_filter)) {
            $_filter = $field["filter"];
        }
    }

    if ($field['type'] == "int" && $_filter != "int") {
        $_filter = "int";
    }


    echo _tab($i_tab) . "//获取 {$field["name"]}\n";

    if ($method == "get") {
        if (isset($field["default_value"])) {
            echo _tab($i_tab) . "{$vvv} = \$this->request->get(\"{$key}\", \"{$_filter}\", \"{$field["default_value"]}\");\n";
        } else {
            echo _tab($i_tab) . "{$vvv} = \$this->request->get(\"{$key}\", \"{$_filter}\");\n";
        }
    } else {
        if (isset($field["default_value"])) {
            echo _tab($i_tab) . "{$vvv} = \$this->request->getPost(\"{$key}\", \"{$_filter}\", \"{$field["default_value"]}\");\n";
        } else {
            echo _tab($i_tab) . "{$vvv} = \$this->request->getPost(\"{$key}\", \"{$_filter}\");\n";
        }
    }
    echo _tab($i_tab) . "\$this->logger->debug(\"{$lu_method}[{$key}][{$field["name"]}]--({$vvv})\");\n\n";

}

/**
 * 获取php中参数的定义
 *
 * @param $field_type
 * @return string
 */
function _php_get_key_type($field_type)
{
    switch ($field_type) {
        case "blob":
        case "longblob":
            return "string|object";
            break;
        case "int":
            return "int";
            break;
        default:
            return "string";
            break;
    }
}

/**
 * 获取php中参数的默认值
 *
 * @param $field_type
 * @return string
 */
function _php_get_key_default_value($field_type)
{
    switch ($field_type) {
        case "blob":
        case "longblob":
            return "null";
            break;
        case "int":
            return "-1";
            break;
        default:
            return "\"\"";
            break;
    }
}

/**
 * 获取php中参数的前缀
 *
 * @param $field_type
 * @return string
 */
function _php_get_key_prefix($field_type)
{
    switch ($field_type) {
        case "blob":
        case "longblob":
            return "b";
            break;
        case "int":
            return "i";
            break;
        default:
            return "s";
            break;
    }
}

/**
 * 获取php中参数的绑定方式
 *
 * @param $field_type
 * @return string
 */
function _php_get_key_bind($field_type)
{
    switch ($field_type) {
        case "blob":
        case "longblob":
            return "Db\\Column::BIND_PARAM_BLOB";
            break;
        case "int":
            return "Db\\Column::BIND_PARAM_INT";
            break;
        default:
            return "Db\\Column::BIND_PARAM_STR";
            break;
    }
}


function _php_before_query()
{
    echo _tab(2) . "\$a_ret_arr = self::getInst()->getReadConnection()->query(\n";
    echo _tab(3) . "\$sql,\n";
    echo _tab(3) . "[\n";
}

function _php_on_query()
{
    echo _tab(3) . "],\n";
    echo _tab(3) . "[\n";
}

function _php_after_query()
{
    echo _tab(3) . "]\n";
    // echo _tab(2) . ")->fetchAll(PDO::FETCH_ASSOC);\n";
    echo _tab(2) . ")->fetchAll();\n";
}

function _php_before_result_loop()
{
    echo "\n";
    echo _tab(2) . "if(\$a_ret_arr != null && is_array(\$a_ret_arr) && count(\$a_ret_arr) > 0 ){\n";
    echo _tab(3) . "foreach (\$a_ret_arr as \$a_ret) {\n";
}

function _php_after_result_loop()
{
    echo _tab(3) . "}\n";
    echo _tab(2) . "}\n\n";
}


/**
 * 参数渲染尾部
 * @param $a_temp
 * @param int $i_tab
 * @param bool $for_proc
 */
function _php_param_footer($a_temp, $i_tab = 0, $for_proc = false)
{
    $ii = count($a_temp);
    if ($for_proc) {
        echo _tab($i_tab);
        echo implode(",\n" . _tab($i_tab), $a_temp);
        echo "\n";
    } else {
        if ($ii > 3) {
            echo "\n" . _tab($i_tab);
            echo implode(",\n" . _tab($i_tab), $a_temp);

            if ($i_tab > 0) {
                echo "\n" . _tab($i_tab - 1);
            } else {
                echo "\n";
            }
        } else {
            echo implode(",", $a_temp);
        }
    }
}


/**
 * 参数渲染尾部
 * @param $a_temp
 * @param int $i_tab
 * @return string
 */
function _php_param_footer2($a_temp, $i_tab = 0)
{
    $s = "";
    $ii = count($a_temp);
    if ($ii > 3) {
        $s .= "\n" . _tab($i_tab);
        $s .= implode(",\n" . _tab($i_tab), $a_temp);

        if ($i_tab > 0) {
            $s .= "\n" . _tab($i_tab - 1);
        } else {
            $s .= "\n";
        }
    } else {
        $s .= implode(",", $a_temp);
    }

    return $s;
}


function _php_controller_header($model, $fun)
{

    echo "\n\n";
    if (!isset($model["{$fun}_enable"]) || !$model["{$fun}_enable"]) {
        echo "// ----------------------------\n";
        echo "// NO controller for {$model['table_name']} {$fun} \n";
        echo "// ----------------------------\n";
        return false;
    }

    echo "//----------------------------\n";
    echo "// Define controller {$model['table_name']} {$fun} \n";
    echo "//----------------------------\n";

    return true;
}

function _php_controller_footer($model, $fun)
{

    echo "\n";
    echo "//----------------------------\n";
    echo "//END controller {$model['table_name']} {$fun} \n";
    echo "//----------------------------\n";
}
