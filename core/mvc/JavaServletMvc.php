<?php
if (!defined("MVC_ROOT")) {
    define('MVC_ROOT', realpath(dirname(__FILE__)));
}

include_once(MVC_ROOT . "/MvcBase.php");

/**
 * java servlet 模型
 */
class JavaServletMvc extends MvcBase
{


    /**
     * 创建模型
     * @param $model
     * @return mixed|void
     */
    function ccModel($model)
    {
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        SeasLog::info("创建JAVA数据模型--{$model_name}");
        $_target = $this->odir_models . DS . "{$uc_model_name}Model.java";
        ob_start();

        echo "package  {$this->final_package}.models;\n";
        echo "import {$this->final_package}.beans.{$uc_model_name}Bean;\n";
        echo "import {$this->final_package}.model.MvcBase;\n";


        echo "import org.slf4j.Logger;\n";
        echo "import org.slf4j.LoggerFactory;\n";

        echo "import java.io.InputStream;\n";
        echo "import java.io.ByteArrayInputStream;\n";

        echo "import java.sql.CallableStatement;\n";
        echo "import java.sql.Connection;\n";
        echo "import java.sql.ResultSet;\n";
        echo "import java.sql.Types;\n";
        echo "import java.sql.SQLException;\n";

        echo "import java.util.HashMap;\n";
        echo "import java.util.Map;\n";
        echo "import java.util.Vector;\n";


        _fun_comment("操作模型类--{$model->title}");
        echo "public class {$uc_model_name}Model extends ModelBase {\n";

        _fun_comment("私有日志类", 1);
        echo _tab(1) . "private  static Logger logger = LoggerFactory.getLogger({$uc_model_name}Model.class);\n\n";

        $a_all_fields = array();
        //转换用name作为主键
        foreach ($model->field_list as $field) {
            /* @var MyField $field */
            $key = $field->name;
            $a_all_fields[$key] = $field;
        }

        //没有字段不操作
        if (count($a_all_fields) > 0) {

            $model->field_list_kv = $a_all_fields;

            _fun_comment("基本数据字段映射,模型中的字段和数据库的字段的对英关系", 1);
            echo _tab(1) . "public Map<String, String> mPlainRowMap = new HashMap<String, String>() {{\n";
            foreach ($model->field_list as $field) {
                /* @var MyField $field */
                if ($field->type == Constant::DB_FIELD_TYPE_BLOB || $field->type == Constant::DB_FIELD_TYPE_LONGBLOB) {
                    continue;
                }
                $key = $field->name;
                echo _tab(2) . "put(\"{$key}\", \"{$key}\");//{$field->title}\n";
            }
            echo _tab(1) . "}};\n";

            _fun_comment("数据类型", 1);
            echo _tab(1) . "{$uc_model_name}Bean bean;\n";

            _fun_comment("获取bean", 1);
            echo _tab(1) . "public {$uc_model_name}Bean getBean() {\n";
            echo _tab(2) . "return bean;\n";
            echo _tab(1) . "}\n";

            _fun_comment("设置bean", 1);
            echo _tab(1) . "public void setBean({$uc_model_name}Bean bean0) {\n";
            echo _tab(2) . "this.bean = bean0;\n";
            echo _tab(1) . "}\n";

            foreach ($model->field_list as $field) {
                /* @var MyField $field */
                if ($field->input_hash != "") {
                    $key = $field->name;
                    $uc_key = ucfirst($key);
                    _fun_comment("基础字典-{$field->title}", 1);
                    echo _tab(1) . "public static HashMap<String,String> get{$uc_key}_KV(){\n";
                    echo _tab(2) . "HashMap<String,String> mList = new HashMap<String,String>();\n";
                    $a_hash = explode(";", $field->input_hash);
                    foreach ($a_hash as $s_kv) {
                        $a_kv = explode(",", $s_kv);
                        echo _tab(2) . "mList.put(\"{$a_kv[0]}\",\"{$a_kv[1]}\");\n";
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

        $_target2 = $this->odir_models . DS . "{$uc_model_name}ModelX.java";
        ob_start();

        echo "package  {$this->final_package}.models;\n";
        _fun_comment("自定义的操作模型类--{$model->title}");
        echo "public class {$uc_model_name}ModelX extends {$uc_model_name}Model {\n";
        echo "}";

        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target2, $data);
    }

    function cAdd(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $fun_name = $fun->name;
        $proc_name = $this->findProcName($model->table_name, $fun_name, "add");//存储过曾的名字
        $fun_name1 = $this->makeModelFunName($fun_name, "add");//散列参数添加
        $fun_name2 = $this->makeModelFunName($fun_name, "add", true);//通过bean添加

        list($is_return_new_id, $i_param, $a_param_comment, $a_param_define, $a_param_use, $a_param_key, $a_param_field) = $this->parseAdd_field($model, $fun);

        if ($i_param == 0) {
            //没有输入参数
            return;
        }

        if ($is_return_new_id) {
            $i_param++;
        }

        _fun_comment_header("插入数据vars-", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        foreach ($a_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        echo _tab(1) . " * @return int\n";
        _fun_comment_footer(1);


        echo _tab(1) . "public int {$fun_name1}(";
        $ii = 0;
        foreach ($a_param_define as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        echo _tab(1) . "\n" . _tab(1) . ")\n";
        echo _tab(1) . "{\n";

        $s_qm = _db_question_marks($i_param);
        echo _tab(2) . "//question_marks = {$i_param} \n";
        echo _tab(2) . "int iRet = 0;\n";
        $this->_dbQueryHeader();
        echo _tab(4) . "String sql = \"{CALL `{$proc_name}`({$s_qm})}\";\n";
        echo _tab(4) . "st = conn.prepareCall(sql);\n";

        $ii = 0;
        foreach ($a_param_use as $param) {
            $field = $a_param_field[$ii];
            echo $this->_procStatementParam($field->name, $field->type, $param, $ii, 4);
            $ii++;
        }
        if ($is_return_new_id) {
            echo "\n";
            echo _tab(4) . "st.registerOutParameter({$ii}, Types.INTEGER);\n";
            echo _tab(4) . "rs = st.executeQuery();\n";
            echo _tab(4) . "iRet = st.getInt({$ii});\n";
            echo _tab(4) . "logger.debug(\"call {$proc_name} -- \" + iRet);\n";
        } else {
            echo _tab(4) . "iRet = 1;\n";
        }
        $this->_dbQueryFooter();
        echo _tab(2) . "return iRet;\n";
        echo _tab(1) . "}";


        _fun_comment_header("插入数据--通过bean", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        echo _tab(1) . " * @param v_{$uc_model_name}Bean\n";
        echo _tab(1) . " * @return int\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public int {$fun_name2}({$uc_model_name}Bean v_{$uc_model_name}Bean) \n";
        echo _tab(1) . "{\n";
        echo _tab(2) . "int iRet = {$fun_name1}(";
        $ii = 0;
        foreach ($a_param_field as $field) {
            echo _warp2join($ii) . _tab(5) . "v_{$uc_model_name}Bean->{$field->name}";
            $ii++;
        }
        echo _tab(2) . ");\n";
        echo _tab(2) . "return iRet;\n";
        echo _tab(1) . "}";

        $this->_funFooter($model, $fun);

    }

    function _dbQueryHeader($op_type = "write")
    {
        $db_type = "Mysql";//默认
        $db_conf = $this->curr_app->getCurrDb();
        switch ($db_conf->driver) {
            case Constant::DB_MYSQL_56:
            case Constant::DB_MYSQL_57:
            case Constant::DB_MYSQL_80:
                $db_type = "Mysql";
                break;
            default:
        }

        if ($op_type == "read") {
            echo _tab(2) . "InputStream is=null;\n";
            echo _tab(2) . "byte[] buf = null;\n";
        }

        echo _tab(2) . "Connection conn = null;\n";
        echo _tab(2) . "CallableStatement st = null;\n";
        echo _tab(2) . "ResultSet rs = null;\n";
        echo _tab(2) . "try {\n";
        echo _tab(3) . "conn = Db{$db_type}.getConnection();\n";
        //TODO 这里需要确认数据池的链接
        echo _tab(3) . "if (!conn.isClosed()) {\n";
    }

    /**
     * 根据字段名来判断数据库字段名
     * @param $field_key
     * @param $field_type
     * @param $pv_use 用的字段
     * @param $ii
     * @param $tab_idx
     * @return void
     */
    function _procStatementParam($field_key, $field_type, $pv_use, $ii, $tab_idx)
    {

        switch ($field_type) {
            case Constant::DB_FIELD_TYPE_BOOL :
                echo _tab($tab_idx) . "st.setInt({$ii}, {$pv_use}?1:0); \n";
                break;
            //整型
            case Constant::DB_FIELD_TYPE_INT:
                echo _tab($tab_idx) . "st.setInt({$ii}, {$pv_use}); \n";
                break;
            case Constant::DB_FIELD_TYPE_LONGINT:
                echo _tab($tab_idx) . "st.setLong({$ii}, {$pv_use}); \n";
                break;
            case Constant::DB_FIELD_TYPE_BLOB :
            case Constant::DB_FIELD_TYPE_LONGBLOB :
                echo _tab($tab_idx) . "if({$pv_use} == null) {\n";
                echo _tab(1 + $tab_idx) . "{$pv_use} = new byte[0];\n";
                echo _tab($tab_idx) . "}\n";
                echo _tab($tab_idx) . "ByteArrayInputStream _bis_{$field_key} = new ByteArrayInputStream({{$pv_use}}); \n";
                echo _tab($tab_idx) . "st.setBinaryStream({$ii}, _bis_{$field_key}, _bis_{$field_key}.available());\n";
                break;

            //字符
            default:
                echo _tab($tab_idx) . "st.setString({$ii},{$pv_use}); \n";
                break;
        }
    }

    function _dbQueryFooter($op_type = "write")
    {

        $db_type = "Mysql";//默认
        $db_conf = $this->curr_app->getCurrDb();
        switch ($db_conf->driver) {
            case Constant::DB_MYSQL_56:
            case Constant::DB_MYSQL_57:
            case Constant::DB_MYSQL_80:
                $db_type = "Mysql";
                break;
            default:
        }
        echo _tab(3) . "}\n";
        echo _tab(2) . "} catch (SQLException e0) {\n";
        echo _tab(3) . "logger.error(\"SQLException-e0\",e0);\n";
        echo _tab(2) . "} catch (ClassNotFoundException e1) {\n";
        echo _tab(3) . "logger.error(\"ClassNotFoundException\",e1);\n";
        echo _tab(2) . "} catch (Exception e2) {\n";
        echo _tab(3) . "logger.error(\"FinalException\",e2);\n";
        echo _tab(2) . "} finally {\n";
        echo _tab(3) . "Db{$db_type}.release(conn, st, rs);\n";
        if ($op_type == "read") {

            echo _tab(3) . "try {\n";
            echo _tab(4) . "if(null != is){\n";
            echo _tab(5) . "is.close();\n";
            echo _tab(4) . "}\n";
            echo _tab(3) . "} catch (Exception e3) {\n";
            echo _tab(4) . "e3.printStackTrace();\n";
            echo _tab(3) . "}\n";

            echo _tab(3) . "is=null;\n";
            echo _tab(3) . "buf = null;\n";
        }
        echo _tab(2) . "}\n";
    }

    /**
     *  需要删除的UI
     * @param MyModel $model
     * @param MyFun $fun
     * @return mixed|void
     */
    function cDelete(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $fun_name = $fun->name;
        $proc_name = $this->findProcName($model->table_name, $fun_name, "delete");//存储过曾的名字
        $fun_name1 = $this->makeModelFunName($fun_name, "delete");//散列参数添加


        $i_param = 0;
        list($a_param_comment, $a_param_define, $a_param_use, $a_param_field) = $this->_procWhereCond($model, $fun);
        $i_param = count($a_param_comment);

        _fun_comment_header("删除数据vars", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        foreach ($a_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        echo _tab(1) . " * @return int\n";
        _fun_comment_footer(1);

        echo _tab(1) . "public int {$fun_name1}(";
        $ii = 0;
        foreach ($a_param_define as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        echo _tab(1) . "\n" . _tab(1) . ")\n";
        echo _tab(1) . "{\n";
        $s_qm = _db_question_marks($i_param + 1);
        echo _tab(2) . "//question_marks = {$i_param} + 1 \n";
        echo _tab(2) . "int iRet = 0;\n";
        $this->_dbQueryHeader();
        echo _tab(4) . "String sql = \"{CALL `{$proc_name}`({$s_qm})}\";\n";
        echo _tab(4) . "st = conn.prepareCall(sql);\n";

        $ii = 0;
        foreach ($a_param_use as $param) {
            $o_field = $a_param_field[$ii];
            echo $this->_procStatementParam($o_field->name, $o_field->type, $param, $ii, 4);
            $ii++;
        }
        echo "\n";
        echo _tab(4) . "st.registerOutParameter({$ii}, Types.INTEGER);\n";
        echo _tab(4) . "rs = st.executeQuery();\n";
        echo _tab(4) . "iRet = st.getInt({$ii});\n";
        echo _tab(4) . "logger.debug(\"call {$proc_name} -- \" + iRet);\n";
        $this->_dbQueryFooter();
        echo _tab(2) . "return iRet;\n";
        echo _tab(1) . "}";

        $this->_funFooter($model, $fun);
    }

    /**
     * 条件的输入参数
     *
     * 返回值说民
     * $a_param_comment = array();//用于注释
     * $a_param_define = array();//用于定义
     * $a_param_use = array();//用于使用
     * $a_param_field = array();//用于定位原来的field的值
     *
     * @param MyModel $model
     * @param MyFun $o_fun
     * @return array[]
     */
    function _procWhereCond(MyModel $model, MyFun $o_fun)
    {

        $a_param_comment = array();//用于注释
        $a_param_define = array();//用于定义
        $a_param_use = array();//用于使用
        $a_param_field = array();//用于使用

        //var_dump($o_fun->where);
        $jj = 0;
        if ($o_fun->where != null) {

            $cond_list = $o_fun->where->cond_list;
            $where_list = $o_fun->where->where_list;

            //var_dump($cond_list);
            foreach ($cond_list as $cond) {
                /* @var MyCond $cond */
                switch ($cond->type) {
                    case Constant::COND_TYPE_DATE:    // = "DATE";//关键字模糊匹配
                    case Constant::COND_TYPE_TIME:    // = "TIME";//日期范围内
                    case Constant::COND_TYPE_DATETIME:    // = "TIME";//日期范围内
                    case Constant::COND_TYPE_BETWEEN: // = "BETWEEN";//标量范围内
                    case Constant::COND_TYPE_NOTBETWEEN: // = "NOTBETWEEN";//标量范围外
                        if ($cond->v1_type == Constant::COND_VAl_TYPE_INPUT) {
                            $o_field = $model->field_list[$cond->field];
                            list($s_param1, $s_param2, $s_param3) = $this->_procParam($o_field, $jj, "w");
                            $a_param_comment[] = $s_param1;
                            $a_param_define[] = $s_param2;
                            $a_param_use[] = $s_param3;
                            $a_param_field[] = $o_field;
                            $jj++;
                        }
                        if ($cond->v2_type == Constant::COND_VAl_TYPE_INPUT) {
                            $o_field = $model->field_list[$cond->field];
                            list($s_param1, $s_param2, $s_param3) = $this->_procParam($o_field, $jj, "w");
                            $a_param_comment[] = $s_param1;
                            $a_param_define[] = $s_param2;
                            $a_param_use[] = $s_param3;
                            $a_param_field[] = $o_field;
                            $jj++;
                        }
                        break;
                    default:
                        if ($cond->v1_type == Constant::COND_VAl_TYPE_INPUT) {
                            $o_field = $model->field_list[$cond->field];
                            list($s_param1, $s_param2, $s_param3) = $this->_procParam($o_field, $jj, "w");
                            $a_param_comment[] = $s_param1;
                            $a_param_define[] = $s_param2;
                            $a_param_use[] = $s_param3;
                            $a_param_field[] = $o_field;
                            $jj++;
                        }
                }
            }
            foreach ($where_list as $where2) {
                //子查询部分
                if ($where2 != null) {
                    $cond_list2 = $where2->cond_list;
                    if (count($cond_list2) == 0) {
                        continue;
                    }
                    foreach ($cond_list2 as $cond) {
                        /* @var MyCond $cond */
                        switch ($cond->type) {
                            case Constant::COND_TYPE_DATE:    // = "DATE";//关键字模糊匹配
                            case Constant::COND_TYPE_TIME:    // = "TIME";//日期范围内
                            case Constant::COND_TYPE_DATETIME:    // = "TIME";//日期范围内
                            case Constant::COND_TYPE_BETWEEN: // = "BETWEEN";//标量范围内
                            case Constant::COND_TYPE_NOTBETWEEN: // = "NOTBETWEEN";//标量范围外
                                if ($cond->v1_type == Constant::COND_VAl_TYPE_INPUT) {
                                    $o_field = $model->field_list[$cond->field];
                                    list($s_param1, $s_param2, $s_param3) = $this->_procParam($o_field, $jj, "w");
                                    $a_param_comment[] = $s_param1;
                                    $a_param_define[] = $s_param2;
                                    $a_param_use[] = $s_param3;
                                    $a_param_field[] = $o_field;
                                    $jj++;
                                }
                                if ($cond->v2_type == Constant::COND_VAl_TYPE_INPUT) {
                                    $o_field = $model->field_list[$cond->field];
                                    list($s_param1, $s_param2, $s_param3) = $this->_procParam($o_field, $jj, "w");
                                    $a_param_comment[] = $s_param1;
                                    $a_param_define[] = $s_param2;
                                    $a_param_use[] = $s_param3;
                                    $a_param_field[] = $o_field;
                                    $jj++;
                                }
                                break;
                            default:
                                if ($cond->v1_type == Constant::COND_VAl_TYPE_INPUT) {
                                    $o_field = $model->field_list[$cond->field];
                                    list($s_param1, $s_param2, $s_param3) = $this->_procParam($o_field, $jj, "w");
                                    $a_param_comment[] = $s_param1;
                                    $a_param_define[] = $s_param2;
                                    $a_param_use[] = $s_param3;
                                    $a_param_field[] = $o_field;
                                    $jj++;
                                }
                        }
                    }
                }
            }
        }
        //用于注释
        //用于定义
        //用于使用
        return array($a_param_comment, $a_param_define, $a_param_use, $a_param_field);
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
        $param_key = "v_{$idx_append}_{$prefix}_{$key}";
        $param_type = "String";
        switch ($type) {
            case Constant::DB_FIELD_TYPE_BOOL :
                $param_type = "int";//tinyint
                break;
            //整型
            case Constant::DB_FIELD_TYPE_INT:
                $param_type = "int";
                break;
            case Constant::DB_FIELD_TYPE_LONGINT:
                $param_type = "long";
                break;

            case Constant::DB_FIELD_TYPE_BLOB :
            case Constant::DB_FIELD_TYPE_LONGBLOB :
                $param_type = "byte[]";
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
                break;
        }
        $ret1 = " * @param {$param_key} [{$param_type}] {$desc}";
        $ret2 = "{$param_type} {$param_key}";
        $ret3 = "{$param_key}";

        return array($ret1, $ret2, $ret3, $param_type);
    }

    function cUpdate(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);

        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $fun_name = $fun->name;
        $proc_name = $this->findProcName($model->table_name, $fun_name, "update");//存储过曾的名字
        $fun_name1 = $this->makeModelFunName($fun_name, "update");//散列参数添加
        $fun_name2 = $this->makeModelFunName($fun_name, "update", true);//散列参数添加

        $a_all_fields = $model->field_list_kv;
        //需要更新的字段
        $i_u_param = 0;
        $a_u_param_comment = array();//用于注释
        $a_u_param_define = array();//用于定义
        $a_u_param_use = array();//用于使用
        $a_u_param_field = array();//用于定位原来的field的值
        $a_field_update = $fun->field_list;
        if ($fun->all_field == 1) {
            $a_field_update = $model->field_list;
        }
        foreach ($a_field_update as $field) {
            /* @var MyField $field */
            $field_name = $field->name;
            //存在于fun, 但不存在于model的字段，不处理
            if (!isset($a_all_fields[$field_name])) {
                continue;
            }
            $i_u_param++;
            list($param1, $param2, $param3) = $this->_procParam($field, $i_u_param, "u");
            $a_u_param_comment[] = $param1;
            $a_u_param_define[] = $param2;
            $a_u_param_use[] = $param3;
            $a_u_param_field[] = $field;
        }

        //更新条件
        list($a_w_param_comment, $a_w_param_define, $a_w_param_use, $a_w_param_field) = $this->_procWhereCond($model, $fun);
        $i_w_param = count($a_w_param_comment);

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

        echo _tab(1) . "public int {$fun_name1}(";
        $ii = 0;
        foreach ($a_u_param_define as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        foreach ($a_w_param_define as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        echo _tab(1) . "\n" . _tab(1) . ")\n";
        echo _tab(1) . "{\n";
        $s_qm = _db_question_marks($i_u_param + $i_w_param + 1);
        echo _tab(2) . "//question_marks = u {$i_u_param} + w {$i_w_param} + r 1 \n";
        echo _tab(2) . "int iRet = 0;\n";
        $this->_dbQueryHeader();
        echo _tab(4) . "String sql = \"{CALL `{$proc_name}`({$s_qm})}\";\n";
        echo _tab(4) . "st = conn.prepareCall(sql);\n";

        $ii = 0;
        foreach ($a_u_param_use as $param) {
            $o_field = $a_u_param_field[$ii];
            echo $this->_procStatementParam($o_field->name, $o_field->type, $param, $ii, 4);
            $ii++;
        }
        foreach ($a_w_param_use as $param) {
            $o_field = $a_w_param_field[$ii];
            echo $this->_procStatementParam($o_field->name, $o_field->type, $param, $ii, 4);
            $ii++;
        }
        echo "\n";
        echo _tab(4) . "st.registerOutParameter({$ii}, Types.INTEGER);\n";
        echo _tab(4) . "rs = st.executeQuery();\n";
        echo _tab(4) . "iRet = st.getInt({$ii});\n";
        echo _tab(4) . "logger.debug(\"call {$proc_name} -- \" + iRet);\n";
        $this->_dbQueryFooter();
        echo _tab(2) . "return iRet;\n";
        echo _tab(1) . "}";

        _fun_comment_header("更新数据--通过bean", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        echo _tab(1) . " * @param v_{$uc_model_name}Bean\n";
        foreach ($a_w_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        echo _tab(1) . " * @return int\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public int {$fun_name2}({$uc_model_name}Bean v_{$uc_model_name}Bean";
        $ii = 1;
        foreach ($a_w_param_define as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        echo _tab(1) . "\n" . _tab(1) . ")\n";
        echo _tab(1) . "{\n";
        echo _tab(2) . "int iRet = {$fun_name1}(";
        $ii = 0;
        foreach ($a_u_param_field as $field) {
            echo _warp2join($ii) . _tab(5) . "v_{$uc_model_name}Bean->{$field->name}";
            $ii++;
        }
        foreach ($a_w_param_use as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        echo _tab(2) . ");\n";
        echo _tab(2) . "return iRet;\n";
        echo _tab(1) . "}";

        $this->_funFooter($model, $fun);
    }

    function cFetch(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $fun_name = $fun->name;
        $proc_name = $this->findProcName($model->table_name, $fun_name, "fetch");//存储过曾的名字
        $fun_name1 = $this->makeModelFunName($fun_name, "fetch");//散列参数添加
        $fun_name2 = $this->makeModelFunName($fun_name, "fetch", true);//散列参数添加返回bean

        $a_all_fields = $model->field_list_kv;

        $i_param = 0;
        $a_param_comment = array();//用于注释
        $a_param_define = array();//用于定义
        $a_param_use = array();//用于使用
        $a_param_field = array();//用于定位原来的field的值
        list($a_param_comment, $a_param_define, $a_param_use, $a_param_field) = $this->_procWhereCond($model, $fun);
        $i_param = count($a_param_comment);
        //var_dump($fun->field_list);

        _fun_comment_header("通过条件获取一个数据，返回值是hash", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        foreach ($a_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        echo _tab(1) . " * @return HashMap\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public HashMap {$fun_name1}(";
        $ii = 0;
        foreach ($a_param_define as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        echo _tab(1) . "\n" . _tab(1) . ")\n";
        echo _tab(1) . "{\n";
        $s_qm = _db_question_marks($i_param);
        echo _tab(2) . "//question_marks = {$i_param}\n";
        echo _tab(2) . "HashMap<String, String> mRet = new HashMap<>();\n";
        $this->_dbQueryHeader("read");
        echo _tab(4) . "String sql = \"{CALL `{$proc_name}`({$s_qm})}\";\n";
        echo _tab(4) . "st = conn.prepareCall(sql);\n";

        $ii = 0;
        foreach ($a_param_use as $param) {
            $o_field = $a_param_field[$ii];
            echo $this->_procStatementParam($o_field->name, $o_field->type, $param, $ii, 4);
            $ii++;
        }
        echo "\n";
        echo _tab(4) . "rs = st.executeQuery();\n";
        echo _tab(4) . "while (rs.next()) {\n";
        echo _tab(5) . "for (Map.Entry<String, String> entry : mPlainRowMap.entrySet()) {\n";
        echo _tab(6) . "mRet.put(entry.getKey(), rs.getString(entry.getValue())); \n";
        echo _tab(5) . "}\n";
        echo _tab(5) . "break;\n";
        echo _tab(4) . "}\n";

        echo _tab(4) . "logger.debug(\"call {$proc_name} done\");\n";
        $this->_dbQueryFooter();
        echo _tab(2) . "return mRet;\n";
        echo _tab(1) . "}";

        _fun_comment_header("通过条件获取一个数据，返回值是bean", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        foreach ($a_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        echo _tab(1) . " * @return {$uc_model_name}Bean\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public {$uc_model_name}Bean {$fun_name2}(";
        $ii = 0;
        foreach ($a_param_define as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        echo _tab(1) . "\n" . _tab(1) . ")\n";
        echo _tab(1) . "{\n";
        $s_qm = _db_question_marks($i_param + 1);
        echo _tab(2) . "//question_marks = {$i_param} + 1 \n";
        echo _tab(2) . "{$uc_model_name}Bean mBean = new {$uc_model_name}Bean();\n";
        $this->_dbQueryHeader("read");
        echo _tab(4) . "String sql = \"{CALL `{$proc_name}`({$s_qm})}\";\n";
        echo _tab(4) . "st = conn.prepareCall(sql);\n";
        $ii = 0;
        foreach ($a_param_use as $param) {
            $o_field = $a_param_field[$ii];
            echo $this->_procStatementParam($o_field->name, $o_field->type, $param, $ii, 4);
            $ii++;
        }
        echo "\n";
        echo _tab(4) . "rs = st.executeQuery();\n";
        echo _tab(4) . "while (rs.next()) {\n";
        foreach ($model->field_list as $key => $o_field) {
            echo $this->_procResultBean($o_field->name, $o_field->type, 5);
        }
        echo _tab(5) . "break;\n";
        echo _tab(4) . "}\n";

        echo _tab(4) . "logger.debug(\"call {$proc_name} done\");\n";
        $this->_dbQueryFooter();
        echo _tab(2) . "return mBean;\n";
        echo _tab(1) . "}";
        $this->_funFooter($model, $fun);
    }

    function _procResultBean($key, $field_type, $tab_idx)
    {

        switch ($field_type) {
            case Constant::DB_FIELD_TYPE_BOOL :
                echo _tab($tab_idx) . "mBean.{$key} = rs.getInt(\"{$key}\");\n";
                break;
            //整型
            case Constant::DB_FIELD_TYPE_INT:
                echo _tab($tab_idx) . "mBean.{$key} = rs.getInt(\"{$key}\");\n";
                break;//

            case Constant::DB_FIELD_TYPE_LONGINT:
                echo _tab($tab_idx) . "mBean.{$key} = rs.getLong(\"{$key}\");\n";
                break;
            //
            case Constant::DB_FIELD_TYPE_BLOB :
            case Constant::DB_FIELD_TYPE_LONGBLOB :
                echo _tab($tab_idx) . "is = rs.getBinaryStream(\"{$key}\");\n";
                echo _tab($tab_idx) . "if (is != null) {\n";
                echo _tab(1 + $tab_idx) . "buf = new byte[is.available()];\n";
                echo _tab(1 + $tab_idx) . "is.read(buf);\n";
                echo _tab(1 + $tab_idx) . "mBean.{$key} = buf ;\n";
                echo _tab($tab_idx) . "}\n";
                break;

            //字符
            default:
                echo _tab($tab_idx) . "mBean.{$key} = rs.getString(\"{$key}\");\n";
                break;
        }

    }

    /**
     * 单纯的统计，
     * @param MyModel $model
     * @param MyFun $fun
     * @return mixed|void
     */
    function cCount(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $fun_name = $fun->name;
        $proc_name = $this->findProcName($model->table_name, $fun_name, "count");//存储过曾的名字
        $fun_name1 = $this->makeModelFunName($fun_name, "count");//散列参数添加

        $a_all_fields = $model->field_list_kv;
        $i_param = 0;
        $a_param_comment = array();//用于注释
        $a_param_define = array();//用于定义
        $a_param_use = array();//用于使用
        $a_param_field = array();//用于定位原来的field的值
        list($a_param_comment, $a_param_define, $a_param_use, $a_param_field) = $this->_procWhereCond($model, $fun);
        $i_param = count($a_param_comment);
        //var_dump($fun->field_list);

        _fun_comment_header("普通统计数据", 1);
        echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
        echo _tab(1) . " *\n";
        foreach ($a_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        echo _tab(1) . " * @return int\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public int {$fun_name1}(";
        $ii = 0;
        foreach ($a_param_define as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        echo _tab(1) . "\n" . _tab(1) . ")\n";
        echo _tab(1) . "{\n";
        $s_qm = _db_question_marks($i_param);
        echo _tab(2) . "//question_marks = {$i_param}\n";
        echo _tab(2) . "int iRet = 0;\n";
        $this->_dbQueryHeader("read");
        echo _tab(4) . "String sql = \"{CALL `{$proc_name}`({$s_qm})}\";\n";
        echo _tab(4) . "st = conn.prepareCall(sql);\n";

        $ii = 0;
        foreach ($a_param_use as $param) {
            $o_field = $a_param_field[$ii];
            echo $this->_procStatementParam($o_field->name, $o_field->type, $param, $ii, 4);
            $ii++;
        }
        echo "\n";
        echo _tab(4) . "rs = st.executeQuery();\n";
        echo _tab(4) . "while (rs.next()) {\n";
        echo _tab(5) . "iRet = rs.getInt(\"i_count\");\n";
        echo _tab(5) . "break;\n";
        echo _tab(4) . "}\n";

        echo _tab(4) . "logger.debug(\"call {$proc_name} done --\" + iRet);\n";
        $this->_dbQueryFooter();
        echo _tab(2) . "return iRet;\n";
        echo _tab(1) . "}";

        $this->_funFooter($model, $fun);
    }

    function cList(MyModel $model, MyFun $fun)
    {
        $this->_funHeader($model, $fun);
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        $fun_name = $fun->name;
        $proc_name = $this->findProcName($model->table_name, $fun_name, "list");//存储过曾的名字
        $fun_name1 = $this->makeModelFunName($fun_name, "list");//散列参数添加
        $fun_name2 = $this->makeModelFunName($fun_name, "list", true);//散列参数添加返回bean

        $a_all_fields = $model->field_list_kv;//通过主键访问的字段

        $fun_type = $fun->type;
        $has_return_bean = false;
        if ($fun_type == Constant::FUN_TYPE_LIST) {
            $has_return_bean = true;
        }
        //1111基本条件
        $i_param = 0;//基本参数格式
        $a_param_comment = array();//用于注释
        $a_param_define = array();//用于定义
        $a_param_use = array();//用于使用
        $a_param_field = array();//用于定位原来的field的值

        list($a_param_comment, $a_param_define, $a_param_use, $a_param_field) = $this->_procWhereCond($model, $fun);
        $i_param = count($a_param_comment);
        $i_param_list = $i_param;

        //22222被聚合键 TODO 放到父级函数里处理
        $fun_type = $fun->type;
        list($has_group_field, $group_field, $o_group_field, $group_field_final) = $this->parseGroup_field($model, $fun);
        //3333分组键
        list($has_group_by, $group_by) = $this->parseGroup_by($model, $fun);

        //4444先处理having
        //预先处理hading的条件
        $o_having = $fun->group_having;
        $has_having = $this->parseGroup_field($model, $fun, $has_group_field, $has_group_by);

        //5555排序键
        list($has_order, $is_order_by_input, $s_order_by, $is_order_dir_input, $s_order_dir) = $this->parseOrder_by($model, $fun);

        //6666 分页
        list($has_pager, $is_pager_size_input, $pager_size) = $this->parsePager($model, $fun);
        //var_dump($fun->field_list);
        _fun_comment_header("通过条件列表，返回值是Vector", 1);
        echo _tab(1) . " * {$fun->type}--{$fun->name}--{$fun->title}\n";
        echo _tab(1) . " *\n";
        //11111
        foreach ($a_param_comment as $param) {
            echo _tab(1) . "{$param}\n";
        }
        //2222
        //3333
        //4444
        //5555
        if ($has_order) {
            if ($is_order_by_input) {
                echo _tab(1) . " * @param v_order_by 排序字段\n";
                $i_param_list++;
            }
            if ($is_order_by_input) {
                echo _tab(1) . " * @param v_order_dir 排序方式\n";
                $i_param_list++;
            }
        }
        //6666
        if ($has_pager) {
            echo _tab(1) . " * @param v_page 页码\n";
            $i_param_list++;
            if ($is_pager_size_input) {
                echo _tab(1) . " * @param v_page_size 分页大小\n";
                $i_param_list++;
            }
        }
        echo _tab(1) . " *\n";
        echo _tab(1) . " * @return Vector<HashMap>\n";
        _fun_comment_footer(1);
        echo _tab(1) . "public Vector<HashMap> {$fun_name1}(";
        $ii = 0;
        foreach ($a_param_define as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        //5555
        if ($has_order) {
            if ($is_order_by_input) {
                echo _warp2join($ii) . _tab(5) . "String v_order_by";
                $ii++;
            }
            if ($is_order_by_input) {
                echo _warp2join($ii) . _tab(5) . "String v_order_dir";
                $ii++;
            }
        }
        //6666
        if ($has_pager) {
            echo _warp2join($ii) . _tab(5) . "int v_page";
            $ii++;
            if ($is_pager_size_input) {
                echo _warp2join($ii) . _tab(5) . "int v_page_size";
                $ii++;
            }
        }

        echo _tab(1) . "\n" . _tab(1) . ")\n";
        echo _tab(1) . "{\n";
        $s_qm = _db_question_marks($i_param_list);
        echo _tab(2) . "//question_marks = {$i_param_list}\n";
        echo _tab(2) . "Vector<HashMap> vList = new Vector<>();\n";
        $this->_dbQueryHeader("read");
        echo _tab(4) . "String sql = \"{CALL `{$proc_name}`({$s_qm})}\";\n";
        echo _tab(4) . "st = conn.prepareCall(sql);\n";

        $ii = 0;
        foreach ($a_param_use as $param) {
            $o_field = $a_param_field[$ii];
            echo $this->_procStatementParam($o_field->name, $o_field->type, $param, $ii, 4);
            $ii++;
        }
        //5555
        if ($has_order) {
            if ($is_order_by_input) {
                echo _tab(4) . "st.setString({$ii}, {v_order_by}); \n";
                $ii++;
            }
            if ($is_order_by_input) {
                echo _tab(4) . "st.setString({$ii}, {v_order_dir}); \n";
                $ii++;
            }
        }
        //6666
        if ($has_pager) {
            echo _tab(4) . "st.setInt({$ii}, {v_page}); \n";
            $ii++;
            if ($is_pager_size_input) {

                echo _tab(4) . "st.setInt({$ii}, {v_page_size}); \n";
                $ii++;
            }
        }

        echo "\n";
        echo _tab(4) . "rs = st.executeQuery();\n";
        echo _tab(4) . "while (rs.next()) {\n";
        echo _tab(2) . "HashMap<String, String> mRet = new HashMap<>();\n";
        echo _tab(5) . "for (Map.Entry<String, String> entry : mPlainRowMap.entrySet()) {\n";
        echo _tab(6) . "mRet.put(entry.getKey(), rs.getString(entry.getValue())); \n";
        echo _tab(5) . "}\n";
        //TODO 去掉不需要的
        if (!$has_return_bean) {
            //聚合的结果
            echo _tab(5) . "mRet.put({$group_field_final}, rs.getInt({$group_field_final})); \n";
        }
        echo _tab(5) . "vList.add(mRet);\n";
        echo _tab(4) . "}\n";

        echo _tab(4) . "logger.debug(\"call {$proc_name} done\");\n";
        $this->_dbQueryFooter();
        echo _tab(2) . "return vList;\n";
        echo _tab(1) . "}";

        //选中分页的才分页/////////////////////////////////////////////////////////////////////////////////
        if ($has_return_bean) {
            //非聚合的才有bean
            _fun_comment_header("通过条件获取列表，返回值是bean", 1);
            echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
            echo _tab(1) . " *\n";
            foreach ($a_param_comment as $param) {
                echo _tab(1) . "{$param}\n";
            }
            //5555
            if ($has_order) {
                if ($is_order_by_input) {
                    echo _tab(1) . " * @param v_order_by 排序字段\n";
                }
                if ($is_order_by_input) {
                    echo _tab(1) . " * @param v_order_dir 排序方式\n";
                }
            }
            //6666
            if ($has_pager) {
                echo _tab(1) . " * @param v_page 页码\n";
                if ($is_pager_size_input) {
                    echo _tab(1) . " * @param v_page_size 分页大小\n";
                }
            }

            echo _tab(1) . " * @return  Vector<{$uc_model_name}Bean\n";
            _fun_comment_footer(1);
            echo _tab(1) . "public Vector<{$uc_model_name}Bean> {$fun_name2}(";
            $ii = 0;
            foreach ($a_param_define as $param) {
                echo _warp2join($ii) . _tab(5) . "{$param}";
                $ii++;
            }
            //5555
            if ($has_order) {
                if ($is_order_by_input) {
                    echo _warp2join($ii) . _tab(5) . "String v_order_by";
                    $ii++;
                }
                if ($is_order_by_input) {
                    echo _warp2join($ii) . _tab(5) . "String v_order_dir";
                    $ii++;
                }
            }
            //6666
            if ($has_pager) {
                echo _warp2join($ii) . _tab(5) . "int v_page";
                $ii++;
                if ($is_pager_size_input) {
                    echo _warp2join($ii) . _tab(5) . "int v_page_size";
                    $ii++;
                }
            }
            echo _tab(1) . "\n" . _tab(1) . ")\n";
            echo _tab(1) . "{\n";
            $s_qm = _db_question_marks($i_param_list);
            echo _tab(2) . "//question_marks = {$i_param_list} \n";
            echo _tab(2) . "Vector<{$uc_model_name}Bean> vList = new Vector<>();\n";
            $this->_dbQueryHeader("read");
            echo _tab(4) . "String sql = \"{CALL `{$proc_name}`({$s_qm})}\";\n";
            echo _tab(4) . "st = conn.prepareCall(sql);\n";

            $ii = 0;
            foreach ($a_param_use as $param) {
                $o_field = $a_param_field[$ii];
                echo $this->_procStatementParam($o_field->name, $o_field->type, $param, $ii, 4);
                $ii++;
            }

            //5555
            if ($has_order) {
                if ($is_order_by_input) {
                    echo _tab(4) . "st.setString({$ii}, {v_order_by}); \n";
                    $ii++;
                }
                if ($is_order_by_input) {
                    echo _tab(4) . "st.setString({$ii}, {v_order_dir}); \n";
                    $ii++;
                }
            }
            //6666
            if ($has_pager) {
                echo _tab(4) . "st.setInt({$ii}, {v_page}); \n";
                $ii++;
                if ($is_pager_size_input) {

                    echo _tab(4) . "st.setInt({$ii}, {v_page_size}); \n";
                    $ii++;
                }
            }

            echo "\n";
            echo _tab(4) . "rs = st.executeQuery();\n";
            echo _tab(4) . "while (rs.next()) {\n";
            echo _tab(2) . "{$uc_model_name}Bean mBean = new {$uc_model_name}Bean();\n";
            foreach ($model->field_list as $key => $o_field) {
                echo $this->_procResultBean($o_field->name, $o_field->type, 5);
            }
            echo _tab(5) . "vList.add({$uc_model_name}Bean);\n";
            echo _tab(4) . "}\n";

            echo _tab(4) . "logger.debug(\"call {$proc_name} done\");\n";
            $this->_dbQueryFooter();
            echo _tab(2) . "return vList;\n";
            echo _tab(1) . "}";
        }
        //分页时包含对应的计数/////////////////////////////////////////////////////////////////////////////////
        if ($has_pager) {
            _fun_comment_header("获取分页对应的记录总数", 1);
            echo _tab(1) . " * {$fun->type}-{$fun->title}\n";
            echo _tab(1) . " *\n";
            foreach ($a_param_comment as $param) {
                echo _tab(1) . "{$param}\n";
            }
            echo _tab(1) . " * @return  int\n";
            _fun_comment_footer(1);
            echo _tab(1) . "public int {$fun_name1}_Count(";
            $ii = 0;
            foreach ($a_param_define as $param) {
                echo _warp2join($ii) . _tab(5) . "{$param}";
                $ii++;
            }
            echo _tab(1) . "\n" . _tab(1) . ")\n";
            echo _tab(1) . "{\n";
            $s_qm = _db_question_marks($i_param);
            echo _tab(2) . "//question_marks = {$i_param} \n";
            echo _tab(2) . "int iRet = 0;\n";
            $this->_dbQueryHeader("read");
            echo _tab(4) . "String sql = \"{CALL `{$proc_name}_c`({$s_qm})}\";\n";
            echo _tab(4) . "st = conn.prepareCall(sql);\n";
            $ii = 0;
            foreach ($a_param_use as $param) {
                $o_field = $a_param_field[$ii];
                echo $this->_procStatementParam($o_field->name, $o_field->type, $param, $ii, 4);
                $ii++;
            }
            echo "\n";
            echo _tab(4) . "rs = st.executeQuery();\n";
            echo _tab(4) . "while (rs.next()) {\n";
            echo _tab(5) . "iRet = rs.getInt(\"i_count\");\n";
            echo _tab(5) . "break;\n";
            echo _tab(4) . "}\n";

            echo _tab(4) . "logger.debug(\"call {$proc_name}_c done\");\n";
            $this->_dbQueryFooter();
            echo _tab(2) . "return iRet;\n";
            echo _tab(1) . "}";

        }


        $this->_funFooter($model, $fun);
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
     * 创建数据结构
     * @param MyModel $model
     * @return mixed
     */
    function ccBean(MyModel $model)
    {
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        SeasLog::info("创建JAVA数据结构--{$model_name}");
        $_target = $this->odir_beans . DS . "{$uc_model_name}Bean.java";
        ob_start();


        echo "package  {$this->final_package}.beans;\n\n";
        echo "import java.util.HashMap;\n";
        echo "import java.util.Map;\n";
        echo "import java.util.Vector;\n";
        echo "import java.io.Serializable;\n";

        _fun_comment("数据bean-{$model->title}-{$model->name}");
        echo "public class {$uc_model_name}Bean implements Serializable {\n";

        foreach ($model->field_list as $field) {
            /* @var MyField $field */
            _fun_comment("{$field->title}", 1);
            $key = $field->name;
            switch ($field->type) {
                //bool
                case Constant::DB_FIELD_TYPE_BOOL :
                    //echo _tab(1) . "public boolean {$key} = false;\n";
                    echo _tab(1) . "public int {$key} = 0;//0 for false,1 for true\n";
                    break;
                //整型
                case Constant::DB_FIELD_TYPE_INT:
                    echo _tab(1) . "public int {$key} = 0;\n";
                    break;
                    //长整形
                case Constant::DB_FIELD_TYPE_LONGINT:
                    echo _tab(1) . "public long {$key} = 0;\n";
                    break;
                //blob
                case  Constant::DB_FIELD_TYPE_BLOB:
                case Constant::DB_FIELD_TYPE_LONGBLOB:
                    echo _tab(1) . "public  byte[] {$key} = null;\n";
                    break;
                default:
                    //default all string
                    echo _tab(1) . "public String {$key} = \"\";\n";
                    break;
            }
        }
        _fun_comment("获取bean2String", 1);
        echo _tab(1) . "public String toString() {\n";
        echo _tab(2) . "return \"{$uc_model_name}Bean [\"\n";
        $ii = 0;
        foreach ($model->field_list as $field) {
            /* @var MyField $field */
            $key = $field->name;
            if ($ii == 0) {
                echo _tab(5) . "+ \"{$key} = \" +  {$key}\n";
            } else {
                echo _tab(5) . "+ \", {$key} = \" +  {$key}\n";
            }
            $ii++;
        }
        echo _tab(4) . "+ \"]\";\n";


        echo _tab(1) . "}\n";
        echo "}";
        $cc_data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $cc_data);
    }

}