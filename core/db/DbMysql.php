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
        $i_index_size = count($model->idx_list);

        _db_comment_begin();
        _db_comment("Table structure for t_{$model->table_name} [{$model->title}]");
        _db_comment("Table fields count ({$i_field_size})");
        _db_comment("Table index count ({$i_index_size})");
        _db_comment_end();

        if ($i_field_size == 0) {
            _db_comment("Is is a empty Table");
            return;
        }

        echo "CREATE TABLE `t_{$model->table_name}`\n(\n";

        $ii = 0;
        $has_primary_key = false;//判断主键和ID的区别
        $model_primary_key = "";//由model定义的PK，不一定存在
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
                $model_primary_key = $key;
            }

            $type = $field->type;
            $after_null = "";
            $type_size = "VARCHAR(255)";
            switch ($type) {
                case Constant::DB_FIELD_TYPE_BOOL :
                    $type_size = "TINYINT";
                    break;
                //整型
                case Constant::DB_FIELD_TYPE_INT:
                    //$size = 11;
                    $type_size = "INT";
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


        if ($uq_auto_increment != "") {
            $a_temp[] = "PRIMARY KEY (`{$uq_auto_increment}`)";
        } else {
            if ($has_primary_key) {
                $a_temp[] = "PRIMARY KEY (`{$model_primary_key}`)";
            }
        }

        $ii = 0;
        foreach ($model->idx_list as $o_index) {

            /* @var MyIndex $o_index */
            $a_temp0 = array();
            $idx_type = $o_index->type;
            $jj = 0;
            foreach ($o_index->field_list as $field) {
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
            $charset = Constant::DB_CHARSET_UTF8;
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

        $a_all_fields = array();
        //转换用name作为主键
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

                case Constant::FUN_TYPE_COUNT:
                    $this->cCount($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_LIST_WITH_COUNT:
                case Constant::FUN_TYPE_LIST_WITH_AVG:
                case Constant::FUN_TYPE_LIST_WITH_SUM:
                case Constant::FUN_TYPE_LIST_WITH_MAX:
                case Constant::FUN_TYPE_LIST_WITH_MIN:
                case Constant::FUN_TYPE_LIST:
                default:
                    $this->cList($model, $o_fun);
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


        $a_all_fields = $model->field_list_kv;

        list($is_return_new_id, $i_param, $a_param_comment, $a_param_define, $a_param_use, $a_param_type, $a_param_key, $a_param_field) = $this->parseAdd_field($model, $fun);

        if ($is_return_new_id) {
            $i_param++;
            $a_param_define[] = "INOUT `v_new_id` INT";
        }

        $proc_name = $this->_procHeader($model, $fun->name, $fun->title, "add", $i_param);
        $this->_procBegin($a_param_define);

        echo "DECLARE m_new_id INT;\n";
        //注意这里除去inc外的是全部字段
        echo "INSERT INTO `t_{$model->table_name}` (\n";
        $ii = 0;
        $a_temp = array();
        //顺序按照all field的顺序
        foreach ($a_all_fields as $key => $field) {
            if ($key == "id" && $field->auto_increment = 1) {
                continue;
            }
            $ii++;
            $a_temp[] = "`{$key}`";
        }
        echo _tab(1);
        echo implode(",\n" . _tab(1), $a_temp);
        echo "\n) VALUES (\n";

        $ii = 0;
        $a_temp = array();
        foreach ($a_all_fields as $key => $field) {
            if ($key == "id" && $field->auto_increment = 1) {
                continue;
            }
            //查询不是输入的部分
            /* @var MyField $field */
            if (!in_array($key, $a_param_key)) {
                //部分预置默认值
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
                        //那些blob类型应该用\n结尾
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
                $a_temp[] = "`{$a_param_use[$key]}`";
            }
        }
        echo _tab(1);
        echo implode(",\n" . _tab(1), $a_temp);
        echo "\n);\n";

        echo "SET m_new_id = LAST_INSERT_ID();\n";
        //非自增可能为空
        echo "SET @s_new_id = CONCAT('', m_new_id);\n";

        echo "CALL p__debug('{$proc_name}', @s_new_id);\n";
        if ($is_return_new_id) {
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
    function _procHeader($model, $fun_name, $fun_title, $base_fun, $i_param_count = 0)
    {

        $real_fun = $this->findProcName($model->table_name, $fun_name, $base_fun);

        _db_comment_begin();
        _db_comment("Procedure structure for {$real_fun}");
        _db_comment("Desc : {$fun_title}");
        _db_comment("Param : {$i_param_count}");
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
     * 存储过程的参数
     * @param array $a_param
     */
    function _procBegin($a_param)
    {
        if (count($a_param) > 1) {
            echo "\n";
            echo _tab(1);
            echo implode(",\n" . _tab(1), $a_param);
            echo "\n";
        }
        if (count($a_param) == 1) {
            echo $a_param[0];
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

        list($a_param, $_sql1, $_sql2) = $this->_procWhereCond($model, $o_fun);
        $a_param[] = "INOUT `v_affected_rows` INT";
        self::_procBegin($a_param);
        echo "DECLARE m_affected_rows INT;\n";
        echo "DECLARE s_affected_rows VARCHAR(255);\n";
        echo "DELETE FROM `t_{$model->table_name}` \n WHERE ";
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
        echo "CALL p__debug('{$proc_name}', s_affected_rows);\n";
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

        $a_param = array();//
        $s_sql1 = "";
        $s_sql2 = "";

        $jj = 0;
        if ($o_fun->where != null) {

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
                list($_params, $_sql1, $_sql2) = $this->_procWhereOneCond(0, $jj, $model, $cond, $where_joiner);
                foreach ($_params as $_param) {
                    $a_param[] = $_param;
                }
                if ($_sql1 != "") {
                    $s_sql1 = $s_sql1 . "\n" . $_sql1;
                }
                if ($_sql2 != "") {
                    $s_sql2 = $s_sql2 . "\n" . $_sql2;
                }
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
                        list($_params, $_sql1, $_sql2) = $this->_procWhereOneCond(1, $jj, $model, $cond, $where_joiner2);
                        foreach ($_params as $_param) {
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
                    $s_sql2 = $s_sql2 . "\n" . "SET @s_sql = CONCAT( @s_sql, ')');\n\n";
                }
            }
            //$s_param = implode(",\n" . _tab(1), $a_param);
        }
        return array($a_param, $s_sql1, $s_sql2);
    }

    /**
     * 解析一个返回3个字符串
     * - 参数结构 array
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
        if ($field_type == Constant::DB_FIELD_TYPE_BLOB || $field_type == Constant::DB_FIELD_TYPE_LONGBLOB) {
            //blob字段不参与条件运算
            return array(array(), "", "");
        }
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
                if ($this->isIntType($field_type)) {
                    //int字段不参与like运算
                    return array(array(), "", "");
                }
                return $this->_procWhere_V_like($tab_idx, $inc, $WHERE_JOIN, $field, $cond_type, $v1_type, $v1);
                break;
            default:
                return array(array(), "", "");
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
     * @param $param_inc 防止重复的输入参数自增值，外部生成
     * @param $WHERE_JOIN
     * @param $o_field
     * @param $v_cond
     * @param $v_type
     * @param $val
     * @return string|string[]
     */
    function _procWhere_V1($tab_idx, $param_inc, $WHERE_JOIN, $o_field, $v_cond, $v_type, $val)
    {
        if (!isset(Constant::$a_cond_type_on_sql_1[$v_cond])) {
            SeasLog::error("known cond_type1 to proc");
            return array(array(), "", "");
        }
        $s_cond = Constant::$a_cond_type_on_sql_1[$v_cond];

        $s_param1 = "";
        $a_param1 = array();
        $s_sql1 = "";
        $s_sql2 = "";

        $f_type = $o_field->type;
        $key = $o_field->name;
        $has_if = false;

        switch ($v_type) {
            //固定值，理论上不能时blob的数据
            case Constant::COND_VAl_TYPE_FIXED:
                if ($this->isIntType($f_type)) {
                    $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} {$val}";
                    $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} {$val}'";
                } else {
                    $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} '{$val}'";
                    $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} \'{$val}\''";
                }
                break;

            //函数
            case   Constant::COND_VAl_TYPE_FUN:
                if ($val == "") {
                    SeasLog::error("DB FUN v1 is Empty");
                    return array(array(), "", "");
                    break;
                }
                //自行确保无参数函数已经存在
                $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} {$val}()";
                $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} {$val}()'";
                break;
            //输入值
            case Constant::COND_VAl_TYPE_INPUT:
            default:
                list($param_comment, $param_define, $param_use, $type_size) = $this->_procParam($o_field, $param_inc, "w");
                $s_param1 = $param_use;
                $a_param1[] = $param_define;
                $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} {$s_param1}";
                $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} {$s_param1}'";
                $has_if = true;
                break;
        }
        $s_sql1 = _tab($tab_idx) . $s_sql1;

        if ($has_if) {
            if ($this->isIntType($f_type)) {
                $s_sql3 = _tab($tab_idx) . "IF {$s_param1} != -1 THEN\n";
            } else {
                $s_sql3 = _tab($tab_idx) . "IF {$s_param1} != '' THEN\n";
            }
            $s_sql3 = $s_sql3 . _tab($tab_idx + 1) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
            $s_sql3 = $s_sql3 . _tab($tab_idx) . "END IF;\n";
            $s_sql2 = $s_sql3;
        } else {
            $s_sql2 = _tab($tab_idx) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
        }
        return array($a_param1, $s_sql1, $s_sql2);
    }

    /**
     * @inheritDoc
     * 创建存储过程-查询多个、聚合、统计
     * @param MyModel $model
     */

    /**
     * 是否数字的类型
     * @param $field_type
     * @return bool
     */
    function isIntType($field_type)
    {
        return ($field_type == Constant::DB_FIELD_TYPE_INT || $field_type == Constant::DB_FIELD_TYPE_LONGINT);
    }

    /**
     * 处理参数
     * 不考虑小数,如果是金钱，用分做单位
     * 第一个是参数名，第二个是定义
     *
     * @param MyField $o_field
     * @param string $idx_append 避免重复的计数器
     * @param string $append u/w  update or where
     * @param bool $for_hash 输入是否一堆数据组合
     * @return string[]
     */
    function _procParam($o_field, $idx_append = 0, $append = "", $for_hash = false)
    {
        $param_comment = "";//sql 里不需要参数注释
        $param_define = "";//sql  for input
        $param_use = "";//sql
        $charset = $this->db_conf->charset;
        if ("" == $charset) {
            $charset = "utf8";
        }
        $key = $o_field->name;
        $type = $o_field->type;
        //i_w_1_key
        //c_w_2_from_key
        //s_w_3_to_key
        if (!$for_hash) {
            //这是正常参数
            $prefix = $this->getFieldParamPrefix($type);
            if ($append != "") {
                $prefix = "{$prefix}_{$append}";
            }
            $param_use = "v_{$idx_append}_{$prefix}_{$key}";
            $has_charset = true;
            $size = $o_field->size;
            $type_size = "VARCHAR(255)";
            switch ($type) {
                //布尔
                case Constant::DB_FIELD_TYPE_BOOL :
                    $type_size = "TINYINT";
                    $has_charset = false;
                    break;

                //整型
                case Constant::DB_FIELD_TYPE_INT:
                    //$size = 11;
                    $type_size = "INT";
                    $has_charset = false;
                    break;

                //长整形
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
                    //$type_size = "DATE";
                    $type_size = "VARCHAR(10)";
                    $has_charset = false;
                    break;
                case Constant::DB_FIELD_TYPE_TIME :
                    //$type_size = "DATE";
                    $type_size = "VARCHAR(8)";
                    $has_charset = false;
                    break;
                case Constant::DB_FIELD_TYPE_DATETIME :
                    //$type_size = "DATETIME";
                    $type_size = "VARCHAR(19)";
                    $has_charset = false;
                    break;
                //默认为255的字符串
                default :
                    $type_size = "VARCHAR(255)";
                    break;
            }
        } else {
            //离散函数
            $has_charset = true;
            $param_use = "v_{$idx_append}_s_{$key}";
            $type_size = "VARCHAR(9999)";
        }
        if ($has_charset) {
            $param_define = "IN `{$param_use}` {$type_size} CHARSET {$charset}";
        } else {
            $param_define = "IN `{$param_use}` {$type_size}";
        }
        return array($param_comment, $param_define, $param_use, $type_size);
    }

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
    function _procWhere_V2($tab_idx, $param_inc, $WHERE_JOIN, $o_field, $v_cond, $v1_type, $val1, $v2_type, $val2)
    {
        if (!isset(Constant::$a_cond_type_on_sql_2[$v_cond])) {
            SeasLog::error("unknown cond_type2 to proc");
            return array(array(), "", "");
        }
        $s_cond = Constant::$a_cond_type_on_sql_2[$v_cond];
        $f_type = $o_field->type;
        $key = $o_field->name;

        $s_param = "";
        $s_param1_use = "";
        $s_param2_use = "";

        $s_sql1 = " {$WHERE_JOIN} (`{$key}` {$s_cond} ";
        $s_sql2 = "' {$WHERE_JOIN} (`{$key}` {$s_cond} ";

        $has_if = false;

        switch ($v1_type) {
            //v1固定值
            case Constant::COND_VAl_TYPE_FIXED:
                if ($this->isIntType($f_type)) {
                    $s_sql1 = $s_sql1 . " {$val1} AND";
                    $s_sql2 = $s_sql2 . " {$val1} AND";
                } else {
                    $s_sql1 = $s_sql1 . " \'{$val1}\' AND";
                    $s_sql2 = $s_sql2 . " \'{$val1}\' AND";
                }

                switch ($v2_type) {
                    //v2固定值
                    case Constant::COND_VAl_TYPE_FIXED:
                        if ($this->isIntType($f_type)) {
                            $s_sql1 = $s_sql1 . " {$val2} ";
                            $s_sql2 = $s_sql2 . " {$val2} ";
                        } else {
                            $s_sql1 = $s_sql1 . " \'{$val2}\' ";
                            $s_sql2 = $s_sql2 . " \'{$val2}\' ";
                        }
                        break;

                    //v2函数
                    case Constant::COND_VAl_TYPE_FUN:
                        if ($val2 == "") {
                            SeasLog::error("DB FUN v20 is Empty");
                            return array(array(), "", "");
                        }
                        $s_sql1 = $s_sql1 . " {$val2}() ";
                        $s_sql2 = $s_sql2 . " {$val2}() ";
                        break;

                    //v2输入值
                    case Constant::COND_VAl_TYPE_INPUT:
                    default:

                        list($param_comment, $param_define, $param_use, $type_size) = $this->_procParam($o_field, $param_inc, "to");

                        $s_param2_use = $param_use;
                        $a_param[] = $param_define;

                        $s_sql1 = $s_sql1 . " {$s_param2_use} ";
                        $s_sql2 = $s_sql2 . " {$s_param2_use} ";
                        break;
                }
                break;
            //v1函数
            case Constant::COND_VAl_TYPE_FUN:

                if ($val1 == "") {
                    SeasLog::error("DB FUN v21 is Empty");
                    return array(array(), "", "");
                }
                $s_sql1 = $s_sql1 . " {$val1}() AND";
                $s_sql2 = $s_sql2 . " {$val1}() AND";

                switch ($v2_type) {
                    //v2固定值
                    case Constant::COND_VAl_TYPE_FIXED:
                        if ($this->isIntType($f_type)) {
                            $s_sql1 = $s_sql1 . " {$val2} ";
                            $s_sql2 = $s_sql2 . " {$val2} ";

                        } else {
                            $s_sql1 = $s_sql1 . " \'{$val2}\' ";
                            $s_sql2 = $s_sql2 . " \'{$val2}\' ";
                        }
                        break;
                    //v2函数
                    case Constant::COND_VAl_TYPE_FUN:
                        if ($val2 == "") {
                            SeasLog::error("DB FUN v22 is Empty");
                            return array(array(), "", "");
                        }
                        $s_sql1 = $s_sql1 . " {$val2}() ";
                        $s_sql2 = $s_sql2 . " {$val2}() ";
                        break;

                    //v2输入值
                    case Constant::COND_VAl_TYPE_INPUT:
                    default:
                        list($param_comment, $param_define, $param_use, $type_size) = $this->_procParam($o_field, $param_inc, "to");

                        $s_param2_use = $param_use;
                        $a_param[] = $param_define;

                        $s_sql1 = $s_sql1 . " {$s_param2_use} ";
                        $s_sql2 = $s_sql2 . " {$s_param2_use} ";
                        break;
                }
                break;
            //v1输入值
            case  Constant::COND_VAl_TYPE_INPUT:
            default:

                list($param_comment, $param_define, $param_use, $type_size) = $this->_procParam($o_field, $param_inc, "from");

                $s_param1_use = $param_use;
                $a_param[] = $param_define;


                $s_sql1 = $s_sql1 . " {$s_param1_use} AND ";
                $s_sql2 = $s_sql2 . " {$s_param1_use} AND ";

                switch ($v2_type) {
                    //v2固定值
                    case Constant::COND_VAl_TYPE_FIXED:
                        if ($this->isIntType($f_type)) {
                            $s_sql1 = $s_sql1 . " {$val2} ";
                            $s_sql2 = $s_sql2 . " {$val2} ";

                        } else {
                            $s_sql1 = $s_sql1 . " \'{$val2}\' ";
                            $s_sql2 = $s_sql2 . " \'{$val2}\' ";
                        }
                        break;

                    //v2函数
                    case Constant::COND_VAl_TYPE_FUN:
                        if ($val2 == "") {
                            SeasLog::error("DB FUN v23 is Empty");
                            return array(array(), "", "");
                        }
                        $s_sql1 = $s_sql1 . " {$val2}() ";
                        $s_sql2 = $s_sql2 . " {$val2}() ";
                        break;

                    case  Constant::COND_VAl_TYPE_INPUT:
                    default:
                        list($param_comment, $param_define, $param_use, $type_size) = $this->_procParam($o_field, $param_inc, "to");

                        $s_param2_use = $param_use;
                        $a_param[] = $param_define;

                        $s_sql1 = $s_sql1 . " {$s_param2_use} ";
                        $s_sql2 = $s_sql2 . " {$s_param2_use} ";
                        //不完整的支持，当2个都是输入时，才允许用if判断是否为空
                        $has_if = true;
                        break;
                }

                $s_sql1 = $s_sql1 . " )";
                $s_sql2 = $s_sql2 . " )' ";

                if ($has_if) {
                    if ($this->isIntType($f_type)) {
                        $s_sql3 = _tab($tab_idx) . "IF {$s_param1_use} != -1 AND {$s_param2_use} != -1  THEN\n";
                    } else {
                        $s_sql3 = _tab($tab_idx) . "IF {$s_param1_use} != '' AND {$s_param2_use} != '' THEN\n";
                    }
                    $s_sql3 = $s_sql3 . _tab($tab_idx + 1) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
                    $s_sql3 = $s_sql3 . _tab($tab_idx) . "END IF;\n";
                    $s_sql2 = $s_sql3;
                } else {
                    $s_sql2 = _tab($tab_idx) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
                }

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
            SeasLog::error("unknown cond_type3 to _procWhere_V_range");
            return array(array(), "", "");
        }
        $s_cond = Constant::$a_cond_type_on_sql_3[$v_cond];

        $s_param1 = "";
        $a_param = array();
        $s_sql1 = "";
        $s_sql2 = "";
        $has_if = false;
        $delay_join = false;

        $f_type = $o_field->type;
        $key = $o_field->name;

        switch ($v_type) {
            //固定值
            case Constant::COND_VAl_TYPE_FIXED:
                if ($val == "") {
                    SeasLog::error("V_range value is Empty");
                    return array(array(), "", "");
                }
                if ($this->isIntType($f_type)) {
                    //val should be some string like 1,3,4,5
                    $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} ($val)";
                    $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} ($val)'";
                } else {
                    //val should be some string like a,c,d,e
                    $a_temp = explode(",", $val);
                    $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} ('" . implode("','", $a_temp) . "')";
                    $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} (\'" . implode("\',\'", $a_temp) . "\')'";
                }
                break;
            //函数
            case Constant::COND_VAl_TYPE_FUN:
                if ($val == "") {
                    SeasLog::error("DB FUN is Empty");
                    return array(array(), "", "");
                }
                $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond}({$val}())";
                $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond}({$val}())'";
                break;
            //输入值
            case Constant::COND_VAl_TYPE_INPUT:
            default:
                $has_if = true;
                list($param_comment, $param_define, $param_use, $type_size) = $this->_procParam($o_field, $inc, "", true);
                $s_param1 = $param_use;
                $a_param[] = $param_define;

                $s_sql1 = " {$WHERE_JOIN} `{$key}` {$s_cond} ($s_param1)";
                if ($this->isIntType($f_type)) {
                    $s_sql2 = "' {$WHERE_JOIN} `{$key}` {$s_cond} ($s_param1)'";
                } else {
                    //字符串类,需要外部输入时，先行添加单引号来隔离字符串
                    $delay_join = true;
                    //echo _tab(1) . "SET @s_sql_v = REPLACE(s_{$cond}_{$key}, '|', '\',\'');\n";
                    //echo _tab(1) . "SET @s_sql = CONCAT( @s_sql, ' {$key} NOT IN(\'',@s_sql_v,'\') ');\n";
                }
                break;
        }

        if ($has_if) {
            $s_sql3 = _tab($tab_idx) . "IF {$s_param1} != '' THEN\n";
            if ($delay_join) {
                $s_sql3 = $s_sql3 . _tab($tab_idx + 1) . "SET @s_sql_v = REPLACE({$s_param1}, ',', '\',\'');\n";
                $s_sql3 = $s_sql3 . _tab($tab_idx + 1) . "SET @s_sql = CONCAT( @s_sql, ' {$WHERE_JOIN} `{$key}` {$s_cond} (\'',@s_sql_v,'\')');\n";
            } else {
                $s_sql3 = $s_sql3 . _tab($tab_idx + 1) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
            }
            $s_sql3 = $s_sql3 . _tab($tab_idx) . "END IF;\n";
            $s_sql2 = $s_sql3;
        } else {
            $s_sql2 = _tab($tab_idx) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
        }

        return array($a_param, $s_sql1, $s_sql2);
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

        $s_param1 = "";
        $a_param = array();
        $s_sql1 = "";
        $s_sql2 = "";
        $has_if = false;

        $f_type = $o_field->type;
        $key = $o_field->name;
        //$a_temp0[] = "\'',s_kw,'\',`{$key}`) > 0  ";

        switch ($v_type) {
            //固定值
            case Constant::COND_VAl_TYPE_FIXED:
                if ($val == "") {
                    SeasLog::error("FIXED value is Empty");
                    return array(array(), "", "");
                }
                $s_sql1 = " {$WHERE_JOIN} LOCATE('{$val}', `{$key}`) > 0  ";
                $s_sql2 = "' {$WHERE_JOIN} LOCATE(\'{$val}\', `{$key}`) > 0'";
                break;
            //函数
            case Constant::COND_VAl_TYPE_FUN:
                if ($val == "") {
                    SeasLog::error("DB FUN is Empty");
                    return array(array(), "", "");
                }
                $s_sql1 = " {$WHERE_JOIN} LOCATE({$val}(), `{$key}`) > 0  ";
                $s_sql2 = "' {$WHERE_JOIN} LOCATE({$val}(), `{$key}`) > 0'";

                break;
            //输入值
            case Constant::COND_VAl_TYPE_INPUT:
            default:
                $has_if = true;
                list($param_comment, $param_define, $param_use, $type_size) = $this->_procParam($o_field, $inc, "like");
                $s_param1 = $param_use;
                $a_param[] = $param_define;
                $s_sql1 = " {$WHERE_JOIN} LOCATE($s_param1, `{$key}`) > 0  ";
                $s_sql2 = "' {$WHERE_JOIN} LOCATE($s_param1, `{$key}`) > 0'";
                break;
        }

        if ($has_if) {
            $s_sql3 = _tab($tab_idx) . "IF {$s_param1} != '' THEN\n";
            $s_sql3 = $s_sql3 . _tab($tab_idx + 1) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
            $s_sql3 = $s_sql3 . _tab($tab_idx) . "END IF;\n";
            $s_sql2 = $s_sql3;
        } else {
            $s_sql2 = _tab($tab_idx) . "SET @s_sql = CONCAT( @s_sql, {$s_sql2});\n";
        }
        return array($a_param, $s_sql1, $s_sql2);
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

        $a_all_fields = $model->field_list_kv;
        $limit = $o_fun->limit;//更新限制

        $ii = 0;
        $a_param_all = array();

        //需要更新的字段
        list($i_u_param, $a_u_param_comment, $a_u_param_define, $a_u_param_use, $a_u_param_type, $a_u_param_key, $a_u_param_field) = $this->_parseUpdate_field($model, $o_fun);
        foreach ($a_u_param_define as $item) {
            $a_param_all[] = $item;
        }
        //查询条件的字段
        list($a_param, $_sql1, $_sql2) = $this->_procWhereCond($model, $o_fun);
        $i_count_where = count($a_param);
        foreach ($a_param as $item) {
            $a_param_all[] = $item;
        }
        $a_param_all[] = "INOUT `v_affected_rows` INT";


        $proc_name = self::_procHeader($model, $o_fun->name, $o_fun->title, "update", $i_u_param + $i_count_where);

        self::_procBegin($a_param_all);

        _db_comment("input update count {$i_u_param} ");
        _db_comment("input cond count {$i_count_where} ");

        echo "DECLARE m_affected_rows INT;\n";
        echo "DECLARE s_affected_rows VARCHAR(255);\n";
        echo "UPDATE `t_{$model->table_name}`\n SET ";

        $a_temp = array();
        $ii = 0;
        foreach ($a_u_param_use as $u_key) {
            $key = $a_u_param_key[$ii];
            $a_temp[] = "`{$key}` = {$u_key}";
            $ii++;
        }
        if (isset($a_all_fields["utime"]) && !in_array("utime", $a_u_param_key)) {
            $a_temp[] = "`utime` = NOW()";
        }

        if (count($a_temp) > 1) {
            echo "\n";
            echo _tab(1);
            echo implode(",\n" . _tab(1), $a_temp);
            echo "\n";
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
        echo "CALL p__debug('{$proc_name}', s_affected_rows);\n";

        echo "SELECT m_affected_rows INTO v_affected_rows;\n";
        echo "SELECT m_affected_rows AS i_affected_rows;\n";

        self::_procEnd($model, $proc_name);
    }

    /**
     * @inheritDoc
     * 创建存储过程-查询一个
     * @param MyModel $model
     * @param MyFun $o_fun
     * @return int|void
     */
    function cFetch(MyModel $model, MyFun $o_fun)
    {
        //查询条件的字段
        list($a_param, $_sql1, $_sql2) = $this->_procWhereCond($model, $o_fun);
        $i_param = count($a_param);
        $proc_name = self::_procHeader($model, $o_fun->name, $o_fun->title, "fetch", $i_param);

        self::_procBegin($a_param);

        echo "SELECT * FROM `t_{$model->table_name}`\n WHERE ";
        echo $_sql1;
        echo "\n";
        echo "LIMIT 1;\n";
        self::_procEnd($model, $proc_name);

    }

    function cCount(MyModel $model, MyFun $o_fun)
    {
        //SELECT * FROM xxx a JOIN (select id from xxx limit 1000000, 20) b ON a.ID = b.id;
        $base_fun = strtolower($o_fun->type);
        $fun_name = $o_fun->name;
        $fun_type = $o_fun->type;
        //预先处理查询条件的
        //查询条件的字段
        list($a_param, $_sql1, $_sql2) = $this->_procWhereCond($model, $o_fun);

        $proc_name = self::_procHeader($model, $fun_name, $o_fun->title, $base_fun);
        $group_field = "";
        $group_field_id = $o_fun->group_field;
        if ($group_field_id != "" && isset($model->field_list[$group_field_id])) {
            $o_group_field = $model->field_list[$group_field_id];
            $group_field = $o_group_field->name;
        }
        if ($group_field == "") {
            //默认count id
            $group_field = "id";
        }

        $this->_procBegin($a_param);
        echo "SET @s_sql = 'SELECT COUNT(`{$group_field}`) AS i_count FROM `t_{$model->table_name}` WHERE ';\n";

        //基本条件sql
        echo $_sql2;
        echo "CALL p__debug('{$proc_name}', @s_sql);\n";
        echo "PREPARE stmt FROM @s_sql;\n";
        echo "EXECUTE stmt;\n";

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
        //SELECT * FROM xxx a JOIN (select id from xxx limit 1000000, 20) b ON a.ID = b.id;
        $base_fun = strtolower($o_fun->type);
        $fun_name = $o_fun->name;
        if ($count_only) {
            $fun_name = $fun_name . "_c";
        }
        $proc_name = self::_procHeader($model, $fun_name, $o_fun->title, $base_fun);

        $fun_type = $o_fun->type;
        $group_field = "";
        $o_group_field = null;
        $group_field_id = $o_fun->group_field;
        if (isset($model->field_list[$group_field_id])) {
            $o_group_field = $model->field_list[$group_field_id];
            $group_field = $o_group_field->name;
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
        //先处理having
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

        //预先处理查询条件的
        list($_param, $_sql1, $_sql2) = $this->_procWhereCond($model, $o_fun);


        //预先处理hading的条件
        $o_having = $o_fun->group_having;
        $_param_having = "";
        $_sql1_having = "";
        $_sql2_having = "";

        if ($group_by != "" && $o_having != null && $group_field_final) {
            switch ($o_having->type) {
                case Constant::COND_TYPE_EQ:// = "EQ";//= 等于
                case Constant::COND_TYPE_NEQ:// = "NEQ";//!= 不等于
                case Constant::COND_TYPE_GT:// = "GT";//&GT; 大于
                case Constant::COND_TYPE_GTE:// = "GTE";//&GT;= 大于等于
                case Constant::COND_TYPE_LT:// = "LT";//&LT; 少于
                case Constant::COND_TYPE_LTE:// = "LTE";//&LT;= 少于等于
                    list($_param_having, $_sql1_having, $_sql2_having) = $this->_procHaving_V1(1, 1, $group_field_final, $o_group_field, $o_having->type, $o_having->v1_type, $o_having->v1);
                    break;
                case Constant::COND_TYPE_DATE:    // = "DATE";//关键字模糊匹配
                case Constant::COND_TYPE_TIME:    // = "TIME";//日期范围内
                case Constant::COND_TYPE_DATETIME:    // = "TIME";//日期范围内
                case Constant::COND_TYPE_BETWEEN: // = "BETWEEN";//标量范围内
                case Constant::COND_TYPE_NOTBETWEEN: // = "NOTBETWEEN";//标量范围外
                    list($_param_having, $_sql1_having, $_sql2_having) = $this->_procHaving_V2(1, 1, $group_field_final, $o_group_field, $o_having->type, $o_having->v1_type, $o_having->v1, $o_having->v2_type, $o_having->v2);
                    break;
                case Constant::COND_TYPE_IN:// = "IN";//离散量范围内
                case Constant::COND_TYPE_NOTIN:// = "NOTIN";//离散量范围外
                    list($_param_having, $_sql1_having, $_sql2_having) = $this->_procHaving_V_range(1, 1, $group_field_final, $o_group_field, $o_having->type, $o_having->v1_type, $o_having->v1);
                    break;
                default:

                    break;
            }
        }
        if ($o_having != null && $group_field_final != "") {
            if ($_param != "") {
                $_param = $_param . ",\n" . _tab(1) . $_param_having;
            } else {
                $_param = $_param_having;
            }
        }

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
                $a_temp = array();
                foreach ($o_fun->field_list as $s_key => $o_filed) {
                    if (isset($model->field_list[$s_key])) {
                        $a_temp[] = "\n" . _tab(1) . "`{$o_filed->name}`";
                        $has_pre_key = ",";
                    }
                }
                echo implode(",", $a_temp);
            }

            if ($group_field_sel != "") {
                echo "{$has_pre_key} {$group_field_sel}";
            }

            echo "\nFROM `t_{$model->table_name}` WHERE ';\n";
        }
        //基本条件sql
        echo $_sql2;

        if ($group_by != "") {
            echo "SET @s_sql = CONCAT( @s_sql, ' GROUP BY {$group_by}');\n";
            if ($o_having != null && $group_field_final != "") {
                echo "SET @s_sql = CONCAT( @s_sql, ' HAVING ', {$_sql2_having});\n";
            }
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
        echo "CALL p__debug('{$proc_name}', @s_sql);\n";
        echo "PREPARE stmt FROM @s_sql;\n";
        echo "EXECUTE stmt;\n";

        self::_procEnd($model, $proc_name);

    }

    /**
     * Having聚合的都是整数
     *
     *
     * @param $tab_idx
     * @param $inc
     * @param $new_group_key
     * @param $o_field
     * @param $v_cond
     * @param $v_type
     * @param $val
     * @return string|string[]
     */
    function _procHaving_V1($tab_idx, $inc, $new_group_key, $o_field, $v_cond, $v_type, $val)
    {
        if (!isset(Constant::$a_cond_type_on_sql_1[$v_cond])) {
            SeasLog::error("known cond_type1 to proc");
            return;
        }
        $s_cond = Constant::$a_cond_type_on_sql_1[$v_cond];

        $s_param1 = "";
        $s_param_input = "";
        $s_sql1 = "";
        $s_sql2 = "";

        //输入值
        if ($v_type == Constant::COND_VAl_TYPE_INPUT) {
            list($param_key, $param_key2) = $this->_procParam($o_field, $inc, "gw");
            $s_param1 = $param_key;
            //$s_param_input = $param_key2;
            $s_sql1 = "  `{$new_group_key}` {$s_cond} {$s_param1}";
            $s_sql2 = "' `{$new_group_key}` {$s_cond} {$s_param1}'";
            $s_param_input = "IN {$s_param1} INT";
            $has_if = true;
        }
        //固定值
        if ($v_type == Constant::COND_VAl_TYPE_FIXED) {
            $s_param_input = "";
            $s_sql1 = " {$new_group_key}` {$s_cond} {$val}";
            $s_sql2 = "' {$new_group_key}` {$s_cond} {$val}'";
        }
        //函数
        if ($v_type == Constant::COND_VAl_TYPE_FUN) {
            $s_param_input = "";
            $s_sql1 = " {$new_group_key}` {$s_cond} {$val}()";
            $s_sql2 = "' {$new_group_key}` {$s_cond} {$val}()'";
        }

        if ($s_sql1 != "") {
            $s_sql1 = _tab($tab_idx) . $s_sql1;
        }

        return array($s_param_input, $s_sql1, $s_sql2);
    }

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
     * @param $new_group_key
     * @param $o_field
     * @param $v_cond
     * @param $v1_type
     * @param $val1
     * @param $v2_type
     * @param $val2
     * @return string
     */
    function _procHaving_V2($tab_idx, $inc, $new_group_key, $o_field, $v_cond, $v1_type, $val1, $v2_type, $val2)
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
        $s_param_key1_join = "";
        $s_param_key1_input = "";
        $s_param_key2_join = "";
        $s_param_key2_input = "";

        $s_sql1 = " {$new_group_key} {$s_cond} ";
        $s_sql2 = "' {$new_group_key} {$s_cond} ";

        $has_if = false;
        //v1输入值
        if ($v1_type == Constant::COND_VAl_TYPE_INPUT) {
            list($param_key_join, $param_key_input) = $this->_procParam($o_field, $inc, "gfrom");
            $s_param_key1_join = $param_key_join;
            $s_param_key1_input = $param_key_input;
            $s_param_key1_input = "IN {$s_param_key1_join} INT";

            $s_sql1 = $s_sql1 . " {$s_param_key1_join} AND ";
            $s_sql2 = $s_sql2 . " {$s_param_key1_join} AND ";

            //v2输入值
            if ($v2_type == Constant::COND_VAl_TYPE_INPUT) {
                list($param_key_join, $param_key_input) = $this->_procParam($o_field, $inc, "gto");

                $s_param_key2_join = $param_key_join;
                $s_param_key2_input = $param_key_input;

                $s_param_key2_input = "IN {$s_param_key2_join} INT";

                $s_param = "{$s_param_key1_input},\n" . _tab($tab_idx) . "{$s_param_key2_input}";
                $s_sql1 = $s_sql1 . " {$s_param_key2_join} ";
                $s_sql2 = $s_sql2 . " {$s_param_key2_join} ";
                $has_if = true;
            }
            //v2固定值
            if ($v2_type == Constant::COND_VAl_TYPE_FIXED) {
                $s_param = "{$s_param_key1_input}";
                $s_sql1 = $s_sql1 . " {$val2} ";
                $s_sql2 = $s_sql2 . " {$val2} ";
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
            $s_sql1 = $s_sql1 . " {$val1} AND";
            $s_sql2 = $s_sql2 . " {$val1} AND";

            //v2输入值
            if ($v2_type == Constant::COND_VAl_TYPE_INPUT) {
                list($param_key_join, $param_key_input) = $this->_procParam($o_field, $inc, "gto");

                $s_param_key2_join = $param_key_join;
                $s_param_key2_input = $param_key_input;
                $s_param = "{$s_param_key2_input}";

                $s_sql1 = $s_sql1 . " {$s_param_key2_join} ";
                $s_sql2 = $s_sql2 . " {$s_param_key2_join} ";
            }
            //v2固定值
            if ($v2_type == Constant::COND_VAl_TYPE_FIXED) {
                $s_sql1 = $s_sql1 . " {$val2} ";
                $s_sql2 = $s_sql2 . " {$val2} ";
            }
            //v2函数
            if ($v2_type == Constant::COND_VAl_TYPE_FUN) {
                $s_sql1 = $s_sql1 . " {$val2}() ";
                $s_sql2 = $s_sql2 . " {$val2}() ";
            }
        }
        //v1函数
        if ($v1_type == Constant::COND_VAl_TYPE_FUN) {
            $s_sql1 = $s_sql1 . " {$val2}() AND";
            $s_sql2 = $s_sql2 . " {$val2}() AND";

            //v2输入值
            if ($v2_type == Constant::COND_VAl_TYPE_INPUT) {
                list($param_key_join, $param_key_input) = $this->_procParam($o_field, $inc, "gto");

                $s_param_key2_join = $param_key_join;
                $s_param_key2_input = $param_key_input;
                $s_param = "{$s_param_key2_input}";

                $s_sql1 = $s_sql1 . " {$s_param_key2_join} ";
                $s_sql2 = $s_sql2 . " {$s_param_key2_join} ";
            }
            //v2固定值
            if ($v2_type == Constant::COND_VAl_TYPE_FIXED) {
                $s_sql1 = $s_sql1 . " {$val2} ";
                $s_sql2 = $s_sql2 . " {$val2} ";
            }
            //v2函数
            if ($v2_type == Constant::COND_VAl_TYPE_FUN) {

                $s_sql1 = $s_sql1 . " {$val2}() ";
                $s_sql2 = $s_sql2 . " {$val2}() ";
            }
        }


        if ($s_sql1 != "") {
            $s_sql1 = _tab($tab_idx) . $s_sql1;
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
     * @param $new_group_key
     * @param $o_field
     * @param $v_cond
     * @param $v_type
     * @param $val
     * @return string|string[]
     */
    function _procHaving_V_range($tab_idx, $inc, $new_group_key, $o_field, $v_cond, $v_type, $val)
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
            $s_sql1 = " {$new_group_key} {$s_cond} ($param_key_join)";
            $s_sql2 = "' {$new_group_key} {$s_cond} ($param_key_join)'";
            if ($this->isIntType($f_type)) {

            } else {
                //字符串类
                //需要外部输入时，先行添加单引号来隔离字符串

                //echo _tab(1) . "SET @s_sql_v = REPLACE(s_{$cond}_{$key}, '|', '\',\'');\n";
                //echo _tab(1) . "SET @s_sql = CONCAT( @s_sql, ' {$key} NOT IN(\'',@s_sql_v,'\') ');\n";
            }
            $s_param_input = "IN {$s_param_join} VARCHAR(9999)";
        }
        //固定值
        if ($v_type == Constant::COND_VAl_TYPE_FIXED) {

            $s_sql1 = " {$new_group_key} {$s_cond} ($val)";
            $s_sql2 = "' {$new_group_key} {$s_cond} ($val)'";
        }
        //函数
        if ($v_type == Constant::COND_VAl_TYPE_FUN) {

            $s_sql1 = " {$new_group_key} {$s_cond}({$val}())";
            $s_sql2 = "' {$new_group_key} {$s_cond}({$val}())'";
        }

        return array($s_param_input, $s_sql1, $s_sql2);
    }

    /**
     * 获取聚合的新key
     * @param $fun_type
     * @param $group_field
     * @return string
     */
    public function getGroupKey($fun_type, $group_field)
    {
        $group_field_final = "";
        switch ($fun_type) {
            case Constant::FUN_TYPE_LIST_WITH_AVG:
                $group_field_final = "i_agv_{$group_field}";
                break;
            case Constant::FUN_TYPE_LIST_WITH_SUM:
                $group_field_final = "i_sum_{$group_field}";

                break;
            case Constant::FUN_TYPE_LIST_WITH_MAX:
                $group_field_final = "i_max_{$group_field}";

                break;
            case Constant::FUN_TYPE_LIST_WITH_MIN:
                $group_field_final = "i_min_{$group_field}";

                break;
            case Constant::FUN_TYPE_LIST_WITH_COUNT:
                $group_field_final = "i_count_{$group_field}";

                break;
            default:
                break;
        }
        return $group_field_final;
    }
}