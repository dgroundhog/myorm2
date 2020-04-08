<?php


/**
 * 条件定义
 * Class MyWhere
 */
class MyWhere implements MyBase
{


    /**
     * 条件链接方式
     */
    const   JOIN_AND = "and";
    const   JOIN_OR = "or";

    /**
     * 条件链接方式
     */
    const   TYPE_AND = "and";
    const   TYPE_OR = "or";


    /**
     * 查询列表的名字
     * @var string
     */
    public $joiner = self::JOIN_AND;


    /**
     * 查询列表的名字
     * @var string
     */
    public $type = "eq";


    /**
     * 查询列表的名字
     * 当key为数组时，内部为or关系
     * @var string|array
     */
    public $key = null;

    /**
     * MyWhere constructor.
     * @param string $joiner
     * @param string $type
     * @param array|string $key
     */
    public function __construct($joiner, $type, $key)
    {
        $this->joiner = $joiner;
        $this->type = $type;
        $this->key = $key;
    }

    /**
     * 获取查询条件
     * @return array
     */
    static function getQueryConditions()
    {
        return array(
            'eq', //等于
            'neq', //不等于
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
     * 获取数组结构
     * @return array
     */
    public function getAsArray()
    {
        return array(
            "joiner" => $this->joiner,
            "type" => $this->type,
            "key" => $this->key
        );
    }



    /**
     * @inheritDoc
     */
    static function parseToObj($a_data)
    {
        /**
         * 可以更新全部，但必须要有更新的内容
         */
        if (isset($a_data['joiner']) && isset($a_data['type']) && isset($a_data['key']) ) {


            $o_obj = new MyWhere($fun_name, $fun_title);
            $o_obj->joiner = $a_update_keys;
            $o_obj->type = $a_update_by;
            $o_obj->key = $i_limit;

            return $o_obj;
        }
        return null;
    }
}