<?php


/**
 * 查询列表的结构
 */
class MyList
{
    /**
     * MyList constructor.
     * @param $name 唯一标识
     * @param $title 标题
     */
    function __construct($name, $title)
    {
        $this->list_name = $name;
        $this->list_title = $title;
    }

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