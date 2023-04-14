<?php
if (!defined("CC_ROOT")) {
    define('CC_ROOT', realpath(dirname(__FILE__)));
}
include_once(CC_ROOT . "/_cc.inc.php");

/**
 * CRUD 解析器的抽象类
 *
 * Class CcBase
 */
abstract class CcBase
{

    /**
     * 应用
     * @var MyApp
     */
    public $curr_app = null;

    /**
     * 应用配置
     * @var MyArch
     */
    public $arch_conf = null;


    /**
     * 数据库配置
     * @var MyDb
     */
    public $db_conf = null;


    function _funHeader(MyModel $model, MyFun $fun)
    {
        echo "\n\n";
        echo "//----------------------------\n";
        echo "//BEGIN define {$model->name} -- {$fun->type} -- {$fun->name} \n";
        echo "//----------------------------\n";
        return true;
    }

    function _funFooter(MyModel $model, MyFun $fun)
    {
        echo "\n";
        echo "//----------------------------\n";
        echo "//END define {$model->name} -- {$fun->type} -- {$fun->name} \n";
        echo "//----------------------------\n";
    }


    //和代码无关的函数放在这里

    /**
     * @param MyModel $model
     * @param MyFun $fun
     * @return []
     */
    public function parseAdd_field(MyModel $model, MyFun $fun)
    {

        $a_all_fields = $model->field_list_kv;
        $i_param = 0;
        $a_param_comment = array();//用于参数注释
        $a_param_define = array();//用于参数定义
        $a_param_use = array();//用于参数使用,key--v 结构
        $a_param_type = array();//参数实际使用的类型
        $a_param_key = array();//原始key值
        $a_param_field = array();//原始字段结构

        $a_field_add = $fun->field_list;
        if ($fun->all_field == 1) {
            $a_field_add = $model->field_list;
        }
        //制作参数
        $is_return_new_id = false;
        foreach ($a_field_add as $field) {
            /* @var MyField $field */
            $field_name = $field->name;
            //存在于fun, 但不存在于model的字段，不处理
            if (!isset($a_all_fields[$field_name])) {
                continue;
            }
            //如果id也是自inc的，也不用输入了
            if ($field_name == 'id' && $field->auto_increment = 1) {
                $is_return_new_id = true;
                continue;
            }
            $i_param++;
            list($param1, $param2, $param3, $param4) = $this->_procParam($field, $i_param);
            $a_param_comment[] = $param1;
            $a_param_define[] = $param2;
            $a_param_use[$field_name] = $param3;
            $a_param_type[] = $param4;
            $a_param_key[] = $field_name;
            $a_param_field[] = $field;
        }

        //不怕内存不够
        return array($is_return_new_id, $i_param, $a_param_comment, $a_param_define, $a_param_use, $a_param_type, $a_param_key, $a_param_field);
    }

    /**
     * 处理参数
     * 不考虑小数,如果是金钱，用分做单位
     * 需要返回4个参数
     *
     * @param MyField $o_field
     * @param string $idx_append 避免重复的计数器
     * @param string $append u/w  update or where
     * @param bool $for_hash 是否一堆数据组合输入
     * @return string[]
     */
    abstract function _procParam($o_field, $idx_append = 0, $append = "", $for_hash = false);

