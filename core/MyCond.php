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
    public $input_by = "";//输入类型

    /**
     * MyWhere constructor.
     */
    public function __construct()
    {
        $this->scope = "COND";
    }


    public $basic_keys = array(
        //type
        "input_by",
        "v1",
        "v2",
        "field"
    );

    /**
     * 获取数组结构
     * @return array
     */
    function getAsArray()
    {
        return  $this->getBasicAsArray();
    }

    function parseToObj($a_data)
    {
        $this->parseToBasicObj($a_data);


        return $this;
    }

    function init($v1)
    {
        // TODO: Implement init() method.
    }
}