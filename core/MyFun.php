<?php


/**
 * 查询列表的结构
 */
abstract class MyFun implements MyBase
{

    /**
     * 查询列表的名字
     * @var string
     */
    public $fun_name = "default";


    /**
     * 查询的标题
     * @var string
     */
    public $fun_title = "未命名函数";

    /**
     * MyFunList constructor.
     * @param string $name 唯一标识
     * @param string $title 标题
     */
    function __construct($name, $title)
    {
        $this->fun_name = strtolower($name);;
        $this->fun_title = $title;
    }

    /**
     * 获取函数名称
     * @return string
     */
    public function getName()
    {
        return $this->fun_name;
    }


}