<?php

/**
 * 用空格替代缩进
 * @param $size
 */
function _tab($size)
{
    $space = "";
    for ($ii = 0; $ii < $size; $ii++) {
        $space .= "    ";
    }
    return $space;
}

/**
 * 默认模型结构
 * @return array
 */
function _get_default_model()
{
    return array(
        "name" => "账户",
        "size" => "32",
        "type" => "varchar",
        "required" => "1",
        "help" => "6-20长度限制",
        "valid_rule" => "size_range",
        "valid_min" => 6,
        "valid_max" => 20
    );
}

/**
 * 获取list的聚合类型
 * @return array
 */
function _get_list_group_type()
{
    return array(
        "sum" => "求和",
        "avg" => "求平均值",
        "max" => "最大值",
        "min" => "最小值",
        "count" => "统计记录数"
    );
}

/**
 * 获取基本过滤器
 */
function _get_default_filter()
{
    return array(
        "int",
        "trim",
        "string",
        "email",
        "alphanum",
    );
}


/**
 * 默认字段结构
 * @return array
 */
function _get_default_table_field()
{
    return array(
        "name" => "名称",
        "size" => "255",
        "type" => "varchar", //有限的几种类型，int string  longblob date datetime
        "required" => "0",
        "default_value" => "",
        "help" => "帮助提示",
        "valid_rule" => "no_rule", //默认无规则
        "valid_regexp" => "", //正则表达式
        "valid_min" => 0,
        "valid_max" => 0,
        "filter" => "string"
    );
}

/**
 * 列表查询的结构
 */
class MyList
{

    /**
     * 查询列表的名字
     * @var string
     */
    public $list_name = "default";


    /**
     * 查询的标题
     * @var string
     */
    public $list_title = "默认列表";

    /**
     * 查询那些字段
     * @var array
     */
    public $is_distinct = false;

    /**
     * 查询那些字段
     * @var array
     */
    public $list_keys = array();

    /**
     * 索引组合
     * @var array
     */
    public $list_by = array();

    /**
     * 是否带关键字
     * @var boolean
     */
    public $list_has_kw = false;

    /**
     * 关键字数组
     * @var array
     */
    public $list_kw = array();

    /**
     * 启用日期过滤
     * @var boolean
     */
    public $list_has_date = false;

    /**
     * 日期字段
     * @var string
     */
    public $list_date_key = "";

    /**
     * 启用时间过滤
     * @var boolean
     */
    public $list_has_time = false;

    /**
     * 时间字段
     * @var string
     */
    public $list_time_key = "";

    /**
     * 仅查询数量
     * @var boolean
     */
    public $count_only = false;

    /**
     * 用来统计的id
     * @var string
     */
    public $list_count_key = "id";

    /**
     * 索引组合
     * @var boolean
     */
    public $list_has_group = false;

    /**
     * 聚合类型
     *
     * @var string
     */
    public $list_group_type = "";

    /**
     * 用来统计的列名
     * @var string
     */
    public $list_group_key = "";

    /**
     * 聚合依据
     * @var array
     */
    public $list_group_by = array();


    /**
     * 是否带排序
     * @var boolean
     */
    public $list_has_order = true;

    /**
     * 默认排序字段
     * @var string|array
     */
    public $list_order_by = "";

    /**
     * 默认排序字段,方向  DESC|ASC|空白
     * @var string
     */
    public $list_order_dir = "";

    /**
     * 是否带分页
     * @var boolean
     */
    public $list_has_pager = true;

    /**
     * 分页的类型,决定limit的个数
     * @var string normal|cursor
     */
    public $list_pager_type = "normal";


    /**
     * cursor 用来计算的偏移量
     * @var string
     */
    public $cursor_offset_key = "";

    /**
     * XXX 下面非常规字段
     */

    /**
     * 子列表，在扩展本模型
     * @var array
     */
    public $sub_list = array();

