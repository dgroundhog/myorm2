<?php


class MyModel extends MyStruct
{

    public function __construct()
    {
        $this->scope = "MODEL";
    }

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

    public $basic_keys = array(
        "primary_key",
        "has_ui",
        "fa_icon",
        "table_name",
    );


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
     * "uk2" => array("account")
     */
    public $unique_key = array();

    /**
     * 用作索引的key
     * @var array
     *
     * "ik1" => array("op_id"),
     * "ik2" => array("account")
     */
    public $index_key = array();


    /**
     * 预定义kv字典值的定义
     * 从myfield计算
     * @var array
     */
    public $kv_list = array();


    /**
     * 用select输入的key
     * 从myfield计算
     * @var array
     */
    public $keys_by_select = array();

    /**
     * 通过文件上传来的key
     * 从myfield计算
     * @var array
     */
    public $keys_by_upload = array();


    /**
     * 允许添加
     * @var bool
     */
    public $add_enable = true;


    /**
     * 插入的的条件
     * @var array [key=> MyFunAdd]
     */
    public $add_confs = array();


    /**
     * 允许查询一个
     * @var bool
     */
    public $fetch_enable = true;

    /**
     * 允许查询一个的条件
     * @var array [key=> MyFunFetch]
     */
    public $fetch_confs = array();


    /**
     * 启用更新
     * = if count（update_confs）>0
     *
     * @var bool
     */
    public $update_enable = true;

    /**
     * 允许查询一个的条件
     * @var array [key=> MyFunUpdate]
     */
    public $update_confs = array();


    /**
     * 启用删除
     * = if count（delete_confs）>0
     *
     * @var bool
     */
    public $delete_enable = true;

    /**
     * 允许删除一个的条件组合
     * @var array [key=> MyFunDelete]
     */
    public $delete_confs = array();

    /**
     * "delete_confs" => array(
     * "default" => array(
     * "name" => "default",
     * "title" => "默认删除",
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
     * @var bool
     */
    public $list_enable = true;

    /**
     * 列表查询的条件组合
     * @var array [key=> MyFunList]
     */
    public $list_confs = array();



    /**
     * 其他获取参数
     *
     * @param $model
     * @return array
     */
    function _model_get_ok_other_fetch_by($model)
    {
        $a_list = array();
        if (isset($model['fetch_by_other']) && is_array($model['fetch_by_other'])) {
            foreach ($model['fetch_by_other'] as $fetch_name => $a_vv) {
                $lc_kk = strtolower($fetch_name);
                if (isset($a_vv['fetch_by']) && count($a_vv['fetch_by']) > 0) {
                    $a_temp = array();
                    foreach ($a_vv['fetch_by'] as $pk_key) {
                        if (!isset($model['table_fields'][$pk_key])) {
                            continue;
                        }
                        $a_temp[] = $pk_key;
                    }
                    if (count($a_temp) > 0) {
                        //无主键
                        $a_list[$lc_kk] = array();
                        $a_list[$lc_kk]["fetch_title"] = $a_vv['fetch_title'];
                        $a_list[$lc_kk]["fetch_by"] = $a_temp;
                    }
                }
            }
        }
        return $a_list;
    }

