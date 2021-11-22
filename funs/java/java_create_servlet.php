<?php
if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");


include_once JAVA_BASE . "/java_servlet_new.php";
include_once JAVA_BASE . "/java_servlet_save.php";
include_once JAVA_BASE . "/java_servlet_detail.php";
include_once JAVA_BASE . "/java_servlet_edit.php";
include_once JAVA_BASE . "/java_servlet_list.php";
include_once JAVA_BASE . "/java_servlet_modify.php";
include_once JAVA_BASE . "/java_servlet_modify_state.php";
include_once JAVA_BASE . "/java_servlet_delete.php";
include_once JAVA_BASE . "/java_servlet_drop.php";


/**
 * 建立servlet类
 * @param $package
 * @param $model
 */
function java_create_servlet($package, $model)
{

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    echo "package  {$package}.servlet;\n";

    echo "import {$package}.MyApp;\n";
    echo "import {$package}.MyAdmin;\n";
    echo "import {$package}.MyContext;\n";
    echo "import {$package}.bean.{$uc_table}Bean;\n";
    echo "import {$package}.bean.AjaxResult;\n";
    echo "import {$package}.bean.AjaxBeanx;\n";
    echo "import {$package}.bean.DateFromToBeanx;\n";
    echo "import {$package}.bean.UploadBeanx;\n";
    echo "import {$package}.bean.AdminBean;\n";

    echo "import {$package}.model.{$uc_table}Model;\n";

    echo "import com.hongshi_tech.utils.PagerUtil;\n";
    echo "import com.hongshi_tech.utils.DatetimeUtil;\n";

    echo "import org.slf4j.Logger;\n";
    echo "import org.slf4j.LoggerFactory;\n";
    echo "import org.apache.commons.lang3.StringUtils;\n";


    echo "import java.io.IOException;\n";
    echo "import java.io.InputStream;\n";
    echo "import java.io.ByteArrayInputStream;\n";
    echo "import org.thymeleaf.context.WebContext;\n";
    echo "import javax.servlet.ServletException;\n";
    echo "import javax.servlet.http.HttpServlet;\n";
    echo "import javax.servlet.http.HttpServletResponse;\n";
    echo "import javax.servlet.http.HttpServletRequest;\n";
    echo "import java.text.SimpleDateFormat;\n";
    echo "import java.util.*;\n";


    _java_comment("java servlet类--{$table_name}");
    echo "public class {$uc_table}Servlet extends HtmlServletBase {\n";
    _java_comment("日志类", 1);
    echo _tab(1) . "private  static Logger logger = LoggerFactory.getLogger({$uc_table}Servlet.class);\n";

    _java_comment("GET请求", 1);
    echo _tab(1) . "public void onGet(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";
    _java_comment("检查登陆状态", 2);
    echo _tab(2) . "if (!checkLogin(req, resp, ctx)) {\n";
    echo _tab(3) . "return;\n";
    echo _tab(2) . "}\n";

    echo _tab(2) . "ctx.setVariable(\"model_title\",\"{$model['table_title']}\");\n";

    if (isset($model['keys_by_select']) && count($model['keys_by_select']) > 0) {
        foreach ($model['keys_by_select'] as $key) {
            if (isset($model['table_fields'][$key])) {
                $uc_key = ucfirst($key);
                echo _tab(2) . "ctx.setVariable(\"m{$uc_key}List\", {$uc_table}Model.get{$uc_key}List());\n";
            }
        }
    }
    _java_comment("判断路由", 2);
    echo _tab(2) . "String PATH_ACTION = getPrimaryActionFromPathInfo(req);\n";
    echo _tab(2) . "logger.debug(\"\\nPATH_ACTION--\" + PATH_ACTION);\n";
    echo _tab(2) . "switch (PATH_ACTION) {\n";


    if (isset($model["add_enable"]) && $model["add_enable"]) {

        _java_comment("新建", 3);
        echo _tab(3) . "case \"/add\":\n";
        echo _tab(4) . "ctx.setVariable(\"action_title\", \"新建\");\n";
        echo _tab(4) . "_gNew(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";
    }

    if (isset($model["fetch_enable"]) && $model["fetch_enable"]) {
        _java_comment("详情", 3);
        echo _tab(3) . "case \"/detail\":\n";
        echo _tab(4) . "ctx.setVariable(\"action_title\", \"详情\");\n";
        echo _tab(4) . "_gDetail(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";
        echo _tab(3) . "case \"/ajax_detail\":\n";
        echo _tab(4) . "_gAjaxDetail(req,resp,ctx);\n";
        echo _tab(4) . "break;";
    }

    if (isset($model["update_enable"]) && $model["update_enable"]) {
        _java_comment("编辑", 3);
        echo _tab(3) . "case \"/edit\":\n";
        echo _tab(4) . "ctx.setVariable(\"action_title\", \"编辑\");\n";
        echo _tab(4) . "_gEdit(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";
    }

    if (isset($model["list_enable"]) && $model["list_enable"]) {
        _java_comment("列表", 3);
        echo _tab(3) . "case \"/list\":\n";
        echo _tab(4) . "ctx.setVariable(\"action_title\", \"列表\");\n";
        echo _tab(4) . "_gList(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";
        echo _tab(3) . "case \"/ajax_list\":\n";
        echo _tab(4) . "_gAjaxList(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";
    }
    _java_comment("默认", 3);
    echo _tab(3) . "default:\n";
    echo _tab(4) . "ctx.setVariable(\"action_title\", \"默认页\");\n";
    echo _tab(4) . "_indexAction(req,resp,ctx);\n";
    echo _tab(4) . "break;\n";


    echo _tab(2) . "}\n";
    echo _tab(1) . "}\n";

    _java_comment("POST请求", 1);
    echo _tab(1) . "public void onPost(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

    echo _tab(2) . "if (!checkLogin(req, resp, ctx)) {\n";
    echo _tab(3) . "return;\n";
    echo _tab(2) . "}\n";

    echo _tab(2) . "ctx.setVariable(\"model_title\",\"{$model['table_title']}\");\n";

    _java_comment("判断路由", 2);
    echo _tab(2) . "String PATH_ACTION = getPrimaryActionFromPathInfo(req);\n";
    echo _tab(2) . "logger.debug(\"\\nPATH_ACTION--\" + PATH_ACTION);\n";
    echo _tab(2) . "switch (PATH_ACTION) {\n";

    if (isset($model["add_enable"]) && $model["add_enable"]) {
        _java_comment("添加", 3);
        echo _tab(3) . "case \"/save\":\n";
        echo _tab(4) . "_pSave(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";
        echo _tab(3) . "case \"/ajax_save\":\n";
        echo _tab(4) . "_pAjaxSave(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";

    }

    if (isset($model["update_enable"]) && $model["update_enable"]) {

        _java_comment("编辑", 3);
        echo _tab(3) . "case \"/modify\":\n";
        echo _tab(4) . "_pModify(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";
        echo _tab(3) . "case \"/ajax_modify\":\n";
        echo _tab(4) . "_pAjaxModify(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";

    }

    if (isset($model["update_state_enable"]) && $model["update_state_enable"]) {

        _java_comment("更新状态", 3);
        echo _tab(3) . "case \"/modify_state\":\n";
        echo _tab(4) . "_pModifyState(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";
        echo _tab(3) . "case \"/ajax_modify_state\":\n";
        echo _tab(4) . "_pAjaxModifyState(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";


    }

    if (isset($model["delete_enable"]) && $model["delete_enable"]) {
        _java_comment("删除1", 3);
        echo _tab(3) . "case \"/delete\":\n";
        echo _tab(4) . "_pDelete(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";
        echo _tab(3) . "case \"/ajax_delete\":\n";
        echo _tab(4) . "_pAjaxDelete(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";


    }

    if (isset($model["drop_enable"]) && $model["drop_enable"]) {
        _java_comment("清除2", 3);
        echo _tab(3) . "case \"/drop\":\n";
        echo _tab(4) . "_pDrop(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";
        echo _tab(3) . "case \"/ajax_drop\":\n";
        echo _tab(4) . "_pAjaxDrop(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";

    }
    _java_comment("默认", 3);
    echo _tab(3) . "default:\n";
    echo _tab(4) . "_indexAction(req,resp,ctx);\n";
    echo _tab(4) . "break;\n";


    echo _tab(2) . "}\n";

    echo _tab(1) . "}\n";


    /**
     * 获取详情用于显示
     */
    _java_comment("默认页", 1);
    echo _tab(1) . "protected void _indexAction(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException { \n";
    echo _tab(2) . "ctx.setVariable(\"hello\", \"world\");\n";
    echo _tab(2) . "String tmpl = \"{$table_name}_index.html\";\n";
    echo _tab(2) . "display(req, resp, ctx, tmpl);\n";
    echo _tab(1) . "}\n";


    java_servlet_new($model);
    java_servlet_detail($model);
    java_servlet_edit($model);
    java_servlet_list($model);
    java_servlet_modify($model);
    java_servlet_modify_state($model);
    java_servlet_delete($model);
    java_servlet_drop($model);
    java_servlet_save($model);


    echo "}

";
}