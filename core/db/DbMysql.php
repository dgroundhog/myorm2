<?php
if (!defined("DB_ROOT")) {
    define('DB_ROOT', realpath(dirname(__FILE__)));
}

include_once(DB_ROOT . "/DbBase.php");

class DbMysql extends DbBase
{


    /**
     * 创建初始化结构
     * @param MyDb $db
     * @return mixed|void
     */
    function ccInitDb()
    {
        $db = $this->db_conf;
        _db_comment_begin();
        _db_comment("Init mysql user {$db->user} and database {$db->database}");
        _db_comment("You should run this As super user");
        _db_comment_end();


        switch ($db->driver) {
            case Constant::DB_MYSQL_56:
            case Constant::DB_MYSQL_57:
                _db_comment("for mysql {$db->driver}", true);
                echo "CREATE USER IF NOT EXISTS '{$db->user}'@'{$db->host}' IDENTIFIED BY '{$db->password}';\n";
                echo "CREATE DATABASE IF NOT EXISTS `{$db->database}` CHARACTER SET {$db->charset} COLLATE {$db->charset}_general_ci;\n";
                echo "GRANT ALL PRIVILEGES ON `{$db->user}\\_%`.* TO '{$db->user}'@'{$db->host}';\n";
                echo "GRANT SELECT ON mysql.proc TO '{$db->user}'@'{$db->host}';\n";
                break;
            case Constant::DB_MYSQL_80:
                _db_comment("for mysql 8.0 not finished yet;", true);
                break;
            default:

        }

        echo "FLUSH PRIVILEGES;\n";

        _db_comment("for events", true);
        echo "SET GLOBAL event_scheduler = ON;\n";

        _db_comment("for functions", true);
        _db_comment("TODO");

    }

    /**
     *
     * @inheritDoc
     * @param MyModel $model
     * @return mixed|void
     */
    function ccTable(MyModel $model)
    {
        $i_field_size = count($model->field_list);

        _db_comment_begin();
        _db_comment("Table structure for t_{$model->table_name} [{$model->title}]");
        _db_comment("Table fields count ({$i_field_size})");
        _db_comment_end();

        if ($i_field_size == 0) {
            _db_comment("Is is a empty Table");
            return;
        }

        echo "CREATE TABLE `t_{$model->table_name}`\n(\n";

        $ii = 0;
        $has_primary_key = false;//判断主键和ID的区别
        $uq_auto_increment = "";//判断自整主键
        $a_temp = array();
        $a_field_keys = array();//用来过滤索引的有效性

        //过滤重复的inc主键
        $uq_inc_key = false;
        foreach ($model->field_list as $field) {
            /* @var MyField $field */
            if ($uq_inc_key == true && $field->auto_increment) {
                $field->auto_increment = false;
            }
            if ($field->auto_increment) {
                $uq_inc_key = true;
            }
        }

        foreach ($model->field_list as $field) {
            $ii++;
            /* @var MyField $field */
            $key = $field->name;
            $a_field_keys[] = $key;

            $size = $field->size;
            $comment = $field->title;
            //对于自增和主键，强制修改required的值
            if ($key == $model->primary_key) {
                $has_primary_key = true;
            }

            $type = $field->type;
            $after_null = "";
            $type_size = "VARCHAR(255)";
            switch ($type) {
                case Constant::DB_FIELD_TYPE_BOOL :
                    $type_size = "CHAR(1)";
                    break;
                //整型
                case Constant::DB_FIELD_TYPE_INT:
                    if ($size < 1 || $size > 255) {
                        $size = 11;
                    }
                    $type_size = "INT({$size})";
                    if ($field->auto_increment) {
                        $uq_auto_increment = $key;
                        $after_null = "AUTO_INCREMENT";
                    }
                    break;
                case Constant::DB_FIELD_TYPE_LONGINT:
                    $type_size = "BIGINT";
                    if ($field->auto_increment) {
                        $uq_auto_increment = $key;
                        $after_null = "AUTO_INCREMENT";
                    }
                    break;

                //单个字符
                case Constant::DB_FIELD_TYPE_CHAR:

                    if ($size < 1 || $size > 255) {
                        $size = 1;
                    }
                    $type_size = "CHAR({$size})";
                    break;

                //字符串
                case Constant::DB_FIELD_TYPE_VARCHAR:
                    if ($size < 1 || $size > 9999) {
                        $size = 255;
                    }
                    $type_size = "VARCHAR({$size})";
                    break;

                case Constant::DB_FIELD_TYPE_TEXT :
                    $type_size = "TEXT";
                    break;
                case Constant::DB_FIELD_TYPE_LONGTEXT :
                    $type_size = "LONGTEXT";
                    break;

                case Constant::DB_FIELD_TYPE_BLOB :
                    $type_size = "BLOB";
                    break;
                case Constant::DB_FIELD_TYPE_LONGBLOB :
                    $type_size = "LONGBLOB";
                    break;

                case Constant::DB_FIELD_TYPE_DATE :
                    $type_size = "DATE";
                    break;
                case Constant::DB_FIELD_TYPE_TIME :
                    $type_size = "TIME";
                    break;
                case Constant::DB_FIELD_TYPE_DATETIME :
                    $type_size = "DATETIME";
                    break;
                //默认为255的字符串
                default :
                    break;
            }
            $a_temp[] = "`{$key}` {$type_size} NOT NULL {$after_null} COMMENT '{$comment}'";
        }

        if ($has_primary_key) {
            if ($uq_auto_increment != null) {
                $a_temp[] = "PRIMARY KEY (`{$uq_auto_increment}`)";
            } else {
                $a_temp[] = "PRIMARY KEY (`{$model->primary_key}`)";
            }
        }

        $ii = 0;
        foreach ($model->idx_list as $o_index) {

            /* @var MyIndex $o_index */
            $a_temp0 = array();
            $idx_type = $o_index->type;
            $jj = 0;
            foreach ($o_index->field_list as $field) {
                $ii++;
                /* @var MyField $field */
                $key = $field->name;
                if (in_array($key, $a_field_keys)) {
                    $jj++;
                    $a_temp0[] = "`{$key}`";
                }
            }
            if ($jj > 0) {
                $ii++;
                $s_idx_type = ($idx_type == Constant::DB_INDEX_TYPE_UNIQUE) ? "UNIQUE uk" : "KEY ik";
                $s_temp0 = implode(", ", $a_temp0);
                $a_temp[] = "{$s_idx_type}_{$model->name}_{$ii} ({$s_temp0})";
            }
        }

        echo _tab(1);
        echo implode(",\n" . _tab(1), $a_temp);
        echo "\n)";

        $charset = $this->db_conf->charset;
        if ("" == $charset || !in_array($charset, Constant::$a_db_charset)) {
            $charset = Constant::DB_CHARSET_UTF8MB4;
        }

        echo "ENGINE=InnoDB\n";
        echo "DEFAULT CHARSET={$charset}\n";
        echo "COLLATE {$charset}_GENERAL_CI\n";
        echo "COMMENT='{$model->title}表';";
    }

    /**
     * @inheritDoc
     */
    function ccTable_reset(MyModel $model)
    {

        _db_comment("Delete Table   t_{$model->table_name}", true);
        echo "DROP TABLE IF EXISTS `t_{$model->table_name}`;\n";
    }