    /**
     * 其他获取参数
     *
     * @param $model
     * @return array
     */
    function _model_get_ok_list($model)
    {
        $a_list = array();
        /**
         * 查询列表是一个大数组
         * list_confs =>
         *  ----list_name  => title
         *                  => keys
         */
        if (isset($model['list_confs']) && is_array($model['list_confs'])) {
            $a_list_group_type = _get_list_group_type();
            foreach ($model['list_confs'] as $list_key => $list_conf) {
                if (strlen($list_key) == 0) {
                    continue;
                }
                $lc_kk = strtolower($list_key);


                /**
                 * 基本查询
                 */
                $my_list = new MyFunList();
                $my_list->list_name = $list_key;
                $my_list->list_title = $list_conf['list_title'];

                if (isset($list_conf['list_has_group'])
                    && $list_conf['list_has_group'] == true
                    && isset($list_conf['list_group_type'])
                    && $list_conf['list_group_type'] != ""
                    && isset($a_list_group_type[$list_conf['list_group_type']])
                    && isset($list_conf['list_group_key'])
                    && $list_conf['list_group_key'] != ""
                    && isset($model['table_fields'][$list_conf['list_group_key']])
                ) {
                    $list_group_type = $list_conf['list_group_type'];
                    $list_group_key = $list_conf['list_group_key'];
                    //TODO
                    $my_list->list_has_group = true;
                    $my_list->list_group_type = $list_group_type;
                    $my_list->list_group_key = $list_group_key;

                    //group_key 可以空
                    if (isset($list_conf['list_group_by']) || is_array($list_conf['list_group_by'])) {
                        $a_temp = array();
                        foreach ($list_conf['list_group_by'] as $kw_key) {
                            if (!isset($model['table_fields'][$kw_key])) {
                                continue;
                            }
                            $a_temp[] = $kw_key;
                        }
                        $my_list->list_group_by = $a_temp;
                    }
                }

                //查询的字段不能为空
                if (!isset($list_conf['list_keys']) || !is_array($list_conf['list_keys'])) {
                    $my_list->list_keys = array();
                } else {
                    $a_temp = array();
                    foreach ($list_conf['list_keys'] as $kw_key) {
                        if (!isset($model['table_fields'][$kw_key])) {
                            continue;
                        }
                        $a_temp[] = $kw_key;
                    }
                    $my_list->list_keys = $a_temp;
                }

                //当选择distinct时，查询的字段不能为空
                if (count($my_list->list_keys) == 0) {
                    $my_list->is_distinct = false;
                }

                //基本索引
                if (isset($list_conf['list_by']) && count($list_conf['list_by']) > 0) {

                    $a_temp = array();
                    foreach ($list_conf['list_by'] as $kw_key) {
                        if (!isset($model['table_fields'][$kw_key])) {
                            continue;
                        }
                        $type = $model['table_fields'][$kw_key]['type'];
                        if ($type == "int" || $type == "string" || $type == "varchar" || $type == "char") {
                            $a_temp[] = $kw_key;
                        }
                    }
                    if (count($a_temp) > 0) {
                        $my_list->list_by = $a_temp;
                    }
                }

                //模糊关键字
                if (isset($list_conf['list_has_kw']) && $list_conf['list_has_kw'] == true && isset($list_conf['list_kw']) && count($list_conf['list_kw']) > 0) {
                    $a_temp = array();
                    foreach ($list_conf['list_kw'] as $kw_key) {
                        if (!isset($model['table_fields'][$kw_key])) {
                            continue;
                        }
                        $a_temp[] = $kw_key;
                    }
                    if (count($a_temp) > 0) {
                        $my_list->list_has_kw = true;
                        $my_list->list_kw = $a_temp;
                    }
                }

                $a_has_key = _php_list_get_conds();
                foreach ($a_has_key as $cond) {
                    if ($cond == "kw") {
                        //多个关键字，单独处理了
                        continue;
                    }
                    $bv = "list_has_{$cond}";
                    $kv = "list_{$cond}_key";
                    if (isset($list_conf[$bv]) && $list_conf[$bv] == true && isset($list_conf[$kv]) && $list_conf[$kv] != "") {
                        if (isset($model['table_fields'][$list_conf[$kv]])) {
                            $my_list->$bv = true;
                            $my_list->$kv = $list_conf[$kv];
                        }
                    }
                }

                //排序，默认true
                if (isset($list_conf['list_has_order']) && $list_conf['list_has_order'] == false) {
                    $my_list->list_has_order = false;
                }
                //有分页时，判断条件
                if ($my_list->list_has_order) {
                    if (isset($list_conf['list_order_by'])) {
                        if (is_array($list_conf['list_order_by'])) {
                            $a_temp[] = array();
                            foreach ($list_conf['list_order_by'] as $order_by => $order_dir) {
                                if (!isset($model['table_fields'][$order_by])) {
                                    continue;
                                }
                                $lc_order_dir = strtolower($order_dir);
                                if ($lc_order_dir != "desc") {
                                    $order_dir = "ASC";
                                } else {
                                    $order_dir = "DESC";
                                }
                                $a_temp[$order_by] = $order_dir;
                            }
                            $my_list->list_order_by = $a_temp;
                        } else {
                            //定向组合
                            if ($list_conf['list_order_by'] != "") {
                                if (isset($model['table_fields'][$list_conf['list_order_by']])) {
                                    $my_list->list_order_by = $list_conf['list_order_by'];
                                }
                            }
                            //排序
                            if (isset($list_conf['list_order_dir']) && $list_conf['list_order_dir'] != "") {
                                $lc_order_dir = strtolower($list_conf['list_order_dir']);
                                if ($lc_order_dir != "desc") {
                                    $order_dir = "ASC";
                                } else {
                                    $order_dir = "DESC";
                                }
                                $my_list->list_order_dir = $order_dir;
                            }
                        }
                    } else {
                        // order_by 和 order_dir  都需要输入
                    }
                }

                //统计数目，默认false,
                if (isset($list_conf['count_only']) && $list_conf['count_only'] == true) {
                    $my_list->count_only = true;

                    if (isset($list_conf['list_count_key']) && $list_conf['list_count_key'] != "") {
                        if (isset($model['table_fields'][$list_conf['list_count_key']])) {
                            $my_list->list_count_key = $list_conf['list_count_key'];
                        }
                    }
                }

                //分页，默认true, 不分页即获取全部
                if (isset($list_conf['list_has_page']) && $list_conf['list_has_page'] == false) {
                    $my_list->list_has_pager = false;
                }

                if ($my_list->list_has_pager == true) {
                    //默认为normal
                    $my_list->list_pager_type = "normal";
                    if (isset($list_conf['list_pager_type']) && $list_conf['list_pager_type'] == "cursor") {
                        if (isset($list_conf['cursor_offset_key']) && $list_conf['cursor_offset_key'] != "") {
                            if (isset($model['table_fields'][$list_conf['cursor_offset_key']])) {
                                $my_list->cursor_offset_key = $list_conf['cursor_offset_key'];
                                $my_list->list_pager_type = "cursor";
                            }
                        }
                    }
                }


                //子列表
                $my_list->sub_list = array();
                if (isset($list_conf['sub_list']) && is_array($list_conf['sub_list']) && count($list_conf['sub_list']) > 0) {
                    foreach ($list_conf['sub_list'] as $sub_list_name => $sub_list_conf) {
                        $sub_lc_list_name = strtolower($sub_list_name);
                        $my_list->sub_list[$sub_lc_list_name] = $sub_list_conf;
                    }
                }

                $a_list[$lc_kk] = $my_list;
            }
        }

        return $a_list;
    }