    function _echoFunParams($a_param1 = array(), $a_param2 = array())
    {
        $i_size1 = count($a_param1);
        $i_size2 = count($a_param2);


        if (($i_size1 + $i_size2) == 0) {
            return;
        }

        if (($i_size1 + $i_size2) == 1) {
            if ($i_size1 == 1) {
                echo $a_param1[0];
                return;
            }
            if ($i_size2 == 1) {
                echo $a_param2[0];
                return;
            }
        }
        $ii = 0;
        foreach ($a_param1 as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        foreach ($a_param2 as $param) {
            echo _warp2join($ii) . _tab(5) . "{$param}";
            $ii++;
        }
        // echo "\n";
        //echo _tab(1);
    }


    /**
     * 处理更新的字段
     * @param MyModel $model
     * @param MyFun $fun
     * @return void
     */
    function _parseUpdate_field(MyModel $model, MyFun $fun)
    {
        $a_all_fields = $model->field_list_kv;
        //需要更新的字段
        $i_u_param = 0;
        $a_u_param_comment = array();//用于注释,db 里面没有用到的
        $a_u_param_define = array();//用于定义
        $a_u_param_use = array();//用于使用
        $a_u_param_type = array();//
        $a_u_param_key = array();//
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
            list($param1, $param2, $param3, $param4) = $this->_procParam($field, $i_u_param, "u");
            $a_u_param_comment[] = $param1;
            $a_u_param_define[] = $param2;
            $a_u_param_use[] = $param3;
            $a_u_param_type[] = $param4;
            $a_u_param_key[] = $field_name;
            $a_u_param_field[] = $field;
        }

        return array($i_u_param, $a_u_param_comment, $a_u_param_define, $a_u_param_use, $a_u_param_type, $a_u_param_key, $a_u_param_field);
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
        $a_param_type = array();//类型关键字
        $a_param_field = array();//用于使用

        //var_dump($o_fun->where);
        $jj = 0; //jj是用来构建参数序列的，防止用错
        if ($o_fun->where != null) {
            $cond_list0 = $o_fun->where->cond_list;
            foreach ($cond_list0 as $cond0) {
                /* @var MyCond $cond0 */
                if ($cond0->is_sub_where == "1") {
                    //子嵌套
                    $cond_list1 = $cond0->cond_list;
                    //子查询部分
                    if ($cond_list1 != null && count($cond_list1) > 0) {

                        foreach ($cond_list1 as $cond1) {
                            /* @var MyCond $cond1 */
                            if ($cond1->is_sub_where == "1") {
                                //仅允许一级子嵌套
                                continue;
                            }
                            $field1 = $model->field_list[$cond1->field];
                            /* @var MyField $field1 */
                            $field_type = $field1->type;
                            if ($field_type == Constant::DB_FIELD_TYPE_BLOB
                                || $field_type == Constant::DB_FIELD_TYPE_LONGBLOB
                                || $field_type == Constant::DB_FIELD_TYPE_TEXT
                                || $field_type == Constant::DB_FIELD_TYPE_LONGTEXT
                            ) {
                                SeasLog::error("{ $field1->name}--{$field1->type}字段不参与条件运算2");
                                //blob字段不参与条件运算2
                                continue;
                            }
                            switch ($cond1->type) {
                                case Constant::COND_TYPE_DATE:    // = "DATE";//关键字模糊匹配
                                case Constant::COND_TYPE_TIME:    // = "TIME";//日期范围内
                                case Constant::COND_TYPE_DATETIME:    // = "TIME";//日期范围内
                                    if ($cond1->v1_type == Constant::COND_VAl_TYPE_INPUT) {
                                        list($s_param1, $s_param2, $s_param3, $s_param4) = $this->_procParam($field1, $jj, "w");
                                        $a_param_comment[] = $s_param1;
                                        $a_param_define[] = $s_param2;
                                        $a_param_use[] = $s_param3;
                                        $a_param_type[] = $s_param4;
                                        $a_param_field[] = $field1;
                                        $jj++;
                                        //执行2次
                                        list($s_param1, $s_param2, $s_param3, $s_param4) = $this->_procParam($field1, $jj, "w");
                                        $a_param_comment[] = $s_param1;
                                        $a_param_define[] = $s_param2;
                                        $a_param_use[] = $s_param3;
                                        $a_param_type[] = $s_param4;
                                        $a_param_field[] = $field1;
                                        $jj++;
                                    }
                                    break;
                                case Constant::COND_TYPE_BETWEEN: // = "BETWEEN";//标量范围内
                                case Constant::COND_TYPE_NOTBETWEEN: // = "NOTBETWEEN";//标量范围外
                                    if ($cond1->v1_type == Constant::COND_VAl_TYPE_INPUT) {
                                        list($s_param1, $s_param2, $s_param3, $s_param4) = $this->_procParam($field1, $jj, "w");
                                        $a_param_comment[] = $s_param1;
                                        $a_param_define[] = $s_param2;
                                        $a_param_use[] = $s_param3;
                                        $a_param_type[] = $s_param4;
                                        $a_param_field[] = $field1;
                                        $jj++;
                                    }
                                    if ($cond1->v2_type == Constant::COND_VAl_TYPE_INPUT) {
                                        list($s_param1, $s_param2, $s_param3, $s_param4) = $this->_procParam($field1, $jj, "w");
                                        $a_param_comment[] = $s_param1;
                                        $a_param_define[] = $s_param2;
                                        $a_param_use[] = $s_param3;
                                        $a_param_type[] = $s_param4;
                                        $a_param_field[] = $field1;
                                        $jj++;
                                    }
                                    break;
                                default:
                                    if ($cond1->v1_type == Constant::COND_VAl_TYPE_INPUT) {
                                        list($s_param1, $s_param2, $s_param3, $s_param4) = $this->_procParam($field1, $jj, "w");
                                        $a_param_comment[] = $s_param1;
                                        $a_param_define[] = $s_param2;
                                        $a_param_use[] = $s_param3;
                                        $a_param_type[] = $s_param4;
                                        $a_param_field[] = $field1;
                                        $jj++;
                                    }
                            }
                        }
                    }
                } else {
                    //普通条件
                    $field0 = $model->field_list[$cond0->field];
                    /* @var MyField $field0 */
                    $field_type = $field0->type;
                    if ($field_type == Constant::DB_FIELD_TYPE_BLOB
                        || $field_type == Constant::DB_FIELD_TYPE_LONGBLOB
                        || $field_type == Constant::DB_FIELD_TYPE_TEXT
                        || $field_type == Constant::DB_FIELD_TYPE_LONGTEXT
                    ) {
                        SeasLog::error("{ $field0->name}--{$field0->type}字段不参与条件运算1");
                        //blob字段不参与条件运算2
                        continue;
                    }

                    switch ($cond0->type) {
                        case Constant::COND_TYPE_DATE:    // = "DATE";//关键字模糊匹配
                        case Constant::COND_TYPE_TIME:    // = "TIME";//日期范围内
                        case Constant::COND_TYPE_DATETIME:    // = "TIME";//日期范围内
                            if ($cond0->v1_type == Constant::COND_VAl_TYPE_INPUT) {

                                list($s_param1, $s_param2, $s_param3, $s_param4) = $this->_procParam($field0, $jj, "w");
                                $a_param_comment[] = $s_param1;
                                $a_param_define[] = $s_param2;
                                $a_param_use[] = $s_param3;
                                $a_param_type[] = $s_param4;
                                $a_param_field[] = $field0;
                                $jj++;
                                //执行2次
                                list($s_param1, $s_param2, $s_param3, $s_param4) = $this->_procParam($field0, $jj, "w");
                                $a_param_comment[] = $s_param1;
                                $a_param_define[] = $s_param2;
                                $a_param_use[] = $s_param3;
                                $a_param_type[] = $s_param4;
                                $a_param_field[] = $field0;
                                $jj++;
                            }
                            break;
                        case Constant::COND_TYPE_BETWEEN: // = "BETWEEN";//标量范围内
                        case Constant::COND_TYPE_NOTBETWEEN: // = "NOTBETWEEN";//标量范围外
                            if ($cond0->v1_type == Constant::COND_VAl_TYPE_INPUT) {

                                list($s_param1, $s_param2, $s_param3, $s_param4) = $this->_procParam($field0, $jj, "w");
                                $a_param_comment[] = $s_param1;
                                $a_param_define[] = $s_param2;
                                $a_param_use[] = $s_param3;
                                $a_param_type[] = $s_param4;
                                $a_param_field[] = $field0;
                                $jj++;
                            }
                            if ($cond0->v2_type == Constant::COND_VAl_TYPE_INPUT) {

                                list($s_param1, $s_param2, $s_param3, $s_param4) = $this->_procParam($field0, $jj, "w");
                                $a_param_comment[] = $s_param1;
                                $a_param_define[] = $s_param2;
                                $a_param_use[] = $s_param3;
                                $a_param_type[] = $s_param4;
                                $a_param_field[] = $field0;
                                $jj++;
                            }
                            break;
                        default:
                            if ($cond0->v1_type == Constant::COND_VAl_TYPE_INPUT) {

                                list($s_param1, $s_param2, $s_param3, $s_param4) = $this->_procParam($field0, $jj, "w");
                                $a_param_comment[] = $s_param1;
                                $a_param_define[] = $s_param2;
                                $a_param_use[] = $s_param3;
                                $a_param_type[] = $s_param4;
                                $a_param_field[] = $field0;
                                $jj++;
                            }
                    }
                }
            }

        }
        //参数个数，用于注释,用于定义,用于使用
        return array($jj, $a_param_comment, $a_param_define, $a_param_use, $a_param_type, $a_param_field);
    }


