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
    //TODO
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
                    break;
                case Constant::FUN_TYPE_LIST_WITH_SUM:
                    $group_field_final = "i_sum_{$group_field}";
                    break;
                case Constant::FUN_TYPE_LIST_WITH_MAX:
                    $group_field_final = "i_max_{$group_field}";
                    break;
                case Constant::FUN_TYPE_LIST_WITH_MIN:
                    $group_field_final = "i_min_{$group_field}";
                    break;
                case Constant::FUN_TYPE_LIST_WITH_COUNT:
                    $group_field_final = "i_count_{$group_field}";
                    break;
                case Constant::FUN_TYPE_LIST:
                default:
                    SeasLog::error("!!!!出现了未定义的分组方法");
                    return array(false, null, null, null);
                    break;
            }
        }
        return array($has_group_field, $group_field, $o_group_field, $group_field_final);
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
     * @return bool
     */
    public function parseHaving(MyModel $model, MyFun $fun, $has_group_field, $has_group_by)
    {
        $has_having = false;//判断分组统计之前的所有条件
        if ($has_group_field && $has_group_by) {
            //再去判断having
            //
            $o_having = $fun->group_having;
            if ($o_having != null) {
                //TODO 可能需要的having 参数
                $has_having = true;
            }
        }
        return $has_having;
    }

    /**
     * 预先Order_by
     * @param MyModel $model
     * @param MyFun $fun
     * @return []
     */
    public function parseOrder_by(MyModel $model, MyFun $fun)
    {
        $s_order_by = "";
        $s_order_dir = "";
        $is_order_by_input = false;
        $is_order_dir_input = false;
        $has_order = false;
        if ($fun->order_enable == 1) {
            $has_order = true;
            $order_by_id = $fun->order_by;
            if ($order_by_id != "" && isset($model->field_list[$order_by_id])) {
                $o_f = $model->field_list[$order_by_id];
                $s_order_by = $o_f->name;
            }
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
            if ($pager_size >= 100) {
                //大于100的强制的降低为20
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
    function findProcName($table_name,$fun_name,$base_fun){
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

}