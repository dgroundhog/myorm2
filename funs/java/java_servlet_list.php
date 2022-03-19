<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_servlet_list.php");


/**
 * java 列表
 *
 * @param $model
 */

function java_servlet_list($model)
{

    if (!_java_servlet_header($model, "list")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);


    _java_comment("获取详情用于显示", 1);
    echo _tab(1) ."protected void  _gList(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

    echo _tab(2) ."MyAdmin currAdmin = (MyAdmin) ctx.getVariable(\"admin\");\n";
    foreach ($model['list_by'] as $key) {
        if (!isset($model['table_fields'][$key]['default'])) {
            $d_value = "";
        } else {
            $d_value = $model['table_fields'][$key]['default'];
        }
        _java_comment($model['table_fields'][$key]['name'], 2);
        _java_req_param($key, $model['table_fields'][$key]['type'], $d_value);
    }

    if (isset($model["list_kw"]) && count($model["list_kw"]) > 0) {
        _java_comment("关键字", 2);
        _java_req_param("kw", "string", "");
        echo _tab(2) . "v_kw = new String(v_kw.getBytes(\"ISO-8859-1\"), \"UTF-8\"); \n";

    }

    if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {

        _java_comment("日期范围", 2);
        echo _tab(2) . "String v_date_from = req.getParameter(\"date_from\");\n";
        echo _tab(2) . "String v_date_to = req.getParameter(\"date_to\");\n";
        echo _tab(2) . "DateFromToBeanx dateFromToBeanx = listDataFromTo(v_date_from,v_date_to);\n";
        echo _tab(2) . "v_date_from = dateFromToBeanx.from;\n";
        echo _tab(2) . "v_date_to = dateFromToBeanx.to;\n";
        echo _tab(2) . "logger.debug(\"date_from--date_to---\" + v_date_from + \"---\" + v_date_to);\n";

    }

    if (isset($model["list_has_order"]) && $model["list_has_order"] != false) {
        _java_comment("排序", 2);
        _java_req_param("order_by", "string", $model["list_order_by"]);
        _java_req_param("order_dir", "string", "DESC");
    }

    _java_comment("分页", 2);
    _java_req_param("page", "int", "1");
    echo _tab(2) . "int i_page_size = 20;\n";

    //计数
    _java_comment("计数", 2);
    echo _tab(2) . "int iTotal = {$uc_table}Model.count(";
    _java_abs_count_param4use($model);
    echo ");\n";

    //列表
    _java_comment("列表", 2);
    echo _tab(2) . "Vector<HashMap> vList = {$uc_table}Model.listBasic(";
    _java_abs_list_param4use($model);
    echo ");\n";




    echo _tab(2) . "ctx.setVariable(\"iTotal\", iTotal);\n";
    echo _tab(2) . "ctx.setVariable(\"vList\", vList);\n";

    echo _tab(2) . "String tmpl = \"{$table_name}_list.html\";\n";


    _java_comment("回传参数", 2);
    foreach ($model['list_by'] as $key) {
        echo _java_tmpl_param($key, $model['table_fields'][$key]['type']);
    }
    if (isset($model["list_kw"]) && count($model["list_kw"]) > 0) {
        echo _java_tmpl_param("kw", "string");
    }

    if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {
        echo _java_tmpl_param("date_from", "string");
        echo _java_tmpl_param("date_to", "string");
    }

    if (isset($model["list_has_order"]) && $model["list_has_order"] != false) {
        echo _java_tmpl_param("order_by", "string");
        echo _java_tmpl_param("order_dir", "string");
    }
    echo _java_tmpl_param("page", "int");


    _java_comment("分页", 2);
    echo _tab(2) . "String urlPage = \"list/?\"\n";
    $ii = 0;
    foreach ($model['list_by'] as $key) {
        $ii++;
        $_prefix = _java_url_join($ii);
        echo _tab(4) ._java_url_param($key, $model['table_fields'][$key]['type'], $_prefix);
    }
    if (isset($model["list_kw"]) && count($model["list_kw"]) > 0) {
        $ii++;
        $_prefix = _java_url_join($ii);
        echo _tab(4) ._java_url_param("kw", "string", $_prefix);
    }
    if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {
        $ii++;
        $_prefix = _java_url_join($ii);
        echo _tab(4) ._java_url_param("date_from", "string", $_prefix);
        $ii++;
        $_prefix = _java_url_join($ii);
        echo _tab(4) ._java_url_param("date_to", "string", $_prefix);
    }
    if (isset($model["list_has_order"]) && $model["list_has_order"] != false) {
        $ii++;
        $_prefix = _java_url_join($ii);
        echo _tab(4) ._java_url_param("order_by", "string", $_prefix);
        $ii++;
        $_prefix = _java_url_join($ii);
        echo _tab(4) ._java_url_param("order_dir", "string", $_prefix);
    }
    echo _tab(4) ."+ \"&\";\n";

    echo _tab(2) . "PagerUtil myPage = new PagerUtil(urlPage, iTotal, i_page_size, i_page);\n";
    echo _tab(2) . "String sPagerHtml =  myPage.getSimplePagerBar();\n";
    echo _tab(2) . "ctx.setVariable(\"pager_html\", sPagerHtml);\n";

    echo _tab(2) . "display(req,resp,ctx,tmpl);\n";
    echo _tab(2) . "return;\n";
    echo _tab(1) . "}\n";


    _java_comment("获取详情用于显示", 1);
    echo _tab(1) ."protected void  _gAjaxList(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";


    foreach ($model['list_by'] as $key) {
        if (!isset($model['table_fields'][$key]['default'])) {
            $d_value = "";
        } else {
            $d_value = $model['table_fields'][$key]['default'];
        }
        _java_comment($model['table_fields'][$key]['name'], 2);
        _java_req_param($key, $model['table_fields'][$key]['type'], $d_value);
    }

    if (isset($model["list_kw"]) && count($model["list_kw"]) > 0) {
        _java_comment("关键字", 2);
        _java_req_param("kw", "string", "");
        echo _tab(2) . "v_kw = new String(v_kw.getBytes(\"ISO-8859-1\"), \"UTF-8\"); \n";
    }

    if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {

        _java_comment("日期范围", 2);
        echo _tab(2) . "String v_date_from = req.getParameter(\"date_from\");\n";
        echo _tab(2) . "String v_date_to = req.getParameter(\"date_to\");\n";
        echo _tab(2) . "DateFromToBeanx dateFromToBeanx = listDataFromTo(v_date_from,v_date_to);\n";
        echo _tab(2) . "v_date_from = dateFromToBeanx.from;\n";
        echo _tab(2) . "v_date_to = dateFromToBeanx.to;\n";
        echo _tab(2) . "logger.debug(\"date_from--date_to---\" + v_date_from + \"---\" + v_date_to);\n";

    }

    if (isset($model["list_has_order"]) && $model["list_has_order"] != false) {
        _java_comment("排序", 2);
        _java_req_param("order_by", "string", $model["list_order_by"]);
        _java_req_param("order_dir", "string", "DESC");
    }

    _java_comment("分页", 2);
    _java_req_param("page", "int", "1");
    echo _tab(2) . "int i_page_size = 20;\n";

    //计数
    _java_comment("计数", 2);
    echo _tab(2) . "int iTotal = {$uc_table}Model.count(";
    _java_abs_count_param4use($model);
    echo ");\n";

    //列表
    _java_comment("列表", 2);
    echo _tab(2) . "Vector<HashMap> vList = {$uc_table}Model.listBasic(";
    _java_abs_list_param4use($model);
    echo ");\n";


    _java_comment("ajax 列表", 2);
    echo _tab(2) . "AjaxResult ajaxResult = new AjaxResult();\n";
    echo _tab(2) . "ajaxResult.setCode(\"ok\");\n";
    echo _tab(2) . "ajaxResult.setI_total(iTotal);\n";
    echo _tab(2) . "ajaxResult.setV_list(vList);\n";
    echo _tab(2) . "_jsonOut(resp,ajaxResult);\n";
    echo _tab(2) . "return;\n";


    echo _tab(1) . "}\n";


    _java_servlet_footer($model, "list");
}