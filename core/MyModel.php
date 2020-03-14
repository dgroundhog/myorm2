<?php


class MyModel
{

    /**
     * 模型名称
     * @var string
     */
    public $model_name = "abc";

    /**
     * 模型名称
     * @var string
     */
    public $model_title = "模型ABC";

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
     * 图标
     * @var string
     */
    public $table_icon = "linux";


    /**
     * 包含的字段，key => MyField
     * @var array
     */
    public $table_fields = array();


    /**
     * 主键，可能没有
     * @var string
     */
    public $primary_key = "id";


    /**
     * 唯一的key对
     * @var array
     *
     * "uk1" => array("op_id"),
     *"uk2" => array("account")
     */
    public $unique_key = array();

    /**
     * 用作索引的key
     * @var array
     *
     * "ik1" => array("op_id"),
     *"ik2" => array("account")
     */
    public $index_key = array();


    /**
     * 预定义kv字典值的定义
     * TODO，写到myfield里去
     * @var array
     */
    public $kv_list = array();


    /**
     * 用select输入的key
     * @var array
     */
    public $keys_by_select = array();

    /**
     * 通过文件上传来的key
     * TODO，写到myfield里去
     * @var array
     */
    public $keys_by_upload = array();

    /**
     * 允许上传文件
     * = if count（keys_by_upload）>0
     * @var array
     */
    public $upload_enable = false;


    /**
     * 允许添加
     * @var array
     */
    public $add_enable = true;

    /**
     * 允许返回数据库生产的ID
     * @var array
     */
    public $add_will_return_new_id = true;


    /**
     * 允许外部添加key
     * @var array
     */
    public $add_keys = array();


    /**
     * 允许查询一个
     * @var array
     */
    public $fetch_enable = true;

    /**
     * 允许查询一个的条件
     * @var array
     */
    public $fetch_confs = array();

    /**
     * 第一个fetch_confs
     * @var array
     */
    public $fetch_by = array();


    /**
     * 启用更新
     * = if count（update_confs）>0
     *
     * @var array
     */
    public $update_enable = true;

    /**
     * 允许查询一个的条件
     * @var array
     */
    public $update_confs = array();


    /**
     * 启用删除
     * = if count（update_confs）>0
     *
     * @var array
     */
    public $delete_enable = true;

    /**
     * 允许删除一个的条件组合
     * @var array
     */
    public $delete_confs = array();

    /**
     * "delete_confs" => array(
     * "default" => array(
     * "delete_title" => "默认删除",
     * "delete_by" => "default"
     * ),
     * "account" => array(
     * "delete_title" => "痛殴账号删除",
     * "delete_by" => array("account", "passwd_en")
     * )
     */


    /**
     * 启用列表查询
     * = if count（update_confs）>0
     *
     * @var array
     */
    public $list_enable = true;

    /**
     * 列表查询的条件组合
     * @var array
     */
    public $list_confs = array();


}