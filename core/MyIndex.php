<?php

/**
 * 缩影
 */
class MyIndex extends MyStruct
{

    /**
     * 私有字段字段列表
     * 包含的字段，key => MyField
     * @var array
     */
    public $field_list = array();

    public function __construct()
    {
        $this->scope = "INDEX";
    }

    function init($v1)
    {
        // TODO: Implement init() method.
    }



    function getAsArray()
    {
        $a_data = $this->getBasicAsArray();
        $a_data['field_list'] = array();
        foreach ($this->field_list as $key => $o_field) {
            /* @var MyField $o_field */
            $a_data['field_list'][$key] = $o_field->getAsArray();
        }
        return $a_data;
    }

    function parseToObj($a_data)
    {
        $this->parseToBasicObj($a_data);
        if (isset($a_data['field_list']) && is_array($a_data['field_list'])) {
            foreach ($a_data['field_list'] as $key => $field) {
                $o_obj = new MyField();
                $o_obj->parseToObj($field);
                $this->field_list[$key] = $o_obj;
            }
        }
        //TODO model
        return $this;
    }
}