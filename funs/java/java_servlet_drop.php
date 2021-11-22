<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_drop.php");


/**
 * java 删除2
 *
 * @param $model
 */

function java_servlet_drop($model)
{

    if (!_java_servlet_header($model, "drop")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);


    _java_comment("删除2", 1);
    echo _tab(1) . "protected void  _pDrop(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

    _java_comment("//TODO 判断权限", 2);

    _java_comment("检查攻击", 2);
    echo _tab(2) . "if (!checkFromToken(\"{$table_name}_edit\", req)) {\n";
    echo _tab(3) . "resp.sendRedirect(\"../index.jsp?err_code=CSRF\");\n";
    echo _tab(3) . "return;\n";
    echo _tab(2) . "}\n";
    echo _tab(2) ."MyAdmin currAdmin = (MyAdmin) ctx.getVariable(\"admin\");\n";

    foreach ($model['fetch_by'] as $key) {
        _java_comment($model['table_fields'][$key]['name'], 2);
        _java_req_param($key, $model['table_fields'][$key]['type'], "");
    }


    echo _tab(2) . "int iRet = {$uc_table}Model.drop(";
    _java_abs_drop_param4use($model);
    echo ");\n";

    echo _tab(2) . "String urlGoto = \"list/?ret=\" + iRet;\n";
    echo _tab(2) . "ctx.setVariable(\"url_goto\", urlGoto);\n";
    echo _tab(2) . "String tmpl = \"_302.html\";\n";
    echo _tab(2) . "display(req,resp,ctx,tmpl);\n";
    echo _tab(2) . "return;\n";
    echo _tab(1) . "}\n";

    _java_comment("ajax删除2", 1);
    echo _tab(1) . "protected void  _pAjaxDrop(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

    _java_comment("//TODO 判断权限", 2);
    _java_comment("检查攻击", 2);
    echo _tab(2) ."MyAdmin currAdmin = (MyAdmin) ctx.getVariable(\"admin\");\n";

    _java_comment("检查攻击", 2);
    echo _tab(2) . "if (!checkFromToken(\"{$table_name}_edit\", req)) {\n";
    echo _tab(3) . "resp.sendRedirect(\"../index.jsp?err_code=CSRF\");\n";
    echo _tab(3) . "return;\n";
    echo _tab(2) . "}\n";

    foreach ($model['fetch_by'] as $key) {
        _java_comment($model['table_fields'][$key]['name'], 2);
        _java_req_param($key, $model['table_fields'][$key]['type'], "");
    }


    echo _tab(2) . "int iRet = {$uc_table}Model.drop(";
    _java_abs_drop_param4use($model);
    echo ");\n";

    _java_comment("ajax删除2", 2);
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