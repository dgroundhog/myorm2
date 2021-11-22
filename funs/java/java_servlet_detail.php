<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_fetch.php");


/**
 * java 显示- 展示细节 detail--fetch
 *
 * @param $model
 */

function java_servlet_detail($model)
{

    if (!_java_servlet_header($model, "fetch")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);


    _java_comment("获取详情用于显示", 1);
    echo _tab(1) . "protected void  _gDetail(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

    echo _tab(2) ."MyAdmin currAdmin = (MyAdmin) ctx.getVariable(\"admin\");\n";
    foreach ($model['fetch_by'] as $key) {
        _java_comment($model['table_fields'][$key]['name'], 2);
        _java_req_param($key, $model['table_fields'][$key]['type'], "");
    }


    echo _tab(2) . "Map<String,String> mInfo = {$uc_table}Model.fetch(";
    _java_abs_fetch_param4use($model);
    echo ");\n";


    echo _tab(2) . "ctx.setVariable(\"mInfo\", mInfo);\n";

    echo _tab(2) . "String tmpl = \"{$table_name}_detail.html\";\n";

    echo _tab(2) . "display(req,resp,ctx,tmpl);\n";
    echo _tab(2) . "return;\n";
    echo _tab(1) . "}\n";


    _java_comment("获取ajax详情用于显示", 1);
    echo _tab(1) . "protected void  _gAjaxDetail(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";


    foreach ($model['fetch_by'] as $key) {
        _java_comment($model['table_fields'][$key]['name'], 2);
        _java_req_param($key, $model['table_fields'][$key]['type'], "");
    }


    echo _tab(2) . "Map<String,String> mInfo = {$uc_table}Model.fetch(";
    _java_abs_fetch_param4use($model);
    echo ");\n";


    _java_comment("ajax 详情", 2);
    echo _tab(2) . "AjaxResult ajaxResult = new AjaxResult();\n";
    echo _tab(2) . "ajaxResult.setCode(\"ok\");\n";
    echo _tab(2) . "ajaxResult.setM_detail(mInfo);\n";
    echo _tab(2) . "_jsonOut(resp,ajaxResult);\n";

    echo _tab(2) . "return;\n";


    echo _tab(1) . "}\n";


    _java_servlet_footer($model, "fetch");
}