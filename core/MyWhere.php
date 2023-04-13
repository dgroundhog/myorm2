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
    public $cond_list= [];




    /**
     * MyWhere constructor.
     */
    public function __construct()
    {
        $this->scope = "WHERE";
    }

    public $basic_keys = array(
        //type

    );

    /**
     * 获取数组结构
     * @return array
     */
    function getAsArray()
    {
        $a_data = $this->getBasicAsArray();

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

        $this->cond_list = array();

        if (isset($a_data['cond_list']) && is_array($a_data['cond_list'])) {
            foreach ($a_data['cond_list'] as $key => $cond) {
                $o_obj = new MyCond();
                $o_obj->parseToObj($cond);
                $this->cond_list[$key] = $o_obj;
            }
        }
        return $this;
    }

    function init($v1)
    {
        // TODO: Implement init() method.
    }
}