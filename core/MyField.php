<?php

/**
 * Class MyField
 *
 */
class MyField
{
    

    /**
     * 字段标识
     * @var string
     */
    public $field_name = "f1";


    /**
     * 字段中文名
     * @var string
     */
    public $field_title = "字段1";

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
     * 是否非空
     * @var bool
     */
    public $not_null = false;


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
     * 帮助提示
     * @var string
     */
    public $help = "帮助提示";


    /**
     * 默认过滤器
     * @var string
     */
    public $filter = "string";

    /**
     * 默认排序
     * @var int
     */
    public $position = 255;

    /**
     * 下面的特殊的输入规则
     */

    /**
     * 通过上传输入
     * @var bool
     */
    public $input_by_upload = false;

    /**
     * 通过上传图片输入
     * @var bool
     */
    public $input_by_upload_img = false;

    /**
     * 通过下拉框输入
     * @var bool
     */
    public $input_by_select = false;


    /**
     * 一组key-value组合
     * @var array
     */
    public $kv_list = array();

    /**
     * 下面的部分为强验证的规则
     */

    /**
     * 验证规则
     * no_rule  无规则
     * hash_range 散列值
     * int_range 整数范围
     * regexp 散列值
     * @var string
     */
    public $valid_rule = "no_rule";

    /**
     * rule =  regexp  验证正则表达式
     * @var string
     */
    public $valid_regexp = "";

    /**
     * rule =  int_range 验证最大值
     * @var int
     */
    public $valid_max = 0;

    /**
     * rule =  int_range 验证最小值
     * @var string
     */
    public $valid_min = 0;


    /**
     * rule =  hash_range 字典可选范围，简单散列值，逗号分割
     * @var string
     */
    public $valid_hash = "";

    /**
     * MyField constructor.
     * @param string $field_name
     * @param string $field_title
     * @param string $type
     * @param int $size
     * @param bool $required
     * @param string $default_value
     * @param string $help
     */
    public function __construct($field_name,
                                $field_title,
                                $type = "varchar",
                                $size = 255,
                                $required = false,
                                $default_value = "",
                                $help = "")
    {
        $this->field_name = $field_name;
        $this->field_title = $field_title;
        $this->type = $type;
        $this->size = $size;
        $this->required = $required;
        $this->default_value = $default_value;
        $this->help = $help;
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


}