    /**
     * 定点范围内
     * @var boolean
     */
    public $list_has_in = false;

    /**
     * Key in
     * @var string
     */
    public $list_in_key = "";

    /**
     * 定点范围外
     * @var boolean
     */
    public $list_has_notin = false;

    /**
     * Key notin
     * @var string
     */
    public $list_notin_key = "";

    /**
     * 数值范围内
     * @var boolean
     */
    public $list_has_between = false;

    /**
     * Key between
     * @var string
     */
    public $list_between_key = "";

    /**
     * 数值范围外
     * @var boolean
     */
    public $list_has_notbetween = false;

    /**
     * Key notbetween
     * @var string
     */
    public $list_notbetween_key = "";

    /**
     * 数值大于
     * @var boolean
     */
    public $list_has_gt = false;

    /**
     * Key gt
     * @var string
     */
    public $list_gt_key = "";

    /**
     * 数值大于等于
     * @var boolean
     */
    public $list_has_gte = false;

    /**
     * Key gte
     * @var string
     */
    public $list_gte_key = "";

    /**
     * 数值少于
     * @var boolean
     */
    public $list_has_lt = false;

    /**
     * Key lt
     * @var string
     */
    public $list_lt_key = "";

    /**
     * 数值少于等于
     * @var boolean
     */
    public $list_has_lte = false;

    /**
     * Key lte
     * @var string
     */
    public $list_lte_key = "";

}

/**
 * 子列表查询的结构
 */
class MySubList
{

    /**
     * 查询列表的名字
     * @var string
     */
    public $list_name = "default";

    /**
     * 查询的标题
     * @var string
     */
    public $list_title = "默认列表";

    /**
     * 索引组合
     * @var array
     */
    public $list_by = array();

    /**
     * 是否带关键字
     * @var boolean
     */
    public $list_has_kw = false;

    /**
     * 启用日期过滤
     * @var boolean
     */
    public $list_has_date = false;

    /**
     * 启用时间过滤
     * @var boolean
     */
    public $list_has_time = false;

}


function _php_list_get_conds()
{
    return array(
        'kw', //关键字模糊匹配
        'date', //日期范围
        'time', //时间范围
        'in', //离散量范围内
        'notin', //离散量范围外
        'between', //标量范围内
        'notbetween', //标量范围外
        'gt', //大于
        'gte', //大于等于
        'lt', //少于
        'lte', //少于等于
    );
}

/**
 * 获取,默认主键查询
 *
 * @param $model
 * @return array
 */
function _model_get_ok_fetch_by($model)
{
    $a_temp = array();
    if (!isset($model['fetch_by'])) {
        return $a_temp;
    }
    foreach ($model['fetch_by'] as $pk_key) {
        if (!isset($model['table_fields'][$pk_key])) {
            continue;
        }
        $a_temp[] = $pk_key;
    }
    return $a_temp;
}

/**
 * 其他获取参数
 *
 * @param $model
 * @return array
 */
function _model_get_ok_other_fetch_by($model)
{
    $a_list = array();
    if (isset($model['fetch_by_other']) && is_array($model['fetch_by_other'])) {
        foreach ($model['fetch_by_other'] as $fetch_name => $a_vv) {
            $lc_kk = strtolower($fetch_name);
            if (isset($a_vv['fetch_by']) && count($a_vv['fetch_by']) > 0) {
                $a_temp = array();
                foreach ($a_vv['fetch_by'] as $pk_key) {
                    if (!isset($model['table_fields'][$pk_key])) {
                        continue;
                    }
                    $a_temp[] = $pk_key;
                }
                if (count($a_temp) > 0) {
                    //无主键
                    $a_list[$lc_kk] = array();
                    $a_list[$lc_kk]["fetch_title"] = $a_vv['fetch_title'];
                    $a_list[$lc_kk]["fetch_by"] = $a_temp;
                }
            }
        }
    }
    return $a_list;
}

