<?php


/**
 * 查询列表的结构
 */
class MyFun extends MyStruct
{
    /**
     * 查询列表的名字
     * 仅在普通查询有效，聚合查询不能返回全部
     * @var string
     */
    public $return_all = 1;

    /**
     * 需要返回字段的列表
     * @var array
     */
    public $field_list = [];

    /**
     * 查询条件的列表
     * @var string
     */
    public $where = [];

    /**
     * 被聚合的字段
     * @var string
     */
    public $group_field = "";
    /**
     * 聚合字段
     * @var string
     */
    public $group_by = "";


    /**
     * 排序字段,是否启用排序
     * @var string
     */
    public $order_enable = 0;
    public $order_by = "";//排序字段，为空时外部输入
    public $order_dir = "";//排序方向，默认DESC，为空时外部输入

    public $basic_keys = array(
        "return_all",
        "group_field",
        "group_by",
        "order_enable",
        "order_by",
        "order_dir",
    );

    /**
     * MyFunList constructor.
     */
    function __construct()
    {
        $this->scope = "FUN";
    }


    function init($v1)
    {
        // TODO: Implement init() method.
    }

    function getAsArray()
    {
        $a_data = $this->getBasicAsArray();
        $a_data['field_list'] = array();
        foreach ($this->field_list as $key => $o_field) {
            /* @var MyField $o_field */
            $a_data['field_list'][$key] = $o_field->getAsArray();
        }

        /* @var MyWhere $o_where */
        $o_where = $this->where;
        $a_data['where'] = $o_where->getAsArray();
        return $a_data;
    }

    function parseToObj($a_data)
    {
        $this->parseToBasicObj($a_data);
        $this->field_list = array();

        if (isset($a_data['field_list']) && is_array($a_data['field_list'])) {
            foreach ($a_data['field_list'] as $key => $field) {
                $o_obj = new MyField();
                $o_obj->parseToObj($field);
                $this->field_list[$key] = $o_obj;
            }
        }

        $o_obj = new MyWhere();
        $o_obj->parseToObj($a_data['where']);
        $this->where = $o_obj;

        return $this;
    }
}