<?php


/**
 * 条件定义
 * Class MyWhere
 */
class MyWhere implements MyBase
{


  


    /**
     * 查询列表的名字
     * @var string
     */
    public $joiner =Constant::WHERE_JOIN_AND;


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
    static $conditions = array(
        'eq', //等于
        'neq', //不等于
        'gt', //大于
        'gte', //大于等于
        'lt', //少于
        'lte', //少于等于
        '=', //等于
        '!=', //不等于
        '>', //大于
        '>=', //大于等于
        '<', //少于
        '<=', //少于等于
        'kw', //关键字模糊匹配
        'date', //日期范围
        'time', //时间范围
        'in', //离散量范围内
        'notin', //离散量范围外
        'between', //标量范围内
        'notbetween' //标量范围外
    );


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
         * 解析一个条件
         */
        if (isset($a_data['joiner']) && isset($a_data['type']) && isset($a_data['key'])) {

            $joiner = $a_data['joiner'];
            $type = $a_data['type'];
            $key = $a_data['key'];

            if ($joiner != Constant::WHERE_JOIN_OR) {
                //默认与
                $joiner = Constant::WHERE_JOIN_AND;
            }

            if (!in_array($type, self::$conditions)) {
                //默认等于
                $type = "=";
            }
            //在这里不判断key
            return new MyWhere($joiner, $type, $key);

        }
        return null;
    }
}