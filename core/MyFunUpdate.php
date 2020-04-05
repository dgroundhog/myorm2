<?php


/**
 * 更新的结构
 */
class MyFunUpdate extends MyFun
{


    /**
     * 获取的字段
     * @var array
     */
    public $update_keys = array();


    /**
     * 允许外部输入添加key
     * @var array
     */
    public $update_by = array();


    /**
     * 删除的限制
     * @var array
     */
    public $update_limit = 1;


    /**
     * 输出字段结构为数组
     * @return array
     */
    public function getAsArray()
    {
        return array(
            "name" => $this->fun_name,
            "title" => $this->fun_title,
            "update_keys" => $this->update_keys,
            "update_by" => $this->update_by,
            "limit" => $this->update_limit
        );
    }

    /**
     * @inheritDoc
     * @return MyFunUpdate
     */
    static function parseToObj($a_data)
    {

        /**
         * 可以更新全部，但必须要有更新的内容
         */
        if (isset($a_data['update_keys']) && is_array($a_data['update_keys']) && count($a_data['update_keys']) > 0) {
            $fun_name = (!isset($a_data['name']) || $a_data['name'] == "") ? "default" : trim($a_data['name']);
            $fun_title = (!isset($a_data['title']) || $a_data['title'] == "") ? "查询" : trim($a_data['title']);

            $a_update_keys = $a_data['update_keys'];

            $a_update_by = (!is_array($a_data['update_by']) || count($a_data['update_keys']) == 0) ? array() : $a_data['update_keys'];

            $i_limit = (!isset($a_data["limit"]) || $a_data["limit"] < 0) ? 1 : $a_data["limit"];

            $o_obj = new MyFunUpdate($fun_name, $fun_title);
            $o_obj->update_keys = $a_update_keys;
            $o_obj->update_by = $a_update_by;
            $o_obj->update_limit = $i_limit;

            return $o_obj;
        }
        return null;

    }


}