    /**
     * @param MyModel $model
     * @param MyFun $fun
     * @return []
     */
    public function parseGroup_field(MyModel $model, MyFun $fun)
    {
        $fun_type = $fun->type;
        $group_field = "";
        $group_field_final = "";
        $group_field_sel = "";//db 专用
        $has_group_field = true;//分组键
        $o_group_field = null;//TODO 可能无用
        $group_field_id = $fun->group_field;
        if ($group_field_id != "" && isset($model->field_list[$group_field_id])) {
            //判断一次分组键是否有效
            $o_group_field = $model->field_list[$group_field_id];
            $group_field = $o_group_field->name;
        }
        if ($group_field != "" && $fun_type == Constant::FUN_TYPE_LIST) {
            $group_field = "";
        }
        if ($group_field == "") {
            $has_group_field = false;
        }
        if ($has_group_field) {
            //检查是否带有聚合
            switch ($fun_type) {
                case Constant::FUN_TYPE_LIST_WITH_AVG:
                    $group_field_final = "i_agv_{$group_field}";
                    $group_field_sel = " AVG(`{$group_field}`) AS {$group_field_final}\n";
                    break;
                case Constant::FUN_TYPE_LIST_WITH_SUM:
                    $group_field_final = "i_sum_{$group_field}";
                    $group_field_sel = " SUM(`{$group_field}`) AS {$group_field_final}\n";
                    break;
                case Constant::FUN_TYPE_LIST_WITH_MAX:
                    $group_field_final = "i_max_{$group_field}";
                    $group_field_sel = " MAX(`{$group_field}`) AS {$group_field_final}\n";
                    break;
                case Constant::FUN_TYPE_LIST_WITH_MIN:
                    $group_field_final = "i_min_{$group_field}";
                    $group_field_sel = " MIN(`{$group_field}`) AS {$group_field_final}\n";
                    break;
                case Constant::FUN_TYPE_LIST_WITH_COUNT:
                    $group_field_final = "i_count_{$group_field}";
                    $group_field_sel = " COUNT(`{$group_field}`) AS {$group_field_final}\n";
                    break;
                case Constant::FUN_TYPE_LIST:
                default:
                    SeasLog::error("!!!!出现了未定义的分组方法");
                    return array(false, null, null, null);
                    break;
            }
        }
        return array($has_group_field, $group_field, $o_group_field, $group_field_final, $group_field_sel);
    }

