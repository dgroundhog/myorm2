<?php


/**
 * 查询个体的结构
 */
class MyFunFetch extends MyFun
{


    /**
     * 获取的字段
     * @var array
     */
    public $fetch_keys = array();


    /**
     * 允许外部输入添加key
     * @var array
     */
    public $fetch_by = array();


    /**
     * 输出字段结构为数组
     * @return array
     */
    public function getAsArray()
    {
        return array(
            "name" => $this->fun_name,
            "title" => $this->fun_title,
            "fetch_keys" => $this->fetch_keys,
            "fetch_by" => $this->fetch_by
        );
    }

    /**
     * @inheritDoc
     * @return MyFunFetch
     */
    static function parseToObj($a_data)
    {

        /**
         * fetch_by不能为空
         */
        if (isset($a_data['fetch_by']) && is_array($a_data['fetch_by']) && count($a_data['fetch_by']) > 0) {
            $fun_name = (!isset($a_data['name']) || $a_data['name'] == "") ? "default" : trim($a_data['name']);
            $fun_title = (!isset($a_data['title']) || $a_data['title'] == "") ? "查询" : trim($a_data['title']);

            $a_fetch_keys = (!is_array($a_data['fetch_keys']) || count($a_data['fetch_keys']) == 0) ? array() : $a_data['fetch_keys'];


            $a_fetch_by = array();
            foreach ($a_data['fetch_by'] as $vv) {
                $ww = MyWhere::parseToObj($vv);
                $a_fetch_by[] = $ww;
            }

            $o_obj = new MyFunFetch($fun_name, $fun_title);
            $o_obj->fetch_keys = $a_fetch_keys;
            $o_obj->fetch_by = $a_fetch_by;

            return $o_obj;
        }
        return null;

    }

}