    /**
     * @inheritDoc
     */
    function ccProc(MyModel $model)
    {

        $a_all_fields = array();//转换用name作为主键
        foreach ($model->field_list as $field) {
            /* @var MyField $field */
            $key = $field->name;
            $a_all_fields[$key] = $field;
        }
        if (count($a_all_fields) == 0) {
            return;
        }
        $model->field_list_kv = $a_all_fields;

        foreach ($model->fun_list as $o_fun) {

            /* @var MyFun $o_fun */
            $fun_type = $o_fun->type;

            switch ($fun_type) {
                case Constant::FUN_TYPE_ADD:
                    $this->cAdd($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_DELETE:
                    $this->cDelete($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_UPDATE:
                    $this->cUpdate($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_FETCH:
                    $this->cFetch($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_LIST_WITH_COUNT:
                case Constant::FUN_TYPE_LIST_WITH_AVG:
                case Constant::FUN_TYPE_LIST_WITH_SUM:
                case Constant::FUN_TYPE_LIST_WITH_MAX:
                case Constant::FUN_TYPE_LIST_WITH_MIN:
                case Constant::FUN_TYPE_LIST:
                default:
                    $this->cList($model, $o_fun);
                    if ($o_fun->pager_enable) {
                        $this->cList($model, $o_fun, true);
                    }
                    break;
            }
        }


    }

    /**
     * @inheritDoc
     * 创建存储过程-添加
     * @param MyModel $model
     * @param MyFun $fun
     */
    function cAdd(MyModel $model, MyFun $fun)
    {
        $proc_name = $this->_procHeader($model, $fun->name, $fun->title, "add");
        $a_all_fields = $model->field_list_kv;
        $ii = 0;
        $a_temp = array();
        $add_key_by_input = array();
        $a_field_add = $fun->field_list;
        if ($fun->all_field == 1) {
            $a_field_add = $model->field_list;
        }
        //制作参数
        $return_new_id = false;
        foreach ($a_field_add as $field) {
            /* @var MyField $field */
            $key = $field->name;
            if (!isset($a_all_fields[$key])) {
                continue;
            }
            //如果id也是自inc的，也不用输入了
            if ($key == 'id' && $field->auto_increment = 1) {
                $return_new_id = true;
                continue;
            }
            $ii++;
            $add_key_by_input[] = $key;
            list($param_key, $param_key2) = $this->_procParam($field, $ii);
            $a_temp[] = $param_key2;
        }
        if ($return_new_id) {
            $ii++;
            $a_temp[] = "INOUT `v_new_id` INT";
        }
        $this->_procBegin($a_temp);
        echo "DECLARE m_new_id INT;\n";
        echo "INSERT INTO `t_{$model->table_name}` \n(\n";

        $ii = 0;
        $a_temp = array();
        foreach ($a_all_fields as $key => $field) {
            if ($key == "id" && $field->auto_increment = 1) {
                continue;
            }
            $ii++;
            $a_temp[] = "`{$key}`";
        }
        echo _tab(1);
        echo implode(",\n" . _tab(1), $a_temp);
        echo "\n) \nVALUES\n(\n";

        $ii = 0;
        $a_temp = array();
        foreach ($a_all_fields as $key => $field) {
            if ($key == "id" && $field->auto_increment = 1) {
                continue;
            }

            /* @var MyField $field */
            if (!in_array($key, $add_key_by_input)) {
                //部分预置值
                switch ($key) {
                    case "flag":
                        $a_temp[] = "'N'";
                        break;

                    case "state":
                        if ($field->default_value != "") {
                            if ($field->type == Constant::DB_FIELD_TYPE_INT || $field->type == Constant::DB_FIELD_TYPE_LONGINT) {
                                $a_temp[] = "{$field->default_value}";
                            } else {
                                $a_temp[] = "'{$field->default_value}'";
                            }
                        } else {
                            if ($field->type == Constant::DB_FIELD_TYPE_INT || $field->type == Constant::DB_FIELD_TYPE_LONGINT) {
                                $a_temp[] = "0";
                            } else {
                                $a_temp[] = "'N'";
                            }
                        }
                        break;

                    case "ctime":
                    case "utime":
                        $a_temp[] = "NOW()";
                        break;

                    default:
                        if ($field->default_value != "") {
                            if ($field->type == Constant::DB_FIELD_TYPE_INT || $field->type == Constant::DB_FIELD_TYPE_LONGINT) {
                                $a_temp[] = "{$field->default_value}";
                            } else {
                                $a_temp[] = "'{$field->default_value}'";
                            }
                        } else {
                            $a_temp[] = "''";
                        }
                        break;
                }
            } else {
                $ii++;
                list($param_key, $param_key2) = $this->_procParam($field, $ii);
                $a_temp[] = "`{$param_key}`";
            }
        }
        echo _tab(1);
        echo implode(",\n" . _tab(1), $a_temp);

        echo "\n);\n";

        echo "SET m_new_id = LAST_INSERT_ID();\n";
        //echo "COMMIT;\n";
        echo "SET @s_new_id = CONCAT('', m_new_id);\n";

        echo "CALL p_debug('{$proc_name}', @s_new_id);\n";
        if ($return_new_id) {
            echo "SELECT m_new_id INTO v_new_id;\n";
            echo "SELECT m_new_id AS i_new_id;\n";
        }

        self::_procEnd($model, $proc_name);
    }

    /**
     * 公用存储过程头头
     * @param MyModel $model
     * @param string $fun_name
     * @param string $fun_title
     * @param string $base_fun
     * @return string
     */
    function _procHeader($model, $fun_name, $fun_title, $base_fun)
    {

        if ($fun_name != "" && $fun_name != "default" && $fun_name != $base_fun) {
            $fun = "{$base_fun}_{$fun_name}";
        } else {
            $fun = $base_fun;
        }

        $real_fun = "p_{$model->table_name}_{$fun}";

        _db_comment_begin();
        _db_comment("Procedure structure for {$real_fun}");
        _db_comment("Desc : {$fun_title}");
        _db_comment_end();

        $user = $this->db_conf->user;
        $host = $this->db_conf->host;

        echo "DROP PROCEDURE IF EXISTS `{$real_fun}`;\n";
        echo "delimiter ;;\n";
        echo "CREATE DEFINER=`{$user}`@`{$host}` PROCEDURE `{$real_fun}`";
        echo "(";
        return $real_fun;
    }

    /**
     * 处理参数
     * XXX 不考虑小数,如果是金钱，用分做单位
     *
     * @param MyField $o_field
     * @param string $idx_append 避免重复的计数器
     * @param string $append u/w  update or where
     *
     * @return string
     */
    function _procParam($o_field, $idx_append = 0, $append = "", $for_hash = false)
    {

        $charset = $this->db_conf->charset;
        if ("" == $charset) {
            $charset = "utf8mb4";
        }
        $key = $o_field->name;
        $type = $o_field->type;
        //i_w_1_key
        //c_w_2_from_key
        //s_w_3_to_key
        if (!$for_hash) {
            $prefix = self::_procKeyPrefix($type);
            if ($append != "") {
                $prefix = "{$prefix}_{$append}";
            }
            $param_key = "v_{$idx_append}_{$prefix}_{$key}";

            $has_charset = true;
            $size = $o_field->size;
            $type_size = "VARCHAR(255)";
            switch ($type) {
                case Constant::DB_FIELD_TYPE_BOOL :
                    $type_size = "CHAR(1)";
                    break;
                //整型
                case Constant::DB_FIELD_TYPE_INT:
                    if ($size < 1 || $size > 255) {
                        $size = 11;
                    }
                    $type_size = "INT({$size})";
                    $has_charset = false;
                    break;
                case Constant::DB_FIELD_TYPE_LONGINT:
                    $type_size = "BIGINT";
                    $has_charset = false;
                    break;

                //单个字符
                case Constant::DB_FIELD_TYPE_CHAR:

                    if ($size < 1 || $size > 255) {
                        $size = 1;
                    }
                    $type_size = "CHAR({$size})";
                    break;

                //字符串
                case Constant::DB_FIELD_TYPE_VARCHAR:
                    if ($size < 1 || $size > 9999) {
                        $size = 255;
                    }
                    $type_size = "VARCHAR({$size})";
                    break;

                case Constant::DB_FIELD_TYPE_TEXT :
                    $type_size = "TEXT";
                    break;
                case Constant::DB_FIELD_TYPE_LONGTEXT :
                    $type_size = "LONGTEXT";
                    break;

                case Constant::DB_FIELD_TYPE_BLOB :
                    $type_size = "BLOB";
                    $has_charset = false;
                    break;
                case Constant::DB_FIELD_TYPE_LONGBLOB :
                    $type_size = "LONGBLOB";
                    $has_charset = false;
                    break;

                case Constant::DB_FIELD_TYPE_DATE :
                    $type_size = "DATE";
                    $type_size = "VARCHAR(10)";
                    break;
                case Constant::DB_FIELD_TYPE_TIME :
                    $type_size = "DATE";
                    $type_size = "VARCHAR(8)";
                    break;
                case Constant::DB_FIELD_TYPE_DATETIME :
                    $type_size = "DATETIME";
                    $type_size = "VARCHAR(19)";
                    break;
                //默认为255的字符串
                default :
                    break;
            }
        } else {
            //准备离散函数
            $has_charset = true;
            $param_key = "v_{$idx_append}_s_{$key}";
            $type_size = "VARCHAR(9999)";
        }
        if ($has_charset) {
            return array(
                $param_key,
                "IN `{$param_key}` {$type_size} CHARSET {$charset}"
            );
        }
        return array(
            $param_key,
            "IN `{$param_key}` {$type_size}"
        );
    }


    /**
     * 获取参数的前缀
     *
     * @param string $field_type
     * @return string
     */
    function _procKeyPrefix($field_type)
    {

        switch ($field_type) {
            //bool
            case Constant::DB_FIELD_TYPE_BOOL :
                return "b";

            //整型
            case Constant::DB_FIELD_TYPE_INT:
            case Constant::DB_FIELD_TYPE_LONGINT:
                return "i";

            case Constant::DB_FIELD_TYPE_BLOB :
            case Constant::DB_FIELD_TYPE_LONGBLOB :
                return "lb";

            //日期时间
            case Constant::DB_FIELD_TYPE_DATE :
            case Constant::DB_FIELD_TYPE_TIME :
            case Constant::DB_FIELD_TYPE_DATETIME :
                return "dt";

            //字符串
            case Constant::DB_FIELD_TYPE_CHAR:
            case Constant::DB_FIELD_TYPE_VARCHAR:
            case Constant::DB_FIELD_TYPE_TEXT :
            case Constant::DB_FIELD_TYPE_LONGTEXT :
            default :
                return "s";
        }


    }

    /**
     * 存储过程的参数
     * @param array $a_param
     */
    function _procBegin($a_param)
    {
        if (is_array($a_param)) {
            if (count($a_param) > 1) {
                echo "\n";
                echo _tab(1);
                echo implode(",\n" . _tab(1), $a_param);
                echo "\n";
            }
            if (count($a_param) == 1) {
                echo $a_param[0];
            }
        } else {
            if ($a_param != "") {
                echo "\n";
                echo _tab(1);
                echo $a_param;
                echo "\n";
            }
        }

        echo ")\n";
        echo "BEGIN\n";
    }

    /**
     * 公共存储过程尾巴
     * @param MyModel $model
     * @param string $proc_name
     * @return void
     */
    static function _procEnd($model, $proc_name)
    {
        echo "END;\n";
        echo ";;\n";
        echo "delimiter ;\n";
        _db_comment("End structure for {$proc_name}");
        echo "\n";
    }

    /**
     * @inheritDoc
     * 创建存储过程-删除
     * @param MyModel $model
     */
    function cDelete(MyModel $model, MyFun $o_fun)
    {

        $proc_name = self::_procHeader($model, $o_fun->name, $o_fun->title, "delete");
        // $a_all_fields = $model->field_list_kv;
        $limit = $o_fun->limit;//更新限制

        list($_param, $_sql1, $_sql2) = $this->_procWhereCond($model, $o_fun);
        if ($_param != "") {
            $_param = $_param . ",\n" . _tab(1) . "INOUT `v_affected_rows` INT";
        } else {
            $_param = "INOUT `v_affected_rows` INT";
        }
        self::_procBegin($_param);


        echo "DECLARE m_affected_rows INT;\n";
        echo "DECLARE s_affected_rows VARCHAR(12);\n";
        echo "DELETE FROM `t_{$model->table_name}` WHERE ";
        echo $_sql1;
        if ($limit > 0) {
            echo "\n";
            echo "LIMIT {$limit};\n";
        } else {
            echo ";\n";
        }
        echo "SET m_affected_rows = ROW_COUNT();\n";
        //echo "COMMIT;\n";
        echo "SET s_affected_rows = CONCAT( 'deleted_rows--' , m_affected_rows);\n";
        echo "CALL p_debug('{$proc_name}', s_affected_rows);\n";
        echo "SELECT m_affected_rows INTO v_affected_rows;\n";
        echo "SELECT m_affected_rows AS i_affected_rows;\n";

        self::_procEnd($model, $proc_name);
    }


    /**
     * 条件的输入参数
     * @param MyModel $model
     * @param MyFun $o_fun
     */
    function _procWhereCond($model, $o_fun)
    {

        $s_param = "";
        $s_sql1 = "";
        $s_sql2 = "";

        $jj = 0;
        if ($o_fun->where != null) {
            $a_param = array();//
            $where_joiner = $o_fun->where->type;
            $cond_list = $o_fun->where->cond_list;
            $where_list = $o_fun->where->where_list;
            if ($where_joiner == Constant::WHERE_JOIN_AND) {
                $s_sql1 = " 1=1 ";
                $s_sql2 = "SET @s_sql = CONCAT( @s_sql, ' {$s_sql1} ');\n";
            } else {
                $s_sql1 = " 1=0 ";
                $s_sql2 = "SET @s_sql = CONCAT( @s_sql, ' {$s_sql1} ');\n";
            }
            foreach ($cond_list as $cond) {
                $jj++;
                list($_param, $_sql1, $_sql2) = $this->_procWhereOneCond(2, $jj, $model, $cond, $where_joiner);
                if ($_param != "") {
                    $a_param[] = $_param;
                }
                if ($_sql1 != "") {
                    $s_sql1 = $s_sql1 . "\n" . $_sql1;
                }
                if ($_sql2 != "") {
                    $s_sql2 = $s_sql2 . "\n" . $_sql2;
                }
                //TODO
            }
            foreach ($where_list as $where2) {

                if ($where2 != null) {

                    $where_joiner2 = $where2->type;
                    $cond_list2 = $where2->cond_list;

                    if (count($cond_list2) == 0) {
                        continue;
                    }

                    $ss_sql1 = "";
                    if ($where_joiner2 == Constant::WHERE_JOIN_AND) {
                        $ss_sql1 = $where_joiner . "( 1=1 ";
                        $s_sql2 = $s_sql2 . "\n" . "SET @s_sql = CONCAT( @s_sql, ' {$where_joiner} ( 1=1  ');\n";
                    } else {
                        $ss_sql1 = $where_joiner . "( 1=0 ";
                        $s_sql2 = $s_sql2 . "\n" . "SET @s_sql = CONCAT( @s_sql, ' {$where_joiner} ( 1=0  ');\n";
                    }

                    foreach ($cond_list2 as $cond) {

                        $jj++;
                        list($_param, $_sql1, $_sql2) = $this->_procWhereOneCond(3, $jj, $model, $cond, $where_joiner2);
                        if ($_param != "") {
                            $a_param[] = $_param;
                        }
                        if ($_sql1 != "") {
                            $ss_sql1 = $ss_sql1 . "\n" . $_sql1;
                        }
                        if ($_sql2 != "") {
                            $s_sql2 = $s_sql2 . "\n" . $_sql2;
                        }
                    }

                    $s_sql1 = $s_sql1 . "\n" . $ss_sql1 . ")";
                    $s_sql2 = $s_sql2 . "\n" . ")";
                }
            }
            $s_param = implode(",\n" . _tab(1), $a_param);
        }
        return array($s_param, $s_sql1, $s_sql2);
    }

    /**
     * 解析一个返回3个字符串
     * - 参数结构
     * - 直接的sql语句
     * - 拼接的sql语句
     *
     * @param $tab_idx
     * @param $inc
     * @param MyModel $model
     * @param MyCond $cond
     * @param string $WHERE_JOIN // "AND"  or
     * @return string|void
     */
    function _procWhereOneCond($tab_idx, $inc, MyModel $model, MyCond $cond, $WHERE_JOIN)
    {

        $field = $model->field_list[$cond->field];
        $field_type = $field->type;
        $key = $field->name;
        $cond_type = $cond->type;
        $v1_type = $cond->v1_type;
        $v2_type = $cond->v2_type;
        //SeasLog::debug($cond->uuid."---".$cond_type."---".$v1_type."---".$v2_type);
        $v1 = $cond->v1;
        $v2 = $cond->v2;

        switch ($cond_type) {
            case Constant::COND_TYPE_EQ:// = "EQ";//= 等于
            case Constant::COND_TYPE_NEQ:// = "NEQ";//!= 不等于
            case Constant::COND_TYPE_GT:// = "GT";//&GT; 大于
            case Constant::COND_TYPE_GTE:// = "GTE";//&GT;= 大于等于
            case Constant::COND_TYPE_LT:// = "LT";//&LT; 少于
            case Constant::COND_TYPE_LTE:// = "LTE";//&LT;= 少于等于
                return $this->_procWhere_V1($tab_idx, $inc, $WHERE_JOIN, $field, $cond_type, $v1_type, $v1);

            case Constant::COND_TYPE_DATE:    // = "DATE";//关键字模糊匹配
            case Constant::COND_TYPE_TIME:    // = "TIME";//日期范围内
            case Constant::COND_TYPE_DATETIME:    // = "TIME";//日期范围内
            case Constant::COND_TYPE_BETWEEN: // = "BETWEEN";//标量范围内
            case Constant::COND_TYPE_NOTBETWEEN: // = "NOTBETWEEN";//标量范围外
                return $this->_procWhere_V2($tab_idx, $inc, $WHERE_JOIN, $field, $cond_type, $v1_type, $v1, $v2_type, $v2);

            case Constant::COND_TYPE_IN:// = "IN";//离散量范围内
            case Constant::COND_TYPE_NOTIN:// = "NOTIN";//离散量范围外
                return $this->_procWhere_V_range($tab_idx, $inc, $WHERE_JOIN, $field, $cond_type, $v1_type, $v1);

            case Constant::COND_TYPE_KW:// = "KW";//关键字模糊匹配
                return $this->_procWhere_V_like($tab_idx, $inc, $WHERE_JOIN, $field, $cond_type, $v1_type, $v1);
                break;
            default:
                return array("", "", "");
                break;
        }
    }

    /**
     * 用来拼接的
     * 用来拼接的 int or notin
     * - 参数结构
     * - 直接的sql语句
     * - 拼接的sql语句
     *
     * @param $tab_idx
     * @param $inc
     * @param $WHERE_JOIN
     * @param $o_field
     * @param $v_cond
     * @param $v_type
     * @param $val
     * @return string|string[]
     */
    function _procWhere_V1($tab_idx, $inc, $WHERE_JOIN, $o_field, $v_cond, $v_type, $val)
    {
        if (!isset(Constant::$a_cond_type_on_sql_1[$v_cond])) {
            SeasLog::error("known cond_type1 to proc");
            return;
        }
        $s_cond = Constant::$a_cond_type_on_sql_1[$v_cond];

        $s_param1 = "";
        $s_param2 = "";
        $s_sql1 = "";
        $s_sql2 = "";

        $f_type = $o_field->type;
        $key = $o_field->name;
        $has_if = false;

        //输入值
        if ($v_type == Constant::COND_VAl_TYPE_INPUT) {
            list($param_key, $param_key2) = $this->_procParam($o_field, $inc, "w");
            $s_param1 = $param_key;
            $s_param2 = $param_key2;
            $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} {$s_param1}";
            $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} {$s_param1}'";
            $has_if = true;
        }
        //固定值
        if ($v_type == Constant::COND_VAl_TYPE_FIXED) {
            $s_param2 = "";
            if ($f_type == Constant::DB_FIELD_TYPE_INT || $f_type == Constant::DB_FIELD_TYPE_LONGINT) {
                $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} {$val}";
                $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} {$val}'";
            } else {
                $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} '{$val}'";
                $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} \'{$val}\''";
            }
        }
        //函数
        if ($v_type == Constant::COND_VAl_TYPE_FUN) {
            $s_param2 = "";
            $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} {$val}()";
            $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} {$val}()'";
        }

        if ($s_sql1 != "") {
            $s_sql1 = _tab($tab_idx) . $s_sql1;
        }

        if ($has_if) {
            if ($f_type == Constant::DB_FIELD_TYPE_INT || $f_type == Constant::DB_FIELD_TYPE_LONGINT) {
                $s_sql3 = _tab($tab_idx) . "IF {$s_param1} != '' THEN\n";
                $s_sql3 = $s_sql3 . _tab($tab_idx + 1) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
                $s_sql3 = $s_sql3 . _tab($tab_idx) . "END IF;\n";
            } else {
                $s_sql3 = _tab($tab_idx) . "IF {$s_param1} != -1 THEN\n";
                $s_sql3 = $s_sql3 . _tab($tab_idx + 1) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
                $s_sql3 = $s_sql3 . _tab($tab_idx) . "END IF;\n";
            }
            $s_sql2 = $s_sql3;
        } else {
            $s_sql2 = _tab($tab_idx) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
        }

        return array($s_param2, $s_sql1, $s_sql2);
    }

    /**
     * @inheritDoc
     * 创建存储过程-查询多个、聚合、统计
     * @param MyModel $model
     */

    /**
     * 用来拼接的， 双函数结构
     * 不能忽略
     * 用来拼接的 int or notin
     * - 参数结构
     * - 直接的sql语句
     * - 拼接的sql语句
     *
     * @param $tab_idx
     * @param $inc
     * @param $WHERE_JOIN
     * @param $o_field
     * @param $v_cond
     * @param $v1_type
     * @param $val1
     * @param $v2_type
     * @param $val2
     * @return string
     */
    function _procWhere_V2($tab_idx, $inc, $WHERE_JOIN, $o_field, $v_cond, $v1_type, $val1, $v2_type, $val2)
    {

        //SeasLog::info("_procWhere_V2");
        if (!isset(Constant::$a_cond_type_on_sql_2[$v_cond])) {
            SeasLog::error("unknown cond_type2 to proc");
            return;
        }
        $s_cond = Constant::$a_cond_type_on_sql_2[$v_cond];

        $f_type = $o_field->type;
        $key = $o_field->name;

        $s_param = "";
        $s_param_key1_join= "";
        $s_param_key1_input= "";
        $s_param_key2_join= "";
        $s_param_key2_input= "";

        $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} (";
        $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} (";
        SeasLog::info($s_sql2);
        $has_if = false;
        //v1输入值
        if ($v1_type == Constant::COND_VAl_TYPE_INPUT) {
            list($param_key_join, $param_key_input) = $this->_procParam($o_field, $inc, "from");
            $s_param_key1_join = $param_key_join;
            $s_param_key1_input = $param_key_input;


            $s_sql1 = $s_sql1 . " {$s_param_key1_join} , ";
            $s_sql2 = $s_sql2 . " {$s_param_key1_join} , ";
            SeasLog::info($v1_type . "---" . $v2_type);
            //v2输入值
            if ($v2_type == Constant::COND_VAl_TYPE_INPUT) {
                list($param_key_join, $param_key_input) = $this->_procParam($o_field, $inc, "to");

                $s_param_key2_join = $param_key_join;
                $s_param_key2_input = $param_key_input;

                $s_param = "{$s_param_key1_input},\n"._tab($tab_idx)."{$s_param_key2_input}";
                $s_sql1 = $s_sql1 . " {$s_param_key2_join} ";
                $s_sql2 = $s_sql2 . " {$s_param_key2_join} ";
                $has_if = true;
            }
            //v2固定值
            if ($v2_type == Constant::COND_VAl_TYPE_FIXED) {
                $s_param = "{$s_param_key1_input}";
                if ($f_type == Constant::DB_FIELD_TYPE_INT || $f_type == Constant::DB_FIELD_TYPE_LONGINT) {
                    $s_sql1 = $s_sql1 . " {$val2} ";
                    $s_sql2 = $s_sql2 . " {$val2} ";

                } else {
                    $s_sql1 = $s_sql1 . " \'{$val2}\' ";
                    $s_sql2 = $s_sql2 . " \'{$val2}\' ";
                }
            }
            //v2函数
            if ($v2_type == Constant::COND_VAl_TYPE_FUN) {
                $s_param = "{$s_param_key1_input}";
                $s_sql1 = $s_sql1 . " {$val2}() ";
                $s_sql2 = $s_sql2 . " {$val2}() ";
            }

        }
        //v1固定值
        if ($v1_type == Constant::COND_VAl_TYPE_FIXED) {
            if ($f_type == Constant::DB_FIELD_TYPE_INT || $f_type == Constant::DB_FIELD_TYPE_LONGINT) {
                $s_sql1 = $s_sql1 . " {$val1} ,";
                $s_sql2 = $s_sql2 . " {$val1} ,";

            } else {
                $s_sql1 = $s_sql1 . " \'{$val1}\' ,";
                $s_sql2 = $s_sql2 . " \'{$val1}\' ,";
            }

            //v2输入值
            if ($v2_type == Constant::COND_VAl_TYPE_INPUT) {
                list($param_key_join, $param_key_input) = $this->_procParam($o_field, $inc, "to");

                $s_param_key2_join = $param_key_join;
                $s_param_key2_input = $param_key_input;
                $s_param = "{$s_param_key2_input}";

                $s_sql1 = $s_sql1 . " {$s_param_key2_join} ";
                $s_sql2 = $s_sql2 . " {$s_param_key2_join} ";
            }
            //v2固定值
            if ($v2_type == Constant::COND_VAl_TYPE_FIXED) {
                if ($f_type == Constant::DB_FIELD_TYPE_INT || $f_type == Constant::DB_FIELD_TYPE_LONGINT) {
                    $s_sql1 = $s_sql1 . " {$val2} ";
                    $s_sql2 = $s_sql2 . " {$val2} ";

                } else {
                    $s_sql1 = $s_sql1 . " \'{$val2}\' ";
                    $s_sql2 = $s_sql2 . " \'{$val2}\' ";
                }
            }
            //v2函数
            if ($v2_type == Constant::COND_VAl_TYPE_FUN) {
                $s_sql1 = $s_sql1 . " {$val2}() ";
                $s_sql2 = $s_sql2 . " {$val2}() ";
            }
        }
        //v1函数
        if ($v1_type == Constant::COND_VAl_TYPE_FUN) {
            $s_sql1 = $s_sql1 . " {$val2}() ,";
            $s_sql2 = $s_sql2 . " {$val2}() ,";

            //v2输入值
            if ($v2_type == Constant::COND_VAl_TYPE_INPUT) {
                list($param_key_join, $param_key_input) = $this->_procParam($o_field, $inc, "to");

                $s_param_key2_join = $param_key_join;
                $s_param_key2_input = $param_key_input;
                $s_param = "{$s_param_key2_input}";

                $s_sql1 = $s_sql1 . " {$s_param_key2_join} ";
                $s_sql2 = $s_sql2 . " {$s_param_key2_join} ";
            }
            //v2固定值
            if ($v2_type == Constant::COND_VAl_TYPE_FIXED) {
                if ($f_type == Constant::DB_FIELD_TYPE_INT || $f_type == Constant::DB_FIELD_TYPE_LONGINT) {
                    $s_sql1 = $s_sql1 . " {$val2} ";
                    $s_sql2 = $s_sql2 . " {$val2} ";

                } else {
                    $s_sql1 = $s_sql1 . " \'{$val2}\' ";
                    $s_sql2 = $s_sql2 . " \'{$val2}\' ";
                }
            }
            //v2函数
            if ($v2_type == Constant::COND_VAl_TYPE_FUN) {

                $s_sql1 = $s_sql1 . " {$val2}() ";
                $s_sql2 = $s_sql2 . " {$val2}() ";
            }
        }

        $s_sql1 = $s_sql1 . " )";
        $s_sql2 = $s_sql2 . " )' ";

        if ($s_sql1 != "") {
            $s_sql1 = _tab($tab_idx) . $s_sql1;
        }

        if ($has_if) {
            if ($f_type == Constant::DB_FIELD_TYPE_INT || $f_type == Constant::DB_FIELD_TYPE_LONGINT) {
                $s_sql3 = _tab($tab_idx) . "IF {$s_param_key1_join} != '' AND {$s_param_key2_join} != '' THEN\n";
                $s_sql3 = $s_sql3 . _tab($tab_idx + 1) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
                $s_sql3 = $s_sql3 . _tab($tab_idx) . "END IF;\n";
            } else {
                $s_sql3 = _tab($tab_idx) . "IF {$s_param_key1_join} != -1 AND {$s_param_key2_join} != -1  THEN\n";
                $s_sql3 = $s_sql3 . _tab($tab_idx + 1) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
                $s_sql3 = $s_sql3 . _tab($tab_idx) . "END IF;\n";
            }
            $s_sql2 = $s_sql3;
        } else {
            $s_sql2 = _tab($tab_idx) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
        }


        return array($s_param, $s_sql1, $s_sql2);
    }

    /**
     * 用来拼接的
     * 用来拼接的 in or notin
     * - 参数结构
     * - 直接的sql语句
     * - 拼接的sql语句
     *
     * @param $tab_idx
     * @param $inc
     * @param $WHERE_JOIN
     * @param $o_field
     * @param $v_cond
     * @param $v_type
     * @param $val
     * @return string|string[]
     */
    function _procWhere_V_range($tab_idx, $inc, $WHERE_JOIN, $o_field, $v_cond, $v_type, $val)
    {

        if (!isset(Constant::$a_cond_type_on_sql_3[$v_cond])) {
            SeasLog::error("unknown cond_type3 to proc");
            return;
        }
        $s_cond = Constant::$a_cond_type_on_sql_3[$v_cond];

        $s_param_join = "";
        $s_param_input = "";
        $s_sql1 = "";
        $s_sql2 = "";
        $has_if = false;

        $f_type = $o_field->type;
        $key = $o_field->name;


        //输入值
        if ($v_type == Constant::COND_VAl_TYPE_INPUT) {
            $has_if = true;
            list($param_key_join, $param_key_input) = $this->_procParam($o_field, $inc, "", true);
            $s_param_join = $param_key_join;
            $s_param_input = $param_key_input;
            $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} ($param_key_join)";
            $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} ($param_key_join)'";
            if ($f_type == Constant::DB_FIELD_TYPE_INT || $f_type == Constant::DB_FIELD_TYPE_LONGINT) {

            } else {
                //字符串类
                //需要外部输入时，先行添加单引号来隔离字符串

                //echo _tab(1) . "SET @s_sql_v = REPLACE(s_{$cond}_{$key}, '|', '\',\'');\n";
                //echo _tab(1) . "SET @s_sql = CONCAT( @s_sql, ' {$key} NOT IN(\'',@s_sql_v,'\') ');\n";
            }

        }
        //固定值
        if ($v_type == Constant::COND_VAl_TYPE_FIXED) {

            if ($f_type == Constant::DB_FIELD_TYPE_INT || $f_type == Constant::DB_FIELD_TYPE_LONGINT) {
                $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} ($val)";
                $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} ($val)'";
            } else {
                if ($val == "") {
                    return array("", "", "");
                }
                $a_temp = explode(",", $val);

                $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} ('" . implode("','", $a_temp) . "')";
                $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} (\'" . implode("\',\'", $a_temp) . "\')'";
            }
        }
        //函数
        if ($v_type == Constant::COND_VAl_TYPE_FUN) {

            $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond}({$val}())";
            $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond}({$val}())'";
        }

        if ($has_if) {
            $s_sql3 = _tab($tab_idx) . "IF {$s_param_join} != '' THEN\n";
            $s_sql3 = $s_sql3 . _tab($tab_idx + 1) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
            $s_sql3 = $s_sql3 . _tab($tab_idx) . "END IF;\n";
            $s_sql2 = $s_sql3;
        } else {
            $s_sql2 = _tab($tab_idx) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
        }

        return array($s_param_input, $s_sql1, $s_sql2);
    }

    /**
     * 用来拼接的
     * 用来拼接的 like
     * - 参数结构
     * - 直接的sql语句
     * - 拼接的sql语句
     *
     * @param $tab_idx
     * @param $inc
     * @param $WHERE_JOIN
     * @param $o_field
     * @param $v_cond
     * @param $v_type
     * @param $val
     * @return string|string[]
     */
    function _procWhere_V_like($tab_idx, $inc, $WHERE_JOIN, $o_field, $v_cond, $v_type, $val)
    {

        $s_param_join = "";
        $s_param_input = "";
        $s_sql1 = "";
        $s_sql2 = "";
        $has_if = false;

        $f_type = $o_field->type;
        $key = $o_field->name;
        //$a_temp0[] = "\'',s_kw,'\',`{$key}`) > 0  ";

        //输入值
        if ($v_type == Constant::COND_VAl_TYPE_INPUT) {
            $has_if = true;
            list($param_key_join, $param_key_input) = $this->_procParam($o_field, $inc, "", true);
            $s_param_join = $param_key_join;
            $s_param_input = $param_key_input;
            $s_sql1 = " {$WHERE_JOIN} LOCATE($param_key_join, `{$key}`) > 0  ";
            $s_sql2 = "' {$WHERE_JOIN} LOCATE($param_key_join, `{$key}`) > 0'";
            if ($f_type == Constant::DB_FIELD_TYPE_INT || $f_type == Constant::DB_FIELD_TYPE_LONGINT) {

            } else {
                //字符串类
                //需要外部输入时，先行添加单引号来隔离字符串
                //echo _tab(1) . "SET @s_sql_v = REPLACE(s_{$cond}_{$key}, '|', '\',\'');\n";
                //echo _tab(1) . "SET @s_sql = CONCAT( @s_sql, ' {$key} NOT IN(\'',@s_sql_v,'\') ');\n";
            }

        }
        //固定值
        if ($v_type == Constant::COND_VAl_TYPE_FIXED) {
            //$s_param = "";
            $s_sql1 = " {$WHERE_JOIN} LOCATE('{$val}', `{$key}`) > 0  ";
            $s_sql2 = "' {$WHERE_JOIN} LOCATE(\'{$val}\', `{$key}`) > 0'";
        }
        //函数
        if ($v_type == Constant::COND_VAl_TYPE_FUN) {
            //$s_param = "";
            $s_sql1 = " {$WHERE_JOIN} LOCATE({$val}(), `{$key}`) > 0  ";
            $s_sql2 = "' {$WHERE_JOIN} LOCATE({$val}(), `{$key}`) > 0'";
        }

        if ($has_if) {
            $s_sql3 = _tab($tab_idx) . "IF {$s_param_join} != '' THEN\n";
            $s_sql3 = $s_sql3 . _tab($tab_idx + 1) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
            $s_sql3 = $s_sql3 . _tab($tab_idx) . "END IF;\n";
            $s_sql2 = $s_sql3;
        } else {
            $s_sql2 = _tab($tab_idx) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
        }

        return array($s_param_input, $s_sql1, $s_sql2);
    }

    /**
     * @inheritDoc
     * 有多种更新
     * 创建存储过程-更新
     * @param MyModel $model
     * @param MyFun $o_fun
     */
    function cUpdate(MyModel $model, MyFun $o_fun)
    {

        $proc_name = self::_procHeader($model, $o_fun->name, $o_fun->title, "update");
        $a_all_fields = $model->field_list_kv;
        $limit = $o_fun->limit;//更新限制

        $ii = 0;
        $a_temp = array();

        //需要更新的字段
        $update_key_by_input = array();
        foreach ($o_fun->field_list as $field) {
            /* @var MyField $field */
            $key = $field->name;
            if (!isset($a_all_fields[$key])) {
                continue;
            }
            $ii++;
            $update_key_by_input[] = $key;
            list($param_key, $param_key2) = self::_procParam($field, $ii, "u");
            $a_temp[] = $param_key2;
        }
        echo "\n";
        echo _tab(1);
        echo implode(",\n" . _tab(1), $a_temp);
        echo ",\n";

        //查询条件的字段
        list($_param, $_sql1, $_sql2) = $this->_procWhereCond($model, $o_fun);
        $_param = $_param . ",\n" . _tab(1) . "INOUT `v_affected_rows` INT";
        self::_procBegin($_param);

        _db_comment("input update count {$ii} ");

        echo "DECLARE m_affected_rows INT;\n";
        echo "DECLARE s_affected_rows VARCHAR(12);\n";
        echo "UPDATE `t_{$model->table_name}` SET ";


        $a_temp = array();
        $ii = 0;
        foreach ($o_fun->field_list as $field) {
            /* @var MyField $field */
            $key = $field->name;
            if (!isset($a_all_fields[$key])) {
                continue;
            }
            $ii++;
            list($param_key, $param_key2) = self::_procParam($field, $ii, "u");
            $a_temp[] = "`{$key}` = {$param_key}";
        }

        if (isset($a_all_fields["utime"]) && !in_array("utime", $update_key_by_input)) {
            $a_temp[] = "`utime` = NOW()";
        }

        if (count($a_temp) > 1) {
            echo "\n";
            echo _tab(1);
            echo implode(",\n" . _tab(1), $a_temp);
        }
        if (count($a_temp) == 1) {
            echo $a_temp[0];
        }

        echo "\nWHERE ";

        echo $_sql1;

        if ($limit > 0) {
            echo "\n";
            echo "LIMIT {$limit};\n";
        } else {
            echo ";\n";
        }

        echo "SET m_affected_rows = ROW_COUNT();\n";
        //echo "COMMIT;\n";
        echo "SET s_affected_rows = CONCAT( 'updated_rows--' , m_affected_rows);\n";
        echo "CALL p_debug('{$proc_name}', s_affected_rows);\n";

        echo "SELECT m_affected_rows INTO v_affected_rows;\n";
        echo "SELECT m_affected_rows AS i_affected_rows;\n";

        self::_procEnd($model, $proc_name);
    }

    /**
     * @inheritDoc
     * 创建存储过程-查询一个
     * @param MyModel $model
     */
    function cFetch(MyModel $model, MyFun $o_fun)
    {
        $proc_name = self::_procHeader($model, $o_fun->name, $o_fun->title, "fetch");
        //查询条件的字段
        list($_param, $_sql1, $_sql2) = $this->_procWhereCond($model, $o_fun);
        self::_procBegin($_param);

        echo "SELECT * FROM `t_{$model->table_name}` WHERE ";
        echo $_sql1;
        echo "\n";
        echo "LIMIT 1;\n";
        self::_procEnd($model, $proc_name);

    }

    /**
     * @param MyModel $model
     * @param MyFun $o_fun
     * @param $count_only
     * @return mixed|void
     */
    function cList(MyModel $model, MyFun $o_fun, $count_only = false)
    {
        $base_fun = strtolower($o_fun->type);
        $fun_name = $o_fun->name;
        if ($count_only) {
            $fun_name = $fun_name . "_c";
        }
        $proc_name = self::_procHeader($model, $fun_name, $o_fun->title, $base_fun);

        $fun_type = $o_fun->type;
        $group_field = "";
        $group_field_id = $o_fun->group_field;
        if (isset($model->field_list[$group_field_id])) {
            $o_f = $model->field_list[$group_field_id];
            $group_field = $o_f->name;
        }
        //
        $group_by = "";
        $group_by_id = $o_fun->group_by;
        if (isset($model->field_list[$group_by_id])) {
            $o_f = $model->field_list[$group_by_id];
            $group_by = $o_f->name;
        }

        $order_by = "";
        $order_by_id = $o_fun->order_by;
        if (isset($model->field_list[$order_by_id])) {
            $o_f = $model->field_list[$order_by_id];
            $order_by = $o_f->name;
        }


        $group_field_final = "";
        $group_field_sel = "";
        $allow_pager = false;
        //检查是否带有聚合
        switch ($fun_type) {
            case Constant::FUN_TYPE_LIST_WITH_AVG:
                $group_field_final = "i_agv_{$group_field}";
                $group_field_sel = " AVG(`{$group_field}`) AS {$group_field_final}\n";
                break;
            case Constant::FUN_TYPE_LIST_WITH_SUM:
                $group_field_final = "i_sum_{$group_field}";
                $group_field_sel = " SUM(`{$group_field}`) AS {$group_field_final}\n";
                break;
            case Constant::FUN_TYPE_LIST_WITH_MAX:
                $group_field_final = "i_max_{$group_field}";
                $group_field_sel = " MAX(`{$group_field}`) AS {$group_field_final}\n";
                break;
            case Constant::FUN_TYPE_LIST_WITH_MIN:
                $group_field_final = "i_min_{$group_field}";
                $group_field_sel = " MIN(`{$group_field}`) AS {$group_field_final}\n";
                break;
            case Constant::FUN_TYPE_LIST_WITH_COUNT:
                $group_field_final = "i_count_{$group_field}";
                $group_field_sel = " COUNT(`{$group_field}`) AS {$group_field_final}\n";
                break;
            case Constant::FUN_TYPE_LIST:
                $allow_pager = true;
                break;
            default:
                break;
        }

        //TODO 预先处理查询条件的
        list($_param, $_sql1, $_sql2) = $this->_procWhereCond($model, $o_fun);
        if (!$count_only) {
            if ($allow_pager && $o_fun->pager_enable == 1) {
                //带有分页
                if ($_param != "") {
                    $_param = $_param . ",\n" . _tab(1) . "IN `v_page` INT";
                    $_param = $_param . ",\n" . _tab(1) . "IN `v_page_size` INT";
                } else {
                    $_param = "IN`v_page` INT";
                    $_param = $_param . ",\n" . _tab(1) . "IN `v_page_size` INT";
                }
            }
            if ($o_fun->order_enable) {
                if ($o_fun->order_by == "@@") {
                    //输入排序主键
                    if ($_param != "") {
                        $_param = $_param . "\n" . _tab(1) . ", IN `v_order_by` VARCHAR(255)";
                    } else {
                        $_param = "\n" . _tab(1) . "IN `v_order_by` VARCHAR(255)";
                    }
                }
                if ($o_fun->order_dir == "@@") {
                    //输入排序方向
                    if ($_param != "") {
                        $_param = $_param . ", IN `v_order_dir` VARCHAR(4)";
                    } else {
                        $_param = "IN `v_order_dir` VARCHAR(4)";
                    }
                }
            }
        }


        $this->_procBegin($_param);


        if ($count_only) {
            //仅返回统计
            //带聚合的数据，如果返回带有聚合以外的数据，count的主键需要时group_by，否则统一为count(`id`)
            if ($allow_pager) {
                echo "SET @s_sql = 'SELECT COUNT(`id`) AS i_count  FROM `t_{$model->table_name}` WHERE ';\n";
            } else {
                echo "SET @s_sql = 'SELECT COUNT(`{$group_by}`) AS i_count  FROM `t_{$model->table_name}` WHERE ';\n";
            }


        } else {
            if ($allow_pager && $o_fun->pager_enable) {
                echo "DECLARE m_offset INT;\n";
                echo "DECLARE m_length INT;\n";
                echo "SET m_length = i_page_size;\n";
                echo "SET m_offset = ( i_page - 1 ) * i_page_size;\n\n";
            }

            echo "SET @s_sql = 'SELECT ";

            $has_pre_key = "";
            if ($o_fun->all_field == 1) {
                echo "*";
                $has_pre_key = ",";
            } else {
                $a_field_return = $model->field_list;
                foreach ($model->field_list2 as $skey => $o_filed) {
                    $a_temp[] = "`{$skey}`\n";
                    $has_pre_key = ",";
                }
                echo implode(",", $a_temp);
            }

            if ($group_field_sel != "") {
                echo "{$has_pre_key} {$group_field_sel}";
            }

            echo " FROM `t_{$model->table_name}` WHERE ';\n";
        }

        echo $_sql2;

        if ($group_by != "") {
            echo "SET @s_sql = CONCAT( @s_sql, ' GROUP BY {$group_by}');\n";
        }

        if (!$count_only) {


            if ($o_fun->order_enable) {

                if ($o_fun->order_by == "@@") {
                    //输入排序主键
                    echo "SET @s_sql = CONCAT( @s_sql, ' ORDER BY ',v_order_by);\n";
                } else if ($o_fun->order_by == "##") {
                    //聚合按键
                    echo "SET @s_sql = CONCAT( @s_sql, ' ORDER BY {$group_field_final}');\n";
                } else {
                    echo "SET @s_sql = CONCAT( @s_sql, ' ORDER BY {$order_by}');\n";
                }

                if ($o_fun->order_dir == "@@") {
                    //输入排序方向
                    echo "SET @s_sql = CONCAT( @s_sql, v_order_dir);\n";
                } else {
                    echo "SET @s_sql = CONCAT( @s_sql, ' {$o_fun->order_dir}');\n";
                }
            }

            if ($allow_pager && $o_fun->pager_enable) {
                echo "SET @s_sql = CONCAT( @s_sql, ' LIMIT ', m_offset, ',', m_length);\n";
            }
        }
        echo "CALL p_debug('{$proc_name}', @s_sql);\n";
        echo "PREPARE stmt FROM @s_sql;\n";
        echo "EXECUTE stmt;\n";

        self::_procEnd($model, $proc_name);

    }

    /**
     * 条件的输入参数,内部二次拼接
     * @param MyModel $model
     * @param MyFun $o_fun
     */
    function _procWhereCond2($model, $o_fun)
    {
        $s_temp = "1=1";
        $a_temp = array();
        $jj = 0;
        if ($o_fun->where != null) {
            $where_type_joiner = $o_fun->where->type;
            $cond_list = $o_fun->where->cond_list;
            $where_list = $o_fun->where->where_list;
            foreach ($cond_list as $cond) {
                /* @var MyCond $cond */
                $field = $model->field_list[$cond->field];
                $field_type = $field->type;
                $key = $field->name;
                $cond_type = $cond->type;
                $v1_type = $cond->v1_type;
                $v2_type = $cond->v2_type;

                if ($cond_type == Constant::COND_TYPE_BETWEEN || $cond_type == Constant::COND_TYPE_DATE || $cond_type == Constant::COND_TYPE_TIME || $cond_type == Constant::COND_TYPE_NOTBETWEEN) {
                    if ($v1_type == Constant::COND_VAl_TYPE_INPUT) {
                        $jj++;
                        $prefix = self::_procKeyPrefix($field_type);
                        $key2 = "{$prefix}_{$jj}_from_{$key}";
                        $a_temp[] = "`{$key}` = {$key2}";
                    }
                    if ($v1_type == Constant::COND_VAl_TYPE_FIXED) {
                        if ($field_type == Constant::DB_FIELD_TYPE_INT || $field_type == Constant::DB_FIELD_TYPE_LONGINT) {
                            $a_temp[] = "`{$key}` = {$cond->v1}";
                        } else {
                            $a_temp[] = "`{$key}` = '{$cond->v1}'";
                        }
                    }
                    if ($v1_type == Constant::COND_VAl_TYPE_FUN) {
                        $a_temp[] = "`{$key}` = {$cond->v1}()";
                    }
                    //第二个参数
                    if ($v2_type == Constant::COND_VAl_TYPE_INPUT) {
                        $jj++;
                        $prefix = self::_procKeyPrefix($field_type);
                        $key2 = "{$prefix}_{$jj}_to_{$key}";
                        $a_temp[] = "`{$key}` = {$key2}";
                    }
                    if ($v2_type == Constant::COND_VAl_TYPE_FIXED) {
                        if ($field_type == Constant::DB_FIELD_TYPE_INT || $field_type == Constant::DB_FIELD_TYPE_LONGINT) {
                            $a_temp[] = "`{$key}` = {$cond->v2}";
                        } else {
                            $a_temp[] = "`{$key}` = '{$cond->v2}'";
                        }
                    }
                    if ($v2_type == Constant::COND_VAl_TYPE_FUN) {
                        $a_temp[] = "`{$key}` = {$cond->v2}()";
                    }
                } else {
                    if ($v1_type == Constant::COND_VAl_TYPE_INPUT) {
                        $jj++;
                        $prefix = self::_procKeyPrefix($field_type);
                        $key2 = "{$prefix}_{$jj}_w_{$key}";
                        $a_temp[] = "`{$key}` = {$key2}";
                    }
                    if ($v1_type == Constant::COND_VAl_TYPE_FIXED) {
                        if ($field_type == Constant::DB_FIELD_TYPE_INT || $field_type == Constant::DB_FIELD_TYPE_LONGINT) {
                            $a_temp[] = "`{$key}` = {$cond->v1}";
                        } else {
                            $a_temp[] = "`{$key}` = '{$cond->v1}'";
                        }
                    }
                    if ($v1_type == Constant::COND_VAl_TYPE_FUN) {
                        $a_temp[] = "`{$key}` = {$cond->v1}()";
                    }
                }
            }
            foreach ($where_list as $where2) {
                if ($where2 != null) {
                    $cond_list2 = $where2->cond_list;
                    $a_temp2 = array();
                    foreach ($cond_list2 as $cond) {
                        /* @var MyCond $cond */
                        $field = $model->field_list[$cond->field];
                        $field_type = $field->type;
                        $key = $field->name;
                        $cond_type = $cond->type;
                        $v1_type = $cond->v1_type;
                        $v2_type = $cond->v2_type;

                        if ($cond_type == Constant::COND_TYPE_BETWEEN || $cond_type == Constant::COND_TYPE_DATE || $cond_type == Constant::COND_TYPE_TIME || $cond_type == Constant::COND_TYPE_NOTBETWEEN) {
                            if ($v1_type == Constant::COND_VAl_TYPE_INPUT) {
                                $jj++;
                                $prefix = self::_procKeyPrefix($field_type);
                                $key2 = "{$prefix}_{$jj}_from_{$key}";
                                $a_temp2[] = "`{$key}` = {$key2}";
                            }
                            if ($v1_type == Constant::COND_VAl_TYPE_FIXED) {
                                if ($field_type == Constant::DB_FIELD_TYPE_INT || $field_type == Constant::DB_FIELD_TYPE_LONGINT) {
                                    $a_temp2[] = "`{$key}` = {$cond->v1}";
                                } else {
                                    $a_temp2[] = "`{$key}` = '{$cond->v1}'";
                                }
                            }
                            if ($v1_type == Constant::COND_VAl_TYPE_FUN) {
                                $a_temp2[] = "`{$key}` = {$cond->v1}()";
                            }
                            //第二个参数
                            if ($v2_type == Constant::COND_VAl_TYPE_INPUT) {
                                $jj++;
                                $prefix = self::_procKeyPrefix($field_type);
                                $key2 = "{$prefix}_{$jj}_to_{$key}";
                                $a_temp2[] = "`{$key}` = {$key2}";
                            }
                            if ($v2_type == Constant::COND_VAl_TYPE_FIXED) {
                                if ($field_type == Constant::DB_FIELD_TYPE_INT || $field_type == Constant::DB_FIELD_TYPE_LONGINT) {
                                    $a_temp2[] = "`{$key}` = {$cond->v2}";
                                } else {
                                    $a_temp2[] = "`{$key}` = '{$cond->v2}'";
                                }
                            }
                            if ($v2_type == Constant::COND_VAl_TYPE_FUN) {
                                $a_temp2[] = "`{$key}` = {$cond->v2}()";
                            }
                        } else {
                            if ($v1_type == Constant::COND_VAl_TYPE_INPUT) {
                                $jj++;
                                $prefix = self::_procKeyPrefix($field_type);
                                $key2 = "{$prefix}_{$jj}_w_{$key}";
                                $a_temp2[] = "`{$key}` = {$key2}";
                            }
                            if ($v1_type == Constant::COND_VAl_TYPE_FIXED) {
                                if ($field_type == Constant::DB_FIELD_TYPE_INT || $field_type == Constant::DB_FIELD_TYPE_LONGINT) {
                                    $a_temp2[] = "`{$key}` = {$cond->v1}";
                                } else {
                                    $a_temp2[] = "`{$key}` = '{$cond->v1}'";
                                }
                            }
                            if ($v1_type == Constant::COND_VAl_TYPE_FUN) {
                                $a_temp2[] = "`{$key}` = {$cond->v1}()";
                            }
                        }
                    }
                    $where_type_joiner2 = $where2->type;
                    $a_temp = "(" . implode("\n" . _tab(2) . "{$where_type_joiner2} ", $a_temp2) . ")";
                }
            }
            $s_temp = implode("\n" . _tab(1) . "{$where_type_joiner} ", $a_temp);
        }
        return $s_temp;

    }

    /**
     * 方法 list-1 构造可变参数sql
     *
     * @param array $model 模型
     * @param MyFunList $my_list 查询结构
     * @param boolean $count_only 仅计数
     * @return void
     */
    function _mysql_proc_list_sql($model, $my_list, $count_only)
    {

        $ii = 0;

        if (count($my_list->list_by) > 0) {
            foreach ($my_list->list_by as $key) {
                //$a_temp[] = self::_procParam($field);
                $ii++;
                $p_type = $model->table_fields[$key]['type'];
                $prefix = self::_procKeyPrefix($p_type);

                if ($p_type == "int") {
                    echo "IF {$prefix}_{$key} >= 0 THEN\n";
                    echo _tab(1) . "SET @s_sql = CONCAT( @s_sql, ' AND `{$key}` =  \'', {$prefix}_{$key}, '\' ' );\n";
                    echo "END IF;\n";
                } else {
                    echo "IF {$prefix}_{$key} != '' THEN\n";
                    echo _tab(1) . "SET @s_sql = CONCAT( @s_sql, ' AND `{$key}` =  \'', {$prefix}_{$key}, '\' ' );\n";
                    echo "END IF;\n";
                }
            }
        }

        $a_has_key = _php_list_get_conds();
        foreach ($a_has_key as $cond) {

            $bv = "list_has_{$cond}";
            $kv = "list_{$cond}_key";
            if ($my_list->$bv) {
                switch ($cond) {
                    //搜索关键字
                    case "kw":
                        //$a_temp[] = "IN `s_kw` VARCHAR ( 255 )";
                        echo "IF v_kw != '' THEN\n";
                        echo _tab(1) . "SET @s_sql = CONCAT( @s_sql, ' AND (\n";
                        $a_temp0 = array();
                        foreach ($my_list->list_kw as $key) {
                            $a_temp0[] = "LOCATE(\'',s_kw,'\',`{$key}`) > 0  ";
                        }
                        echo implode("\n" . _tab(2) . "OR", $a_temp0);
                        echo "\n";
                        echo _tab(1) . " )');\n";
                        echo "END IF;\n";
                        $ii++;
                        break;
                    //开始日期--结束日期
                    case "date":
                        //$a_temp[] = "IN `s_date_from` VARCHAR ( 10 )";
                        //$a_temp[] = "IN `s_date_to` VARCHAR ( 10 )";

                        echo "IF s_date_from != '' AND  s_date_to != '' THEN\n";
                        echo _tab(1) . "SET @s_sql = CONCAT( @s_sql, ' AND (";
                        $date_key = $my_list->$kv;
                        $p_type = $model->table_fields[$date_key]['type'];
                        if ($p_type == "datetime") {
                            echo "{$date_key} BETWEEN \'', s_date_from, ' 00:00:00\' AND \'', s_date_to,' 23:59:59\')'";
                        } else {
                            echo "{$date_key} BETWEEN \'', s_date_from, '\' AND \'', s_date_to,'\')'";
                        }
                        echo " );\n";
                        echo "END IF;\n";

                        $ii++;
                        $ii++;
                        break;
                    //开始时间--结束时间
                    case "time":
                        //$a_temp[] = "IN `s_time_from` VARCHAR ( 19 )";
                        //$a_temp[] = "IN `s_time_to` VARCHAR ( 19 )";
                        echo "IF s_time_from != '' AND  s_time_to != '' THEN\n";
                        echo _tab(1) . "SET @s_sql = CONCAT( @s_sql, ' AND (";
                        $time_key = $my_list->$kv;
                        echo "{$time_key} BETWEEN \'', s_time_from, '\' AND \'', s_time_to,'\')'";
                        echo " );\n";
                        echo "END IF;\n";

                        $ii++;
                        $ii++;
                        break;

                    //
                    case "between":
                        //$a_temp[] = "IN `i_between_from` INT";
                        //$a_temp[] = "IN `i_between_to` INT";
                        $ii++;
                        $ii++;
                        echo "SET @s_sql = CONCAT( @s_sql, ' AND (";
                        $between_key = $my_list->$kv;
                        echo "{$between_key} BETWEEN \'', i_between_from, '\' AND \'', i_between_to,'\')'";
                        echo " );\n";

                        break;

                    //
                    case "notbetween":
                        //$a_temp[] = "IN `i_between_before` INT";
                        //$a_temp[] = "IN `i_between_after` INT";
                        $ii++;
                        $ii++;
                        echo "SET @s_sql = CONCAT( @s_sql, ' AND (";
                        $between_key = $my_list->$kv;
                        echo "{$between_key} NOT BETWEEN \'', i_between_before, '\' AND \'', i_between_after,'\')'";
                        echo " );\n";
                        break;

                    //
                    case "in":
                        $key = $my_list->$kv;
                        //$a_temp[] = "IN `s_{$cond}_{$key}` VARCHAR ( 9999 )";
                        $ii++;
                        echo "IF s_{$cond}_{$key} != '' THEN\n";
                        if ($key == "int") {
                            echo _tab(1) . "SET @s_sql = CONCAT( @s_sql, ' {$key} IN(', s_{$cond}_{$key}, ') ');\n";
                        } else {
                            echo _tab(1) . "SET @s_sql_v = REPLACE(s_{$cond}_{$key}, '|', '\',\'');\n";
                            echo _tab(1) . "SET @s_sql = CONCAT( @s_sql, ' {$key} IN(\'',@s_sql_v,'\') ');\n";
                        }
                        echo "END IF;\n";
                        break;

                    case "notin":
                        $key = $my_list->$kv;
                        //$a_temp[] = "IN `s_{$cond}_{$key}` VARCHAR ( 9999 )";
                        $ii++;
                        echo "IF s_{$cond}_{$key} != '' THEN\n";
                        if ($key == "int") {
                            echo _tab(1) . "SET @s_sql = CONCAT( @s_sql, ' {$key} NOT IN(', s_{$cond}_{$key}, ') ');\n";
                        } else {
                            echo _tab(1) . "SET @s_sql_v = REPLACE(s_{$cond}_{$key}, '|', '\',\'');\n";
                            echo _tab(1) . "SET @s_sql = CONCAT( @s_sql, ' {$key} NOT IN(\'',@s_sql_v,'\') ');\n";
                        }
                        echo "END IF;\n";
                        break;

                    //
                    case "gt":
                    case "gte":
                    case "lt":
                    case "lte":
                        $key = $my_list->$kv;
                        //$a_temp[] = "IN `i_{$cond}_{$key}` INT";
                        $ii++;
                        $real_cond = self::_procGtEqLt($cond);
                        echo "SET @s_sql = CONCAT( @s_sql, ' AND (";
                        $gtelt_key = $my_list->$kv;
                        echo "{$gtelt_key}  {$real_cond}  \'', i_{$cond}_{$key},'\')'";
                        echo " );\n";
                        break;

                    default:
                        break;

                }
            }
        }

        if (isset($model->table_fields['flag'])) {
            echo "SET @s_sql = CONCAT( @s_sql, ' AND `flag` = \'n\'');\n";
        }

        //仅查询数量
        if ($count_only) {
            return;
        }


        if (!$my_list->list_has_group && $my_list->list_has_pager) {
            if ($my_list->list_pager_type != "normal") {
                $kkk = $my_list->cursor_offset_key;
                if ($my_list->list_order_dir == "" || $my_list->list_order_dir == "DESC") {
                    echo "SET @s_sql = CONCAT( @s_sql, ' AND {$kkk} < ',i_cursor_from,' ');\n";
                } else {
                    echo "SET @s_sql = CONCAT( @s_sql, ' AND {$kkk} > ',i_cursor_from,' ');\n";
                }
            }
        }

        //排序
        if ($my_list->list_has_order) {
            //        if ($my_list->list_order_by == "") {
            //            $a_temp[] = "IN `s_order_by` VARCHAR ( 255 )";
            //            $ii++;
            //        }
            //        $a_temp[] = "IN `s_order_dir` VARCHAR ( 255 )";
            //        $ii++;
            if (!is_array($my_list->list_order_by)) {
                if ($my_list->list_order_by == "" && $my_list->list_order_dir == "") {
                    $ii++;
                    echo "SET @s_sql = CONCAT( @s_sql, ' ORDER BY ',s_order_by,' ',s_order_dir);\n";
                } else {
                    if ($my_list->list_order_dir == "") {
                        echo "SET @s_sql = CONCAT( @s_sql, ' ORDER BY {$my_list->list_order_by} ',s_order_dir);\n";
                        //echo "SET @s_sql = CONCAT( @s_sql, ' ORDER BY ',s_order_by,' {$my_list->list_order_dir}');\n";
                    } else {
                        //不允许 list_order_by为空，而s_order_dir 不为空
                    }
                }
            } else {
                $a_temp = array();
                foreach ($my_list->list_order_by as $order_by => $order_dir) {
                    $a_temp[] = "{$order_by} {$order_dir}";
                }
                if (count($a_temp) > 0) {
                    $s_temp = implode(", ", $a_temp);
                    echo "SET @s_sql = CONCAT( @s_sql, ' ORDER BY {$s_temp}');\n";
                }
            }
        }

        if (!$my_list->list_has_group) {
            if ($my_list->list_has_pager) {
                //$a_temp[] = "IN `i_page` INT";
                //$a_temp[] = "IN `i_page_size` INT";
                $ii++;
                $ii++;
                if ($my_list->list_pager_type == "normal") {
                    echo "SET @s_sql = CONCAT( @s_sql, ' LIMIT  ', m_offset, ',',m_length);\n";
                } else {
                    //页码,TODO 偏移量  大于数id
                    echo "SET @s_sql = CONCAT( @s_sql, ' LIMIT  ', i_cursor_offset);\n";
                }
            }
        }
        return;
    }

    /**
     * 获取大于小于的操作符号
     * @param string $gt_eq_lt
     * @return string
     */
    static function _procGtEqLt($gt_eq_lt)
    {
        switch ($gt_eq_lt) {
            case "gt":
                return ">";
            case "gte":
                return ">=";
            case "lt":
                return "<";
            case "lte":
                return "<=";
            default:
                return "=";
        }
    }

    /**
     * 方法 list-2 构造可变参数
     *
     * @param array $model 模型
     * @param MyFunList $my_list 查询结构
     * @param boolean $count_only 仅计数
     * @return array 参数组合
     */
    function _mysql_proc_list_param($model, $my_list, $count_only)
    {

        $ii = 0;
        $a_temp = array();
        //$a_temp[] = "1 = 1";

        if (count($my_list->list_by) > 0) {
            foreach ($my_list->list_by as $key) {
                $a_temp[] = self::_procParam($field);
                $ii++;
            }
        }

        $a_has_key = _php_list_get_conds();
        foreach ($a_has_key as $cond) {

            $bv = "list_has_{$cond}";
            $kv = "list_{$cond}_key";
            if ($my_list->$bv) {
                switch ($cond) {
                    //搜索关键字
                    case "kw":
                        $a_temp[] = "IN `s_kw` VARCHAR ( 255 )";
                        $ii++;
                        break;
                    //开始日期--结束日期
                    case "date":
                        $a_temp[] = "IN `s_date_from` VARCHAR ( 10 )";
                        $a_temp[] = "IN `s_date_to` VARCHAR ( 10 )";
                        $ii++;
                        $ii++;
                        break;
                    //开始时间--结束时间
                    case "time":
                        $a_temp[] = "IN `s_time_from` VARCHAR ( 19 )";
                        $a_temp[] = "IN `s_time_to` VARCHAR ( 19 )";
                        $ii++;
                        $ii++;
                        break;

                    //
                    case "between":
                        $a_temp[] = "IN `i_between_from` INT";
                        $a_temp[] = "IN `i_between_to` INT";
                        $ii++;
                        $ii++;
                        break;

                    //
                    case "notbetween":
                        $a_temp[] = "IN `i_between_before` INT";
                        $a_temp[] = "IN `i_between_after` INT";
                        $ii++;
                        $ii++;
                        break;

                    //
                    case "in":
                    case "notin":
                        $key = $my_list->$kv;
                        $a_temp[] = "IN `s_{$cond}_{$key}` VARCHAR ( 9999 )";
                        $ii++;
                        break;

                    //
                    case "gt":
                    case "gte":
                    case "lt":
                    case "lte":
                        $key = $my_list->$kv;
                        $a_temp[] = "IN `i_{$cond}_{$key}` INT";
                        $ii++;
                        break;

                    default:
                        break;

                }
            }
        }

        //仅查询数量
        if ($count_only) {
            return $a_temp;
        }

        //排序
        if ($my_list->list_has_order) {
            if (!is_array($my_list->list_order_by)) {
                //TODO
                if ($my_list->list_order_by == "") {
                    $a_temp[] = "IN `s_order_by` VARCHAR ( 255 )";
                    $ii++;
                    $a_temp[] = "IN `s_order_dir` VARCHAR ( 255 )";
                    $ii++;
                } else {
                    if ($my_list->list_order_dir == "") {
                        $a_temp[] = "IN `s_order_dir` VARCHAR ( 255 )";
                        $ii++;
                    }
                }
            }
        }

        if (!$my_list->list_has_group) {
            if ($my_list->list_has_pager) {

                if ($my_list->list_pager_type == "normal") {
                    $a_temp[] = "IN `i_page` INT";
                    $a_temp[] = "IN `i_page_size` INT";
                    $ii++;
                    $ii++;
                } else {
                    //cursor
                    $a_temp[] = "IN `i_cursor_from` INT";
                    $a_temp[] = "IN `i_cursor_offset` INT";
                    $ii++;
                    $ii++;
                }
            }
        }

        return $a_temp;
    }

    /**
     * mysql存储过程--获取列表的计数器
     *
     * @param array $model
     * @param MyFunList $my_list 查询结构
     */
    function _mysql_create_proc_count($model, $my_list)
    {
        $proc_name = self::_procHeader($model, $my_list->list_name, $my_list->fetch_title, "count");

        $a_temp = _mysql_proc_list_param($model, $my_list, true);
        self::_procBegin($a_temp);

        echo "SET @s_sql = 'SELECT COUNT(`{$my_list->list_count_key}`) AS i_count FROM`t_{$model['table_name']}` WHERE 1=1 '; \n";

        _mysql_proc_list_sql($model, $my_list, true);
        echo "CALL p_debug('{$proc_name}', @s_sql);\n";
        echo "PREPARE stmt FROM @s_sql;\n";
        echo "EXECUTE stmt;\n";
        //echo "COMMIT;\n";
        self::_procEnd($model, $proc_name);
    }

    /**
     * mysql存储过程--获取列表
     *
     * @param array $model
     * @param MyFunList $my_list 查询结构
     */
    function _mysql_create_proc_list($model, $my_list)
    {
        $proc_name = self::_procHeader($model, $my_list->list_name, $my_list->list_title, "list");

        $a_temp = _mysql_proc_list_param($model, $my_list, false);
        self::_procBegin($a_temp);

        if (!$my_list->list_has_group && $my_list->list_has_pager) {
            if ($my_list->list_pager_type == "normal") {
                echo "DECLARE m_offset INT;\n";
                echo "DECLARE m_length INT;\n";
                echo "SET m_length = i_page_size;\n";
                echo "SET m_offset = ( i_page - 1 ) * i_page_size;\n\n";
            }
        }


        echo "SET @s_sql = 'SELECT ";

        if (!$my_list->list_has_group) {
            if (count($my_list->list_keys) == 0) {
                echo "*";
            } else {
                if ($my_list->is_distinct) {
                    echo "DISTINCT ";
                }
                $a_temp = array();
                foreach ($my_list->list_keys as $skey) {
                    $a_temp[] = "`{$skey}`";
                }
                echo implode(",", $a_temp);
            }
        } else {
            $list_group_type = $my_list->list_group_type;
            $list_group_key = $my_list->list_group_key;
            $a_temp = array();
            $a_temp[] = strtoupper($list_group_type) . "(`{$list_group_key}`) AS i_" . strtolower($list_group_type);
            foreach ($my_list->list_group_by as $skey) {
                $a_temp[] = "`{$skey}`";
            }
            echo implode(",", $a_temp);
        }
        echo " FROM `t_{$model['table_name']}` WHERE 1=1 '; \n";

        _mysql_proc_list_sql($model, $my_list, false);
        echo "CALL p_debug('{$proc_name}', @s_sql);\n";
        echo "PREPARE stmt FROM @s_sql;\n";
        echo "EXECUTE stmt;\n";
        //echo "COMMIT;\n";
        self::_procEnd($model, $proc_name);

    }

    /**
     * mysql存储过程--获取列表
     *
     * @param $model
     */
    function mysql_create_proc_list($model)
    {
        $a_list_confs = _model_get_ok_list($model);
        foreach ($a_list_confs as $list_key => $my_list) {

            /**
             * @var MyFunList $my_list
             */
            if (!$my_list->list_has_group) {
                //普通查询
                if ($my_list->count_only == true) {
                    _mysql_create_proc_count($model, $my_list);
                } else {
                    _mysql_create_proc_count($model, $my_list);
                    _mysql_create_proc_list($model, $my_list);
                }
            } else {
                //聚合的只有列表
                _mysql_create_proc_list($model, $my_list);
            }

        }
    }

    function cCount(MyModel $model, MyFun $fun)
    {
        // TODO: Implement cCount() method.
    }
}