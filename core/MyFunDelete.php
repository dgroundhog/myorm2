<?php


/**
 * 删除的结构
 */
class MyFunDelete extends MyFun
{


    /**
     * 删除依据，不能为空
     * @var array  [MyWhere]
     */
    public $delete_by = array();


    /**
     * 删除的限制
     * @var array
     */
    public $delete_limit = 1;


    /**
     * 输出字段结构为数组
     * @return array
     */
    public function getAsArray()
    {
        return array(
            "name" => $this->fun_name,
            "title" => $this->fun_title,
            "delete_by" => $this->delete_by,
            "limit" => $this->limit

        );
    }

    /**
     * @inheritDoc
     * @return MyFunDelete
     */
    static function parseToObj($a_data)
    {
        /**
         * XXX 不允许删除全部
         */
        if (isset($a_data['delete_by']) && is_array($a_data['delete_by']) && count($a_data['delete_by']) > 0) {
            $fun_name = (!isset($a_data['name']) || $a_data['name'] == "") ? "default" : trim($a_data['name']);
            $fun_title = (!isset($a_data['title']) || $a_data['title'] == "") ? "删除" : trim($a_data['title']);

            $a_delete_by = array();
            foreach ($a_data['delete_by'] as $vv) {
                $ww = MyWhere::parseToObj($vv);
                $a_delete_by[] = $ww;
            }

            $i_limit = (!isset($a_data["limit"]) || $a_data["limit"] < 0) ? 1 : $a_data["limit"];

            $o_obj = new MyFunDelete($fun_name, $fun_title);
            $o_obj->delete_by = $a_delete_by;
            $o_obj->delete_limit = $i_limit;

            return $o_obj;
        }
        return null;

    }

}