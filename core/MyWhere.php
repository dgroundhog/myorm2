<?php


/**
 * 条件定义,最多支持二级嵌套
 * Class MyWhere
 */
class MyWhere extends MyStruct
{

    /**
     * 查询列表的名字
     * @var string
     */
    public $joiner =Constant::WHERE_JOIN_AND;

    /**
     * 嵌套级别，最多2级
     * @var int
     */
    public $level = 0;

    /**
     * 父级id
     * @var string
     */
    public $parent_where = "";

    /**
     * 查询列表的名字
     * @var string
     */
    public $cond_list= [];


    /**
     * 潜逃条件组合 MyWhere
     * @var array
     */
    public $where_list= [];

    /**
     * MyWhere constructor.
     */
    public function __construct()
    {
        $this->scope = "WHERE";
    }

    public $basic_keys = array(
        //type
        "joiner",
        "level",
        "parent_where"
    );

    /**
     * 获取数组结构
     * @return array
     */
    function getAsArray()
    {
        $a_data = $this->getBasicAsArray();
        $a_data['where_list'] = array();
        foreach ($this->where_list as $key => $o_where) {
            /* @var MyWhere $o_where */
            $a_data['where_list'][$key] = $o_where->getAsArray();
        }

        $a_data['cond_list'] = array();
        foreach ($this->cond_list as $key => $o_cond) {
            /* @var MyCond $o_cond */
            $a_data['cond_list'][$key] = $o_cond->getAsArray();
        }
        return $a_data;

    }

    function parseToObj($a_data)
    {
        $this->parseToBasicObj($a_data);
        $this->where_list = array();
        $this->cond_list = array();

        if (isset($a_data['cond_list']) && is_array($a_data['cond_list'])) {
            foreach ($a_data['cond_list'] as $key => $cond) {
                $o_obj = new MyCond();
                $o_obj->parseToObj($cond);
                $this->cond_list[$key] = $o_obj;
            }
        }

        if (isset($a_data['where_list']) && is_array($a_data['where_list'])) {
            foreach ($a_data['where_list'] as $key => $where) {
                $o_obj = new MyWhere();
                $o_obj->parseToObj($where);
                $this->where_list[$key] = $o_obj;
            }
        }
        return $this;
    }

    function init($v1)
    {
        // TODO: Implement init() method.
    }
}