    /**
     * 整理的更新的条件
     * @param array $model
     * @return array
     */
    function _model_get_ok_update($model)
    {

        $a_list = array();
        /**
         * 其他可能的主键查询单条语句
         * update_confs=>
         *  ----update_name  => title
         *                  => keys
         */
        if (isset($model['update_confs']) && is_array($model['update_confs'])) {

            foreach ($model['update_confs'] as $update_name => $a_update_conf) {
                if (strlen($update_name) == 0) {
                    continue;
                }
                $lc_kk = strtolower($update_name);


                $a_update_keys_tobe = $a_update_conf['update_keys'];//更新的内容
                $a_update_by_tobe = $a_update_conf['update_by'];//更新依据

                //需要更新的不能为空
                $a_temp = array();
                foreach ($a_update_keys_tobe as $pk_key) {
                    if (!isset($model['table_fields'][$pk_key])) {
                        continue;
                    }
                    $a_temp[] = $pk_key;
                }
                if (count($a_temp) == 0) {
                    //无主键
                    continue;
                }
                $s_update_keys = $a_temp;

                //更新依据的可以为空
                if (null == $a_update_by_tobe || !is_array($a_update_by_tobe)) {
                    $a_update_by_tobe = $model['fetch_by'];
                }
                $a_temp = array();
                foreach ($a_update_by_tobe as $pk_key) {
                    if (!isset($model['table_fields'][$pk_key])) {
                        continue;
                    }
                    $a_temp[] = $pk_key;
                }
                $a_list[$lc_kk] = array();
                $a_list[$lc_kk]["update_keys"] = $s_update_keys;
                $a_list[$lc_kk]["update_by"] = $a_temp;
                $a_list[$lc_kk]['update_title'] = $a_update_conf['update_title'];////更新的标题

                //等于0时不限制
                if (!isset($a_update_conf["limit"]) || $a_update_conf["limit"] < 0) {
                    $a_list[$lc_kk]["limit"] = 1;
                } else {
                    $a_list[$lc_kk]["limit"] = $a_update_conf["limit"];
                }

            }
        }

        if (isset($model["table_fields"]["flag"]) && !isset($model['update_confs']["flag"])) {
            $a_update_by = $model['fetch_by'];
            $a_temp = array();
            foreach ($a_update_by as $pk_key) {
                if (!isset($model['table_fields'][$pk_key])) {
                    continue;
                }
                $a_temp[] = $pk_key;
            }
            if (count($a_temp) > 0) {
                $lc_kk = "flag";
                $a_list[$lc_kk] = array();
                $a_list[$lc_kk]["update_keys"] = array("flag");
                $a_list[$lc_kk]["update_by"] = $a_temp;
                $a_list[$lc_kk]['update_title'] = "更新flag用作逻辑删除";
                $a_list[$lc_kk]["limit"] = 1;
            }
        }

        return $a_list;
    }


    /**
     * 整理的删除的条件
     * @param array $model
     * @return array
     */
    function _model_get_ok_delete($model)
    {
        $a_list = array();
        if (isset($model['delete_confs']) && is_array($model['delete_confs'])) {

            foreach ($model['delete_confs'] as $delete_name => $a_delete_conf) {
                $lc_kk = strtolower($delete_name);
                $a_delete_by = $a_delete_conf['delete_by'];

                //默认删除依据的不能为空
                if (null == $a_delete_by || !is_array($a_delete_by)) {
                    $a_delete_by = $model['fetch_by'];
                }

                $a_temp = array();
                foreach ($a_delete_by as $pk_key) {
                    if (!isset($model['table_fields'][$pk_key])) {
                        continue;
                    }
                    $a_temp[] = $pk_key;
                }
                //允许删除全部
                $a_list[$lc_kk] = array();
                $a_list[$lc_kk]["delete_by"] = $a_temp;
                $a_list[$lc_kk]['delete_title'] = $a_delete_conf['delete_title'];

                //等于0时不限制
                if (!isset($a_delete_conf["limit"]) || $a_delete_conf["limit"] < 0) {
                    $a_list[$lc_kk]["limit"] = 1;
                } else {
                    $a_list[$lc_kk]["limit"] = $a_delete_conf["limit"];
                }

            }
        }
        return $a_list;
    }


}