    /**
     * @param MyModel $model
     * @param MyFun $fun
     * @return []
     */
    public function parseGroup_by(MyModel $model, MyFun $fun)
    {
        $group_by = "";
        $has_group_by = false;//分组键
        $group_by_id = $fun->group_by;
        if ($group_by_id != "" && isset($model->field_list[$group_by_id])) {
            $o_f = $model->field_list[$group_by_id];
            $group_by = $o_f->name;
        }
        if ($group_by != "") {
            $has_group_by = true;
        }
        return array($has_group_by, $group_by);
    }

    /**
     * 预先处理hading的条件
     * @param MyModel $model
     * @param MyFun $fun
     * @param $has_group_field
     * @param $has_group_by
     * @return array
     */
    public function parseHaving(MyModel $model, MyFun $fun, $o_group_field, $group_field_tag)
    {
        //需要 $has_group_field && $has_group_by
        $has_having = false;//判断分组统计之前的所有条件
        $a_param_comment = array();
        $a_param_define = array();
        $a_param_use = array();
        $a_param_type = array();
        $s_sql1 = "";
        $s_sql2 = "";
        $o_having = $fun->group_having;
        if ($o_having != null) {
            switch ($o_having->type) {
                case Constant::COND_TYPE_EQ:// = "EQ";//= 等于
                case Constant::COND_TYPE_NEQ:// = "NEQ";//!= 不等于
                case Constant::COND_TYPE_GT:// = "GT";//&GT; 大于
                case Constant::COND_TYPE_GTE:// = "GTE";//&GT;= 大于等于
                case Constant::COND_TYPE_LT:// = "LT";//&LT; 少于
                case Constant::COND_TYPE_LTE:// = "LTE";//&LT;= 少于等于
                    return $this->_procHaving_V1(1, 1, $o_group_field, $group_field_tag, $o_having);
                    break;
                case Constant::COND_TYPE_DATE:    // = "DATE";//关键字模糊匹配
                case Constant::COND_TYPE_TIME:    // = "TIME";//日期范围内
                case Constant::COND_TYPE_DATETIME:    // = "TIME";//日期范围内
                case Constant::COND_TYPE_BETWEEN: // = "BETWEEN";//标量范围内
                case Constant::COND_TYPE_NOTBETWEEN: // = "NOTBETWEEN";//标量范围外
                    return $this->_procHaving_V2(1, 1, $o_group_field, $group_field_tag, $o_having);
                    break;
                case Constant::COND_TYPE_IN:// = "IN";//离散量范围内
                case Constant::COND_TYPE_NOTIN:// = "NOTIN";//离散量范围外
                    return $this->_procHaving_V_range(1, 1, $o_group_field, $group_field_tag, $o_having);
                    break;
                default:

                    break;
            }
        }

        return array(false, array(), array(), array(), array(), "", "");
    }