/**
 * 其他获取参数
 *
 * @param $model
 * @return array
 */
function _model_get_ok_list($model)
{
    $a_list = array();
    /**
     * 查询列表是一个大数组
     * list_confs =>
     *  ----list_name  => title
     *                  => keys
     */
    if (isset($model['list_confs']) && is_array($model['list_confs'])) {
        $a_list_group_type = _get_list_group_type();
        foreach ($model['list_confs'] as $list_key => $list_conf) {
            if (strlen($list_key) == 0) {
                continue;
            }
            $lc_kk = strtolower($list_key);


            /**
             * 基本查询
             */
            $my_list = new MyList();
            $my_list->list_name = $list_key;
            $my_list->list_title = $list_conf['list_title'];

            if (isset($list_conf['list_has_group'])
                && $list_conf['list_has_group'] == true
                && isset($list_conf['list_group_type'])
                && $list_conf['list_group_type'] != ""
                && isset($a_list_group_type[$list_conf['list_group_type']])
                && isset($list_conf['list_group_key'])
                && $list_conf['list_group_key'] != ""
                && isset($model['table_fields'][$list_conf['list_group_key']])
            ) {
                $list_group_type = $list_conf['list_group_type'];
                $list_group_key = $list_conf['list_group_key'];
                //TODO
                $my_list->list_has_group = true;
                $my_list->list_group_type = $list_group_type;
                $my_list->list_group_key = $list_group_key;

                //group_key 可以空
                if (isset($list_conf['list_group_by']) || is_array($list_conf['list_group_by'])) {
                    $a_temp = array();
                    foreach ($list_conf['list_group_by'] as $kw_key) {
                        if (!isset($model['table_fields'][$kw_key])) {
                            continue;
                        }
                        $a_temp[] = $kw_key;
                    }
                    $my_list->list_group_by = $a_temp;
                }
            }

            //查询的字段不能为空
            if (!isset($list_conf['list_keys']) || !is_array($list_conf['list_keys'])) {
                $my_list->list_keys = array();
            } else {
                $a_temp = array();
                foreach ($list_conf['list_keys'] as $kw_key) {
                    if (!isset($model['table_fields'][$kw_key])) {
                        continue;
                    }
                    $a_temp[] = $kw_key;
                }
                $my_list->list_keys = $a_temp;
            }

            //当选择distinct时，查询的字段不能为空
            if (count($my_list->list_keys) == 0) {
                $my_list->is_distinct = false;
            }

            //基本索引
            if (isset($list_conf['list_by']) && count($list_conf['list_by']) > 0) {

                $a_temp = array();
                foreach ($list_conf['list_by'] as $kw_key) {
                    if (!isset($model['table_fields'][$kw_key])) {
                        continue;
                    }
                    $type = $model['table_fields'][$kw_key]['type'];
                    if ($type == "int" || $type == "string" || $type == "varchar" || $type == "char") {
                        $a_temp[] = $kw_key;
                    }
                }
                if (count($a_temp) > 0) {
                    $my_list->list_by = $a_temp;
                }
            }

            //模糊关键字
            if (isset($list_conf['list_has_kw']) && $list_conf['list_has_kw'] == true && isset($list_conf['list_kw']) && count($list_conf['list_kw']) > 0) {
                $a_temp = array();
                foreach ($list_conf['list_kw'] as $kw_key) {
                    if (!isset($model['table_fields'][$kw_key])) {
                        continue;
                    }
                    $a_temp[] = $kw_key;
                }
                if (count($a_temp) > 0) {
                    $my_list->list_has_kw = true;
                    $my_list->list_kw = $a_temp;
                }
            }

            $a_has_key = _php_list_get_conds();
            foreach ($a_has_key as $cond) {
                if ($cond == "kw") {
                    //多个关键字，单独处理了
                    continue;
                }
                $bv = "list_has_{$cond}";
                $kv = "list_{$cond}_key";
                if (isset($list_conf[$bv]) && $list_conf[$bv] == true && isset($list_conf[$kv]) && $list_conf[$kv] != "") {
                    if (isset($model['table_fields'][$list_conf[$kv]])) {
                        $my_list->$bv = true;
                        $my_list->$kv = $list_conf[$kv];
                    }
                }
            }

            //排序，默认true
            if (isset($list_conf['list_has_order']) && $list_conf['list_has_order'] == false) {
                $my_list->list_has_order = false;
            }
            //有分页时，判断条件
            if ($my_list->list_has_order) {
                if (isset($list_conf['list_order_by'])) {
                    if (is_array($list_conf['list_order_by'])) {
                        $a_temp[] = array();
                        foreach ($list_conf['list_order_by'] as $order_by => $order_dir) {
                            if (!isset($model['table_fields'][$order_by])) {
                                continue;
                            }
                            $lc_order_dir = strtolower($order_dir);
                            if ($lc_order_dir != "desc") {
                                $order_dir = "ASC";
                            } else {
                                $order_dir = "DESC";
                            }
                            $a_temp[$order_by] = $order_dir;
                        }
                        $my_list->list_order_by = $a_temp;
                    } else {
                        //定向组合
                        if ($list_conf['list_order_by'] != "") {
                            if (isset($model['table_fields'][$list_conf['list_order_by']])) {
                                $my_list->list_order_by = $list_conf['list_order_by'];
                            }
                        }
                        //排序
                        if (isset($list_conf['list_order_dir']) && $list_conf['list_order_dir'] != "") {
                            $lc_order_dir = strtolower($list_conf['list_order_dir']);
                            if ($lc_order_dir != "desc") {
                                $order_dir = "ASC";
                            } else {
                                $order_dir = "DESC";
                            }
                            $my_list->list_order_dir = $order_dir;
                        }
                    }
                } else {
                    // order_by 和 order_dir  都需要输入
                }
            }

            //统计数目，默认false,
            if (isset($list_conf['count_only']) && $list_conf['count_only'] == true) {
                $my_list->count_only = true;

                if (isset($list_conf['list_count_key']) && $list_conf['list_count_key'] != "") {
                    if (isset($model['table_fields'][$list_conf['list_count_key']])) {
                        $my_list->list_count_key = $list_conf['list_count_key'];
                    }
                }
            }

            //分页，默认true, 不分页即获取全部
            if (isset($list_conf['list_has_page']) && $list_conf['list_has_page'] == false) {
                $my_list->list_has_pager = false;
            }

            if ($my_list->list_has_pager == true) {
                //默认为normal
                $my_list->list_pager_type = "normal";
                if (isset($list_conf['list_pager_type']) && $list_conf['list_pager_type'] == "cursor") {
                    if (isset($list_conf['cursor_offset_key']) && $list_conf['cursor_offset_key'] != "") {
                        if (isset($model['table_fields'][$list_conf['cursor_offset_key']])) {
                            $my_list->cursor_offset_key = $list_conf['cursor_offset_key'];
                            $my_list->list_pager_type = "cursor";
                        }
                    }
                }
            }


            //子列表
            $my_list->sub_list = array();
            if (isset($list_conf['sub_list']) && is_array($list_conf['sub_list']) && count($list_conf['sub_list']) > 0) {
                foreach ($list_conf['sub_list'] as $sub_list_name => $sub_list_conf) {
                    $sub_lc_list_name = strtolower($sub_list_name);
                    $my_list->sub_list[$sub_lc_list_name] = $sub_list_conf;
                }
            }

            $a_list[$lc_kk] = $my_list;
        }
    }

    return $a_list;
}


