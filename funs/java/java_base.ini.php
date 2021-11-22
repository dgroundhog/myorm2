<?php

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

function _java_comment_header($msg)
{
    echo "\n/**\n";
    if (is_array($msg)) {
        foreach ($msg as $v) {
            echo "* {$v}\n";
        }
    } else {
        echo "* {$msg}\n";
    }
}

function _java_comment_footer()
{
    echo " */\n";
}


function _java_db_header($model, $fun)
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
    echo "//define {$model['table_name']} {$fun} \n";
    echo "//----------------------------\n";

    return true;
}

function _java_db_footer($model, $fun)
{

    echo "\n";
    echo "//----------------------------\n";
    echo "//END define {$model['table_name']} {$fun} \n";
    echo "//----------------------------\n";
}


function _java_servlet_header($model, $fun)
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

function _java_servlet_footer($model, $fun)
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
    return implode(",", $a_qm);
}

function _java_param_join($ii)
{

    return ($ii == 1) ? "" : ", ";

}

function _java_url_join($ii)
{

    return ($ii == 1) ? "" : "&";

}

function _java_db_warp($ii, $join = ",")
{

    return ($ii == 1) ? "\n  " : ",\n  ";

}

function _java_statement_param($key, $p_type, $ii)
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

function _java_result_bean($key, $p_type, $bean)
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


function _java_query_header($db_type, $op_type = "write")
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

function _java_query_footer($db_type, $op_type = "write")
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


function _java_db_param($key, $p_type, $_prefix)
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

function _java_req2db_param($key, $p_type, $_prefix, $in_model = false)
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

function _java_tmpl_param($key, $p_type)
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


function _java_url_param($key, $p_type, $_prefix)
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

function _java_url2_param($key, $p_type, $_prefix)
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
function _java_req_param($key, $p_type, $d_value)
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