    /**
     * 返回一致性参数
     * @param $tab_idx
     * @param $inc
     * @param $group_field_tag
     * @param $o_group_field
     * @param $o_having
     * @return void
     */
    public function _procHaving_V1($tab_idx, $inc, $o_group_field, $group_field_tag, $o_having)
    {

        $v_cond = $o_having->type;
        $v_type = $o_having->v1_type;
        $val = $o_having->v1;
        if (!isset(Constant::$a_cond_type_on_sql_1[$v_cond])) {
            SeasLog::error("known cond_type1 to proc");
            return;
        }
        $s_cond = Constant::$a_cond_type_on_sql_1[$v_cond];

        $empty_having = array(false, array(), array(), array(), array(), "", "");

        $a_param_comment = array();
        $a_param_define = array();
        $a_param_use = array();
        $a_param_type = array();
        $s_sql1 = "";
        $s_sql2 = "";
        switch ($v_type) {
            //固定值
            case  Constant::COND_VAl_TYPE_FIXED:
                if ($val == "") {
                    SeasLog::error("fixed 1 is Empty");
                    return $empty_having;
                }

                $s_sql1 = " {$group_field_tag}` {$s_cond} {$val}";
                $s_sql2 = "' {$group_field_tag}` {$s_cond} {$val}'";
                break;
            //函数
            case  Constant::COND_VAl_TYPE_FUN:
                if ($val == "") {
                    SeasLog::error("fun 1 is Empty");
                    return $empty_having;
                }
                $s_sql1 = " {$group_field_tag}` {$s_cond} {$val}()";
                $s_sql2 = "' {$group_field_tag}` {$s_cond} {$val}()'";
                break;
            //输入值
            case Constant::COND_VAl_TYPE_INPUT:
            default:
                list($param_comment, $param_define, $param_use, $type_size) = $this->_procParam($o_group_field, $inc, "h");
                //group where
                $a_param_comment[] = $param_comment;
                $a_param_define[] = $param_define;
                $a_param_use[] = $param_use;
                $a_param_type[] = $type_size;
                //$s_param_input = $param_key2;
                $s_sql1 = "  `{$group_field_tag}` {$s_cond} {$param_use}";
                $s_sql2 = "' `{$group_field_tag}` {$s_cond} {$param_use}'";
        }
        if ($s_sql1 != "") {
            $s_sql1 = _tab($tab_idx) . $s_sql1;
        }

        return array(true, $a_param_comment, $a_param_define, $a_param_use, $a_param_type, $s_sql1, $s_sql2);

    }

