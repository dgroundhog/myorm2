<?php


/**
 * 最小条件
 * Class MyWhere
 */
class MyCond extends MyStruct
{
    /**
     * 查询列表的名字
     * @var string
     */
    //public $type = "eq";
    public $field = "";
    public $v1 = "";//普通，hash用逗号分隔 @NOW @@ 外部输入
    public $v2 = "";
    public $v1_type = "";//V1输入类型
    public $v2_type = "";//V2输入类型

    public $is_sub_where = "0";//是否子嵌套,先判断这个
    public $type_sub_where = "OR";//子嵌套的类型

    /**
     * 潜逃条件组合 MyCond
     * @var array
     */
    public $cond_list= [];

    /**
     * MyCond constructor.
     */
    public function __construct()
    {
        $this->scope = "COND";
    }


    public $basic_keys = array(
        //type
        "field",
        "v1",
        "v2",
        "v1_type",
        "v2_type",
        "is_sub_where",
        "type_sub_where"
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
        if (isset($a_data['cond_list']) && is_array($a_data['cond_list']) && count($a_data['cond_list']) > 0) {
            //var_dump($a_data['uuid']);
            //var_dump($a_data['cond_list']);
            foreach ($a_data['cond_list'] as $key => $cond) {
                $o_obj = new MyCond();
                $o_obj->parseToObj($cond);
                $this->cond_list[$key] = $o_obj;
            }
            //var_dump($this->getAsArray());
        }
        return $this;
    }

    function init($v1)
    {
        // TODO: Implement init() method.
    }
}