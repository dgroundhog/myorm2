<?php


if (!defined("MVC_ROOT")) {
    define('MVC_ROOT', realpath(dirname(__FILE__)));
}
include_once(MVC_ROOT . "/PhpPhalconMvc.php");

class PhpPhalconMvcCtrl extends PhpPhalconMvc
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
        SeasLog::info("创建PHP控制器--{$model_name}");
        $_target = $this->odir_controllers . DS . "{$uc_model_name}Controller.php";
        ob_start();

        $this->_makeHeader();
        $package = $this->final_package;
        echo "use Phalcon\Mvc\Controller;\n";

        _fun_comment(array("php  控制器", $model->title));
        echo "class {$uc_model_name}Controller extends ControllerBase {\n";

        _fun_comment_header("配对业务模型", 1);
        echo _tab(1) . " * @var {$uc_model_name}Model\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public \$o_{$uc_model_name} = null;\n";

        _fun_comment("最终控制器初始化 for local init level3", 1);
        echo _tab(1) . "protected function _beforeAction() {\n";
        echo _tab(2) . "parent::_beforeAction();\n";
        echo _tab(2) . "\$this->o_{$uc_model_name} = new {$uc_model_name}Model();\n";
        echo _tab(2) . "\$this->assign(\"model_title\",\"{$model->title}\");\n";
        echo _tab(2) . "//TODO 私有在这里定义\n";
        _fun_comment("模块内基础字典", 2);
        foreach ($model->field_list as $field) {
            /* @var MyField $field */
            if ($field->input_hash != "") {
                $key = $field->name;
                $uc_key = ucfirst($key);
                _fun_comment("基础字典-{$field->title}", 2);
                echo _tab(2) . "\$this->assign(\"m_{$uc_key}_KV\", \$this->o_{$uc_model_name}->get{$uc_key}_KV());\n";
            }
        }
        echo _tab(2) . "//TODO more to do here\n";
        //TODO 私有操作连接在这里定义
        echo _tab(1) . "}\n";

        _fun_comment("一般不使用的默认index页入口路由", 1);
        echo _tab(1) . "public function indexAction()\n";
        echo _tab(1) . "{\n";
        echo _tab(2) . "//TODO nothing to do here\n";
        echo _tab(1) . "}\n";


        //提取add个fetch的关键函数
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

                    break;

                case Constant::FUN_TYPE_UPDATE:

                    break;

                case Constant::FUN_TYPE_FETCH:
                    $this->fun_fetch = $o_fun;

                    break;

                case Constant::FUN_TYPE_LIST:
                    $this->fun_list = $o_fun;


                default:
                    break;
            }
        }


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
        list($is_return_new_id, $i_param, $a_param_comment, $a_param_define, $a_param_use, $a_param_type, $a_param_key, $a_param_field) = $this->parseAdd_field($model, $fun);
        echo _tab(1) . "public function addAction() {\n";
        echo _tab(2) . "\$this->assign(\"page_title\",\"新建{$model->title}\");\n";
        _fun_comment("启用CSRF预防", 2);
        echo _tab(2) . "\$this->_beforeFormEdit(\"{$lc_model_name}_new\");\n";

        echo _tab(2) . "\$a_info = array();\n";
        echo _tab(2) . "\$a_info['id'] = '';\n";
        foreach($a_param_key as $key){
            echo _tab(2) . "\$a_info['{$key}'] = '';\n";
        }
        echo _tab(2) . "//TODO 可能需要获取上一个记录剩余的数据\n";
        echo _tab(2) . "\$this->assign(\"a_info\",\$a_info);\n";
        echo _tab(2) . "//TODO其他需要预先输出的参数\n";
        echo _tab(1) . "}\n";



        SeasLog::info("Form保存");
        _fun_comment("Form保存", 1);
        echo _tab(1) . "public function saveAction() {\n";
        _fun_comment("检查CSRF攻击", 2);
        echo _tab(2) . "if (!\$this->_beforeFormSave(\"{$lc_model_name}_new\")){\n";
        echo _tab(3) . "return \$this->_errRedirect('url_{$lc_model_name}_list','CSRF');\n";
        echo _tab(2) . "}\n";

        echo _tab(2) . "//接收请求参数\n";
        echo _tab(2) . "\$a_input_org = array();\n";
        echo _tab(2) . "\$a_input_error = array();\n";


        _fun_comment("获取POST参数", 2);
        $this->_echoReqParams($a_param_define, $a_param_field, "post");
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("执行保存", 2);
        echo _tab(2) . "\$iRet = \$this->o_{$uc_model_name}->add(";
        $this->_echoFunParams($a_param_use);
        echo _tab(2) . ");\n";


        _fun_comment("//TODO 判断返回值的大小", 2);

        echo _tab(2) . "\$a_result = array();\n";
        echo _tab(2) . "if (\$iRet > 0){\n";
        echo _tab(3) . "\$a_result['__code__'] = ECode::SUCC;\n";
        echo _tab(3) . "return \$this->_redirect('url_{$lc_model_name}_list', \$a_result);\n";
        echo _tab(2) . "}\n";
        echo _tab(2) . "else {\n";
        _fun_comment("TODO 把原始输入和错误写入session", 2);
        echo _tab(3) . "\$a_result['__code__'] = ECode::E0000;\n";
        echo _tab(3) . "return \$this->_redirect('url_{$lc_model_name}_add', \$a_result);\n";
        echo _tab(2) . "}\n";
        echo _tab(1) . "}\n";

        _fun_comment("ajax保存插入数据", 1);
        echo _tab(1) . "public function ajaxSaveAction() {\n";

        _fun_comment("检查CSRF攻击", 2);
        echo _tab(2) . "if (!\$this->_beforeAjaxSave(\"ajax_{$lc_model_name}_new\")){\n";
        echo _tab(3) . "return \$this->_errAjax('CSRF');\n";
        echo _tab(2) . "}\n";

        _fun_comment("获取POST参数", 2);
        $this->_echoReqParams($a_param_define, $a_param_field, "post");
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("执行保存", 2);
        echo _tab(2) . "\$iRet = \$this->o_{$uc_model_name}->add(";
        $this->_echoFunParams($a_param_use);
        echo _tab(1) . ");\n";

        echo _tab(2) . "if(\$iRet > 0){\n";
        echo _tab(3) . "\$this->_ajax2(ECode::SUCC,\"保存成功\");\n";
        echo _tab(2) . "}else{\n";
        echo _tab(3) . "\$this->_ajax2(ECode::E0000,\"保存失败\");\n";
        echo _tab(2) . "}\n";
        echo _tab(2) . "return;\n";
        echo _tab(1) . "}\n";

        $this->_funFooter($model, $fun);
    }

    function _echoReqParams($a_param_define, $a_param_field, $method = "get")
    {
        $ii = 0;
        foreach ($a_param_define as $param) {
            $o_field = $a_param_field[$ii];
            $key = $o_field->name;
            if(!isset($a_used_key[$key])){
                $a_used_key[$key]=1;
            }
            else{
                $a_used_key[$key]++;
            }
            $i_key_count = $a_used_key[$key];
            $vkey = $key."_{$i_key_count}";

            echo "\n";

            $d_value = $o_field->default_value;
            if ($this->isIntType($o_field->type) || $this->isBoolType($o_field->type)) {
                $d_value = ($d_value == "") ? 0 : (1 * $d_value);
                if ($method == "get") {
                    echo _tab(2) . "{$param} = \$this->request->get(\"{$key}\",\"int\",{$d_value});\n";
                } else {
                    echo _tab(2) . "{$param} = \$this->request->getPost(\"{$key}\",\"int\",{$d_value});\n";
                }
            } else {
                if ($method == "get") {
                    echo _tab(2) . "{$param} = \$this->request->get(\"{$key}\",\"string\",\"{$d_value}\");\n";
                } else {
                    echo _tab(2) . "{$param} = \$this->request->getPost(\"{$key}\",\"string\",\"{$d_value}\");\n";
                }
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

            echo _tab(2) . "SeasLog::debug(\"GetParam-({$key})--(\" . {$param} . \")\");\n";
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
        echo _tab(1) . "public function deleteAction() {\n";
        _fun_comment("检查CSRF攻击", 2);
        echo _tab(2) . "if (!\$this->_beforeFormSave(\"{$lc_model_name}_delete\")){\n";
        echo _tab(3) . "return \$this->_errRedirect('url_{$lc_model_name}_list','CSRF');\n";
        echo _tab(2) . "}\n";

        _fun_comment("获取POST参数", 2);

        _fun_comment("删除条件", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field, "post");
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("执行删除", 2);
        echo _tab(2) . "\$iRet = \$this->o_{$uc_model_name}->drop(";
        $this->_echoFunParams($a_w_param_use);
        echo ");\n";

        echo _tab(2) . "\$a_result = array();\n";
        echo _tab(2) . "if (\$iRet > 0){\n";
        echo _tab(3) . "\$a_result['__code__'] = ECode::SUCC;\n";
        echo _tab(2) . "}\n";
        echo _tab(2) . "else {\n";
        echo _tab(3) . "\$a_result['__code__'] = ECode::E0000;\n";
        echo _tab(2) . "}\n";
        echo _tab(3) . "return \$this->_redirect('url_{$lc_model_name}_list', \$a_result);\n";

        echo _tab(1) . "}\n";

        _fun_comment("ajax删除", 1);
        echo _tab(1) . "public function ajaxDeleteAction() {\n";
        _fun_comment("检查CSRF攻击", 2);
        echo _tab(2) . "if (!\$this->_beforeAjaxSave(\"ajax_{$lc_model_name}_delete\")){\n";
        echo _tab(3) . "return \$this->_errAjax('CSRF');\n";
        echo _tab(2) . "}\n";

        _fun_comment("获取POST参数", 2);
        _fun_comment("删除条件", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("执行删除", 2);
        echo _tab(2) . "\$iRet = \$this->o_{$uc_model_name}->drop(";
        $this->_echoFunParams($a_w_param_use);
        echo ");\n";

        echo _tab(2) . "if(\$iRet==1){\n";
        echo _tab(3) . "\$this->_ajax2(ECode::SUCC,\"删除成功\");\n";
        echo _tab(2) . "}else{\n";
        echo _tab(3) . "\$this->_ajax2(ECode::E0000,\"删除失败\");\n";
        echo _tab(2) . "}\n";
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
        echo _tab(1) . "public function editAction() {\n";

        _fun_comment("启用CSRF预防", 2);
        echo _tab(2) . "if (!\$this->_beforeFormSave(\"{$lc_model_name}_edit\")){\n";
        echo _tab(3) . "return \$this->_errRedirect('url_{$lc_model_name}_list','CSRF');\n";
        echo _tab(2) . "}\n";

        //更新条件
        list($i_w_param, $a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_type, $a_w_param_field) = $this->_procWhereCond($model, $this->fun_fetch);

        _fun_comment("获取GET参数", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        echo _tab(2) . "\$mInfo = \$this->o_{$uc_model_name}->fetch(";
        $this->_echoFunParams($a_w_param_use);
        echo ");\n";

        echo _tab(2) . "\$this->assign(\"mInfo\", \$mInfo);\n";
        _fun_comment("TODO 其他需要输入的数值", 2);
        echo _tab(1) . "}\n";


        //需要更新的字段
        list($i_u_param, $a_u_param_comment, $a_u_param_define, $a_u_param_use, $a_u_param_type, $a_u_param_key, $a_u_param_field) = $this->_parseUpdate_field($model, $fun);
        //更新条件
        list($i_w_param, $a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_type, $a_w_param_field) = $this->_procWhereCond($model, $fun);


        _fun_comment("FORM更新", 1);
        echo _tab(1) . "public function modifyAction() {\n";
        _fun_comment("检查CSRF攻击", 2);
        echo _tab(2) . "if (!\$this->_beforeFormSave(\"{$lc_model_name}_edit\")){\n";
        echo _tab(3) . "return \$this->_errRedirect('url_{$lc_model_name}_list','CSRF');\n";
        echo _tab(2) . "}\n";


        _fun_comment("需要更新POST的字段", 2);
        $this->_echoReqParams($a_u_param_define, $a_u_param_field);
        _fun_comment("需要条件", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("执行更新", 2);
        echo _tab(2) . "\$iRet = \$this->o_{$uc_model_name}->modify(";
        $this->_echoFunParams($a_u_param_use, $a_w_param_use);
        echo ");\n";

        echo _tab(2) . "\$a_result = array();\n";
        echo _tab(2) . "if (\$iRet > 0){\n";
        echo _tab(3) . "\$a_result['__code__'] = ECode::SUCC;\n";
        $ii = 0;
        foreach ($a_w_param_field as $w_filed) {
            $vvv = $a_w_param_use[$ii];
            echo _tab(3) . "\$a_result['{$w_filed->name}'] = {$vvv} ;\n";
            $ii++;
        }
        echo _tab(3) . "return \$this->_redirect('url_{$lc_model_name}_detail', \$a_result);\n";
        echo _tab(2) . "}\n";
        echo _tab(2) . "else {\n";
        _fun_comment("TODO 把原始输入和错误写入session", 2);
        echo _tab(3) . "\$a_result['__code__'] = ECode::E0000;\n";
        $ii = 0;
        foreach ($a_w_param_field as $w_filed) {
            $vvv = $a_w_param_use[$ii];
            echo _tab(3) . "\$a_result['{$w_filed->name}'] = {$vvv} ;\n";
            $ii++;
        }
        echo _tab(3) . "return \$this->_redirect('url_{$lc_model_name}_edit', \$a_result);\n";
        echo _tab(2) . "}\n";
        echo _tab(1) . "}\n";


        _fun_comment("ajax更新", 1);
        echo _tab(1) . "public function ajaxModifyAction() {\n";
        _fun_comment("检查CSRF攻击", 2);
        echo _tab(2) . "if (!\$this->_beforeAjaxSave(\"ajax_{$lc_model_name}_edit\")){\n";
        echo _tab(3) . "return \$this->_errAjax('CSRF');\n";
        echo _tab(2) . "}\n";

        _fun_comment("获取POST参数", 2);
        _fun_comment("需要更新的字段", 2);
        $this->_echoReqParams($a_u_param_define, $a_u_param_field);
        _fun_comment("需要条件", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("执行更新", 2);
        echo _tab(2) . "\$iRet = \$this->o_{$uc_model_name}->modify(";
        $this->_echoFunParams($a_u_param_use, $a_w_param_use);
        echo ");\n";

        echo _tab(2) . "if(\$iRet > 0){\n";
        echo _tab(3) . "\$this->_ajax2(ECode::SUCC,\"更新成功\");\n";
        echo _tab(2) . "}else{\n";
        echo _tab(3) . "\$this->_ajax2(ECode::E0000,\"更新失败\");\n";
        echo _tab(2) . "}\n";
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
        echo _tab(1) . "public function detailAction() {\n";
        //更新条件
        list($i_w_param, $a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_type, $a_w_param_field) = $this->_procWhereCond($model, $this->fun_fetch);

        _fun_comment("获取GET参数", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        echo _tab(2) . "\$mInfo = \$this->o_{$uc_model_name}->fetch(";
        $this->_echoFunParams($a_w_param_use);
        echo ");\n";

        echo _tab(2) . "\$this->assign(\"mInfo\", \$mInfo);\n";
        echo _tab(1) . "}\n";


        _fun_comment("获取ajax详情用于显示", 1);
        echo _tab(1) . "public function ajaxDetailAction() {\n";

        _fun_comment("获取POST参数", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        echo _tab(2) . "\$mInfo = \$this->o_{$uc_model_name}->fetch(";
        $this->_echoFunParams($a_w_param_use);
        echo ");\n";

        echo _tab(2) . "if(\$mInfo != null){\n";
        echo _tab(3) . "\$this->_ajax2(ECode::SUCC,\"读取成功\",\$mInfo);\n";
        echo _tab(2) . "}else{\n";
        echo _tab(3) . "\$this->_ajax2(ECode::E0000,\"读取失败\");\n";
        echo _tab(2) . "}\n";
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

        _fun_comment("获取列表用于显示", 1);
        echo _tab(1) . "public function listsAction() {\n";

        echo _tab(2) . "\$t1 = microtime(true);\n";
        _fun_comment("获取GET参数", 2);
        $this->_echoReqParams($a_w_param_define, $a_w_param_field);
        _fun_comment("TODO 注意来自session的变量", 2);

        _fun_comment("TODO 默认模板不会出现having的情况", 2);

        //5555
        if ($has_order) {
            if ($is_order_by_input) {
                echo _tab(2) . "\$v_order_by = \$this->request->get(\"order_by\",\"string\",\"id\");\n";
            }
            if ($is_order_by_input) {
                echo _tab(2) . "\$v_order_dir = \$this->request->get(\"order_dir\",\"string\",\"DESC\");\n";
            }
        }
        //6666
        if ($has_pager) {
            echo _tab(2) . "\$i_page = \$this->request->get(\"page\",\"int\",1);\n";
            if ($is_pager_size_input) {
                echo _tab(2) . "\$i_page_size = \$this->request->get(\"page_size\",\"int\",20);\n";
            } else {
                echo _tab(2) . "\$i_page_size = {$pager_size}  ;\n";
            }
        }
        if ($has_pager) {
            _fun_comment("计数", 2);
            echo _tab(2) . "\$i_count = \$this->o_{$uc_model_name}->lists_Count(";
            $this->_echoFunParams($a_w_param_use);
            echo ");\n";
            echo _tab(2) . "\$this->assign(\"i_count\", \$i_count);\n";
        }

        //列表
        _fun_comment("列表", 2);
        echo _tab(2) . "\$a_list = \$this->o_{$uc_model_name}->lists(";
        $this->_echoFunParams($a_w_param_use);
        $ii = count($a_w_param_use);
        if ($has_order) {
            if ($is_order_by_input) {
                echo _warp2join($ii) . _tab(5) . "\$v_order_by";
                $ii++;
            }
            if ($is_order_by_input) {
                echo _warp2join($ii) . _tab(5) . "\$v_order_dir";
                $ii++;
            }
        }
        //6666
        if ($has_pager) {
            echo _warp2join($ii) . _tab(5) . "\$i_page";
            $ii++;
            if ($is_pager_size_input) {
                echo _warp2join($ii) . _tab(5) . "\$i_page_size";
                $ii++;
            }
        }
        echo ");\n";
        _fun_comment("TODO 组装其他数据", 2);
        echo _tab(2) . "\$this->assign(\"a_list\", \$a_list);\n";

        if ($has_pager) {
            _fun_comment("回传参数", 2);
            $ii = 0;
            $a_param_return = array();
            foreach ($a_w_param_use as $use_param) {
                $o_field = $a_w_param_field[$ii];
                $key = $o_field->name;
                echo _tab(2) . "\$this->assign(\"curr_{$key}\", $use_param);\n";
                $a_param_return["curr_{$key}"] = "{$use_param}";
                $ii++;
            }
            if ($has_order) {
                if ($is_order_by_input) {
                    echo _tab(2) . "\$this->assign(\"curr_order_by\", v_order_by);\n";
                    $a_param_return["curr_order_by"] = "\$v_order_by";
                }
                if ($is_order_by_input) {
                    echo _tab(2) . "\$this->assign(\"curr_order_dir\", v_order_dir);\n";
                    $a_param_return["curr_order_dir"] = "\$v_order_dir";
                }
            }
            //6666
            _fun_comment("分页", 2);
            echo _tab(2) . "\$url_this = \$this->_pool['url_{$model_name}_lists']. \"?__foo__=bar\";\n";
            foreach ($a_param_return as $key => $a_p_return) {
                echo _tab(2) . "\$url_this .= \"&{$key}=\" . $a_p_return ;\n";
            }
            echo _tab(2) . "\$url_this .= \"&\";\n";
            echo _tab(2) . "\$this->_pager(\$url_this, \$i_count, \$i_page, \$i_page_size);\n";

            echo _tab(2) . "\$this->assign('need_pager', 1);\n";
        }
        echo _tab(2) . "\$t2 = microtime(true);\n";
        echo _tab(2) . "\$this->assign('i_time_used', intval((\$t2 - \$t1) * 1000));\n";

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

        $view_model_dir = $this->odir_views . DS . "{$model_name}";
        dir_create($view_model_dir);


        $_target = $view_model_dir . DS . "index.volt";
        ob_start();
        $this->makeHtmlIndex($model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

        SeasLog::info("列表页模板--{$model_name}");
        $_target = $view_model_dir . DS . "lists.volt";
        ob_start();
        $this->makeHtmlList($model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

        SeasLog::info("新建页模板--{$model_name}");
        $_target = $view_model_dir . DS . "add.volt";
        ob_start();
        $this->makeHtmlEdit($model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

        SeasLog::info("编辑页模板--{$model_name}");
        $_target = $view_model_dir . DS . "edit.volt";
        ob_start();
        $this->makeHtmlEdit($model);
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

        SeasLog::info("详情页模板--{$model_name}");
        $_target = $view_model_dir . DS . "detail.volt";
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
        $_tag1 = _tag1();
        $_tag2 = _tag2();
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
        echo "<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\" >\n";
        if (!$for_edit) {
            echo "{% include \"layouts/header.inc.volt\" %}\n";
        } else {
            echo "{% include \"layouts/header_for_edit.inc.volt\" %}\n";
        }
        echo "<body class=\"hold-transition sidebar-mini text-sm\">\n";
        echo _tab(1) . "<div class=\"wrapper\">";
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
            echo "{% include \"layouts/footer.inc.volt\" %}\n";
        } else {
            echo "{% include \"layouts/footer_for_edit.inc.volt\" %}\n";
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
        if ($this->fun_list == null) {
            return;
        }
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $lc_model_name = strtolower($model_name);
        $this->_makeHtmlHeader($model);

        list($i_w_param, $a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_type, $a_w_param_field) = $this->_procWhereCond($model, $this->fun_list);
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
                                   href="{{ url_<?= $model_name ?>_add }}">
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
                    <div class="col-lg-12">
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
                                      action="{{ url_<?= $model_name ?>_lists }}">
                                    <div class="form-row">
                                        <?php

                                        foreach ($a_w_param_field as $field) {
                                            /* @var MyField $field */
                                            $key = $field->name;
                                            $uc_key = ucfirst($key);
                                            if ($field->input_hash != "") {
                                                ?>
                                                <div class="col">
                                                    <label for="ipt_<?= $key ?>"><?= $field->title ?></label>

                                                    <select class="form-control"
                                                            name="<?= $key ?>"
                                                            id="ipt_<?= $key ?>">
                                                        <option value="">不限</option>

                                                        {% for k, v in m_<?= $uc_key ?>_KV %}
                                                        <option value="{{ k }}"
                                                                {% if(curr_<?= $key ?> == k) %}
                                                                selected="selected"
                                                                {% endif %}
                                                        >
                                                            {{ v }}
                                                        </option>
                                                        {% endfor %}


                                                    </select>

                                                </div>
                                                <?php
                                            } else if ($field->type == Constant:: DB_FIELD_TYPE_DATE || $field->type == Constant:: DB_FIELD_TYPE_TIME || $field->type == Constant:: DB_FIELD_TYPE_DATETIME) {

                                                ?>
                                                <div class="col">
                                                    <label for="ipt_<?= $key ?>"><?= $field->title ?></label>
                                                    <div class="input-group mr-sm-2">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                               class="form-control"
                                                               name="<?= $key ?>"
                                                               id="ipt_<?= $key ?>"
                                                               placeholder="日期"
                                                               value="{{ curr_<?= $key ?> }}"
                                                        />
                                                    </div>
                                                </div>
                                                <?php
                                            } else {

                                                ?>
                                                <div class="col">
                                                    <label for="ipt_<?= $key ?>"><?= $field->title ?></label>
                                                    <input type="text" class="form-control mr-sm-2"
                                                           name="<?= $key ?>"
                                                           id="ipt_<?= $key ?>"
                                                           placeholder=""
                                                           value="{{ curr_<?= $key ?> }}"/>
                                                </div>

                                                <?php
                                            }
                                        } ?>
                                        <div class="col">
                                            <label>&nbsp;</label>
                                            <div class="">
                                                <button type="submit" class="btn btn-primary ">
                                                    <i class="fa fa-search"></i>
                                                    查询
                                                </button>
                                                <a class="btn btn-secondary"
                                                   href="{{ url_<?= $model_name ?>_lists }}">
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
                                    {% for k, v in a_list %}
                                    <tr>
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
                                            <td rel="<?= $key ?>">
                                                {{ v['<?= $key ?>'] }}
                                            </td>
                                        <?php } ?>
                                        <td>
                                            <a class="btn btn-sm btn-success"
                                               href="{{ url_<?= $model_name ?>_detail }}?<?= $model->primary_key ?>={{ v['<?= $model->primary_key ?>'] }}">
                                                <i class="fa fa-info"></i>
                                                详情
                                            </a>

                                            <a class="btn btn-sm btn-primary"
                                               href="{{ url_<?= $model_name ?>_edit }}?<?= $model->primary_key ?>={{ v['<?= $model->primary_key ?>'] }}">
                                                <i class="fa fa-edit"></i>
                                                编辑
                                            </a>

                                        </td>
                                    </tr>
                                    {% endfor %}
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">


                                <div class="row">
                                    <div class="col-sm-12 col-md-5">
                                        <div class="dataTables_info" role="status" aria-live="polite">

                                            耗时: {{ i_time_used }} 毫秒,
                                            记录: {{ i_count }} 条,
                                            分页：{{ curr_page }} / {{ i_page_count }}

                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-7 text-center">
                                        {% if need_pager==1 %}
                                        <div class="dataTables_paginate paging_simple_numbers">
                                            {{ pager_html }}
                                        </div>
                                        {% endif %}
                                    </div>

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
            //var _url_submit = "[[@{'/<?= $model_name ?>/ajax_modify'}]]";
            var _g_token = "[[${op_token__}]]";
            var url_<?=$lc_model_name?>_modify = '{{ url_<?=$lc_model_name?>_modify }}';
            var url_<?=$lc_model_name?>_ajax_modify = '{{ url_<?=$lc_model_name?>_ajax_modify }}';
            var url_<?=$lc_model_name?>_delete = '{{ url_<?=$lc_model_name?>_delete }}';
            var url_<?=$lc_model_name?>_ajax_delete = '{{ url_<?=$lc_model_name?>_ajax_delete }}';

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
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="#"
                                   href="{{ url_<?= $model_name ?>_lists }}"
                                   id="txt_curr_version_name">返回列表</a>
                            </li>
                            <li class="breadcrumb-item active" id="txt_curr_version_title">编辑</li>
                        </ol>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
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
                                  action="{{ url_<?= $model_name ?>_modify }}">
                                <div class="card-body">
                                    {{ __form_token__ }}
                                    <input type="hidden"
                                           readonly="readonly"
                                           name="_id"
                                           class="form-control"
                                           value="{{ a_info['id'] }}"/>

                                    <?php
                                    foreach ($a_param_field as $key => $o_field) {
                                        /* @var MyField $o_field */
                                        $key = $o_field->name;
                                        //保留字全部不用输入
                                        if (in_array($key, array("flag", "ctime", "utime", "cadmin", "uadmin"))) {
                                            continue;
                                        }
                                        $uc_key = ucfirst($key);


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
                            {% include "layouts/block_upload_avatar.volt" %}
                            <?php
                        } ?>
                        {% include "layouts/block_confirm_delete.volt" %}
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
               App.setupFormSubmit("#form_main");
            });
        </script>
        <?php
        $this->_makeHtmlFooter2();
    }

    function makeHtmlIptMultiText(MyModel $model, MyField $field, $for_show = false)
    {
        $key = $field->name;
        $uc_key = ucfirst($key);
        $val1 = "value=\"{{ a_info['{$key}'] }}\"";
        $val2 = "{{ a_info['{$key}'] }}";

        $required = ($field->required == 1) ? 'required=required' : '';
        if ($for_show) {
            ?>
            <textarea class="form-control"
                      rows="3"
                      readonly="readonly"
            ><?= $val2 ?></textarea>
            <?php
        } else {
            ?>
            <textarea class="form-control"
                      rows="3"
                      name="<?= $key ?>"
                      id="ipt_<?= $key ?>"
        <?= $required ?> ><?= $val2 ?></textarea>
            <?php
        }
    }

    function makeHtmlIptSelect(MyModel $model, MyField $field, $for_show = false)
    {
        $key = $field->name;
        $uc_key = ucfirst($key);
        $val1 = "value=\"{{ a_info['{$key}'] }}\"";
        if ($for_show) {
            ?>
            <input type="text" class="form-control "
                   readonly="readonly"
                <?= $val1 ?>
            />
            <?php
        } else {
            ?>
            <select class="form-control select2"
                    name="<?= $key ?>"
                    id="ipt_<?= $key ?>">
            <option value="">不限</option>
            {% for k, v in m_<?= $uc_key ?>_KV %}
            <option value="{{ k }}"
                    {% if(a_info['<?= $key ?>'] == k) %}
            selected="selected"
            {% endif %}
            >
            {{ v }}
                </option>
            {% endfor %}

            </select>
            <?php
        }
    }

    function makeHtmlIptRadio(MyModel $model, MyField $field, $for_show = false)
    {
        $key = $field->name;
        $uc_key = ucfirst($key);
        $val1 = "value=\"{{ a_info['{$key}'] }}\"";
        $a_hash = explode(";", $field->input_hash);
        $required = ($field->required == 1) ? 'required=required' : '';
        if ($for_show) {
            ?>
            <input type="text" class="form-control "
                   readonly="readonly"
                <?= $val1 ?>
            />
            <?php
        } else {
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

    function makeHtmlIptCheckBox(MyModel $model, MyField $field, $for_show = false)
    {
        $key = $field->name;
        $uc_key = ucfirst($key);
        $val1 = "value=\"{{ a_info['{$key}'] }}\"";
        $a_hash = explode(";", $field->input_hash);
        if ($for_show) {
            ?>
            <input type="text" class="form-control "
                   readonly="readonly"
                <?= $val1 ?>
            />
            <?php
        } else {
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

    function makeHtmlIptDate(MyModel $model, MyField $field, $for_show = false)
    {
        $key = $field->name;
        $uc_key = ucfirst($key);
        $val1 = "value=\"{{ a_info['{$key}'] }}\"";
        $required = ($field->required == 1) ? 'required=required' : '';

        $has_filter = "";
        $this_filter = "";
        if($field->filter != ""){
            if($field->filter != Constant::DB_FIELD_FILTER_NULL){
                $has_filter = "has_filter";
                $this_filter = "filter=\"DATE\"";
            }
        }

        if ($for_show) {
            ?>
            <input type="text" class="form-control "
                   readonly="readonly"
                <?= $val1 ?>
            />
            <?php
        } else {
            ?>
            <div class="input-group <?=$has_filter?> date has_data" id="data_ipt_<?= $key ?>" data-target-input="nearest">
                <input type="text" class="form-control datetimepicker-input "
                       name="<?= $key ?>"
                       id="ipt_<?= $key ?>"
                    <?= $this_filter ?>
                    <?= $val1 ?>
                    <?= $required ?>
                       data-target="#data_ipt_<?= $key ?>">
                <div class="input-group-append"
                     data-target="#data_ipt_<?= $key ?>"
                     data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                </div>
            </div>

            <div class="invalid-feedback">

            </div>
            <div class="valid-feedback">

            </div>

            <?php
        }
    }

    function makeHtmlIptDateTime(MyModel $model, MyField $field, $for_show = false)
    {
        $key = $field->name;
        $uc_key = ucfirst($key);
        $val1 = "value=\"{{ a_info['{$key}'] }}\"";
        $required = ($field->required == 1) ? 'required=required' : '';
        $has_filter = "";
        $this_filter = "";
        if($field->filter != ""){
            if($field->filter != Constant::DB_FIELD_FILTER_NULL){
                $has_filter = "has_filter";
                $this_filter = "filter=\"DATETIME\"";
            }
        }
        if ($for_show) {
            ?>
            <input type="text" class="form-control "
                   readonly="readonly"
                <?= $val1 ?>
            />
            <?php
        } else {
            ?>
            <div class="input-group <?=$has_filter?> date has_datatime" id="datatime_ipt_<?= $key ?>" data-target-input="nearest">
                <input type="text" class="form-control datetimepicker-input "
                       name="<?= $key ?>"
                       id="ipt_<?= $key ?>"
                    <?= $this_filter ?>
                    <?= $val1 ?>
                    <?= $required ?>
                       data-target="#datatime_ipt_<?= $key ?>">
                <div class="input-group-append"
                     data-target="#datatime_ipt_<?= $key ?>"
                     data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                </div>
            </div>

            <div class="invalid-feedback">

            </div>
            <div class="valid-feedback">

            </div>

            <?php
        }
    }

    function makeHtmlIptFile(MyModel $model, MyField $field, $for_show = false)
    {
        $key = $field->name;
        $uc_key = ucfirst($key);

        $required = ($field->required == 1) ? 'required=required' : '';
        if ($for_show) {
            ?>
            <input type="text" class="form-control "
                   readonly="readonly" value="TODO"
            />
            <?php
        } else {
            ?>
            <input type="file" class="form-control "
                   name="<?= $key ?>"
                   id="ipt_<?= $key ?>"
                <?= $required ?>
            />

            <?php
        }
    }

    function makeHtmlIptLineText(MyModel $model, MyField $field, $for_show = false)
    {
        $key = $field->name;
        $uc_key = ucfirst($key);
        $val1 = "value=\"{{ a_info['{$key}'] }}\"";
        $required = ($field->required == 1) ? 'required=required' : '';
        $has_filter = "";
        $this_filter = "";
        $this_filter_reg = "";//
        if($field->filter != ""){
            if($field->filter != Constant::DB_FIELD_FILTER_NULL){
                $has_filter = "has_filter";
                $this_filter = "filter=\"{$field->filter}\"";
                if($field->filter == Constant::DB_FIELD_FILTER_REGEXP){
                    $this_filter_reg = "filter_reg=\"{$field->regexp}\"";
                }
            }
        }
        if ($for_show) {
            ?>
            <input type="text" class="form-control "
                   readonly="readonly"
                <?= $val1 ?>
            />
            <?php
        } else {
            ?>
            <input type="text" class="form-control <?=$has_filter?>"
                   name="<?= $key ?>"
                   id="ipt_<?= $key ?>"
                <?= $this_filter ?>
                <?= $this_filter_reg ?>
                <?= $val1 ?>
                <?= $required ?>
            />

            <div class="invalid-feedback">

            </div>
            <div class="valid-feedback">

            </div>

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
                                <a href="{{ url_<?= $model_name ?>_lists }}"
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
                                  action="{{ url_<?= $model_name ?>_modify }}"
                            >
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
                                <a class="btn btn-lg btn-info "
                                   href="{{ url_<?= $model_name ?>_edit }}?id={{ a_info['id'] }}"
                                >
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
        $_target = $this->odir_config . DS . "url.php";
        ob_start();

        $this->_makeHeader();
        _fun_comment("全局url重写定义");

        $webapp_head = <<< WEB
    return array(
        //入口
        'url_index' => 'index/index',
        //登入登出
        'url_sign_in' => 'index/sign_in',
        'url_sign_out' => 'index/sign_out',
        'url_rnd_img' => 'index/rnd_img',

        //仪表盘,主框架外部
        'url_dashboard' => 'default/dashboard',
        //登陆后主框架默认内容
        'url_home' => 'default/home',
        
WEB;
        echo $webapp_head;
        $package = $this->final_package;
        foreach ($a_models as $id => $model) {
            $model_name = $model->name;
            $uc_model = ucfirst($model_name);
            $lc_model = strtolower($model_name);
            echo "\n";
            echo _tab(1) . "//" . $model->title . "\n";
            ?>
            'url_<?= $lc_model ?>_add' => '<?= $lc_model ?>/add',
            'url_<?= $lc_model ?>_save' => '<?= $lc_model ?>/save',
            'url_<?= $lc_model ?>_ajax_save' => '<?= $lc_model ?>/ajax_save',
            'url_<?= $lc_model ?>_edit' => '<?= $lc_model ?>/edit',
            'url_<?= $lc_model ?>_modify' => '<?= $lc_model ?>/modify',
            'url_<?= $lc_model ?>_ajax_modify' => '<?= $lc_model ?>/ajax_modify',
            'url_<?= $lc_model ?>_delete' => '<?= $lc_model ?>/delete',
            'url_<?= $lc_model ?>_ajax_delete' => '<?= $lc_model ?>/ajax_delete',
            'url_<?= $lc_model ?>_lists' => '<?= $lc_model ?>/lists',
            <?php
        }
        ?>

        <?php
        echo ");";

        $cc_data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $cc_data);

        SeasLog::info("创建menu.php--");
        $_target = $this->odir_config . DS . "menu.php";
        ob_start();

        $this->_makeHeader();
        _fun_comment("菜单配置");

        $menu_head = <<< WEB
 
function cfg_global_menu(\$a_urls)
{

    return array(

        'default' => array(
            'name' => '系统概况',
            'icon' => 'fas fa-tachometer-alt',
            'link' => \$a_urls['url_home'],
            'sub_menu' => array()
        ),

WEB;

        echo $menu_head;
        foreach ($a_models as $id => $model) {
            /* @var MyModel $model */
            $model_name = $model->name;
            $uc_model = ucfirst($model_name);
            $lc_model = strtolower($model_name);
            ?>
            '<?= $lc_model ?>' => array(
            'name' => '<?= $model->title ?>管理',
            'icon' => 'fas fa-<?= $model->fa_icon ?>',
            'link' => "#",
            'sub_menu' => array(
            '<?= $lc_model ?>_edit' => array(
            'name' => '新建<?= $model->title ?>',
            'link' => $a_urls['url_<?= $lc_model ?>_add'],
            'icon' => 'far fa-circle text-success'
            ),

            '<?= $lc_model ?>_list' => array(
            'name' => '<?= $model->title ?>列表',
            'link' => $a_urls['url_<?= $lc_model ?>_lists'],
            'icon' => 'far fa-circle text-danger'
            ),
            )
            ),
            <?php
        }
        echo ");}";
        $cc_data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $cc_data);


    }

}