    public function _procHaving_V2($tab_idx, $inc, $o_group_field, $group_field_tag, $o_having)
    {
        $v_cond = $o_having->type;
        $v1_type = $o_having->v1_type;
        $val1 = $o_having->v1;
        $v2_type = $o_having->v2_type;
        $val2 = $o_having->v2;
        if (!isset(Constant::$a_cond_type_on_sql_2[$v_cond])) {
            SeasLog::error("known cond_type2 to proc");
            return;
        }
        $s_cond = Constant::$a_cond_type_on_sql_2[$v_cond];
        $empty_having = array(false, array(), array(), array(), array(), "", "");

        $a_param_comment = array();
        $a_param_define = array();
        $a_param_use = array();
        $a_param_type = array();
        $s_sql1 = " {$group_field_tag} {$s_cond} ";
        $s_sql2 = "' {$group_field_tag} {$s_cond} ";
        switch ($v1_type) {
            //固定值
            case  Constant::COND_VAl_TYPE_FIXED:
                if ($val1 == "") {
                    SeasLog::error("fixed 1 is Empty");
                    return $empty_having;
                }
                $s_sql1 = $s_sql1 . " {$val1} AND";
                $s_sql2 = $s_sql2 . " {$val1} AND";
                switch ($v2_type) {
                    //固定值
                    case  Constant::COND_VAl_TYPE_FIXED:
                        if ($val2 == "") {
                            SeasLog::error("fixed v2 is Empty");
                            return $empty_having;
                        }
                        $s_sql1 = $s_sql1 . " {$val2} ";
                        $s_sql2 = $s_sql2 . " {$val2} ";
                        break;
                    //函数
                    case  Constant::COND_VAl_TYPE_FUN:
                        if ($val2 == "") {
                            SeasLog::error("fixed f2 is Empty");
                            return $empty_having;
                        }
                        $s_sql1 = $s_sql1 . " {$val2}() ";
                        $s_sql2 = $s_sql2 . " {$val2}() ";
                        break;
                    //输入值
                    case Constant::COND_VAl_TYPE_INPUT:
                    default:
                        list($param_comment2, $param_define2, $param_use2, $param_type2) = $this->_procParam($o_group_field, $inc, "ht");
                        //group where
                        $a_param_comment[] = $param_comment2;
                        $a_param_define[] = $param_define2;
                        $a_param_use[] = $param_use2;
                        $a_param_type[] = $param_type2;
                        $s_sql1 = $s_sql1 . " {$param_use2} ";
                        $s_sql2 = $s_sql2 . " {$param_use2} ";
                }
                break;
            //函数
            case  Constant::COND_VAl_TYPE_FUN:
                if ($val1 == "") {
                    SeasLog::error("fun f1 is Empty");
                    return $empty_having;
                }
                $s_sql1 = $s_sql1 . " {$val1}() AND";
                $s_sql2 = $s_sql2 . " {$val1}() AND";
                switch ($v2_type) {
                    //固定值
                    case  Constant::COND_VAl_TYPE_FIXED:
                        if ($val2 == "") {
                            SeasLog::error("fun v2 is Empty");
                            return $empty_having;
                        }
                        $s_sql1 = $s_sql1 . " {$val2} ";
                        $s_sql2 = $s_sql2 . " {$val2} ";
                        break;
                    //函数
                    case  Constant::COND_VAl_TYPE_FUN:
                        if ($val2 == "") {
                            SeasLog::error("fun f2 is Empty");
                            return $empty_having;
                        }
                        $s_sql1 = $s_sql1 . " {$val2}() ";
                        $s_sql2 = $s_sql2 . " {$val2}() ";
                        break;
                    //输入值
                    case Constant::COND_VAl_TYPE_INPUT:
                    default:
                        //ipt 2
                        list($param_comment2, $param_define2, $param_use2, $param_type2) = $this->_procParam($o_group_field, $inc, "ht");
                        //group where
                        $a_param_comment[] = $param_comment2;
                        $a_param_define[] = $param_define2;
                        $a_param_use[] = $param_use2;
                        $a_param_type[] = $param_type2;
                        $s_sql1 = $s_sql1 . " {$param_use2} ";
                        $s_sql2 = $s_sql2 . " {$param_use2} ";
                }
                break;
            //输入值
            case Constant::COND_VAl_TYPE_INPUT:
            default:
                list($param_comment1, $param_define1, $param_use1, $param_type1) = $this->_procParam($o_group_field, $inc, "hf");
                //group where
                $a_param_comment[] = $param_comment1;
                $a_param_define[] = $param_define1;
                $a_param_use[] = $param_use1;
                $a_param_type[] = $param_type1;
                //$s_param_input = $param_key2;
                $s_sql1 = $s_sql1 . " {$param_use1} AND ";
                $s_sql2 = $s_sql2 . " {$param_use1} AND ";
                switch ($v2_type) {
                    //固定值
                    case  Constant::COND_VAl_TYPE_FIXED:
                        if ($val2 == "") {
                            SeasLog::error("ipt v2 is Empty");
                            return $empty_having;
                        }
                        $s_sql1 = $s_sql1 . " {$val2} ";
                        $s_sql2 = $s_sql2 . " {$val2} ";
                        break;
                    //函数
                    case  Constant::COND_VAl_TYPE_FUN:
                        if ($val2 == "") {
                            SeasLog::error("ipt f2 is Empty");
                            return $empty_having;
                        }
                        $s_sql1 = $s_sql1 . " {$val2}() ";
                        $s_sql2 = $s_sql2 . " {$val2}() ";
                        break;
                    //输入值
                    case Constant::COND_VAl_TYPE_INPUT:
                    default:
                        //ipt 2
                        list($param_comment2, $param_define2, $param_use2, $param_type2) = $this->_procParam($o_group_field, $inc, "ht");
                        //group where
                        $a_param_comment[] = $param_comment2;
                        $a_param_define[] = $param_define2;
                        $a_param_use[] = $param_use2;
                        $a_param_type[] = $param_type2;
                        $s_sql1 = $s_sql1 . " {$param_use2} ";
                        $s_sql2 = $s_sql2 . " {$param_use2} ";
                }
        }
        if ($s_sql1 != "") {
            $s_sql1 = _tab($tab_idx) . $s_sql1;
        }

        return array(true, $a_param_comment, $a_param_define, $a_param_use, $a_param_type, $s_sql1, $s_sql2);

    }


