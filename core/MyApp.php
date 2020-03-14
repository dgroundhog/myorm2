<?php

/**
 * 主程序模型
 * Class MyApp
 */
class MyApp
{


    /**
     * 用空格替代缩进
     * @param $size
     */
    public static function _tab($size)
    {
        $space = "";
        for ($ii = 0; $ii < $size; $ii++) {
            $space .= "    ";
        }
        return $space;
    }

    /**
     * 默认模型结构
     * @return array
     */
    public static function _get_default_model()
    {
        return array(
            "name" => "账户",
            "size" => "32",
            "type" => "varchar",
            "required" => "1",
            "help" => "6-20长度限制",
            "valid_rule" => "size_range",
            "valid_min" => 6,
            "valid_max" => 20
        );
    }

    /**
     * 获取list的聚合类型
     * @return array
     */
    public static function _get_list_group_type()
    {
        return array(
            "sum" => "求和",
            "avg" => "求平均值",
            "max" => "最大值",
            "min" => "最小值",
            "count" => "统计记录数"
        );
    }

    /**
     * 获取基本过滤器
     */
    public static function _get_default_filter()
    {
        return array(
            "int",
            "trim",
            "string",
            "email",
            "alphanum",
        );
    }


    /**
     * 默认字段结构
     * @return array
     */
    public static function _get_default_table_field()
    {
        return array(
            "name" => "名称",
            "size" => "255",
            "type" => "varchar", //有限的几种类型，int string  longblob date datetime
            "required" => "0",
            "default_value" => "",
            "help" => "帮助提示",
            "valid_rule" => "no_rule", //默认无规则
            "valid_regexp" => "", //正则表达式
            "valid_min" => 0,
            "valid_max" => 0,
            "filter" => "string"
        );
    }


    /**
     * 获取可能的配置
     * @return array
     */
    public static function _php_list_get_conds()
    {
        return array(
            'kw', //关键字模糊匹配
            'date', //日期范围
            'time', //时间范围
            'in', //离散量范围内
            'notin', //离散量范围外
            'between', //标量范围内
            'notbetween', //标量范围外
            'gt', //大于
            'gte', //大于等于
            'lt', //少于
            'lte', //少于等于
        );
    }

}