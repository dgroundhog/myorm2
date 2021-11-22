<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_fetch.php");


/**
 * java 新建数据
 *
 * @param $model
 */

function java_servlet_new($model)
{

    if (!_java_servlet_header($model, "add")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);


    _java_comment("新建数据",1);
    echo _tab(1) ."protected void  _gNew(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

    _java_comment("启用CSRF预防",2);
    echo _tab(2) . "touchFromToken(\"{$table_name}_new\", req, ctx);\n";
    echo _tab(2) ."MyAdmin currAdmin = (MyAdmin) ctx.getVariable(\"admin\");\n";
    _java_comment("加载基本数据",2);

    echo _tab(2) . "Map<String,String> mInfo = new HashMap<String,String>();\n";

    echo _tab(2) . "ctx.setVariable(\"mInfo\", mInfo);\n";

    echo _tab(2) . "String tmpl = \"{$table_name}_new.html\";\n";

    echo _tab(2) . "display(req,resp,ctx,tmpl);\n";
    echo _tab(2) . "return;\n";
    echo _tab(1) . "}\n";


    _java_servlet_footer($model, "add");
}