    public function _procHaving_V_range($tab_idx, $inc, $o_group_field, $group_field_tag, $o_having)
    {
        $v_cond = $o_having->type;
        $v_type = $o_having->v1_type;
        $val = $o_having->v1;
        if (!isset(Constant::$a_cond_type_on_sql_1[$v_cond])) {
            SeasLog::error("known cond_type1 to proc");
            return;
        }
        $s_cond = Constant::$a_cond_type_on_sql_1[$v_cond];
        $empty_having = array(false, array(), array(), array(), array(), "", "");


        $a_param_comment = array();
        $a_param_define = array();
        $a_param_use = array();
        $a_param_type = array();
        $s_sql1 = "";
        $s_sql2 = "";
        switch ($v_type) {
            //固定值
            case  Constant::COND_VAl_TYPE_FIXED:
                if ($val == "") {
                    SeasLog::error("fix v1 is Empty");
                    return $empty_having;
                }

                $s_sql1 = " {$group_field_tag} {$s_cond} ($val)";
                $s_sql2 = "' {$group_field_tag} {$s_cond} ($val)'";
                break;
            //函数
            case  Constant::COND_VAl_TYPE_FUN:
                if ($val == "") {
                    SeasLog::error("fun f1 is Empty");
                    return $empty_having;
                }
                $s_sql1 = " {$group_field_tag} {$s_cond}({$val}())";
                $s_sql2 = "' {$group_field_tag} {$s_cond}({$val}())'";

                break;
            //输入值
            case Constant::COND_VAl_TYPE_INPUT:
            default:
                list($param_comment, $param_define, $param_use, $type_size) = $this->_procParam($o_group_field, $inc, "gw", true);
                //group where
                $a_param_comment[] = $param_comment;
                $a_param_define[] = $param_define;
                $a_param_use[] = $param_use;
                $a_param_type[] = $type_size;
                //$s_param_input = $param_key2;
                $s_sql1 = "  `{$group_field_tag}` {$s_cond} {$param_use}";
                $s_sql2 = "' `{$group_field_tag}` {$s_cond} {$param_use}'";

                $s_sql1 = " {$group_field_tag} {$s_cond} ($param_use)";
                $s_sql2 = "' {$group_field_tag} {$s_cond} ($param_use)'";
            //having 仅为数字类型，不需要考虑字符串

        }
        if ($s_sql1 != "") {
            $s_sql1 = _tab($tab_idx) . $s_sql1;
        }

        return array(true, $a_param_comment, $a_param_define, $a_param_use, $a_param_type, $s_sql1, $s_sql2);

    }

    /**
     * 预先Order_by
     * @param MyModel $model
     * @param MyFun $fun
     * @return []
     */
    public function parseOrder_by(MyModel $model, MyFun $fun, $has_group_field = false, $group_field_final)
    {
        //TODO 排序的结构，可能时group by
        //TODO 不输入代表不排序

        $s_order_by = "";
        $s_order_dir = "";
        $is_order_by_input = false;
        $is_order_dir_input = false;
        $has_order = false;
        if ($fun->order_enable == 1) {
            $has_order = true;
            $order_by_id = $fun->order_by;
            if ($order_by_id == "@@" && $has_group_field && $group_field_final != "") {
                //这是聚合分组键排序
                $s_order_by = $group_field_final;
            } else if ($order_by_id != "" && isset($model->field_list[$order_by_id])) {
                $o_f = $model->field_list[$order_by_id];
                $s_order_by = $o_f->name;
            }
            //其他情况，需要外部输入，可能还需要在应用层校验一下
            if ($s_order_by == "") {
                $is_order_by_input = true;
            }

            $s_order_dir = strtoupper($fun->order_dir);
            if ($s_order_dir != "ASC" && $s_order_dir != "DESC") {
                $is_order_dir_input = true;
            }
        }
        return array($has_order, $is_order_by_input, $s_order_by, $is_order_dir_input, $s_order_dir);
    }

