<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_update.php");


/**
 * java 更新
 *
 * @param $model
 */

function java_servlet_modify($model)
{

    if (!_java_servlet_header($model, "update")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);


    _java_comment("更新", 1);
    echo  _tab(1) ."protected void  _pModify(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

    _java_comment("检查攻击", 2);
    echo _tab(2) . "if (!checkFromToken(\"{$table_name}_edit\", req)) {\n";
    echo _tab(3) . "resp.sendRedirect(\"../index.jsp?err_code=CSRF\");\n";
    echo _tab(3) . "return;\n";
    echo _tab(2) . "}\n";
    echo _tab(2) ."MyAdmin currAdmin = (MyAdmin) ctx.getVariable(\"admin\");\n";
    foreach ($model['update_keys'] as $key) {
        if (!isset($model['table_fields'][$key]['default'])) {
            $d_value = "";
        } else {
            $d_value = $model['table_fields'][$key]['default'];
        }
        _java_comment($model['table_fields'][$key]['name'], 2);
        _java_req_param($key, $model['table_fields'][$key]['type'], $d_value);
    }

    foreach ($model['fetch_by'] as $key) {
        _java_comment($model['table_fields'][$key]['name'], 2);
        _java_req_param($key, $model['table_fields'][$key]['type'], "");
    }

    _java_comment("执行更新",2);
    echo _tab(2) . "int iRet = {$uc_table}Model.update(";
    _java_abs_update_param4use($model);
    echo ");\n";

    _java_comment("重新读一次",2);
    echo _tab(2) . "Map<String,String> mInfo = {$uc_table}Model.fetch(";
    _java_abs_fetch_param4use($model);
    echo ");\n";



    echo _tab(2) . "String urlGoto = \"detail/?\"\n";
    $ii = 0;
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        $_prefix = _java_url_join($ii);
        echo _tab(3) ._java_url_param($key, $model['table_fields'][$key]['type'], $_prefix);
    }
    $ii++;
    $_prefix = _java_url_join($ii);
    echo _tab(4) ."+ \"{$_prefix}ret=\" + iRet\n";
    echo _tab(4) ."+ \"&\";\n";

    echo _tab(2) . "ctx.setVariable(\"url_goto\", urlGoto);\n";
    echo _tab(2) . "String tmpl = \"_302.html\";\n";
    echo _tab(2) . "display(req,resp,ctx,tmpl);\n";

    echo _tab(2) . "return;\n";

    echo _tab(1) . "}\n";




    _java_comment("ajax更新", 1);
    echo  _tab(1) ."protected void  _pAjaxModify(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

    _java_comment("检查攻击", 2);
    echo _tab(2) ."MyAdmin currAdmin = (MyAdmin) ctx.getVariable(\"admin\");\n";

    foreach ($model['update_keys'] as $key) {
        if (!isset($model['table_fields'][$key]['default'])) {
            $d_value = "";
        } else {
            $d_value = $model['table_fields'][$key]['default'];
        }
        _java_comment($model['table_fields'][$key]['name'], 2);
        _java_req_param($key, $model['table_fields'][$key]['type'], $d_value);
    }

    foreach ($model['fetch_by'] as $key) {
        _java_comment($model['table_fields'][$key]['name'], 2);
        _java_req_param($key, $model['table_fields'][$key]['type'], "");
    }

    _java_comment("执行更新",2);
    echo _tab(2) . "int iRet = {$uc_table}Model.update(";
    _java_abs_update_param4use($model);
    echo ");\n";

    _java_comment("重新读一次",2);
    echo _tab(2) . "Map<String,String> mInfo = {$uc_table}Model.fetch(";
    _java_abs_fetch_param4use($model);
    echo ");\n";

    _java_comment("ajax更新", 2);
    echo _tab(2) . "AjaxResult ajaxResult = new AjaxResult();\n";
    echo _tab(2) . "if(iRet==1){\n";
    echo _tab(3) . "ajaxResult.setCode(\"ok\");\n";
    echo _tab(2) . "}else{\n";
    echo _tab(3) . "ajaxResult.setCode(\"E0000\");\n";
    echo _tab(3) . "ajaxResult.setRet(\"\"+iRet);\n";
    echo _tab(2) . "}\n";
    echo _tab(2) . "_jsonOut(resp,ajaxResult);\n";
    echo _tab(2) . "return;\n";
    echo _tab(1) . "}\n";


    _java_servlet_footer($model, "fetch");
}