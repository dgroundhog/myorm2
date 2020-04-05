<?php

/**
 * 主程序模型
 * Class MyApp
 */
class MyApp
{

    /**
     * 单例模式的实例
     * @var MyApp
     */
    private static $instance = null;

    /**
     * 构造器私有化:禁止从类外部实例化
     * MyApp constructor.
     */
    private function __construct()
    {
    }

    /**
     * 克隆方法私有化:禁止从外部克隆对象
     */
    private function __clone()
    {
    }

    /**
     * @return MyApp
     */
    public static function getInstance()
    {
        //检测当前类属性$instance是否已经保存了当前类的实例
        if (self::$instance == null) {
            //如果没有,则创建当前类的实例
            self::$instance = new self();
        }
        //如果已经有了当前类实例,就直接返回,不要重复创建类实例
        return self::$instance;
    }

    /**
     * 包含的字段，key => MyModel
     * @var array
     */
    public $models = array();

    /**
     * 数据库配置
     * @var MyDb
     */
    public $db_conf = null;


    /**
     * 解析模型
     * @param array $a_app_data
     * @return bool|void
     */
    public function parse($a_app_data = array())
    {
        return false;
    }

    /**
     * 从json解析系统模型
     * @param string $json_path
     * @return bool|void
     */
    public function parseByJson($json_path)
    {
        $s_json_data = file_get_contents($json_path);
        $a_json_data = json_decode($s_json_data, true);

        if (null != $a_json_data) {
            return $this->parse($a_json_data);

        }
        return false;
    }


    /**
     * 导出到json
     * @param string $json_path
     */
    public function saveToJson($json_path = "")
    {


    }


    /**
     * 获取基本过滤器
     */
    public static function getDefaultFilter()
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
     * 获取可能的查询条件配置
     * @return array
     */
    public static function getQueryCndTypes()
    {
        return array(
            'eq', //等于
            'neq', //不等于
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


    /**
     * 获取list的聚合类型
     * @return array
     */
    public static function getListGroupTypes()
    {
        return array(
            "sum" => "求和",
            "avg" => "求平均值",
            "max" => "最大值",
            "min" => "最小值",
            "count" => "统计记录数"
        );
    }

}