    /**
     * 是否布尔类型
     * @param $field_type
     * @return bool
     */
    function isBoolType($field_type)
    {
        return ($field_type == Constant::DB_FIELD_TYPE_BLOB);
    }

    /**
     * 是否数字的类型
     * @param $field_type
     * @return bool
     */
    function isIntType($field_type)
    {
        return ($field_type == Constant::DB_FIELD_TYPE_INT || $field_type == Constant::DB_FIELD_TYPE_LONGINT);
    }

    /**
     * 是否日期的类型
     * @param $field_type
     * @return bool
     */
    function isDateType($field_type)
    {
        return ($field_type == Constant::DB_FIELD_TYPE_DATE );
    }

    /**
     * 是否事件的类型
     * @param $field_type
     * @return bool
     */
    function isDateTimeType($field_type)
    {
        return ($field_type == Constant::DB_FIELD_TYPE_DATETIME );
    }

    /**
     * 是否二进制类型
     * @param $field_type
     * @return bool
     */
    function isBlobType($field_type)
    {
        return ($field_type == Constant::DB_FIELD_TYPE_BLOB || $field_type == Constant::DB_FIELD_TYPE_LONGBLOB);
    }

    /**
     * 处理一下pager的参数
     * @param MyModel $model
     * @param MyFun $fun
     * @return array
     */
    public function parsePager(MyModel $model, MyFun $fun)
    {
        $has_pager = false;
        $is_pager_size_input = false;
        $pager_size = 20;
        if ($fun->pager_enable == 1) {
            $has_pager = true;
            //生成辅助函数
            $pager_size = $fun->pager_size;
            $pager_size = 1 * $pager_size;
            if ($pager_size <= 0) {
                $is_pager_size_input = true;
                //
            }
            if ($pager_size > 10000) {
                //大于10000的强制的降低为20
                SeasLog::error("分页大小超过10000限制了，请修改参数！！！！！！！！！！！");
                $pager_size = 20;
            }
        }
        return array($has_pager, $is_pager_size_input, $pager_size);
    }


    /**
     * 获取参数的前缀
     *
     * @param string $field_type
     * @return string
     */
    function getFieldParamPrefix($field_type)
    {

        switch ($field_type) {
            //bool
            case Constant::DB_FIELD_TYPE_BOOL :
                return "b";

            //整型
            case Constant::DB_FIELD_TYPE_INT:
            case Constant::DB_FIELD_TYPE_LONGINT:
                return "i";

            case Constant::DB_FIELD_TYPE_BLOB :
            case Constant::DB_FIELD_TYPE_LONGBLOB :
                return "lb";

            //日期时间
            case Constant::DB_FIELD_TYPE_DATE :
            case Constant::DB_FIELD_TYPE_TIME :
            case Constant::DB_FIELD_TYPE_DATETIME :
                return "dt";

            //字符串
            case Constant::DB_FIELD_TYPE_CHAR:
            case Constant::DB_FIELD_TYPE_VARCHAR:
            case Constant::DB_FIELD_TYPE_TEXT :
            case Constant::DB_FIELD_TYPE_LONGTEXT :
            default :
                return "s";
        }

    }


    /**
     * 获取存储过程的名字
     * @param $table_name
     * @param $fun_name
     * @param $base_fun
     * @return string
     */
    function findProcName($table_name, $fun_name, $base_fun)
    {
        switch ($fun_name) {
            case $base_fun:
            case "default":
            case "":
                $fun = $base_fun;
                break;
            case "default_c":
                $fun = "{$base_fun}_c";
                break;
            default:
                $fun = "{$base_fun}_{$fun_name}";
                break;
        }

        return "p_{$table_name}__{$fun}";
    }


    /**
     * 获取模型中函数的名字
     * @param string $fun_name
     * @param string $base_fun
     * @param string $return_bean 针对基本查询，返回bean
     * @return string
     */
    function makeModelFunName($fun_name, $base_fun, $return_bean = false)
    {
        switch ($fun_name) {
            case $base_fun:
            case "default":
            case "":
                $fun = $base_fun;
                break;
            default:
                $fun = "{$base_fun}_{$fun_name}";
                break;
        }

        $real_fun = "{$fun}";
        if ($return_bean) {
            $real_fun = "{$fun}_vBean";
        }
        return $real_fun;
    }

    /**
     * 创建web xml等数据
     * @param $package
     * @param $a_models
     * @return void
     */
    public function makeWebConfig($a_models)
    {

    }

}