/**
 * 整理的更新的条件
 * @param array $model
 * @return array
 */
function _model_get_ok_update($model)
{

    $a_list = array();
    /**
     * 其他可能的主键查询单条语句
     * update_confs=>
     *  ----update_name  => title
     *                  => keys
     */
    if (isset($model['update_confs']) && is_array($model['update_confs'])) {

        foreach ($model['update_confs'] as $update_name => $a_update_conf) {
            if (strlen($update_name) == 0) {
                continue;
            }
            $lc_kk = strtolower($update_name);


            $a_update_keys_tobe = $a_update_conf['update_keys'];//更新的内容
            $a_update_by_tobe = $a_update_conf['update_by'];//更新依据

            //需要更新的不能为空
            $a_temp = array();
            foreach ($a_update_keys_tobe as $pk_key) {
                if (!isset($model['table_fields'][$pk_key])) {
                    continue;
                }
                $a_temp[] = $pk_key;
            }
            if (count($a_temp) == 0) {
                //无主键
                continue;
            }
            $s_update_keys = $a_temp;

            //更新依据的可以为空
            if (null == $a_update_by_tobe || !is_array($a_update_by_tobe)) {
                $a_update_by_tobe = $model['fetch_by'];
            }
            $a_temp = array();
            foreach ($a_update_by_tobe as $pk_key) {
                if (!isset($model['table_fields'][$pk_key])) {
                    continue;
                }
                $a_temp[] = $pk_key;
            }
            $a_list[$lc_kk] = array();
            $a_list[$lc_kk]["update_keys"] = $s_update_keys;
            $a_list[$lc_kk]["update_by"] = $a_temp;
            $a_list[$lc_kk]['update_title'] = $a_update_conf['update_title'];////更新的标题

            //等于0时不限制
            if (!isset($a_update_conf["limit"]) || $a_update_conf["limit"] < 0) {
                $a_list[$lc_kk]["limit"] = 1;
            } else {
                $a_list[$lc_kk]["limit"] = $a_update_conf["limit"];
            }

        }
    }

    if (isset($model["table_fields"]["flag"]) && !isset($model['update_confs']["flag"])) {
        $a_update_by = $model['fetch_by'];
        $a_temp = array();
        foreach ($a_update_by as $pk_key) {
            if (!isset($model['table_fields'][$pk_key])) {
                continue;
            }
            $a_temp[] = $pk_key;
        }
        if (count($a_temp) > 0) {
            $lc_kk = "flag";
            $a_list[$lc_kk] = array();
            $a_list[$lc_kk]["update_keys"] = array("flag");
            $a_list[$lc_kk]["update_by"] = $a_temp;
            $a_list[$lc_kk]['update_title'] = "更新flag用作逻辑删除";
            $a_list[$lc_kk]["limit"] = 1;
        }
    }

    return $a_list;
}


