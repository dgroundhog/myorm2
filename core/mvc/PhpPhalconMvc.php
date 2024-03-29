<?php
if (!defined("MVC_ROOT")) {
    define('MVC_ROOT', realpath(dirname(__FILE__)));
}

include_once(MVC_ROOT . "/MvcBase.php");

class PhpPhalconMvc extends MvcBase
{

    /**
     * 处理参数
     * 需要区分需要输入的参数和使用的参数，还有注释的参数
     *
     * @param MyField $o_field
     * @param string $idx_append 避免重复的计数器
     * @param string $append u/w  update or where
     *
     * @return string [用于注释的，用于输入的，用于使用的]
     */
    function _procParam($o_field, $idx_append = 0, $append = "", $for_hash = false)
    {
        $key = $o_field->name;
        $type = $o_field->type;
        $desc = $o_field->title;

        $ret1 = "";
        $ret2 = "";
        $ret3 = "";

        //i_w_1_key
        //c_w_2_from_key
        //s_w_3_to_key

        $prefix = $this->getFieldParamPrefix($type);
        if ($append != "") {
            $prefix = "{$prefix}_{$append}";
        }
        $param_key = "\$v_{$idx_append}_{$prefix}_{$key}";
        $param_type = "string";
        $param_type_bind = "Db\\Column::BIND_PARAM_STR";
        switch ($type) {
            //布尔
            case Constant::DB_FIELD_TYPE_BOOL :
                $param_type = "int";//0==false 1== true
                //$param_type = "bool";
                $param_type_bind = "Db\\Column::BIND_PARAM_INT";
                break;

            //整型
            case Constant::DB_FIELD_TYPE_INT:
            case Constant::DB_FIELD_TYPE_LONGINT:
                $param_type = "int";
                $param_type_bind = "Db\\Column::BIND_PARAM_INT";
                break;

            //blob
            case Constant::DB_FIELD_TYPE_BLOB :
            case Constant::DB_FIELD_TYPE_LONGBLOB :
                $param_type = "byte[]";
                $param_type_bind = "Db\\Column::BIND_PARAM_BLOB";
                break;

            //单个字符
            case Constant::DB_FIELD_TYPE_CHAR:
                //字符串
            case Constant::DB_FIELD_TYPE_VARCHAR:

            case Constant::DB_FIELD_TYPE_TEXT :

            case Constant::DB_FIELD_TYPE_LONGTEXT :

            case Constant::DB_FIELD_TYPE_DATE :

            case Constant::DB_FIELD_TYPE_TIME :

            case Constant::DB_FIELD_TYPE_DATETIME :
                //默认的字符串
            default :
                $param_type = "string";
                $param_type_bind = "Db\\Column::BIND_PARAM_STR";
                break;
        }
        $ret1 = " * @param {$param_type} {$param_key} {$desc}";
        //$ret2 = "{$param_type} {$param_key}";
        $ret2 = "{$param_key}";
        $ret3 = "{$param_key}";

        return array($ret1, $ret2, $ret3, $param_type_bind);
    }

    function ccModel($model)
    {
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        SeasLog::info("创建PHP数据模型--{$model_name}");
        $_target = $this->odir_models . DS . "{$uc_model_name}Model.php";
        ob_start();
        $this->_makeheader();
        echo "use Phalcon\Db as Db;\n";

        _fun_comment(array("php  操作模型类", $model->title));
        echo "class {$uc_model_name}Model extends ModelBase {\n";

        $a_all_fields = array();
        //转换用name作为主键
        foreach ($model->field_list as $field) {
            /* @var MyField $field */
            $key = $field->name;
            $a_all_fields[$key] = $field;
        }
        if (count($a_all_fields) > 0) {
            _fun_comment("基本数据字段映射,模型中的字段和数据库的字段的对英关系", 1);
            echo _tab(1) . "public static \$m_row_map = array(\n";
            $a_temp = array();
            foreach ($model->field_list as $field) {
                /* @var MyField $field */
                if ($field->type == Constant::DB_FIELD_TYPE_BLOB || $field->type == Constant::DB_FIELD_TYPE_LONGBLOB) {
                    continue;
                }
                $key = $field->name;
                $a_temp[] = _tab(2) . "\"{$key}\" => \"{$key}\"";
            }
            echo implode(",\n", $a_temp) . "\n";
            echo _tab(1) . ");\n";

            /**
             * 基本数据实体
             */
            _fun_comment_header("数据实体", 1);
            echo _tab(1) . " * @var {$uc_model_name}Bean\n";
            _fun_comment_footer(1);
            echo _tab(1) . "public \$bean;\n";


            _fun_comment_header("获取bean", 1);
            echo _tab(1) . " * @return {$uc_model_name}Bean\n";
            _fun_comment_footer(1);
            echo _tab(1) . "public function getBean() {\n";
            echo _tab(2) . "return \$this->bean;\n";
            echo _tab(1) . "}\n";


            _fun_comment_header("设置bean", 1);
            echo _tab(1) . " * @param {$uc_model_name}Bean \$bean0\n";
            echo _tab(1) . " * @return void\n";
            _fun_comment_footer(1);
            echo _tab(1) . "public function setBean(\$bean0) {\n";
            echo _tab(2) . "\$this->bean = \$bean0;\n";
            echo _tab(1) . "}\n";

            //一组关键词
            foreach ($model->field_list as $field) {
                /* @var MyField $field */
                if ($field->input_hash != "") {
                    $key = $field->name;
                    $uc_key = ucfirst($key);
                    _fun_comment("基础字典-{$field->title}", 1);
                    echo _tab(1) . "public function get{$uc_key}_KV(){\n";
                    echo _tab(2) . "\$mList = array();\n";
                    $a_hash = explode(";", $field->input_hash);
                    foreach ($a_hash as $s_kv) {
                        $a_kv = explode(",", $s_kv);
                        echo _tab(2) . "\$mList[\"{$a_kv[0]}\"] = \"{$a_kv[1]}\";\n";
                    }
                    echo _tab(2) . "return \$mList;\n";
                    echo _tab(1) . "}\n\n";
                }
            }

            foreach ($model->fun_list as $o_fun) {

                /* @var MyFun $o_fun */
                $fun_type = $o_fun->type;

                switch ($fun_type) {
                    case Constant::FUN_TYPE_ADD:
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

                    case Constant::FUN_TYPE_COUNT:
                        $this->cCount($model, $o_fun);
                        break;

                    case Constant::FUN_TYPE_LIST_WITH_COUNT:
                    case Constant::FUN_TYPE_LIST_WITH_AVG:
                    case Constant::FUN_TYPE_LIST_WITH_SUM:
                    case Constant::FUN_TYPE_LIST_WITH_MAX:
                    case Constant::FUN_TYPE_LIST_WITH_MIN:
                    case Constant::FUN_TYPE_LIST:
                    default:
                        $this->cList($model, $o_fun);
                        break;
                }
            }
        }
        echo "}";

        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

        $_target2 = $this->odir_models . DS . "{$uc_model_name}ModelX.php";
        ob_start();
        $this->_makeheader();
        echo "use Phalcon\Db as Db;\n";
        _fun_comment("自定义的操作模型类--{$model->title}");
        echo "class {$uc_model_name}ModelX extends {$uc_model_name}Model {\n";
        echo "}";

        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target2, $data);

