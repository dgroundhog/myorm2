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
    public $has_group = false;

    /**
     * 聚合类型
     *
     * @var string
     */
    public $group_type = "";

    /**
     * 用来统计的列名
     * @var string
     */
    public $group_key = "";

    /**
     * 聚合依据
     * @var array
     */
    public $group_by = array();


    /**
     * 是否带排序
     * @var boolean
     */
    public $has_order = true;

    /**
     * 默认排序字段
     * @var string|array
     */
    public $order_by = "";

    /**
     * 默认排序字段,方向  DESC|ASC|空白
     * @var string
     */
    public $order_dir = "";

    /**
     * 是否带分页
     * @var boolean
     */
    public $has_pager = true;

    /**
     * 分页的类型,决定limit的个数
     * @var string normal|cursor
     */
    public $pager_type = "normal";


    /**
     * pager_cursor 用来计算的偏移量
     * @var string
     */
    public $pager_cursor_key = "";


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

            "has_group" => $this->has_group,
            "group_type" => $this->group_type,
            "group_key" => $this->group_key,

            "has_order" => $this->has_order,
            "order_by" => $this->order_by,
            "order_dir" => $this->order_dir,

            "has_pager" => $this->has_pager,
            "pager_type" => $this->pager_type,
            "pager_cursor_key" => $this->pager_cursor_key,


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

        $a_group_type = self::getGroupTypes();

        //TODO 需要针对group聚合的处理
        if (isset($a_data['has_group'])
            && $a_data['has_group'] == true
            && isset($a_data['group_type'])
            && $a_data['group_type'] != ""
            && isset($a_group_type[$a_data['group_type']])
            && isset($a_data['group_key'])
            && $a_data['group_key'] != ""
        ) {

            $o_obj->has_group = true;
            $o_obj->group_type = $a_data['group_type'];
            $o_obj->group_key = $a_data['group_key'];//array

            //TODO group_key 可以是数组，也可以是字符串， 可以空,最后转变为数组
            if (isset($a_data['group_by'])) {
                if (is_array($a_data['group_by'])) {
                    $o_obj->group_by = $a_data['group_by'];
                } else {
                    $o_obj->group_by = array($a_data['group_by']);
                }
            } else {
                $o_obj->group_by = array();
            }
        }

        //基本条件
        $a_list_by = array();
        if (is_array($a_data['list_by']) && count($a_data['list_by']) > 0) {
            foreach ($a_data['list_by'] as $vv) {
                $ww = MyWhere::parseToObj($vv);
                $a_list_by[] = $ww;
            }
        }
        $o_obj->list_by = $a_list_by;

        //排序，默认true
        if (isset($a_data['has_order']) && $a_data['has_order'] == false) {
            $o_obj->has_order = false;
        }
        //有分页时，判断条件
        if ($o_obj->has_order) {
            if (isset($a_data['order_by'])) {
                if (is_array($a_data['order_by'])) {
                    $a_temp[] = array();
                    foreach ($a_data['order_by'] as $order_by => $order_dir) {
                        $lc_order_dir = strtolower($order_dir);
                        if ($lc_order_dir != "desc") {
                            $order_dir = "ASC";
                        } else {
                            $order_dir = "DESC";
                        }
                        $a_temp[$order_by] = $order_dir;
                    }
                    $o_obj->order_by = $a_temp;
                } else {
                    //定向组合
                    if ($a_data['order_by'] != "") {
                        $o_obj->order_by = $a_data['order_by'];
                    }
                    //排序
                    if (isset($a_data['order_dir']) && $a_data['order_dir'] != "") {
                        $lc_order_dir = strtolower($a_data['order_dir']);
                        if ($lc_order_dir != "desc") {
                            $order_dir = "ASC";
                        } else {
                            $order_dir = "DESC";
                        }
                        $o_obj->order_dir = $order_dir;
                    }
                }
            } else {
                //TODO order_by 和 order_dir 为空时，需要 都需要输入
            }
        }

        //统计数目，默认false,
        if (isset($a_data['count_only']) && $a_data['count_only'] == true) {
            $o_obj->count_only = true;
            //屏蔽order
            if (isset($a_data['list_count_key']) && $a_data['list_count_key'] != "") {
                $o_obj->list_count_key = $a_data['list_count_key'];
            }
        }

        //分页，默认true, 不分页即获取全部
        if (isset($a_data['has_pager']) && $a_data['has_pager'] == false) {
            $o_obj->has_pager = false;
        }

        if ($o_obj->has_pager == true) {
            //默认为normal
            $o_obj->pager_type = "normal";
            if (isset($a_data['pager_type']) && $a_data['pager_type'] == "pager_cursor") {
                if (isset($a_data['pager_cursor_key']) && $a_data['pager_cursor_key'] != "") {
                    $o_obj->pager_cursor_key = $a_data['pager_cursor_key'];
                    $o_obj->pager_type = "pager_cursor";
                }
            }
        }


        return $o_obj;


    }


}