/**
 * 整理的删除的条件
 * @param array $model
 * @return array
 */
function _model_get_ok_delete($model)
{
    $a_list = array();
    if (isset($model['delete_confs']) && is_array($model['delete_confs'])) {

        foreach ($model['delete_confs'] as $delete_name => $a_delete_conf) {
            $lc_kk = strtolower($delete_name);
            $a_delete_by = $a_delete_conf['delete_by'];

            //默认删除依据的不能为空
            if (null == $a_delete_by || !is_array($a_delete_by)) {
                $a_delete_by = $model['fetch_by'];
            }

            $a_temp = array();
            foreach ($a_delete_by as $pk_key) {
                if (!isset($model['table_fields'][$pk_key])) {
                    continue;
                }
                $a_temp[] = $pk_key;
            }
            //允许删除全部
            $a_list[$lc_kk] = array();
            $a_list[$lc_kk]["delete_by"] = $a_temp;
            $a_list[$lc_kk]['delete_title'] = $a_delete_conf['delete_title'];

            //等于0时不限制
            if (!isset($a_delete_conf["limit"]) || $a_delete_conf["limit"] < 0) {
                $a_list[$lc_kk]["limit"] = 1;
            } else {
                $a_list[$lc_kk]["limit"] = $a_delete_conf["limit"];
            }

        }
    }
    return $a_list;
}



