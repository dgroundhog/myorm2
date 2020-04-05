<?php


/**
 * 查询列表的结构
 */
class MyFunList extends MyFun
{


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


}