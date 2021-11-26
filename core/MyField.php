<?php

/**
 * Class MyField
 *
 */
class MyField extends MyStruct
{


    /**
     * 字段类型
     * 有限的几种类型，int string  longblob date datetime
     * @var string
     */
    public $type = "varchar";

    /**
     * 字段大小
     * @var int
     */
    public $size = 255;


    /**
     * 是否自动增长
     * @var bool
     */
    public $auto_increment = false;


    /**
     * 默认值
     * @var string
     */
    public $default_value = "";

    /**
     * 表单是否比填
     * @var bool
     */
    public $required = false;


    /**
     * 默认过滤器
     * @var string
     */
    public $filter = "string";


    /**
     * filter =  regexp  验证正则表达式
     * @var string
     */
    public $regexp = "";
    /**
     * 通过上传输入
     * @var bool
     */
    public $input_by = "";


    /**
     * 有限的字典值
     * @var string
     */
    public $input_hash = "";

    /**
     * 是否全局字段
     * @var string
     */
    public $is_global = "0";


    public $basic_keys = array(
        "type",
        "size",
        "auto_increment",
        "default_value",
        "required",
        "filter",
        "regexp",
        "input_by",
        "input_hash",
        "is_global"
    );


    /**
     * MyField constructor.
     */
    public function __construct()
    {
        $this->scope = "FIELD";
    }


    /**
     * 默认字段结构
     * @return array
     */
    public function getDefaultTableField()
    {
        return array(
            "key" => $this->field_name,
            "name" => $this->field_title,
            "size" => $this->size,
            "type" => $this->type,
            "auto_increment" => $this->auto_increment,
            "not_null" => $this->not_null,
            "required" => $this->required,
            "default_value" => $this->default_value,
            "help" => $this->help,
            "filter" => $this->filter,
            "position" => $this->position,
            "kv_list" => $this->kv_list,
            "input_by_upload" => $this->input_by_upload,
            "input_by_upload_img" => $this->input_by_upload_img,
            "input_by_select" => $this->input_by_select,
            "valid_rule" => $this->valid_rule,
            "valid_regexp" => $this->valid_regexp,
            "valid_min" => $this->valid_min,
            "valid_max" => $this->valid_max,
            "valid_hash" => $this->valid_hash
        );
    }


    function init($v1)
    {
        // TODO: Implement init() method.
        //外部js实现
    }

    function getAsArray()
    {
        return $this->getBasicAsArray();
        // TODO: Implement getAsArray() method.
    }

    function parseToObj($a_data)
    {
        // TODO: Implement parseToObj() method.

        $this->parseToBasicObj($a_data);

        return $this;
    }
}