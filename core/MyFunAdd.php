<?php


/**
 * 插入的结构
 */
class MyFunAdd extends MyFun
{


    /**
     * 允许返回数据库生产的ID
     * @var array
     */
    public $return_new_id = true;


    /**
     * 允许外部输入添加key
     * @var array
     */
    public $add_keys = array();


    /**
     * 输出字段结构为数组
     * @return array
     */
    public function getAsArray()
    {
        return array(
            "name" => $this->fun_name,
            "title" => $this->fun_title,
            "add_keys" => $this->add_keys,
            "return_new_id" => $this->return_new_id
        );
    }

    /**
     * @inheritDoc
     * @return  MyFunAdd
     */
    static function parseToObj($a_data)
    {

        if (isset($a_data['add_keys']) && is_array($a_data['add_keys']) && count($a_data['add_keys']) > 0) {
            $fun_name = (!isset($a_data['name']) || $a_data['name'] == "") ? "default" : trim($a_data['name']);
            $fun_title = (!isset($a_data['title']) || $a_data['title'] == "") ? "插入数据" : trim($a_data['title']);

            $a_add_keys = $a_data['add_keys'];
            $return_new_id = ($a_data['return_new_id'] == true) ? true : false;

            $o_obj = new MyFunAdd($fun_name, $fun_title);
            $o_obj->add_keys = $a_add_keys;
            $o_obj->return_new_id = $return_new_id;

            return $o_obj;
        }
        return null;

    }


}