        foreach ($model->field_list as $field) {
            /* @var MyField $field */
            if ($field->input_hash != "") {
                $key = $field->name;
                $uc_key = ucfirst($key);
                //字典作为枚举型
                $kv_list = array();
                $a_hash = explode(";", $field->input_hash);
                foreach ($a_hash as $s_kv) {
                    $a_kv = explode(",", $s_kv);
                    $kv_list[$a_kv[0]] = $a_kv[1];
                }
                $e_name = "E{$uc_model_name}{$uc_key}";
                $_target = $this->odir_enums . DS . "{$e_name}.php";
                ob_start();
                $this->_makeheader();
                //echo "package {$this->final_package}.enums;\n\n";
                //echo "import java.util.HashMap;\n";
                _fun_comment("枚举值：{$model->title} -- {$field->title}");
                _php_enum_common( $e_name,$kv_list);
                $cc_data = ob_get_contents();
                ob_end_clean();
                file_put_contents($_target, $cc_data);
            }
        }
    }

    function _makeHeader()
    {
        echo "<?php";
        echo "\n//auto gen via myorm";
        echo "\n";
        if ($this->final_package != "") {
            //echo "namespace {$this->final_package};";
            echo "\n";
        }
    }

    function cAdd(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $lc_model_name = strtolower($model_name);
        $base_fun = strtolower($fun->type);
        $fun_name = $fun->name;
        $proc_name = $this->findProcName($model->table_name, $fun_name, $base_fun);//存储过曾的名字
        $fun_name1 = $this->makeModelFunName($fun_name, $base_fun);//散列参数添加
        $fun_name2 = $this->makeModelFunName($fun_name, $base_fun, true);//通过bean添加

        list($is_return_new_id, $i_param, $a_param_comment, $a_param_define, $a_param_use, $a_param_type, $a_param_key, $a_param_field) = $this->parseAdd_field($model, $fun);

        if ($i_param == 0) {
            return;
        }

        if ($is_return_new_id) {

            // $i_param++;
        }

        _fun_comment_header("插入数据vars-", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        foreach ($a_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        echo _tab(1) . " * @return int\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public function {$fun_name1}(";
        $this->_echoFunParams($a_param_define);
        echo ")\n";
        echo _tab(1) . "{\n";

        $s_qm = _db_question_marks($i_param);
        echo _tab(2) . "//question_marks = {$i_param} \n";
        echo _tab(2) . "\$i_new_id = 0;\n";
        if ($is_return_new_id) {
            echo _tab(2) . "\$sql = \"CALL `{$proc_name}`({$s_qm}, @_new_id);\";\n";
        } else {
            echo _tab(2) . "\$sql = \"CALL `{$proc_name}`({$s_qm});\";\n";
        }
        if ($i_param == 0) {
            $this->_noBindQuery();
        } else {
            $this->_beforeQuery();
            echo _tab(4);
            $a_param_use2 = array();
            foreach ($a_param_use as $key => $param) {
                $a_param_use2[] = $param;
            }
            echo implode(",\n" . _tab(4), $a_param_use2);
            echo "\n";
            $this->_onQuery();
            echo _tab(4);
            echo implode(",\n" . _tab(4), $a_param_type);
            echo "\n";
            $this->_afterQuery();
        }

        $this->_beforeResultLoop();
        echo _tab(4) . "\$i_new_id = \$a_ret['i_new_id'];\n";
        echo _tab(4) . "break;\n";
        $this->_afterResultLoop();

        echo _tab(2) . "SeasLog::debug(\"call {$proc_name} return {\$i_new_id}\");\n";
        echo _tab(2) . "return \$i_new_id;\n";
        echo _tab(1) . "}";


        _fun_comment_header("插入数据--通过bean", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        echo _tab(1) . " * @param {$uc_model_name}Bean \$v_{$lc_model_name}Bean\n";
        echo _tab(1) . " * @return int\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public function {$fun_name2}({$uc_model_name}Bean \$v_{$lc_model_name}Bean) \n";
        echo _tab(1) . "{\n";
        echo _tab(2) . "\$i_new_id = \$this->{$fun_name1}(";
        $ii = 0;
        foreach ($a_param_key as $key) {
            echo _warp2join($ii) . _tab(5) . "\$v_{$lc_model_name}Bean->{$key}";
            $ii++;
        }
        echo "\n";
        echo _tab(2) . ");\n";
        echo _tab(2) . "return \$i_new_id;\n";
        echo _tab(1) . "}";

        $this->_funFooter($model, $fun);
    }

    function _noBindQuery()
    {
        echo _tab(2) . "\$a_ret_arr = self::getInst()->getReadConnection()->query(\$sql)->fetchAll();\n";
    }

    function _beforeQuery()
    {
        echo _tab(2) . "\$a_ret_arr = self::getInst()->getReadConnection()->query(\n";
        echo _tab(3) . "\$sql,\n";
        echo _tab(3) . "[\n";
    }

    function _onQuery()
    {
        echo _tab(3) . "],\n";
        echo _tab(3) . "[\n";
    }

    function _afterQuery()
    {
        echo _tab(3) . "]\n";
        // echo _tab(2) . ")->fetchAll(PDO::FETCH_ASSOC);\n";
        echo _tab(2) . ")->fetchAll();\n";
    }

    function _beforeResultLoop()
    {
        echo "\n";
        echo _tab(2) . "if(\$a_ret_arr != null && is_array(\$a_ret_arr) && count(\$a_ret_arr) > 0 ){\n";
        echo _tab(3) . "foreach (\$a_ret_arr as \$a_ret) {\n";
    }

    function _afterResultLoop()
    {
        echo _tab(3) . "}\n";
        echo _tab(2) . "}\n\n";
    }

    function cDelete(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);
        $base_fun = strtolower($fun->type);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $lc_model_name = strtolower($model_name);
        $fun_name = $fun->name;
        $proc_name = $this->findProcName($model->table_name, $fun_name, $base_fun);
        $fun_name1 = $this->makeModelFunName($fun_name, $base_fun);


        list($i_param, $a_param_comment, $a_param_define, $a_param_use, $a_param_type, $a_param_field) = $this->_procWhereCond($model, $fun);


        _fun_comment_header("删除数据vars", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        foreach ($a_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        echo _tab(1) . " * @return int\n";
        _fun_comment_footer(1);

        echo _tab(1) . "public function {$fun_name1}(";
        $this->_echoFunParams($a_param_define);
        echo ")\n";
        echo _tab(1) . "{\n";
        $s_qm = _db_question_marks($i_param);
        echo _tab(2) . "//question_marks = {$i_param}  \n";
        echo _tab(2) . "\$i_affected_rows = 0;\n";

        if ($i_param == 0) {
            echo _tab(2) . "\$sql = \"CALL `{$proc_name}`(@_affected_rows);\";\n";
            $this->_noBindQuery();
        } else {
            echo _tab(2) . "\$sql = \"CALL `{$proc_name}`({$s_qm},@_affected_rows);\";\n";
            $this->_beforeQuery();
            echo _tab(4);
            echo implode(",\n" . _tab(4), $a_param_use);
            echo "\n";
            $this->_onQuery();
            echo _tab(4);
            echo implode(",\n" . _tab(4), $a_param_type);
            echo "\n";
            $this->_afterQuery();
        }

        $this->_beforeResultLoop();
        echo _tab(4) . "\$i_affected_rows = \$a_ret['i_affected_rows'];\n";
        echo _tab(4) . "break;\n";
        $this->_afterResultLoop();

        echo _tab(2) . "SeasLog::debug(\"call {$proc_name} return {\$i_affected_rows}\");\n";
        echo _tab(2) . "return \$i_affected_rows;\n";
        echo _tab(1) . "}";

        $this->_funFooter($model, $fun);
    }

    function cUpdate(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);
        $base_fun = strtolower($fun->type);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $lc_model_name = strtolower($model_name);
        $fun_name = $fun->name;
        $proc_name = $this->findProcName($model->table_name, $fun_name, $base_fun);
        $fun_name1 = $this->makeModelFunName($fun_name, $base_fun);
        $fun_name2 = $this->makeModelFunName($fun_name, $base_fun, true);

        $a_all_fields = $model->field_list_kv;
        //需要更新的字段
        list($i_u_param, $a_u_param_comment, $a_u_param_define, $a_u_param_use, $a_u_param_type, $a_u_param_key, $a_u_param_field) = $this->_parseUpdate_field($model, $fun);

        //更新条件
        list($i_w_param, $a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_type, $a_w_param_field) = $this->_procWhereCond($model, $fun);


        _fun_comment_header("更新数据vars", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        foreach ($a_u_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        foreach ($a_w_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        echo _tab(1) . " * @return int\n";
        _fun_comment_footer(1);

        echo _tab(1) . "public function {$fun_name1}(";
        $this->_echoFunParams($a_u_param_define, $a_w_param_define);
        echo ")\n";
        echo _tab(1) . "{\n";
        $s_qm = _db_question_marks($i_u_param + $i_w_param + 1);
        echo _tab(2) . "//question_marks = u {$i_u_param} + w {$i_w_param} + r 1 \n";
        echo _tab(2) . "\$i_affected_rows = 0;\n";
        echo _tab(2) . "\$sql = \"CALL `{$proc_name}`({$s_qm},@_affected_rows);\";\n";

        $this->_beforeQuery();
        echo _tab(4);
        echo implode(",\n" . _tab(4), $a_u_param_use);
        echo ",\n" . _tab(4);
        echo implode(",\n" . _tab(4), $a_w_param_use);
        echo "\n";
        $this->_onQuery();
        echo _tab(4);
        echo implode(",\n" . _tab(4), $a_u_param_type);
        echo ",\n" . _tab(4);
        echo implode(",\n" . _tab(4), $a_w_param_type);
        echo "\n";
        $this->_afterQuery();

        $this->_beforeResultLoop();
        echo _tab(4) . "\$i_affected_rows = \$a_ret['i_affected_rows'];\n";
        echo _tab(4) . "break;\n";
        $this->_afterResultLoop();

        echo _tab(2) . "SeasLog::debug(\"call {$proc_name} return {\$i_affected_rows}\");\n";
        echo _tab(2) . "return \$i_affected_rows;\n";
        echo _tab(1) . "}";

        $this->_funFooter($model, $fun);

        _fun_comment_header("更新数据--通过bean", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        echo _tab(1) . " * @param {$uc_model_name}Bean \$v_{$lc_model_name}Bean\n";
        foreach ($a_w_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        echo _tab(1) . " * @return int\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public function {$fun_name2}({$uc_model_name}Bean \$v_{$lc_model_name}Bean";
        $ii = 1;
        foreach ($a_w_param_define as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        echo _tab(1) . "\n" . _tab(1) . ")\n";
        echo _tab(1) . "{\n";
        echo _tab(2) . "\$iRet = \$this->{$fun_name1}(";
        $ii = 0;
        foreach ($a_u_param_field as $field) {
            echo _warp2join($ii) . _tab(5) . "\$v_{$lc_model_name}Bean->{$field->name}";
            $ii++;
        }
        foreach ($a_w_param_use as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        echo _tab(2) . ");\n";
        echo _tab(2) . "return \$iRet;\n";
        echo _tab(1) . "}";

        $this->_funFooter($model, $fun);
    }

    function cFetch(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);
        $base_fun = strtolower($fun->type);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $fun_name = $fun->name;
        $proc_name = $this->findProcName($model->table_name, $fun_name, $base_fun);//存储过曾的名字
        $fun_name1 = $this->makeModelFunName($fun_name, $base_fun);//散列参数添加
        $fun_name2 = $this->makeModelFunName($fun_name, $base_fun, true);//散列参数添加返回bean

        $a_all_fields = $model->field_list_kv;

        //更新条件
        list($i_w_param, $a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_type, $a_w_param_field) = $this->_procWhereCond($model, $fun);

        _fun_comment_header("通过条件获取一个数据，返回值是hash", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        foreach ($a_w_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        echo _tab(1) . " * @return array\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public function {$fun_name1}(";
        $this->_echoFunParams($a_w_param_define);
        echo ")\n";

        echo _tab(1) . "{\n";
        $s_qm = _db_question_marks($i_w_param);
        echo _tab(2) . "//question_marks = {$i_w_param}\n";
        echo _tab(2) . "\$a_info = array();\n";
        echo _tab(2) . "\$sql = \"CALL `{$proc_name}`({$s_qm});\";\n";
        $this->_beforeQuery();
        echo _tab(4);
        echo implode(",\n" . _tab(4), $a_w_param_use);
        echo "\n";
        $this->_onQuery();
        echo _tab(4);
        echo implode(",\n" . _tab(4), $a_w_param_type);
        echo "\n";
        $this->_afterQuery();
        echo _tab(2) . "\$b_found = false;\n";
        $this->_beforeResultLoop();
        echo _tab(4) . "foreach (\$a_ret as \$kk => \$vv) {\n";
        echo _tab(5) . "if(isset(self::\$m_row_map[\$kk])){\n";
        echo _tab(6) . "\$a_info[\$kk] = \$vv;\n";
        echo _tab(5) . "}\n";
        echo _tab(5) . "\$b_found = true;\n";
        echo _tab(5) . "break;\n";
        echo _tab(4) . "}\n";
        $this->_afterResultLoop();

        echo _tab(2) . "SeasLog::debug(\"call {$proc_name} return {\$b_found}\");\n";
        echo _tab(2) . "return \$a_info;\n";
        echo _tab(1) . "}";

        _fun_comment_header("通过条件获取一个数据，返回值是bean", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        foreach ($a_w_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        echo _tab(1) . " * @return {$uc_model_name}Bean\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public function {$fun_name2}(";
        $this->_echoFunParams($a_w_param_define);
        echo ")\n";
        echo _tab(1) . "{\n";
        $s_qm = _db_question_marks($i_w_param);
        echo _tab(2) . "//question_marks = {$i_w_param}  \n";
        echo _tab(2) . "\$o_bean = new {$uc_model_name}Bean();\n";
        echo _tab(2) . "\$sql = \"CALL `{$proc_name}`({$s_qm});\";\n";
        $this->_beforeQuery();
        echo _tab(4);
        echo implode(",\n" . _tab(4), $a_w_param_use);
        echo "\n";
        $this->_onQuery();
        echo _tab(4);
        echo implode(",\n" . _tab(4), $a_w_param_type);
        echo "\n";
        $this->_afterQuery();
        echo _tab(2) . "\$b_found = false;\n";
        $this->_beforeResultLoop();
        echo _tab(4) . "foreach (\$a_ret as \$kk => \$vv) {\n";
        echo _tab(5) . "if(isset(self::\$m_row_map[\$kk])){\n";
        echo _tab(6) . "\$o_bean->\$kk = \$vv;\n";
        echo _tab(5) . "}\n";
        echo _tab(5) . "\$b_found = true;\n";
        echo _tab(5) . "break;\n";
        echo _tab(4) . "}\n";
        $this->_afterResultLoop();

        echo _tab(2) . "SeasLog::debug(\"call {$proc_name} return {\$b_found}\");\n";
        echo _tab(2) . "return \$o_bean;\n";
        echo _tab(1) . "}";
        $this->_funFooter($model, $fun);
    }

    function cCount(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);
        $model_name = $model->name;
        $base_fun = strtolower($fun->type);
        $uc_model_name = ucfirst($model_name);
        $fun_name = $fun->name;
        $proc_name = $this->findProcName($model->table_name, $fun_name, $base_fun);//存储过曾的名字
        $fun_name1 = $this->makeModelFunName($fun_name, $base_fun);//散列参数添加

        $a_all_fields = $model->field_list_kv;
        //更新条件
        list($i_w_param, $a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_type, $a_w_param_field) = $this->_procWhereCond($model, $fun);

        _fun_comment_header("普通统计数据", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        foreach ($a_w_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        echo _tab(1) . " * @return int\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public function {$fun_name1}(";
        $this->_echoFunParams($a_w_param_define);
        echo ")\n";
        echo _tab(1) . "{\n";
        $s_qm = _db_question_marks($i_w_param);
        echo _tab(2) . "//question_marks = {$i_w_param}\n";
        echo _tab(2) . "\$iCount = 0;\n";
        echo _tab(2) . "\$sql = \"CALL `{$proc_name}`({$s_qm});\";\n";
        if ($i_w_param == 0) {
            $this->_noBindQuery();
        } else {
            $this->_beforeQuery();
            echo _tab(4);
            echo implode(",\n" . _tab(4), $a_w_param_use);
            echo "\n";
            $this->_onQuery();
            echo _tab(4);
            echo implode(",\n" . _tab(4), $a_w_param_type);
            echo "\n";
            $this->_afterQuery();
        }

        $this->_beforeResultLoop();
        echo _tab(4) . "\$iCount = \$a_ret['i_count'];\n";
        echo _tab(4) . "break;\n";
        $this->_afterResultLoop();

        echo _tab(2) . "SeasLog::debug(\"call {$proc_name} return {\$iCount}\");\n";
        echo _tab(2) . "return \$iCount;\n";
        echo _tab(1) . "}";

        $this->_funFooter($model, $fun);
    }

    function cList(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);
        $base_fun = strtolower($fun->type);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $fun_name = $fun->name;
        $proc_name = $this->findProcName($model->table_name, $fun_name, $base_fun);//存储过曾的名字
        $fun_name1 = $this->makeModelFunName($fun_name, $base_fun);//散列参数添加
        $fun_name2 = $this->makeModelFunName($fun_name, $base_fun, true);//散列参数添加返回bean

        $a_all_fields = $model->field_list_kv;//通过主键访问的字段

        $fun_type = $fun->type;
        $has_return_bean = false;
        if ($fun_type == Constant::FUN_TYPE_LIST) {
            $has_return_bean = true;
        }
        //1111基本条件
        list($i_w_param, $a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_type, $a_w_param_field) = $this->_procWhereCond($model, $fun);


        $i_param_list = $i_w_param;

        //22222被聚合键 TODO 放到父级函数里处理
        $fun_type = $fun->type;
        list($has_group_field, $group_field, $o_group_field, $group_field_final, $group_field_sel) = $this->parseGroup_field($model, $fun);
        //3333分组键
        list($has_group_by, $group_by) = $this->parseGroup_by($model, $fun);

        //4444先处理having,预先处理hading的条件
        $has_having = false;
        $o_having = $fun->group_having;//用来判断绑定关系
        if ($has_group_field && $has_group_by) {
            list($has_having, $a_param_comment_having, $a_param_define_having, $a_param_use_having, $a_param_type_having, $_sql1_having, $_sql2_having) = $this->parseHaving($model, $fun, $o_group_field, $group_field_final);
            if ($has_having) {
                foreach ($a_param_define_having as $hvp) {
                    $a_param[] = $hvp;
                }
            }
        }

        //5555排序键
        list($has_order, $is_order_by_input, $s_order_by, $is_order_dir_input, $s_order_dir) = $this->parseOrder_by($model, $fun, $has_group_field, $group_field_final);

        //6666 分页
        list($has_pager, $is_pager_size_input, $pager_size) = $this->parsePager($model, $fun);
        //var_dump($fun->field_list);
        _fun_comment_header("通过条件列表，返回值是Vector", 1);
        echo _tab(1) . " * {$fun->type}--{$fun->name}--{$fun->title}\n";
        echo _tab(1) . " *\n";
        //11111
        foreach ($a_w_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        //2222
        //3333
        //4444
        if ($has_group_field && $has_group_by && $has_having) {
            foreach ($a_param_comment_having as $param) {
                echo _tab(1) . "{$param}\n";
                $i_param_list++;
            }
        }
        //5555
        if ($has_order) {
            if ($is_order_by_input) {
                echo _tab(1) . " * @param string \$v_order_by 排序字段\n";
                $i_param_list++;
            }
            if ($is_order_by_input) {
                echo _tab(1) . " * @param string \$v_order_dir 排序方式\n";
                $i_param_list++;
            }
        }
        //6666
        if ($has_pager) {
            echo _tab(1) . " * @param int \$v_page 页码\n";
            $i_param_list++;
            if ($is_pager_size_input) {
                echo _tab(1) . " * @param int \$v_page_size 分页大小\n";
                $i_param_list++;
            }
        }
        echo _tab(1) . " *\n";
        echo _tab(1) . " * @return array [Vector<HashMap>]\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public function {$fun_name1}(";
        $ii = 0;
        foreach ($a_w_param_define as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        if ($has_group_field && $has_group_by && $has_having) {
            foreach ($a_param_define_having as $param) {
                echo _warp2join($ii) . _tab(5) . "{$param}";
                $ii++;
            }
        }
        //5555
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
            echo _warp2join($ii) . _tab(5) . "\$v_page";
            $ii++;
            if ($is_pager_size_input) {
                echo _warp2join($ii) . _tab(5) . "\$v_page_size";
                $ii++;
            }
        }

        echo _tab(1) . "\n" . _tab(1) . ")\n";
        echo _tab(1) . "{\n";
        $s_qm = _db_question_marks($i_param_list);
        echo _tab(2) . "//question_marks = {$i_param_list}\n";
        echo _tab(2) . "\$a_list = array();\n";
        echo _tab(2) . "\$sql = \"CALL `{$proc_name}`({$s_qm});\";\n";

        if ($i_param_list == 0) {
            $this->_noBindQuery();
        } else {
            $tab4 = ",\n" . _tab(4);
            $this->_beforeQuery();
            echo _tab(4);
            echo implode(",\n" . _tab(4), $a_w_param_use);
            //5555
            $inc = $i_w_param;
            if ($has_group_field && $has_group_by && $has_having) {
                foreach ($a_param_use_having as $param) {
                    echo (($inc > 0) ? $tab4 : "") . "{$param}";
                    $inc++;
                }
            }

            if ($has_order) {
                if ($is_order_by_input) {
                    echo (($inc > 0) ? $tab4 : "") . "\$v_order_by";
                    $inc++;
                }
                if ($is_order_by_input) {
                    echo (($inc > 0) ? $tab4 : "") . "\$v_order_dir";
                    $inc++;
                }
            }
            //6666
            if ($has_pager) {
                echo (($inc > 0) ? $tab4 : "") . "\$v_page";
                $inc++;
                if ($is_pager_size_input) {
                    echo $tab4 . "\$v_page_size";
                    $inc++;
                }
            }
            echo "\n";
            $this->_onQuery();
            echo _tab(4);
            echo implode(",\n" . _tab(4), $a_w_param_type);
            //5555
            $inc = $i_w_param;
            if ($has_group_field && $has_group_by && $has_having) {
                if ($o_having->type == Constant::COND_TYPE_IN || $o_having->type == Constant::COND_TYPE_NOTIN) {
                    foreach ($a_param_use_having as $param) {
                        echo (($inc > 0) ? $tab4 : "") . "Db\\Column::BIND_PARAM_STR";
                        $inc++;
                    }
                } else {
                    foreach ($a_param_use_having as $param) {
                        echo (($inc > 0) ? $tab4 : "") . "Db\\Column::BIND_PARAM_INT";
                        $inc++;
                    }
                }
            }

            if ($has_order) {
                if ($is_order_by_input) {
                    echo (($inc > 0) ? $tab4 : "") . "Db\\Column::BIND_PARAM_STR";
                    $inc++;
                }
                if ($is_order_by_input) {
                    echo (($inc > 0) ? $tab4 : "") . "Db\\Column::BIND_PARAM_STR";
                    $inc++;
                }
            }
            //6666
            if ($has_pager) {
                echo (($inc > 0) ? $tab4 : "") . "Db\\Column::BIND_PARAM_INT";
                $inc++;
                if ($is_pager_size_input) {
                    echo $tab4 . "Db\\Column::BIND_PARAM_INT";
                    $inc++;
                }
            }
            echo "\n";
            $this->_afterQuery();
        }
        echo _tab(2) . "\$b_found = false;\n";
        $this->_beforeResultLoop();
        echo _tab(4) . "\$a_row_info = array();\n";
        if ($fun->all_field == 1) {
            echo _tab(4) . "foreach (\$a_ret as \$kk => \$vv) {\n";
            echo _tab(5) . "if(isset(self::\$m_row_map[\$kk])){\n";
            echo _tab(6) . "\$a_row_info[\$kk] = \$vv;\n";
            echo _tab(5) . "}\n";
            echo _tab(4) . "}\n";
        }
        else{
            foreach ($fun->field_list as $s_key => $o_field) {
                if (isset($model->field_list[$s_key])) {
                    $field_name = $o_field->name;
                    echo _tab(4) . "\$a_row_info['{$field_name}'] = \$a_ret['{$field_name}'];\n";
                }
            }
        }
        if ($has_group_field ) {
            echo _tab(4) . "\$a_row_info['{$group_field_final}'] = \$a_ret['{$group_field_final}'];\n";
        }

        echo _tab(4) . "\$a_list[] = \$a_row_info;\n";
        $this->_afterResultLoop();


        echo _tab(2) . "SeasLog::debug(\"call {$proc_name} return {\$b_found}\");\n";
        echo _tab(2) . "return \$a_list;\n";
        echo _tab(1) . "}";

        //选中分页的才分页/////////////////////////////////////////////////////////////////////////////////
        if ($has_return_bean) {
            //非聚合的才有bean
            _fun_comment_header("通过条件获取列表，返回值是bean", 1);
            echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
            echo _tab(1) . " *\n";
            foreach ($a_w_param_comment as $param) {
                echo _tab(1) . "{$param}\n";
            }
            //2222
            //3333
            //4444
            //5555
            if ($has_order) {
                if ($is_order_by_input) {
                    echo _tab(1) . " * @param string \$v_order_by 排序字段\n";
                    $i_param_list++;
                }
                if ($is_order_by_input) {
                    echo _tab(1) . " * @param string \$v_order_dir 排序方式\n";
                    $i_param_list++;
                }
            }
            //6666
            if ($has_pager) {
                echo _tab(1) . " * @param int \$v_page 页码\n";
                $i_param_list++;
                if ($is_pager_size_input) {
                    echo _tab(1) . " * @param int \$v_page_size 分页大小\n";
                    $i_param_list++;
                }
            }

            echo _tab(1) . " * @return array [Vector<{$uc_model_name}Bean>]\n";
            _fun_comment_footer(1);
            echo _tab(1) . "public function {$fun_name2}(";
            $ii = 0;
            foreach ($a_w_param_define as $param) {
                echo _warp2join($ii) . _tab(5) . "{$param}";
                $ii++;
            }
            //5555
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
                echo _warp2join($ii) . _tab(5) . "\$v_page";
                $ii++;
                if ($is_pager_size_input) {
                    echo _warp2join($ii) . _tab(5) . "\$v_page_size";
                    $ii++;
                }
            }
            echo _tab(1) . "\n" . _tab(1) . ")\n";
            echo _tab(1) . "{\n";
            echo _tab(2) . "\$a_bean_list = array();\n";
            echo _tab(2) . "\$a_map_list = \$this->{$fun_name1}(\n";
            $ii = 0;
            foreach ($a_w_param_define as $param) {
                echo _warp2join($ii) . _tab(5) . "{$param}";
                $ii++;
            }
            //5555
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
                echo _warp2join($ii) . _tab(5) . "\$v_page";
                $ii++;
                if ($is_pager_size_input) {
                    echo _warp2join($ii) . _tab(5) . "\$v_page_size";
                    $ii++;
                }
            }
            echo "\n" . _tab(5) . ");\n";
            echo _tab(2) . "foreach (\$a_map_list as \$a_row) {\n";
            echo _tab(3) . "\$o_bean = new {$uc_model_name}Bean();\n";
            echo _tab(3) . "foreach (\$a_row as \$kk => \$vv) {\n";
            echo _tab(4) . "if(isset(self::\$m_row_map[\$kk])){\n";
            echo _tab(5) . "\$o_bean->\$kk = \$vv;\n";
            echo _tab(4) . "}\n";
            echo _tab(3) . "}\n";
            echo _tab(3) . "\$a_bean_list[] = \$o_bean;\n";
            echo _tab(2) . "}\n";
            echo _tab(2) . "return \$a_bean_list;\n";
            echo _tab(1) . "}";
        }
        //分页时包含对应的计数/////////////////////////////////////////////////////////////////////////////////
        if ($has_pager) {
            _fun_comment_header("获取分页对应的记录总数", 1);
            echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
            echo _tab(1) . " *\n";
            foreach ($a_w_param_comment as $param) {
                echo _tab(1) . "{$param}\n";
            }
            $i_param_count = $i_w_param;
            if ($has_group_field && $has_group_by && $has_having) {
                foreach ($a_param_comment_having as $param) {
                    echo _tab(1) . "{$param}\n";
                    $i_param_count++;
                }
            }
            echo _tab(1) . " * @return  int\n";
            _fun_comment_footer(1);
            echo _tab(1) . "public function {$fun_name1}_Count(";
            $ii = 0;
            foreach ($a_w_param_define as $param) {
                echo _warp2join($ii) . _tab(5) . "{$param}";
                $ii++;
            }
            if ($has_group_field && $has_group_by && $has_having) {
                foreach ($a_param_define_having as $param) {
                    echo _warp2join($ii) . _tab(5) . "{$param}\n";
                    $ii++;
                }
            }
            echo _tab(1) . "\n" . _tab(1) . ")\n";
            echo _tab(1) . "{\n";
            $s_qm = _db_question_marks($i_param_count);
            echo _tab(2) . "//question_marks = {$i_param_count} \n";
            echo _tab(2) . "\$iCount = 0;\n";
            echo _tab(2) . "\$sql = \"CALL `{$proc_name}_c`({$s_qm});\";\n";

            if ($i_param_count == 0) {
                $this->_noBindQuery();
            } else {

                $this->_beforeQuery();
                echo _tab(4);
                echo implode(",\n" . _tab(4), $a_w_param_use);
                //5555
                $inc = $i_w_param;
                if ($has_group_field && $has_group_by && $has_having) {
                    foreach ($a_param_use_having as $param) {
                        echo (($inc > 0) ? $tab4 : "") . "\${$param}";
                        $inc++;
                    }
                }
                echo "\n";
                $this->_onQuery();
                echo _tab(4);
                echo implode(",\n" . _tab(4), $a_w_param_type);
                //5555
                $inc = $i_w_param;
                if ($has_group_field && $has_group_by && $has_having) {
                    if ($o_having->type == Constant::COND_TYPE_IN || $o_having->type == Constant::COND_TYPE_NOTIN) {
                        foreach ($a_param_use_having as $param) {
                            echo (($inc > 0) ? $tab4 : "") . "Db\\Column::BIND_PARAM_STR";
                            $inc++;
                        }
                    } else {
                        foreach ($a_param_use_having as $param) {
                            echo (($inc > 0) ? $tab4 : "") . "Db\\Column::BIND_PARAM_INT";
                            $inc++;
                        }
                    }
                }
                echo "\n";
                $this->_afterQuery();
            }
            $this->_beforeResultLoop();
            echo _tab(4) . "\$iCount = \$a_ret['i_count'];\n";
            echo _tab(4) . "break;\n";
            $this->_afterResultLoop();

            echo _tab(2) . "SeasLog::debug(\"call {$proc_name}_c return {\$iCount}\");\n";
            echo _tab(2) . "return \$iCount;\n";
            echo _tab(1) . "}";

        }


        $this->_funFooter($model, $fun);
    }

    function ccTmpl($model)
    {
        // TODO: Implement ccTmpl() method.
    }

    function ccCtrl($model)
    {
        // TODO: Implement ccWeb() method.
    }

    function ccApi($model)
    {
        // TODO: Implement ccApi() method.
    }

    function ccDoc($model)
    {
        // TODO: Implement ccDoc() method.
    }

    function ccEcode($kv_list)
    {
        $e_name = "MyECode";
        $_target = $this->odir_enums . DS . "{$e_name}.php";
        ob_start();
        $this->_makeheader();
        //echo "package {$this->final_package}.enums;\n\n";
        //echo "import java.util.HashMap;\n";
        _fun_comment("全局枚举值");
        _php_enum_common( $e_name,$kv_list);
        $cc_data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $cc_data);
    }


    /**
     * 创建模型层
     * @param MyModel $model
     * @return mixed
     */
    function ccBean(MyModel $model)
    {
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        SeasLog::info("创建PHP数据结构--{$model_name}");
        $_target = $this->odir_beans . DS . "{$uc_model_name}Bean.php";
        ob_start();


        $this->_makeHeader();


        _fun_comment("数据bean-{$model_name}[{$model->title}]", 1);
        echo "class {$uc_model_name}Bean\n{\n";

        foreach ($model->field_list as $field) {
            /* @var MyField $field */

            $key = $field->name;
            _fun_comment_header("{$field->title}", 1);
            switch ($field->type) {
                //bool
                case Constant::DB_FIELD_TYPE_BOOL :
                    echo _tab(1) . " * 0 for false,1 for true\n";
                    echo _tab(1) . " * @var int\n";
                    // echo _tab(1) . " * @var bool\n";
                    _fun_comment_footer(1);
                    echo _tab(1) . "public \${$key} = false;\n";
                    break;

                //整型
                case Constant::DB_FIELD_TYPE_INT:
                case Constant::DB_FIELD_TYPE_LONGINT:
                    echo _tab(1) . " * @var int\n";
                    _fun_comment_footer(1);
                    echo _tab(1) . "public \${$key} = 0;\n";
                    break;

                //blob
                case  Constant::DB_FIELD_TYPE_BLOB:
                case Constant::DB_FIELD_TYPE_LONGBLOB:
                    echo _tab(1) . " * @var string|object\n";
                    _fun_comment_footer(1);
                    echo _tab(1) . "public \${$key} = null;\n";
                    break;

                default:
                    echo _tab(1) . " * @var string\n";
                    _fun_comment_footer(1);
                    echo _tab(1) . "public \${$key} = \"\";\n";
                    break;
            }
        }

        _fun_comment_header("TO_STRING", 1);
        echo _tab(1) . " * @return string\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public function toString(){ \n";
        echo _tab(2) . "return var_export(\$this, true);\n";
        echo _tab(1) . "}\n\n";

        echo "}";

        $cc_data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $cc_data);
    }

    function ccRestful($model)
    {
        // TODO: Implement ccRestful() method.
    }
}