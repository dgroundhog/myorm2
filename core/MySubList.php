<?php


/**
 * 子列表查询的结构
 */
class MySubList
{


    /**
     * MySubList constructor.
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