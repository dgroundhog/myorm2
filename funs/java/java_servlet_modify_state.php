<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_update_state.php");


/**
 * java 更新状态
 *
 * @param $model
 */

function java_servlet_modify_state($model)
{

    if (!_java_servlet_header($model, "update_state")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);


    _java_comment("更新状态", 1);
    echo _tab(1) . "protected void  _pModifyState(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

    _java_comment("检查攻击", 2);
    echo _tab(2) . "if (!checkFromToken(\"{$table_name}_edit\", req)) {\n";
    echo _tab(3) . "resp.sendRedirect(\"../index.jsp?err_code=CSRF\");\n";
    echo _tab(3) . "return;\n";
    echo _tab(2) . "}\n";
    echo _tab(2) ."MyAdmin currAdmin = (MyAdmin) ctx.getVariable(\"admin\");\n";
    _java_comment("新的状态值", 2);
    _java_req_param("state", $model['table_fields']["state"]['type'], "n");

    foreach ($model['fetch_by'] as $key) {
        _java_comment($model['table_fields'][$key]['name'], 2);
        _java_req_param($key, $model['table_fields'][$key]['type'], "");
    }

    if (isset($model['table_fields']['op_id2'])) {
        _java_comment($model['table_fields']['op_id2']['name'], 2);
        _java_req_param('op_id2', $model['table_fields']['op_id2']['type'], "");
    }
    echo _tab(2) . "int iRet = {$uc_table}Model.updateState(";
    _java_abs_update_state_param4use($model);
    echo ");\n";

    echo _tab(3) . "String urlGoto = \"detail/?\"\n";
    $ii = 0;
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        $_prefix = _java_url_join($ii);
        echo _tab(2) . _java_url_param($key, $model['table_fields'][$key]['type'], $_prefix);
    }
    $ii++;
    $_prefix = _java_url_join($ii);
    echo _tab(5) . "+ \"{$_prefix}ret=\" + iRet\n";
    echo _tab(5) . "+ \"&\";\n";

    echo _tab(2) . "ctx.setVariable(\"url_goto\", urlGoto);\n";
    echo _tab(2) . "String tmpl = \"_302.html\";\n";
    echo _tab(2) . "display(req,resp,ctx,tmpl);\n";
    echo _tab(2) . "return;\n";
    echo _tab(1) . "}\n";

    _java_comment("ajax更新状态", 1);
    echo _tab(1) . "protected void  _pAjaxModifyState(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

    _java_comment("检查攻击", 2);
    echo _tab(2) ."MyAdmin currAdmin = (MyAdmin) ctx.getVariable(\"admin\");\n";

    _java_comment("新的状态值", 2);
    _java_req_param("state", $model['table_fields']["state"]['type'], "n");

    foreach ($model['fetch_by'] as $key) {
        _java_comment($model['table_fields'][$key]['name'], 2);
        _java_req_param($key, $model['table_fields'][$key]['type'], "");
    }

    if (isset($model['table_fields']['op_id2'])) {
        _java_comment($model['table_fields']['op_id2']['name'], 2);
        _java_req_param('op_id2', $model['table_fields']['op_id2']['type'], "");
    }
    echo _tab(2) . "int iRet = {$uc_table}Model.updateState(";
    _java_abs_update_state_param4use($model);
    echo ");\n";
    _java_comment("ajax更新状态", 2);

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