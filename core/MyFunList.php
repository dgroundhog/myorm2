<?php


/**
 * 查询列表的结构
 */
class MyFunList extends MyFun
{

    /**
     * 获取list的聚合类型
     * @return array
     */
    static function getGroupTypes()
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
     * 输出字段结构为数组
     * @return array
     */
    public function getAsArray()
    {
        return array(
            "name" => $this->fun_name,
            "title" => $this->fun_title,


            "is_distinct" => $this->is_distinct,
            "list_keys" => $this->list_keys,
            "list_by" => $this->list_by,
            "list_has_group" => $this->list_has_group,
            "list_group_type" => $this->list_group_type,
            "list_group_key" => $this->list_group_key,


        );
    }

    /**
     * @inheritDoc
     * @return MyFunFetch
     */
    static function parseToObj($a_data)
    {


        $fun_name = (!isset($a_data['name']) || $a_data['name'] == "") ? "default" : trim($a_data['name']);
        $fun_title = (!isset($a_data['title']) || $a_data['title'] == "") ? "列表或统计" : trim($a_data['title']);


        $o_obj = new MyFunList($fun_name, $fun_title);

        $is_distinct = ($a_data['is_distinct'] == true) ? true : false;
        $o_obj->is_distinct = $is_distinct;


        //查询的字段，当为空数组或者group也为空时，用*查询全部
        if (!isset($a_data['list_keys']) || !is_array($a_data['list_keys'])) {
            $o_obj->list_keys = array();
        } else {
            $o_obj->list_keys = $a_data['list_keys'];
        }

        //当选择distinct时，查询的字段不能为空
        if (count($o_obj->list_keys) == 0) {
            $o_obj->is_distinct = false;
        }

        $a_list_group_type = self::getGroupTypes();

        //TODO 需要针对group聚合的处理
        if (isset($a_data['list_has_group'])
            && $a_data['list_has_group'] == true
            && isset($a_data['list_group_type'])
            && $a_data['list_group_type'] != ""
            && isset($a_list_group_type[$a_data['list_group_type']])
            && isset($a_data['list_group_key'])
            && $a_data['list_group_key'] != ""
        ) {

            $o_obj->list_has_group = true;
            $o_obj->list_group_type = $a_data['list_group_type'];
            $o_obj->list_group_key = $a_data['list_group_key'];//array

            //TODO group_key 可以是数组，也可以是字符串， 可以空,最后转变为数组
            if (isset($a_data['list_group_by'])) {
                if (is_array($a_data['list_group_by'])) {
                    $o_obj->list_group_by = $a_data['list_group_by'];
                } else {
                    $o_obj->list_group_by = array($a_data['list_group_by']);
                }
            } else {
                $o_obj->list_group_by = array();
            }
        }


        //基本索引
        if (isset($a_data['list_by'])) {
            if (is_array($a_data['list_by'])) {
                $o_obj->list_by = $a_data['list_by'];
            } else {
                $o_obj->list_by = array($a_data['list_by']);
            }
        } else {
            $o_obj->list_by = array();
        }


        //模糊关键字
        if (isset($a_data['list_has_kw']) && $a_data['list_has_kw'] == true && isset($a_data['list_kw']) && count($a_data['list_kw']) > 0) {
            $a_temp = array();
            foreach ($a_data['list_kw'] as $kw_key) {
                if (!isset($model['table_fields'][$kw_key])) {
                    continue;
                }
                $a_temp[] = $kw_key;
            }
            if (count($a_temp) > 0) {
                $o_obj->list_has_kw = true;
                $o_obj->list_kw = $a_temp;
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
            if (isset($a_data[$bv]) && $a_data[$bv] == true && isset($a_data[$kv]) && $a_data[$kv] != "") {
                if (isset($model['table_fields'][$a_data[$kv]])) {
                    $o_obj->$bv = true;
                    $o_obj->$kv = $a_data[$kv];
                }
            }
        }

        //排序，默认true
        if (isset($a_data['list_has_order']) && $a_data['list_has_order'] == false) {
            $o_obj->list_has_order = false;
        }
        //有分页时，判断条件
        if ($o_obj->list_has_order) {
            if (isset($a_data['list_order_by'])) {
                if (is_array($a_data['list_order_by'])) {
                    $a_temp[] = array();
                    foreach ($a_data['list_order_by'] as $order_by => $order_dir) {
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
                    $o_obj->list_order_by = $a_temp;
                } else {
                    //定向组合
                    if ($a_data['list_order_by'] != "") {
                        if (isset($model['table_fields'][$a_data['list_order_by']])) {
                            $o_obj->list_order_by = $a_data['list_order_by'];
                        }
                    }
                    //排序
                    if (isset($a_data['list_order_dir']) && $a_data['list_order_dir'] != "") {
                        $lc_order_dir = strtolower($a_data['list_order_dir']);
                        if ($lc_order_dir != "desc") {
                            $order_dir = "ASC";
                        } else {
                            $order_dir = "DESC";
                        }
                        $o_obj->list_order_dir = $order_dir;
                    }
                }
            } else {
                // order_by 和 order_dir  都需要输入
            }
        }

        //统计数目，默认false,
        if (isset($a_data['count_only']) && $a_data['count_only'] == true) {
            $o_obj->count_only = true;

            if (isset($a_data['list_count_key']) && $a_data['list_count_key'] != "") {
                if (isset($model['table_fields'][$a_data['list_count_key']])) {
                    $o_obj->list_count_key = $a_data['list_count_key'];
                }
            }
        }

        //分页，默认true, 不分页即获取全部
        if (isset($a_data['list_has_page']) && $a_data['list_has_page'] == false) {
            $o_obj->list_has_pager = false;
        }

        if ($o_obj->list_has_pager == true) {
            //默认为normal
            $o_obj->list_pager_type = "normal";
            if (isset($a_data['list_pager_type']) && $a_data['list_pager_type'] == "cursor") {
                if (isset($a_data['cursor_offset_key']) && $a_data['cursor_offset_key'] != "") {
                    if (isset($model['table_fields'][$a_data['cursor_offset_key']])) {
                        $o_obj->cursor_offset_key = $a_data['cursor_offset_key'];
                        $o_obj->list_pager_type = "cursor";
                    }
                }
            }
        }


        return $o_obj;


    }


}