<?php


class MyModel extends MyStruct
{

    /**
     * 模型名称
     * @var string
     */
    public $name = "abc";
    /**
     * 模型名称
     * @var string
     */
    public $title = "模型ABC";
    /**
     * 表格名称，最终表为 t_$table_name
     * @var string
     */
    public $table_name = "abc";
    /**
     * 表格名称
     * @var string
     */
    public $table_title = "表ABC";
    /**
     * 主键，可能没有
     * @var string
     */
    public $primary_key = "id";
    /**
     * 是否包含UI，没有时只包含数据库和模型
     * @var int
     */
    public $has_ui = 0;
    /**
     * 图标
     * @var string
     */
    public $fa_icon = "linux";
    /**
     * 包含的字段，key => MyField
     * @var array
     */
    public $table_fields = array();
    /**
     * 私有字段字段列表
     * 包含的字段，uuid => MyField
     * @var array
     */
    public $field_list = array();
    /**
     * 私有字段字段列表
     * 包含的字段，key => MyField
     * @var array
     */
    public $field_list_kv = array();
    /**
     * 索引列表
     * 包含的字段，key => MyFun
     * @var array
     */
    public $fun_list = array();
    /**
     * 索引列表
     * 包含的字段，key => MyFun
     * @var array
     */
    public $idx_list = array();
    public $basic_keys = array("primary_key", "has_ui", "fa_icon", "table_name",);

    public function __construct()
    {
        $this->scope = "MODEL";
    }

    function init($v1)
    {
        // TODO: Implement init() method.
        // 外部js生产
    }

    function getAsArray()
    {
        $a_data = $this->getBasicAsArray();
        $a_data['field_list'] = array();
        foreach ($this->field_list as $key => $o_field) {
            /* @var MyField $o_field */
            $a_data['field_list'][$key] = $o_field->getAsArray();
        }

        $a_data['idx_list'] = array();
        foreach ($this->idx_list as $key => $o_index) {
            /* @var MyIndex $o_index */
            $a_data['idx_list'][$key] = $o_index->getAsArray();
        }

        $a_data['fun_list'] = array();
        foreach ($this->fun_list as $key => $o_fun) {
            /* @var MyFun $o_fun */
            $a_data['fun_list'][$key] = $o_fun->getAsArray();
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

        if (isset($a_data['idx_list']) && is_array($a_data['idx_list'])) {
            foreach ($a_data['idx_list'] as $key => $idx) {
                $o_obj = new MyIndex();
                $o_obj->parseToObj($idx);
                $this->idx_list[$key] = $o_obj;
            }
        }

        if (isset($a_data['fun_list']) && is_array($a_data['fun_list'])) {
            foreach ($a_data['fun_list'] as $key => $field) {
                $o_obj = new MyFun();
                $o_obj->parseToObj($field);
                $this->fun_list[$key] = $o_obj;
            }
        }

        return $this;
    }
}