<?php
if (!defined("MVC_ROOT")) {
    define('MVC_ROOT', realpath(dirname(__FILE__)));
}

include_once(MVC_ROOT . "/MvcBase.php");

class PhpPhalconMvc extends MvcBase
{

    function _makeHeader()
    {
        echo "<?php";
        echo "\n//auto gen via myorm";
        echo "\n";
        if ($this->final_package != "") {
            echo "namespace {$this->final_package};";
            echo "\n";
        }
    }

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

    function cAdd(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $lc_model_name = strtolower($model_name);

        $fun_name = $fun->name;
        $proc_name = $this->findProcName($model->table_name, $fun_name, "add");//存储过曾的名字
        $fun_name1 = $this->makeModelFunName($fun_name, "add");//散列参数添加
        $fun_name2 = $this->makeModelFunName($fun_name, "add", true);//通过bean添加

        list($is_return_new_id,
            $i_param,
            $a_param_comment,
            $a_param_define,
            $a_param_use,
            $a_param_type,
            $a_param_key,
            $a_param_field) = $this->parseAdd_field($model,$fun);

        if($i_param==0){
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
        $ii = 0;
        foreach ($a_param_define as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        echo _tab(1) . "\n" . _tab(1) . ")\n";
        echo _tab(1) . "{\n";

        $s_qm = _db_question_marks($i_param);
        echo _tab(2) . "//question_marks = {$i_param} \n";
        echo _tab(2) . "\$i_new_id = 0;\n";
        if ($is_return_new_id) {
            echo _tab(2) . "\$sql = \"{CALL `{$proc_name}`({$s_qm}, @_new_id)}\";\n";
        }
        else{
            echo _tab(2) . "\$sql = \"{CALL `{$proc_name}`({$s_qm})}\";\n";
        }
        $this->_beforeQuery();
        echo _tab(4);
        echo implode(",\n" . _tab(4), $a_param_use);
        echo "\n";
        $this->_onQuery();
        echo _tab(4);
        echo implode(",\n" . _tab(4), $a_param_type);
        echo "\n";
        $this->_afterQuery();

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
        foreach ($a_param_field as $field) {
            echo _warp2join($ii) . _tab(5) . "\$v_{$lc_model_name}Bean->{$field->name}";
            $ii++;
        }
        echo  "\n";
        echo _tab(2) . ");\n";
        echo _tab(2) . "return \$i_new_id;\n";
        echo _tab(1) . "}";

        $this->_funFooter($model, $fun);
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



    function cUpdate(MyModel $model, MyFun $fun)
    {
        // TODO: Implement cUpdate() method.
    }

    function cDelete(MyModel $model, MyFun $fun)
    {
        // TODO: Implement cDelete() method.
    }

    function cFetch(MyModel $model, MyFun $fun)
    {
        // TODO: Implement cFetch() method.
    }

    function cList(MyModel $model, MyFun $fun)
    {
        // TODO: Implement cList() method.
    }

    function cCount(MyModel $model, MyFun $fun)
    {
        // TODO: Implement cCount() method.
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
            echo _tab(1) . "public \$mPlainRowMap = array(\n";
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
                    echo _tab(2) . "return mList;\n";
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
        echo "public class {$uc_model_name}ModelX extends {$uc_model_name}Model {\n";
        echo "}";

        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target2, $data);
    }

    function ccTmpl($model)
    {
        // TODO: Implement ccTmpl() method.
    }

    function ccWeb($model)
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

    /**
     * 创建模型层
     * @param MyModel $model
     * @return mixed
     */
    function ccDb($model)
    {
        // TODO: Implement ccDoc() method.
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


        $this->_makeheader();



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
}