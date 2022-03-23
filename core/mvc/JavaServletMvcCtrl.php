<?php
$tpl_debug = false;
if ($tpl_debug) {
    ?>
    <!doctype html>
    <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:th="http://www.thymeleaf.org">
    <?php
}
if (!defined("MVC_ROOT")) {
    define('MVC_ROOT', realpath(dirname(__FILE__)));
}
include_once(MVC_ROOT . "/JavaServletMvc.php");

class JavaServletMvcCtrl extends JavaServletMvc
{

    /**
     * 获取个体的函数的函数
     * @var MyFun
     */
    public $fun_fetch = null;

    /**
     * 新建的函数
     * @var MyFun
     */
    public $fun_add = null;

    /**
     * 获取列表的函数
     * @var MyFun
     */
    public $fun_list = null;

    /**
     * 创建控制层,只生成default的UI
     * @param MyModel $model
     * @return mixed
     */
    function ccCtrl($model)
    {
        //仅创建default的方法
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $lc_model_name = strtolower($model_name);
        SeasLog::info("创建JAVA控制器--{$model_name}");
        $_target = $this->odir_controllers . DS . "{$uc_model_name}Servlet.java";
        ob_start();

        $package = $this->final_package;
        echo "package  {$package}.servlet;\n";

        echo "import {$package}.MyApp;\n";
        echo "import {$package}.MyAdmin;\n";
        echo "import {$package}.MyContext;\n";
        echo "import {$package}.beans.{$uc_model_name}Bean;\n";
        echo "import {$package}.bean.AjaxResult;\n";
        echo "import {$package}.bean.AjaxBeanx;\n";
        echo "import {$package}.bean.DateFromToBeanx;\n";
        echo "import {$package}.bean.UploadBeanx;\n";
        echo "import {$package}.bean.AdminBean;\n";

        echo "import {$package}.models.{$uc_model_name}Model;\n";

        //echo "import com.hongshi_tech.utils.PagerUtil;\n";
        //echo "import com.hongshi_tech.utils.DatetimeUtil;\n";

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

        _fun_comment("java 控制器 servlet类--{$model_name}");
        echo "public class {$uc_model_name}Servlet extends HtmlServletBase {\n";

        _fun_comment("日志", 1);
        echo _tab(1) . "private static Logger logger = LoggerFactory.getLogger({$uc_model_name}Servlet.class);\n";

        _fun_comment("GET请求", 1);
        echo _tab(1) . "public void onGet(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

        _fun_comment("检查登陆状态", 2);
        echo _tab(2) . "if (!checkLogin(req, resp, ctx)) {\n";
        echo _tab(3) . "return;\n";
        echo _tab(2) . "}\n";

        echo _tab(2) . "ctx.setVariable(\"model_title\",\"{$model->title}\");\n";

        _fun_comment("模块内基础字典", 2);
        foreach ($model->field_list as $field) {
            /* @var MyField $field */
            if ($field->input_hash != "") {
                $key = $field->name;
                $uc_key = ucfirst($key);
                _fun_comment("基础字典-{$field->title}", 2);
                echo _tab(2) . "ctx.setVariable(\"m_{$uc_key}_KV\", {$uc_model_name}Model.get{$uc_key}_KV());\n";
            }
        }


        _fun_comment("判断路由", 2);
        echo _tab(2) . "String PATH_ACTION = getPrimaryActionFromPathInfo(req);\n";
        echo _tab(2) . "logger.debug(\"PATH_ACTION(\" + PATH_ACTION + \")\");\n";
        echo _tab(2) . "switch (PATH_ACTION) {\n";

        foreach ($model->fun_list as $o_fun) {

            /* @var MyFun $o_fun */
            $fun_type = $o_fun->type;
            $fun_name = $o_fun->name;
            if ($fun_name != "default") {
                continue;
            }
            switch ($fun_type) {
                case Constant::FUN_TYPE_ADD:
                    $this->fun_add = $o_fun;
                    _fun_comment("新建", 3);
                    echo _tab(3) . "case \"/add\":\n";
                    echo _tab(4) . "ctx.setVariable(\"action_title\", \"默认新建\");\n";
                    echo _tab(4) . "_gNew(req,resp,ctx);\n";
                    echo _tab(4) . "break;\n";
                    break;

                case Constant::FUN_TYPE_UPDATE:
                    _fun_comment("编辑", 3);
                    echo _tab(3) . "case \"/edit\":\n";
                    echo _tab(4) . "ctx.setVariable(\"action_title\", \"默认编辑\");\n";
                    echo _tab(4) . "_gEdit(req,resp,ctx);\n";
                    echo _tab(4) . "break;\n";
                    break;

                case Constant::FUN_TYPE_FETCH:
                    $this->fun_fetch = $o_fun;
                    _fun_comment("详情", 3);
                    echo _tab(3) . "case \"/detail\":\n";
                    echo _tab(4) . "ctx.setVariable(\"action_title\", \"默认详情\");\n";
                    echo _tab(4) . "_gDetail(req,resp,ctx);\n";
                    echo _tab(4) . "break;\n";
                    echo _tab(3) . "case \"/ajax_detail\":\n";
                    echo _tab(4) . "_gAjaxDetail(req,resp,ctx);\n";
                    echo _tab(4) . "break;";
                    break;

                case Constant::FUN_TYPE_LIST:
                    $this->fun_list = $o_fun;

                    _fun_comment("列表", 3);
                    echo _tab(3) . "case \"/list\":\n";
                    echo _tab(4) . "ctx.setVariable(\"action_title\", \"默认列表\");\n";
                    echo _tab(4) . "_gList(req,resp,ctx);\n";
                    echo _tab(4) . "break;\n";
                    echo _tab(3) . "case \"/ajax_list\":\n";
                    echo _tab(4) . "_gAjaxList(req,resp,ctx);\n";
                    echo _tab(4) . "break;\n";

                default:
                    break;
            }
        }

        _fun_comment("默认index页", 3);
        echo _tab(3) . "default:\n";
        echo _tab(4) . "ctx.setVariable(\"action_title\", \"默认index页\");\n";
        echo _tab(4) . "_indexAction(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";

        echo _tab(2) . "}\n";
        echo _tab(1) . "}\n";

        _fun_comment("POST请求", 1);
        echo _tab(1) . "public void onPost(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

        _fun_comment("检查登陆状态", 2);
        echo _tab(2) . "if (!checkLogin(req, resp, ctx)) {\n";
        echo _tab(3) . "return;\n";
        echo _tab(2) . "}\n";

        echo _tab(2) . "ctx.setVariable(\"model_title\",\"{$model->title}\");\n";

        _fun_comment("判断路由", 2);
        echo _tab(2) . "String PATH_ACTION = getPrimaryActionFromPathInfo(req);\n";
        echo _tab(2) . "logger.debug(\"\\nPATH_ACTION--\" + PATH_ACTION);\n";
        echo _tab(2) . "switch (PATH_ACTION) {\n";


        foreach ($model->fun_list as $o_fun) {

            /* @var MyFun $o_fun */
            $fun_type = $o_fun->type;
            $fun_name = $o_fun->name;
            if ($fun_name != "default") {
                continue;
            }
            switch ($fun_type) {
                case Constant::FUN_TYPE_ADD:

                    _fun_comment("FORM添加", 3);
                    echo _tab(3) . "case \"/save\":\n";
                    echo _tab(4) . "_pSave(req,resp,ctx);\n";
                    echo _tab(4) . "break;\n";

                    _fun_comment("AJAX添加", 3);
                    echo _tab(3) . "case \"/ajax_save\":\n";
                    echo _tab(4) . "_pAjaxSave(req,resp,ctx);\n";
                    echo _tab(4) . "break;\n";
                    break;

                case Constant::FUN_TYPE_UPDATE:
                    _fun_comment("FORM编辑", 3);
                    echo _tab(3) . "case \"/modify\":\n";
                    echo _tab(4) . "_pModify(req,resp,ctx);\n";
                    echo _tab(4) . "break;\n";

                    _fun_comment("AJAX编辑", 3);
                    echo _tab(3) . "case \"/ajax_modify\":\n";
                    echo _tab(4) . "_pAjaxModify(req,resp,ctx);\n";
                    echo _tab(4) . "break;\n";
                    break;

                case Constant::FUN_TYPE_DELETE:
                    _fun_comment("FORM删除", 3);
                    echo _tab(3) . "case \"/delete\":\n";
                    echo _tab(4) . "_pDelete(req,resp,ctx);\n";
                    echo _tab(4) . "break;\n";

                    _fun_comment("AJAX删除", 3);
                    echo _tab(3) . "case \"/ajax_delete\":\n";
                    echo _tab(4) . "_pAjaxDelete(req,resp,ctx);\n";
                    echo _tab(4) . "break;\n";
                    break;


                default:
                    break;
            }
        }


        _fun_comment("默认index页", 3);
        echo _tab(3) . "default:\n";
        echo _tab(4) . "_indexAction(req,resp,ctx);\n";
        echo _tab(4) . "break;\n";


        echo _tab(2) . "}\n";
        echo _tab(1) . "}\n";


        /**
         * 获取详情用于显示
         */
        _fun_comment("默认页", 1);
        echo _tab(1) . "protected void _indexAction(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException { \n";
        echo _tab(2) . "ctx.setVariable(\"hello\", \"world\");\n";
        echo _tab(2) . "String tmpl = \"{$lc_model_name}_index.html\";\n";
        echo _tab(2) . "display(req, resp, ctx, tmpl);\n";
        echo _tab(1) . "}\n";


        //生成各自的函数
        foreach ($model->fun_list as $o_fun) {

            /* @var MyFun $o_fun */
            $fun_type = $o_fun->type;
            $fun_name = $o_fun->name;
            if ($fun_name != "default") {
                continue;
            }
            switch ($fun_type) {
                case Constant::FUN_TYPE_ADD:
                    //add formPost ajaxPost
                    $this->cAdd($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_DELETE:
                    $this->cDelete($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_UPDATE:
                    $this->cUpdate($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_FETCH:
                    $this->cFetch($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_LIST:
                default:
                    $this->cList($model, $o_fun);
                    break;
            }
        }


        echo "}";

        $cc_data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $cc_data);
    }

    /**
     * 增加
     * @param MyModel $model
     * @param MyFun $fun
     * @return int
     */
    function cAdd(MyModel $model, MyFun $fun)
    {

        $this->_funHeader($model, $fun);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $lc_model_name = strtolower($model_name);

        SeasLog::info("新建数据Form");
        _fun_comment("新建数据", 1);
        echo _tab(1) . "protected void  _gNew(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

        _fun_comment("启用CSRF预防", 2);
        echo _tab(2) . "touchFromToken(\"{$model_name}_new\", req, ctx);\n";
        echo _tab(2) . "MyVisitor currVisitor = (MyVisitor) ctx.getVariable(\"__visitor__\");\n";
        _fun_comment("加载基本数据TODO", 2);
        echo _tab(2) . "Map<String,String> mInfo = new HashMap<String,String>();\n";
        echo _tab(2) . "ctx.setVariable(\"mInfo\", mInfo);\n";
        echo _tab(2) . "String tmpl = \"{$model_name}_new.html\";\n";

        echo _tab(2) . "display(req,resp,ctx,tmpl);\n";
        echo _tab(2) . "return;\n";
        echo _tab(1) . "}\n";

        list($is_return_new_id, $i_param, $a_param_comment, $a_param_define, $a_param_use, $a_param_type, $a_param_key, $a_param_field) = $this->parseAdd_field($model, $fun);


        SeasLog::info("Form保存");
        _fun_comment("Form保存", 1);
        echo _tab(1) . "protected void  _pSave(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

        _fun_comment("检查CSRF攻击", 2);
        echo _tab(2) . "if (!checkFromToken(\"{$model_name}_new\", req)) {\n";
        echo _tab(3) . "resp.sendRedirect(\"../index.jsp?err_code=CSRF\");\n";
        echo _tab(3) . "return;\n";
        echo _tab(2) . "}\n";
        echo _tab(2) . "MyVisitor currVisitor = (MyVisitor) ctx.getVariable(\"__visitor__\");\n";

        _fun_comment("获取POST参数", 2);
        $this->_echoReqParams($a_param_define, $a_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("执行保存", 2);
        echo _tab(2) . "{$uc_model_name}Model oModel = new {$uc_model_name}Model();\n";
        echo _tab(2) . "int iRet = oModel.add(";
        $this->_echoFunParams($a_param_use);
        echo _tab(2) . ");\n";

        echo _tab(2) . "String urlGoto = \"list?ret=\" + iRet;\n";
        echo _tab(2) . "ctx.setVariable(\"url_goto\", urlGoto);\n";
        echo _tab(2) . "String tmpl = \"_302.html\";\n";
        echo _tab(2) . "display(req,resp,ctx,tmpl);\n";
        echo _tab(2) . "return;\n";
        echo _tab(1) . "}\n";


        SeasLog::info("Ajax保存");
        _fun_comment("Ajax保存", 1);
        echo _tab(1) . "protected void  _pAjaxSave(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

        _fun_comment("检查攻击", 2);
        echo _tab(2) . "MyVisitor currVisitor = (MyVisitor) ctx.getVariable(\"__visitor__\");\n";

        _fun_comment("获取POST参数", 2);
        $this->_echoReqParams($a_param_define, $a_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("执行保存", 2);
        echo _tab(2) . "{$uc_model_name}Model oModel = new {$uc_model_name}Model();\n";
        echo _tab(2) . "int iRet = oModel.add(";
        $this->_echoFunParams($a_param_use);
        echo _tab(1) . ");\n";

        echo _tab(2) . "AjaxResult ajaxResult = new AjaxResult();\n";
        echo _tab(2) . "if(iRet==1){\n";
        echo _tab(3) . "ajaxResult.setCode(ECode.SUCC);\n";
        echo _tab(2) . "}else{\n";
        echo _tab(3) . "ajaxResult.setCode(ECode.E0000);\n";
        echo _tab(3) . "ajaxResult.setRet(\"\"+iRet);\n";
        echo _tab(2) . "}\n";
        echo _tab(2) . "_jsonOut(resp,ajaxResult);\n";
        echo _tab(2) . "return;\n";
        echo _tab(1) . "}\n";


        $this->_funFooter($model, $fun);
    }

    function _echoReqParams($a_param_define, $a_param_field)
    {
        $ii = 0;
        foreach ($a_param_define as $param) {
            $o_field = $a_param_field[$ii];
            $key = $o_field->name;
            echo "\n";
            echo _tab(2) . "String v_{$key} = req.getParameter(\"{$key}\");\n";
            $d_value = $o_field->default_value;
            if ($this->isIntType($o_field->type) || $this->isBoolType($o_field->type)) {
                $d_value = ($d_value == "") ? 0 : (1 * $d_value);
                echo _tab(2) . "{$param} = tidyIntParam(v_{$key},{$d_value});\n";
            } else {
                echo _tab(2) . "{$param} = tidyStrParam(v_{$key},\"{$d_value}\");\n";
            }
            if ($this->isBlobType($o_field->type)) {
                echo _tab(2) . "//TODO add bin data here;\n";
            }
            if ($o_field->input_hash != "") {
                echo _tab(2) . "//TODO 输入为有限的字典值 {$o_field->input_hash};\n";
            }
            if ($o_field->filter != "" && $o_field->filter != "NO_FILTER") {
                echo _tab(2) . "//TODO 默认过滤器 {$o_field->filter};\n";
            }
            if ($o_field->regexp != "") {
                echo _tab(2) . "//TODO 验证正则表达式 {$o_field->regexp};\n";
            }

            echo _tab(2) . "logger.debug(\"GetParam-({$key})--(\" + v_{$key} + \")\");\n";
            $ii++;
        }

    }

    /**
     * 删除
     * @param MyModel $model
     * @param MyFun $fun
     * @return int
     */
    function cDelete(MyModel $model, MyFun $fun)
    {
        if ($this->fun_fetch == null) {
            SeasLog::error("No default fetch FUN defined in this model ");
            return;
        }

        $this->_funHeader($model, $fun);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $lc_model_name = strtolower($model_name);

        //删除条件
        list($i_w_param, $a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_type, $a_w_param_field) = $this->_procWhereCond($model, $fun);

        _fun_comment("FORM删除", 1);
        echo _tab(1) . "protected void  _pDelete(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

        _fun_comment("检查CSRF攻击", 2);
        echo _tab(2) . "if (!checkFromToken(\"{$model_name}_edit\", req)) {\n";
        echo _tab(3) . "resp.sendRedirect(\"../index.jsp?err_code=CSRF\");\n";
        echo _tab(3) . "return;\n";
        echo _tab(2) . "}\n";
        echo _tab(2) . "MyVisitor currVisitor = (MyVisitor) ctx.getVariable(\"__visitor__\");\n";
        _fun_comment("获取POST参数", 2);

        _fun_comment("删除条件", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("执行删除", 2);
        echo _tab(2) . "{$uc_model_name}Model oModel = new {$uc_model_name}Model();\n";
        echo _tab(2) . "int iRet = oModel.delete(";
        $this->_echoFunParams($a_w_param_use);
        echo ");\n";

        echo _tab(2) . "String urlGoto = \"list/?ret=\" + iRet;\n";
        echo _tab(2) . "ctx.setVariable(\"url_goto\", urlGoto);\n";
        echo _tab(2) . "String tmpl = \"_302.html\";\n";
        echo _tab(2) . "display(req,resp,ctx,tmpl);\n";
        echo _tab(2) . "return;\n";

        echo _tab(1) . "}\n";

        _fun_comment("ajax删除", 1);
        echo _tab(1) . "protected void  _pAjaxDelete(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

        echo _tab(2) . "MyVisitor currVisitor = (MyVisitor) ctx.getVariable(\"__visitor__\");\n";

        _fun_comment("获取POST参数", 2);
        _fun_comment("删除条件", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("执行删除", 2);
        echo _tab(2) . "{$uc_model_name}Model oModel = new {$uc_model_name}Model();\n";
        echo _tab(2) . "int iRet = oModel.delete(";
        $this->_echoFunParams($a_w_param_use);
        echo ");\n";

        echo _tab(2) . "AjaxResult ajaxResult = new AjaxResult();\n";
        echo _tab(2) . "if(iRet==1){\n";
        echo _tab(3) . "ajaxResult.setCode(ECode.SUCC);\n";
        echo _tab(2) . "}else{\n";
        echo _tab(3) . "ajaxResult.setCode(ECode.E0000);\n";
        echo _tab(3) . "ajaxResult.setRet(\"\"+iRet);\n";
        echo _tab(2) . "}\n";
        echo _tab(2) . "_jsonOut(resp,ajaxResult);\n";
        echo _tab(2) . "return;\n";
        echo _tab(1) . "}\n";

        $this->_funFooter($model, $fun);
    }

    /**
     * 修改
     * @param MyModel $model
     * @param MyFun $fun
     * @return int
     */
    function cUpdate(MyModel $model, MyFun $fun)
    {
        if ($this->fun_fetch == null) {
            SeasLog::error("No default fetch FUN defined in this model ");
            return;
        }

        $this->_funHeader($model, $fun);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $lc_model_name = strtolower($model_name);
        SeasLog::info("修改数据Form");

        _fun_comment("获取详情用于显示", 1);
        echo _tab(1) . "protected void  _gEdit(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

        _fun_comment("启用CSRF预防", 2);
        echo _tab(2) . "touchFromToken(\"{$model_name}_edit\", req, ctx);\n";
        echo _tab(2) . "MyVisitor currVisitor = (MyVisitor) ctx.getVariable(\"__visitor__\");\n";


        //更新条件
        list($i_w_param, $a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_type, $a_w_param_field) = $this->_procWhereCond($model, $this->fun_fetch);

        _fun_comment("获取GET参数", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        echo _tab(2) . "{$uc_model_name}Model oModel = new {$uc_model_name}Model();\n";
        echo _tab(2) . "Map<String,String> mInfo = oModel.fetch(";
        $this->_echoFunParams($a_w_param_use);
        echo ");\n";

        echo _tab(2) . "ctx.setVariable(\"mInfo\", mInfo);\n";
        echo _tab(2) . "String tmpl = \"{$model_name}_edit.html\";\n";
        echo _tab(2) . "display(req,resp,ctx,tmpl);\n";
        echo _tab(1) . "}\n";


        //需要更新的字段
        list($i_u_param, $a_u_param_comment, $a_u_param_define, $a_u_param_use, $a_u_param_type, $a_u_param_key, $a_u_param_field) = $this->_parseUpdate_field($model, $fun);
        //更新条件
        list($i_w_param, $a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_type, $a_w_param_field) = $this->_procWhereCond($model, $fun);


        _fun_comment("FORM更新", 1);
        echo _tab(1) . "protected void  _pModify(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

        _fun_comment("检查CSRF攻击", 2);
        echo _tab(2) . "if (!checkFromToken(\"{$model_name}_edit\", req)) {\n";
        echo _tab(3) . "resp.sendRedirect(\"../index.jsp?err_code=CSRF\");\n";
        echo _tab(3) . "return;\n";
        echo _tab(2) . "}\n";
        echo _tab(2) . "MyVisitor currVisitor = (MyVisitor) ctx.getVariable(\"__visitor__\");\n";
        _fun_comment("获取POST参数", 2);
        _fun_comment("需要更新的字段", 2);
        $this->_echoReqParams($a_u_param_define, $a_u_param_field);
        _fun_comment("需要条件", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("执行更新", 2);
        echo _tab(2) . "{$uc_model_name}Model oModel = new {$uc_model_name}Model();\n";
        echo _tab(2) . "int iRet = oModel.update(";
        $this->_echoFunParams($a_u_param_use, $a_w_param_use);
        echo ");\n";

        echo _tab(2) . "String urlGoto = \"detail/?iRet=\" + iRet\n";
        $ii = 0;
        foreach ($a_w_param_field as $o_field) {
            $_param_use = $a_w_param_use[$ii];
            echo _tab(5) . "+ \"{$o_field->key}=\" + {$_param_use}\n";
            $ii++;
        }
        echo _tab(2) . "ctx.setVariable(\"url_goto\", urlGoto);\n";
        echo _tab(2) . "String tmpl = \"_302.html\";\n";
        echo _tab(2) . "display(req,resp,ctx,tmpl);\n";

        echo _tab(2) . "return;\n";

        echo _tab(1) . "}\n";


        _fun_comment("ajax更新", 1);
        echo _tab(1) . "protected void  _pAjaxModify(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

        echo _tab(2) . "MyVisitor currVisitor = (MyVisitor) ctx.getVariable(\"__visitor__\");\n";

        _fun_comment("获取POST参数", 2);
        _fun_comment("需要更新的字段", 2);
        $this->_echoReqParams($a_u_param_define, $a_u_param_field);
        _fun_comment("需要条件", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("执行更新", 2);
        echo _tab(2) . "{$uc_model_name}Model oModel = new {$uc_model_name}Model();\n";
        echo _tab(2) . "int iRet = oModel.update(";
        $this->_echoFunParams($a_u_param_use, $a_w_param_use);
        echo ");\n";

        echo _tab(2) . "AjaxResult ajaxResult = new AjaxResult();\n";
        echo _tab(2) . "if(iRet==1){\n";
        echo _tab(3) . "ajaxResult.setCode(ECode.SUCC);\n";
        echo _tab(2) . "}else{\n";
        echo _tab(3) . "ajaxResult.setCode(ECode.E0000);\n";
        echo _tab(3) . "ajaxResult.setRet(\"\"+iRet);\n";
        echo _tab(2) . "}\n";
        echo _tab(2) . "_jsonOut(resp,ajaxResult);\n";
        echo _tab(2) . "return;\n";
        echo _tab(1) . "}\n";

        $this->_funFooter($model, $fun);
    }

    /**
     * 读取一个
     * @param MyModel $model
     * @param MyFun $fun
     * @return int
     */
    function cFetch(MyModel $model, MyFun $fun)
    {
        if ($this->fun_fetch == null) {
            SeasLog::error("No default fetch FUN defined in this model ");
            return;
        }

        $this->_funHeader($model, $fun);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $lc_model_name = strtolower($model_name);


        _fun_comment("获取详情显示", 1);
        echo _tab(1) . "protected void  _gDetail(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";

        _fun_comment("检查攻击", 2);
        echo _tab(2) . "MyVisitor currVisitor = (MyVisitor) ctx.getVariable(\"__visitor__\");\n";
        //更新条件
        list($i_w_param, $a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_type, $a_w_param_field) = $this->_procWhereCond($model, $this->fun_fetch);

        _fun_comment("获取GET参数", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        echo _tab(2) . "{$uc_model_name}Model oModel = new {$uc_model_name}Model();\n";
        echo _tab(2) . "Map<String,String> mInfo = oModel.fetch(";
        $this->_echoFunParams($a_w_param_use);
        echo ");\n";

        echo _tab(2) . "String tmpl = \"{$model_name}_detail.html\";\n";

        echo _tab(2) . "display(req,resp,ctx,tmpl);\n";
        echo _tab(2) . "return;\n";
        echo _tab(1) . "}\n";


        _fun_comment("获取ajax详情用于显示", 1);
        echo _tab(1) . "protected void  _pAjaxDetail(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";


        _fun_comment("获取POST参数", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        echo _tab(2) . "{$uc_model_name}Model oModel = new {$uc_model_name}Model();\n";
        echo _tab(2) . "Map<String,String> mInfo = oModel.fetch(";
        $this->_echoFunParams($a_w_param_use);
        echo ");\n";


        echo _tab(2) . "AjaxResult ajaxResult = new AjaxResult();\n";
        echo _tab(2) . "ajaxResult.setCode(ECode.SUCC);\n";
        echo _tab(2) . "ajaxResult.setData(mInfo);\n";
        echo _tab(2) . "_jsonOut(resp,ajaxResult);\n";

        echo _tab(2) . "return;\n";


        echo _tab(1) . "}\n";


        $this->_funFooter($model, $fun);

    }

    /**
     * 聚合查询、统计
     * @param MyModel $model
     * @param MyFun $fun
     * @return array
     */
    function cList(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $lc_model_name = strtolower($model_name);

        $a_all_fields = $model->field_list_kv;//通过主键访问的字段
        $fun_type = $fun->type;
        $has_return_bean = false;
        if ($fun_type == Constant::FUN_TYPE_LIST) {
            $has_return_bean = true;
        }
        //1111基本条件
        list($i_w_param, $a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_type, $a_w_param_field) = $this->_procWhereCond($model, $fun);
        $i_param_list = $i_w_param;

        //22222被聚合键
        $fun_type = $fun->type;
        list($has_group_field, $group_field, $o_group_field, $group_field_final, $group_field_sel) = $this->parseGroup_field($model, $fun);
        //3333分组键
        list($has_group_by, $group_by) = $this->parseGroup_by($model, $fun);

        //4444先处理having,预先处理hading的条件
        $has_having = false;
        $o_having = $fun->group_having;//用来判断绑定关系
        if ($has_group_field && $has_group_by) {
            list($has_having, $a_param_comment_having, $a_param_define_having, $a_param_use_having, $a_param_type_having, $_sql1_having, $_sql2_having) = $this->parseHaving($model, $fun, $o_group_field, $group_field_final);
        }

        //5555排序键
        list($has_order, $is_order_by_input, $s_order_by, $is_order_dir_input, $s_order_dir) = $this->parseOrder_by($model, $fun, $has_group_field, $group_field_final);

        //6666 分页
        list($has_pager, $is_pager_size_input, $pager_size) = $this->parsePager($model, $fun);

        _fun_comment("获取详情用于显示", 1);
        echo _tab(1) . "protected void  _gList(HttpServletRequest req, HttpServletResponse resp, WebContext ctx) throws ServletException, IOException {\n";
        //TODO
        _fun_comment("检查攻击", 2);
        echo _tab(2) . "MyVisitor currVisitor = (MyVisitor) ctx.getVariable(\"__visitor__\");\n";
        _fun_comment("获取GET参数", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("TODO 默认模板不会出现having的情况", 2);

        //5555
        if ($has_order) {
            if ($is_order_by_input) {
                echo _tab(2) . "String v_order_by = req.getParameter(\"order_by\");\n";
                echo _tab(2) . "v_order_by = tidyStrParam(v_order_by,\"id\");\n";
            }
            if ($is_order_by_input) {
                echo _tab(2) . "String v_order_dir = req.getParameter(\"order_dir\");\n";
                echo _tab(2) . "v_order_dir = tidyStrParam(v_order_dir,\"DESC\");\n";
            }
        }
        //6666
        if ($has_pager) {
            echo _tab(2) . "String v_page = req.getParameter(\"page\");\n";
            echo _tab(2) . "int i_page = tidyStrParam(v_page, 1);\n";

            if ($is_pager_size_input) {
                echo _tab(2) . "String v_page_size = req.getParameter(\"page_size\");\n";
                echo _tab(2) . "int i_page_size = tidyStrParam(v_page_size, 20);\n";
            }
        }
        echo _tab(2) . "{$uc_model_name}Model oModel = new {$uc_model_name}Model();\n";
        if ($has_pager) {
            _fun_comment("计数", 2);
            echo _tab(2) . "int iTotal = oModel.list_Count(";
            $this->_echoFunParams($a_w_param_use);
            echo ");\n";
            echo _tab(2) . "ctx.setVariable(\"iTotal\", iTotal);\n";
        }

        //列表
        _fun_comment("列表", 2);
        echo _tab(2) . "Vector<HashMap> vList = oModel.list(";
        $this->_echoFunParams($a_w_param_use);
        $ii = count($a_w_param_use);
        if ($has_order) {
            if ($is_order_by_input) {
                echo _warp2join($ii) . _tab(5) . "v_order_by";
                $ii++;
            }
            if ($is_order_by_input) {
                echo _warp2join($ii) . _tab(5) . "v_order_dir";
                $ii++;
            }
        }
        //6666
        if ($has_pager) {
            echo _warp2join($ii) . _tab(5) . "i_page";
            $ii++;
            if ($is_pager_size_input) {
                echo _warp2join($ii) . _tab(5) . "i_page_size";
                $ii++;
            }
        }
        echo ");\n";
        _fun_comment("TODO 组装其他数据", 2);
        echo _tab(2) . "ctx.setVariable(\"vList\", vList);\n";

        if ($has_pager) {
            _fun_comment("回传参数", 2);
            $ii = 0;
            $a_param_return = array();
            foreach ($a_w_param_use as $use_param) {
                $o_field = $a_w_param_field[$ii];
                $key = $o_field->name;
                echo _tab(2) . "ctx.setVariable(\"curr_{$key}\", $use_param);\n";
                $a_param_return["curr_{$key}"] = "{$use_param}";
                $ii++;
            }
            if ($has_order) {
                if ($is_order_by_input) {
                    echo _tab(2) . "ctx.setVariable(\"curr_order_by\", v_order_by);\n";
                    $a_param_return["curr_order_by"] = "v_order_by";
                }
                if ($is_order_by_input) {
                    echo _tab(2) . "ctx.setVariable(\"curr_order_dir\", v_order_dir);\n";
                    $a_param_return["curr_order_dir"] = "v_order_dir";
                }
            }
            //6666
            //if ($has_pager) {
            echo _tab(2) . "ctx.setVariable(\"curr_page\", i_page);\n";
            $a_param_return["curr_page"] = "i_page";
            if ($is_pager_size_input) {
                echo _tab(2) . "ctx.setVariable(\"curr_page_size\", i_page_size);\n";
                $a_param_return["curr_page_size"] = "i_page_size";
            }
            //}
            _fun_comment("分页", 2);
            echo _tab(2) . "String urlPage = \"list/?\"\n";
            foreach ($a_param_return as $key => $a_p_return) {
                echo _tab(5) . "+ \"&{$key}=\" + $a_p_return\n";
            }
            echo _tab(4) . "+ \"&\";\n";


            echo _tab(2) . "PagerHelper myPage = new PagerHelper(urlPage, iTotal, i_page_size, i_page);\n";
            echo _tab(2) . "String sPagerHtml =  myPage.getSimplePagerBar();\n";
            echo _tab(2) . "ctx.setVariable(\"pager_html\", sPagerHtml);\n";
        }

        echo _tab(2) . "display(req,resp,ctx,tmpl);\n";
        echo _tab(2) . "return;\n";
        echo _tab(1) . "}\n";

        $this->_funFooter($model, $fun);
    }

    /**
     * 创建模板层
     * @param MyModel $model
     * @return mixed
     */
    public function ccTmpl($model)
    {
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $lc_model_name = strtolower($model_name);

        SeasLog::info("创建入口页模板--{$model_name}");
        $_target = $this->odir_views . DS . "{$model_name}_index.html";
        ob_start();
        $this->makeHtmlIndex($model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);


        SeasLog::info("列表页模板--{$model_name}");
        $_target = $this->odir_views . DS . "{$model_name}_list.html";
        ob_start();
        $this->makeHtmlList($model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);


        SeasLog::info("编辑页模板--{$model_name}");
        $_target = $this->odir_views . DS . "{$model_name}_edit.html";
        ob_start();
        $this->makeHtmlEdit($model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);


        SeasLog::info("详情页模板--{$model_name}");
        $_target = $this->odir_views . DS . "{$model_name}_detail.html";
        ob_start();
        $this->makeHtmlDetail($model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);


    }

    /**
     * 入口页
     * @param MyModel $model
     */
function makeHtmlIndex($model)
{
    $model_name = $model->name;
    $uc_model_name = ucfirst($model_name);
    $lc_model_name = strtolower($model_name);
    $this->_makeHtmlHeader($model);
    ?>
    <session class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" id="txt_curr_project">Starter Page</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#" id="txt_curr_version_name">Home</a></li>
                        <li class="breadcrumb-item active" id="txt_curr_version_title">Starter Page</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </session>
    <session class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa fa-fw fa-<?= $model->fa_icon ?>"></i> <?= $model->title ?>
                                <small>副标题</small></h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" id="btn_edit_conf">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="maximize"><i
                                            class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            //TODO add your code here
                        </div>
                        <div class="card-footer">
                            The footer of the card
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </session>
<?php
$this->_makeHtmlFooter($model);
?>
    <script type="text/javascript">
        //TODO add js define code here
        $(function () {
            //TODO add js logic code here

        });
    </script>
<?php
$this->_makeHtmlFooter2();
}

/**
 * 头部
 * @param $model
 */
function _makeHtmlHeader($model, $for_edit = false)
{
    echo "<!doctype html>\n";
    echo "<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:th=\"http://www.thymeleaf.org\">\n";
    if (!$for_edit) {
        echo "<head th:replace=\"_header_inc::admin_header\"></head>\n";
    } else {
        echo "<head th:replace=\"_header_inc::admin_header_for_edit\"></head>\n";
    }
    echo "<body class=\"hold-transition sidebar-mini layout-navbar-fixed\">\n";
    echo _tab(1) . "<div class=\"wrapper\">";
    echo _tab(2) . "<nav th:replace=\"_header_inc::navbar\"></nav>\n";
    echo _tab(2) . "<aside th:replace=\"_header_inc::sidebar\"></aside>\n";
    echo _tab(2) . "<div class=\"content-wrapper\">\n";

}

/**
 * 尾部
 * @param $model
 */
function _makeHtmlFooter($model, $for_edit = false)
{
    echo _tab(2) . "</div>\n";
    echo _tab(1) . "</div>\n";
    if (!$for_edit) {
        echo "<div th:replace=\"_footer_inc::admin_footer\"></div>\n";
    } else {
        echo "<div th:replace=\"_footer_inc::admin_footer_for_edit\"></div>\n";
    }
}

/**
 * 尾部
 * @param $model
 */
function _makeHtmlFooter2()
{
    echo "</body>\n";
    echo "</html>\n";
}

/**
 * 列表页
 * @param MyModel $model
 */
function makeHtmlList($model)
{
if ($this->fun_add == null) {
    return;
}
$model_name = $model->name;
$uc_model_name = ucfirst($model_name);
$lc_model_name = strtolower($model_name);
$this->_makeHtmlHeader($model);
list($is_return_new_id, $i_param, $a_param_comment, $a_param_define, $a_param_use, $a_param_type, $a_param_key, $a_param_field) = $this->parseAdd_field($model, $this->fun_add);

?>
    <session class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" id="txt_curr_project">
                        <i class="fa fa-fw fa-<?= $model->fa_icon ?>"></i>
                        <?= $model->title ?> 列表
                        Starter Page</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">

                            <a class="btn btn-sm btn-success"
                               href='<?= $model_name ?>/edit'
                                <?= "th:href=\"@{'/{$model_name}/add'}\"" ?> >
                                <i class="fa fa-plus"></i>
                                添加 <?= $model->title ?>
                            </a>


                        </li>
                        <li class="breadcrumb-item active" id="txt_curr_version_title">编辑</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </session>
    <session class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa fa-fw fa-<?= $model->fa_icon ?>"></i> <?= $model->title ?>
                                <?= "\n" ?>
                                <small>编辑</small></h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" id="btn_edit_conf">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="maximize"><i
                                            class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <form method="get"
                                  action=""
                                  id="form_main"
                                  th:action="@{'/<?= $model_name ?>/list'}">
                                <div class="form-row">
                                    <?php

                                    foreach ($model->field_list as $field) {
                                        /* @var MyField $field */
                                        if ($field->input_hash != "") {
                                            $key = $field->name;
                                            $uc_key = ucfirst($key);

                                            ?>
                                            <div class="col">
                                                <label for="ipt_<?= $key ?>"><?= $model->title ?></label>


                                                <select class="form-control"
                                                        name="<?= $key ?>"
                                                        id="ipt_<?= $key ?>">
                                                    <option value="">不限</option>
                                                    <option <?= "th:each=\"_Id,_Value:\${m_{$uc_key}_KV}\"" ?>
                                                            th:value="${_Value.current.key}"
                                                            th:text="${_Value.current.value}"
                                                        <?= "th:selected=\"\${_Value.current.key}==\${curr_{$key}}\"" ?>
                                                    ></option>
                                                </select>

                                            </div>
                                            <?php
                                        }
                                    } ?>

                                    <div class="col">
                                        <label for="iptKw">关键字TODO</label>
                                        <input type="text" class="form-control mr-sm-2"
                                               id="iptKw"
                                               name="kw"
                                               placeholder="关键字"
                                            <?= "th:value=\"\${curr_kw}\"" ?> />
                                    </div>

                                    <div class="col">
                                        <label for="date_from">开始日</label>
                                        <div class="input-group mr-sm-2">
                                            <div class="input-group-prepend"
                                                 id="h_date_from">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                            <input type="text"
                                                   class="form-control"
                                                   id="date_from"
                                                   value=""
                                                   name="date_from"
                                                   placeholder="开始日期"
                                                <?= "th:value=\"\${curr_date_from}\"" ?> />

                                        </div>
                                    </div>
                                    <div class="col">
                                        <label for="date_to">结束日</label>
                                        <div class="input-group mr-sm-2">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text" id="h_date_to"><i
                                                            class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                            <input type="text"
                                                   class="form-control"
                                                   id="date_to"
                                                   name="date_to"
                                                   value=""
                                                   placeholder="结束日起"
                                                <?= "th:value=\"\${curr_date_to}\"" ?> />

                                        </div>
                                    </div>

                                    <div class="col">
                                        <label>&nbsp;</label>
                                        <div class="">
                                            <button type="submit" class="btn btn-primary ">
                                                <i class="fa fa-search"></i>
                                                查询
                                            </button>
                                            <a class="btn btn-secondary" href="###"
                                               th:href="@{'/<?= $model_name ?>/list'}">
                                                <i class="fa fa-list"></i>
                                                重置
                                            </a>
                                        </div>

                                    </div>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </session>

    <session class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa fa-fw fa-<?= $model->fa_icon ?>"></i> <?= $model->title ?>
                                <small>列表</small></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" id="btn_edit_conf">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="maximize"><i
                                            class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="thead-light">
                                <tr class="">
                                    <?php
                                    foreach ($model->field_list as $field) {
                                        /* @var MyField $field */
                                        $type = $field->type;
                                        $key = $field->name;
                                        if ($this->isBlobType($type) || $type == Constant::DB_FIELD_TYPE_LONGTEXT) {
                                            continue;
                                        }
                                        if ($key == "flag") {
                                            continue;
                                        }
                                        ?>
                                        <th><?= $field->title ?></th>
                                        <?php
                                    }
                                    ?>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr th:each="oneRow : ${vList}">
                                    <?php

                                    foreach ($model->field_list as $field) {
                                        /* @var MyField $field */
                                        $type = $field->type;
                                        $key = $field->name;
                                        if ($this->isBlobType($type) || $type == Constant::DB_FIELD_TYPE_LONGTEXT) {
                                            continue;
                                        }
                                        if ($key == "flag") {
                                            continue;
                                        }
                                        $_row = "th:text=\"\${oneRow.{$key}}\"";
                                        $_row2 = "[[\${oneRow.{$key}}]]";
                                        ?>
                                        <td <?= $_row ?> ><?= $key ?> <?= $_row2 ?></td>
                                        <?php
                                    }
                                    ?>
                                    <td>
                                        <a class="btn btn-sm btn-success"
                                            <?= "th:href=\"@{'/{$model_name}/detail'(id=\${oneRow.id})}\"" ?>
                                           href='<?= $model_name ?>/edit'>
                                            <i class="fa fa-info"></i>
                                            详情
                                        </a>

                                        <a class="btn btn-sm btn-primary"
                                            <?= "th:href=\"@{'/{$model_name}/edit'(id=\${oneRow.id})}\"" ?>
                                           href='<?= $model_name ?>/edit'>
                                            <i class="fa fa-edit"></i>
                                            编辑
                                        </a>

                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <div class="text-center" th:utext="${pager_html}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </session>
<?php
$this->_makeHtmlFooter($model);
?>
    <script type="text/javascript">
        //TODO add js define code here
        var _url_submit = "[[@{'/<?= $model_name ?>/ajax_modify'}]]";
        var _g_token = "[[${op_token__}]]";
        $(function () {
            //TODO add js logic code here
            setupFormSubmit();
        });
    </script>
<?php
$this->_makeHtmlFooter2();
}

/**
 * 编辑页
 * @param MyModel $model
 */
function makeHtmlEdit($model)
{
if ($this->fun_add == null) {
    return;
}

$model_name = $model->name;
$uc_model_name = ucfirst($model_name);
$lc_model_name = strtolower($model_name);
$this->_makeHtmlHeader($model);
list($is_return_new_id, $i_param, $a_param_comment, $a_param_define, $a_param_use, $a_param_type, $a_param_key, $a_param_field) = $this->parseAdd_field($model, $this->fun_add);
$has_upload = false;
?>
    <session class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" id="txt_curr_project">
                        <i class="fa fa-fw fa-<?= $model->fa_icon ?>"></i>
                        <?= $model->title ?> 编辑
                        Starter Page</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="#"
                                <?= "th:href=\"@{'/{$model_name}/list'}\"" ?>
                               id="txt_curr_version_name">返回列表</a>
                        </li>
                        <li class="breadcrumb-item active" id="txt_curr_version_title">编辑</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </session>
    <session class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa fa-fw fa-<?= $model->fa_icon ?>"></i> <?= $model->title ?>
                                <?= "\n" ?>
                                <small>编辑</small></h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" id="btn_edit_conf">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="maximize"><i
                                            class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <form method="post"
                              enctype="application/x-www-form-urlencoded"
                              action=""
                              id="form_main"
                              th:action="@{'/<?= $model_name ?>/modify'}">
                            <div class="card-body">
                                <input type="hidden"
                                       readonly="readonly"
                                       name="form_token__"
                                       value='s3c'
                                    <?= "th:value=\"\${form_token__}\"" ?> />

                                <input type="hidden"
                                       readonly="readonly"
                                       name="_id"
                                       class="form-control"
                                       value='id'
                                    <?= "th:value=\"\${mInfo.id}\"" ?> />

                                <?php
                                foreach ($a_param_field as $key => $o_field) {
                                    /* @var MyField $o_field */
                                    $key = $o_field->name;
                                    //保留字全部不用输入
                                    if (in_array($key, array("flag", "ctime", "utime", "cadmin", "uadmin"))) {
                                        continue;
                                    }
                                    $uc_key = ucfirst($key);
                                    $val1 = "th:value=\"\${mInfo.{$key}}\"";
                                    $val2 = "th:text=\"\${mInfo.{$key}}\"";
                                    $required = ($o_field->required == 1) ? 'required=required' : '';

                                    ?>
                                    <div class="form-group row">
                                        <label for="ipt_<?= $key ?>"
                                               class="col-sm-2 col-form-label"><?= $o_field->title ?></label>
                                        <div class="col-sm-10">

                                            <?php


                                            switch ($o_field->input_by) {
                                                //多行文本
                                                case Constant::DB_FIELD_INPUT_MULTI_TEXT:
                                                    $this->makeHtmlIptMultiText($model, $o_field);
                                                    break;

                                                //下拉框
                                                case Constant::DB_FIELD_INPUT_SELECT:
                                                    $this->makeHtmlIptSelect($model, $o_field);
                                                    break;

                                                //单选
                                                case Constant::DB_FIELD_INPUT_RADIO:
                                                    $this->makeHtmlIptRadio($model, $o_field);

                                                    break;

                                                //复选框
                                                case Constant::DB_FIELD_INPUT_CHECKBOX:
                                                    $this->makeHtmlIptCheckBox($model, $o_field);
                                                    break;

                                                case Constant::DB_FIELD_INPUT_DATE:
                                                    $this->makeHtmlIptDate($model, $o_field);
                                                    break;

                                                case Constant::DB_FIELD_INPUT_DATETIME:
                                                    $this->makeHtmlIptDateTime($model, $o_field);
                                                    break;


                                                case Constant::DB_FIELD_INPUT_UPLOAD_FILE:
                                                case Constant::DB_FIELD_INPUT_UPLOAD_IMAGE:
                                                    $has_upload = true;
                                                    $this->makeHtmlIptFile($model, $o_field);
                                                    break;

                                                case Constant::DB_FIELD_INPUT_DEFAULT:
                                                default:
                                                    $this->makeHtmlIptLineText($model, $o_field);

                                                    break;
                                            } ?>

                                            <small id="help_<?= $key ?>"
                                                   class="help_text form-text text-muted"><?= trim($o_field->memo) ?></small>
                                            <div class="invalid-feedback"></div>
                                            <div class="valid-feedback"></div>
                                        </div>
                                    </div>

                                    <?php
                                }
                                ?>

                            </div>
                        </form>
                        <div class="card-footer">
                            <button type="button" class="btn btn-lg btn-primary btn-block" id="btn_submit">
                                <i class="fa fa-check"></i>
                                保存
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <?php
                    if ($has_upload) {
                        ?>
                        <div th:replace="_comm_ui::block_upload_avatar"></div>
                        <?php
                    } ?>

                    <div th:replace="_comm_ui::block_confirm_delete"></div>
                </div>
            </div>
        </div>
    </session>
<?php
$this->_makeHtmlFooter($model);
?>
    <script type="text/javascript">
        //TODO add js define code here
        var _url_submit = "[[@{'/<?= $model_name ?>/ajax_modify'}]]";
        var _g_token = "[[${op_token__}]]";
        $(function () {
            //TODO add js logic code here
            setupFormSubmit();
        });
    </script>
<?php
$this->_makeHtmlFooter2();
}

function makeHtmlIptMultiText(MyModel $model, MyField $field, $for_show = false){
$key = $field->name;
$uc_key = ucfirst($key);
$val1 = "th:value=\"\${mInfo.{$key}}\"";
$val2 = "th:text=\"\${mInfo.{$key}}\"";
$required = ($field->required == 1) ? 'required=required' : '';
if ($for_show){
?>
    <textarea class="form-control"
              rows="3"
               <?= $val2 ?>  ></textarea>
    <?php
}  else
{
    ?>
    <textarea class="form-control"
              rows="3"
              name="<?= $key ?>"
              id="ipt_<?= $key ?>"
                                                      <?= $val2 ?>
        <?= $required ?>
                                                    ></textarea>
<?php
}
}

function makeHtmlIptSelect(MyModel $model, MyField $field, $for_show = false){
$key = $field->name;
$uc_key = ucfirst($key);
$val1 = "th:value=\"\${mInfo.{$key}}\"";
$val2 = "th:text=\"\${mInfo.{$key}}\"";
if ($for_show){
?>
<input type="text" class="form-control "
       readonly="readonly"
    <?= $val1 ?>
/>
    <?php
}  else
{
    ?>
<select class="form-control select2"
        name="<?= $key ?>"
        id="ipt_<?= $key ?>"
    <option value="">不限</option>
    <option <?= "th:each=\"_Id,_Value:\${m_{$uc_key}_KV}\"" ?>
            th:value="${_Value.current.key}"
            th:text="${_Value.current.value}"
        <?= "th:selected=\"\${_Value.current.key}==\${mInfo.{$key}}\"" ?>
    ></option>
    </select>
<?php
}
}

function makeHtmlIptRadio(MyModel $model, MyField $field, $for_show = false){
$key = $field->name;
$uc_key = ucfirst($key);
$val1 = "th:value=\"\${mInfo.{$key}}\"";
$val2 = "th:text=\"\${mInfo.{$key}}\"";
$a_hash = explode(";", $field->input_hash);
if ($for_show){
?>
<input type="text" class="form-control "
       readonly="readonly"
    <?= $val1 ?>
/>
    <?php
}  else
{
    ?>
    <div class="form-group clearfix" id="ipt_<?= $key ?>">
        <?php
        foreach ($a_hash as $s_kv) {
            $a_kv = explode(",", $s_kv);
            ?>
            <div class="icheck-primary d-inline">
                <input type="radio" id="radio_<?= $a_kv[0] ?>" name="<?= $key ?>">
                <label for="radio_<?= $a_kv[0] ?>">
                    <?= $a_kv[1] ?>
                </label>
            </div>

        <?php } ?>
    </div>
<?php
}
}

function makeHtmlIptCheckBox(MyModel $model, MyField $field, $for_show = false){
$key = $field->name;
$uc_key = ucfirst($key);
$val1 = "th:value=\"\${mInfo.{$key}}\"";
$val2 = "th:text=\"\${mInfo.{$key}}\"";
$a_hash = explode(";", $field->input_hash);
if ($for_show){
?>
<input type="text" class="form-control "
       readonly="readonly"
    <?= $val1 ?>
/>
    <?php
}  else
{
    ?>
    <div class="form-group clearfix" id="ipt_<?= $key ?>">
        <?php
        foreach ($a_hash as $s_kv) {
            $a_kv = explode(",", $s_kv);
            ?>
            <div class="icheck-primary d-inline">
                <input type="checkbox" id="checkbox_<?= $a_kv[0] ?>" name="<?= $key ?>[]">
                <label for="checkbox_<?= $a_kv[0] ?>">
                    <?= $a_kv[1] ?>
                </label>
            </div>

        <?php } ?>
    </div>
<?php
}
}

function makeHtmlIptDate(MyModel $model, MyField $field, $for_show = false){
$key = $field->name;
$uc_key = ucfirst($key);
$val1 = "th:value=\"\${mInfo.{$key}}\"";
$val2 = "th:text=\"\${mInfo.{$key}}\"";
$required = ($field->required == 1) ? 'required=required' : '';
if ($for_show){
?>
<input type="text" class="form-control "
       readonly="readonly"
    <?= $val1 ?>
/>
    <?php
}  else
{
    ?>
    <div class="input-group date has_data" id="data_ipt_<?= $key ?>" data-target-input="nearest">
        <input type="text" class="form-control datetimepicker-input"
               name="<?= $key ?>"
               id="ipt_<?= $key ?>"
            <?= $val1 ?>
            <?= $required ?>
               data-target="#data_ipt_<?= $key ?>">
        <div class="input-group-append"
             data-target="#data_ipt_<?= $key ?>"
             data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
        </div>
    </div>

<?php
}
}

function makeHtmlIptDateTime(MyModel $model, MyField $field, $for_show = false){
$key = $field->name;
$uc_key = ucfirst($key);
$val1 = "th:value=\"\${mInfo.{$key}}\"";
$val2 = "th:text=\"\${mInfo.{$key}}\"";
$required = ($field->required == 1) ? 'required=required' : '';
if ($for_show){
?>
<input type="text" class="form-control "
       readonly="readonly"
    <?= $val1 ?>
/>
    <?php
}  else
{
    ?>
    <div class="input-group date has_datatime" id="datatime_ipt_<?= $key ?>" data-target-input="nearest">
        <input type="text" class="form-control datetimepicker-input"
               name="<?= $key ?>"
               id="ipt_<?= $key ?>"
            <?= $val1 ?>
            <?= $required ?>
               data-target="#data_ipt_<?= $key ?>">
        <div class="input-group-append"
             data-target="#datatime_ipt_<?= $key ?>"
             data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-clock"></i></div>
        </div>
    </div>

<?php
}
}

function makeHtmlIptFile(MyModel $model, MyField $field, $for_show = false){
$key = $field->name;
$uc_key = ucfirst($key);
$val1 = "th:value=\"\${mInfo.{$key}}\"";
$val2 = "th:text=\"\${mInfo.{$key}}\"";
$required = ($field->required == 1) ? 'required=required' : '';
if ($for_show){
?>
<input type="text" class="form-control "
       readonly="readonly" value="TODO"
/>
    <?php
}  else
{
    ?>
<input type="file" class="form-control "
       name="<?= $key ?>"
       id="ipt_<?= $key ?>"
    <?= $val1 ?>
    <?= $required ?>
/>

<?php
}
}

function makeHtmlIptLineText(MyModel $model, MyField $field, $for_show = false){
$key = $field->name;
$uc_key = ucfirst($key);
$val1 = "th:value=\"\${mInfo.{$key}}\"";
$val2 = "th:text=\"\${mInfo.{$key}}\"";
$required = ($field->required == 1) ? 'required=required' : '';
if ($for_show){
?>
<input type="text" class="form-control "
       readonly="readonly"
    <?= $val1 ?>
/>
    <?php
}  else
{
    ?>
<input type="text" class="form-control "
       name="<?= $key ?>"
       id="ipt_<?= $key ?>"
    <?= $val1 ?>
    <?= $required ?>
/>

<?php
}
}

/**
 * 详细页
 * @param MyModel $model
 */
function makeHtmlDetail($model)
{
if ($this->fun_add == null) {
    return;
}

$model_name = $model->name;
$uc_model_name = ucfirst($model_name);
$lc_model_name = strtolower($model_name);
$this->_makeHtmlHeader($model);
list($is_return_new_id, $i_param, $a_param_comment, $a_param_define, $a_param_use, $a_param_type, $a_param_key, $a_param_field) = $this->parseAdd_field($model, $this->fun_add);
$has_upload = false;
?>
    <session class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" id="txt_curr_project">
                        <i class="fa fa-fw fa-<?= $model->fa_icon ?>"></i>
                        <?= $model->title ?>
                        Starter Page</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="#"
                                <?= "th:href=\"@{'/{$model_name}/list'}\"" ?>
                               id="txt_curr_version_name">返回列表</a>
                        </li>
                        <li class="breadcrumb-item active" id="txt_curr_version_title">详情</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </session>
    <session class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa fa-fw fa-<?= $model->fa_icon ?>"></i> <?= $model->title ?>
                                <?= "\n" ?>
                                <small>编辑</small></h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" id="btn_edit_conf">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="maximize"><i
                                            class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <form method="post"
                              enctype="application/x-www-form-urlencoded"
                              action=""
                              id="form_main"
                              th:action="@{'/<?= $model_name ?>/modify'}">
                            <div class="card-body">

                                <?php
                                foreach ($a_param_field as $key => $o_field) {
                                    /* @var MyField $o_field */
                                    $key = $o_field->name;
                                    //保留字全部不用输入
                                    if (in_array($key, array("flag", "ctime", "utime", "cadmin", "uadmin"))) {
                                        continue;
                                    }
                                    $uc_key = ucfirst($key);
                                    $val1 = "th:value=\"\${mInfo.{$key}}\"";
                                    $val2 = "th:text=\"\${mInfo.{$key}}\"";
                                    $required = ($o_field->required == 1) ? 'required=required' : '';

                                    ?>
                                    <div class="form-group row">
                                        <label for="ipt_<?= $key ?>"
                                               class="col-sm-2 col-form-label"><?= $o_field->title ?></label>
                                        <div class="col-sm-10">

                                            <?php


                                            switch ($o_field->input_by) {
                                                //多行文本
                                                case Constant::DB_FIELD_INPUT_MULTI_TEXT:
                                                    $this->makeHtmlIptMultiText($model, $o_field, true);
                                                    break;

                                                //下拉框
                                                case Constant::DB_FIELD_INPUT_SELECT:
                                                    $this->makeHtmlIptSelect($model, $o_field, true);
                                                    break;

                                                //单选
                                                case Constant::DB_FIELD_INPUT_RADIO:
                                                    $this->makeHtmlIptRadio($model, $o_field, true);

                                                    break;

                                                //复选框
                                                case Constant::DB_FIELD_INPUT_CHECKBOX:
                                                    $this->makeHtmlIptCheckBox($model, $o_field, true);
                                                    break;

                                                case Constant::DB_FIELD_INPUT_DATE:
                                                    $this->makeHtmlIptDate($model, $o_field, true);
                                                    break;

                                                case Constant::DB_FIELD_INPUT_DATETIME:
                                                    $this->makeHtmlIptDateTime($model, $o_field, true);
                                                    break;


                                                case Constant::DB_FIELD_INPUT_UPLOAD_FILE:
                                                case Constant::DB_FIELD_INPUT_UPLOAD_IMAGE:
                                                    $has_upload = true;
                                                    $this->makeHtmlIptFile($model, $o_field, true);
                                                    break;

                                                case Constant::DB_FIELD_INPUT_DEFAULT:
                                                default:
                                                    $this->makeHtmlIptLineText($model, $o_field, true);

                                                    break;
                                            } ?>


                                        </div>
                                    </div>

                                    <?php
                                }
                                ?>

                            </div>
                        </form>
                        <div class="card-footer">
                            <a href='<?= $model_name ?>/edit'
                               class="btn btn-lg btn-info "
                                <?= "th:href=\"@{'/{$model_name}/edit'(id={\$mInfo.id})}\"" ?>>
                                <i class="fa fa-edit"></i>
                                编辑
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    //TODO
                </div>
            </div>
        </div>
    </session>
<?php
$this->_makeHtmlFooter($model);
?>
    <script type="text/javascript">
        //TODO add js define code here
        var _url_submit = "[[@{'/<?= $model_name ?>/ajax_modify'}]]";
        var _g_token = "[[${op_token__}]]";
        $(function () {
            //TODO add js logic code here
            setupFormSubmit();
        });
    </script>
<?php
$this->_makeHtmlFooter2();
}

/**
 * 创建接口层
 * @param MyModel $model
 * @return mixed
 */
function ccApi($model)
{
    //TODO
}

/**
 * 创建文档
 * @param MyModel $model
 */
function ccDoc($model)
{
    //TODO
}

/**
 * web xml
 * @param $model
 */
public function makeWebConfig($a_models)
{

SeasLog::info("创建Web.xml--");
$_target = $this->odir_config . DS . "web_url.php";
ob_start();
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";


$webapp_head = <<< WEB
<web-app xmlns="http://java.sun.com/xml/ns/javaee"
             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
             xsi:schemaLocation="http://java.sun.com/xml/ns/javaee
		  http://java.sun.com/xml/ns/javaee/web-app_2_5.xsd"
             version="2.5">
WEB;
echo $webapp_head;
$package = $this->final_package;

?>
    <welcome-file-list>
        <welcome-file>index.html</welcome-file>
        <welcome-file>index.jsp</welcome-file>
    </welcome-file-list>

    <servlet>
        <servlet-name>my-service</servlet-name>
        <servlet-class>org.glassfish.jersey.servlet.ServletContainer</servlet-class>
        <init-param>
            <param-name>jersey.config.server.provider.packages</param-name>
            <param-value><?= $package ?>.service</param-value>
        </init-param>
        <load-on-startup>1</load-on-startup>
    </servlet>
    <servlet-mapping>
        <servlet-name>my-service</servlet-name>
        <url-pattern>/rest/*</url-pattern>
    </servlet-mapping>

    <listener>
        <listener-class><?= $package ?>.MyListener</listener-class>
    </listener>

    <?php
foreach ($a_models as $id => $model)
{
    $model_name = $model->name;
    $uc_model = ucfirst($model_name);
    ?>
    <servlet>
        <servlet-name><?= $uc_model ?>Servlet</servlet-name>
        <servlet-class><?= $package ?>.controllers.<?= $uc_model ?>Servlet</servlet-class>
    </servlet>
    <servlet-mapping>
        <servlet-name><?= $uc_model ?>Servlet</servlet-name>
        <url-pattern>/<?= $model_name ?>/*</url-pattern>
    </servlet-mapping>
<?php }
    ?>

    <?php
    echo "</web-app>";

    $cc_data = ob_get_contents();
    ob_end_clean();
    file_put_contents($_target, $cc_data);
}

}

if ($tpl_debug) {
    ?>
    </html>
    <?php
}