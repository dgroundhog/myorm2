<?php


class DbMysql extends DbBase
{


    /**
     * 公用存储过程头头
     * @param MyModel $model
     * @param string $fun_name
     * @param string $fun_title
     * @param string $base_fun
     * @return string
     */
    static function _procHeader($model, $fun_name, $fun_title, $base_fun)
    {

        if ($fun_name != "" && $fun_name != "default" && $fun_name != $base_fun) {
            $fun = "{$base_fun}_{$fun_name}";
        } else {
            $fun = $base_fun;
        }

        _db_comment_begin();
        _db_comment("Procedure structure for p_{$model->table_name}_{$fun}");
        _db_comment("Desc : {$fun_title}");
        _db_comment_end();

        $user = MyApp::getInstance()->db_conf->user;
        $host = MyApp::getInstance()->db_conf->host;

        echo "DROP PROCEDURE IF EXISTS `p_{$model->table_name}_{$fun}`;\n";
        echo "delimiter ;;\n";
        echo "CREATE DEFINER=`{$user}`@`{$host}` PROCEDURE `p_{$model->table_name}_{$fun}`";
        echo "(";
        return "p_{$model->table_name}_{$fun}";
    }

    /**
     * 存储过程的参数
     * @param array $a_param
     */
    static function _procBegin($a_param)
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
     * 获取参数的前缀
     *
     * @param string $field_type
     * @return string
     */
    static function _procGetKeyPrefix($field_type)
    {
        switch ($field_type) {
            //整型
            case "int":
                return "i";

            //字符字符串
            case "char":
            case "varchar":
            case "text":
            case "longtext":
                return "s";

            //大二进制
            case "blob":
            case "longblob":
                return "lb";

            //时间类型
            case "date":
            case "time":
            case "datetime":
                return "dt";

            //默认字符串
            default :
                return "s";
                break;
        }

    }

    /**
     * 处理参数
     * XXX 不考虑小数,如果是金钱，用分做单位
     *
     * @param MyField $o_field
     * @param string $append_for_update u/w
     *
     * @return string
     */
    static function _procParam($o_field, $append_for_update = "")
    {

        $charset = MyApp::getInstance()->db_conf->charset;
        if ("" == $charset) {
            $charset = "utf8mb4";
        }

        $key = $o_field->field_name;
        $p_type = $o_field->type;
        $p_size = $o_field->size;

        $prefix = self::_procGetKeyPrefix($p_type);

        if ($append_for_update != "") {
            $prefix = "{$prefix}_{$append_for_update}";
        }

        $param_key = "{$prefix}_{$key}";

        switch ($p_type) {
            case "text":
                return "IN `{$param_key}` TEXT  CHARSET {$charset}";

            case "longtext":
                return "IN `{$param_key}` LONGTEXT  CHARSET {$charset}";

            case "blob":
                return "IN `{$param_key}` BLOB ";

            case "longblob":
                return "IN `{$param_key}` LONGBLOB ";


            case "varchar":
                $size = $p_size;
                if ($size < 1 || $size > 9999) {
                    $size = 255;
                }
                return "IN `{$param_key}` VARCHAR ( {$size} ) CHARSET {$charset}";


            case "char":
                $size = $p_size;
                if ($size < 1 || $size > 255) {
                    $size = 1;
                }
                return "IN `{$param_key}` CHAR ( {$size} ) CHARSET {$charset}";


            case "date":
                return "IN `{$param_key}` VARCHAR ( 10 ) CHARSET {$charset}";


            case "time":
            case "datetime":
                return "IN `{$param_key}` VARCHAR ( 19 ) CHARSET {$charset}";


            case "int":
                return "IN `{$param_key}` INT ";


            default:
                return "IN `{$param_key}` VARCHAR ( 255 ) CHARSET {$charset}";

        }
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
     * 创建初始化结构
     * @param MyDbConf $db
     * @return mixed|void
     */
    function ccInitDb()
    {

        _db_comment_begin();
        _db_comment("Init mysql user {$db->user} and database {$db->database}");
        _db_comment("You should run this As super user");
        _db_comment_end();

        $version = $db->version;

        if ($version == "5.6") {
            _db_comment("for mysql 5.6", true);

            echo "CREATE USER IF NOT EXISTS '{$db->user}'@'{$db->host}' IDENTIFIED BY '{$db->password}';\n";
            echo "CREATE DATABASE IF NOT EXISTS `{$db->database}` CHARACTER SET {$db->charset} COLLATE {$db->charset}_general_ci;\n";
            echo "GRANT ALL PRIVILEGES ON `{$db->user}\\_%`.* TO '{$db->user}'@'{$db->host}';\n";
            echo "GRANT SELECT ON mysql.proc TO '{$db->user}'@'{$db->host}';\n";

        }
        if ($version == "5.7") {
            _db_comment("for mysql 5.7", true);

            echo "CREATE DATABASE IF NOT EXISTS `{$db->database}` CHARACTER SET {$db->charset} COLLATE {$db->charset}_general_ci;\n";
            echo "GRANT ALL PRIVILEGES ON `{$db->user}\\_%`.* TO '{$db->user}'@'{$db->host}' IDENTIFIED BY '{$db->password}';\n";
            echo "GRANT SELECT ON mysql.proc TO '{$db->user}'@'{$db->host}' IDENTIFIED BY '{$db->passwd}';\n";

        }

        if ($version == "8.0") {
            _db_comment("for mysql 8.0", true);
            //TODO

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
    function ccTable($model)
    {
        $i_field_size = count($model->table_fields);

        _db_comment_begin();
        _db_comment("Table structure for t_{$model->table_name} [{$model->table_title}]");
        _db_comment("Table fields count ({$i_field_size})");
        _db_comment_end();

        if ($i_field_size == 0) {
            _db_comment("Is is a empty Table");
            return;
        }

        echo "CREATE TABLE `t_{$model->table_name}`\n(\n";

        $ii = 0;
        $has_primary_key = false;
        $a_temp = array();
        $a_table_field_keys = array();
        foreach ($model->table_fields as $key => $field) {
            $ii++;
            $a_table_field_keys[] = $key;
            /* @var MyField $field */
            $size = $field->size;
            $comment = $field->field_title;
            //对于自增和主键，强制修改not_null的值
            if ($key == $model->primary_key) {
                $has_primary_key = true;
            }
            if ($field->auto_increment || $key == $model->primary_key) {
                $field->not_null = true;
            }

            $lc_type = strtolower($field->type);
            $uc_type = strtoupper($field->type);

            switch ($lc_type) {
                //整型
                case "int":

                    if ($size < 1 || $size > 255) {
                        $size = 11;
                    }
                    if ($field->auto_increment) {
                        $a_temp[] = "`{$key}` INT({$size}) NOT NULL AUTO_INCREMENT COMMENT '{$comment}'";
                    } else {
                        if ($field->not_null) {
                            $a_temp[] = "`{$key}` INT({$size}) NOT NULL COMMENT '{$comment}'";
                        } else {
                            $a_temp[] = "`{$key}` INT({$size}) DEFAULT NULL COMMENT '{$comment}'";
                        }
                    }
                    break;

                //单个字符
                case "char":

                    if ($size < 1 || $size > 255) {
                        $size = 1;
                    }
                    if ($field->not_null) {
                        $a_temp[] = "`{$key}` CHAR({$size}) NOT NULL COMMENT '{$comment}'";
                    } else {
                        $a_temp[] = "`{$key}` CHAR({$size}) DEFAULT NULL COMMENT '{$comment}'";
                    }
                    break;

                //字符串
                case "varchar":

                    if ($size < 1 || $size > 9999) {
                        $size = 255;
                    }

                    if ($field->not_null) {
                        $a_temp[] = "`{$key}` VARCHAR({$size}) NOT NULL COMMENT '{$comment}'";
                    } else {
                        $a_temp[] = "`{$key}` VARCHAR({$size}) DEFAULT NULL COMMENT '{$comment}'";
                    }


                    break;

                //其他字段
                case "text":
                case "longtext":
                case "blob":
                case "longblob":
                case "date":
                case "time":
                case "datetime":

                    if ($field->not_null) {
                        $a_temp[] = "`{$key}` {$uc_type} NOT NULL  COMMENT '{$comment}'";
                    } else {
                        $a_temp[] = "`{$key}` {$uc_type} DEFAULT NULL  COMMENT '{$comment}'";
                    }
                    break;

                //默认为255的字符串
                default :
                    $a_temp[] = "`{$key}` VARCHAR(255) DEFAULT NULL  COMMENT '{$comment}'";
                    break;
            }
        }

        if ($has_primary_key) {
            $a_temp[] = "PRIMARY KEY (`{$model->primary_key}`)";
        }

        if (is_array($model->unique_key) && count($model->unique_key) > 0) {
            foreach ($model->unique_key as $index_name => $a_index) {
                if (!is_array($a_index) || count($a_index) == 0) {
                    continue;
                }
                $a_temp0 = array();
                $jj = 0;
                foreach ($a_index as $key) {
                    if (in_array($key, $a_table_field_keys)) {
                        $jj++;
                        $a_temp0[] = "`{$key}`";
                    }
                }
                $s_temp0 = implode(", ", $a_temp0);
                $a_temp[] = "UNIQUE uk_{$model->table_name}_{$index_name} ({$s_temp0})";
            }
        }

        if (is_array($model->index_key) && count($model->index_key) > 0) {
            foreach ($model->index_key as $index_name => $a_index) {
                if (!is_array($a_index) || count($a_index) == 0) {
                    //
                    continue;
                }
                $a_temp0 = array();
                $jj = 0;
                foreach ($a_index as $key) {
                    if (in_array($key, $a_table_field_keys)) {
                        $jj++;
                        $a_temp0[] = "`{$key}`";
                    }
                }
                $s_temp0 = implode(", ", $a_temp0);
                $a_temp[] = "UNIQUE ik_{$model->table_name}_{$index_name} ({$s_temp0})";
            }
        }

        echo _tab(1);
        echo implode(",\n" . _tab(1), $a_temp);
        echo "\n)";

        $charset = MyApp::getInstance()->db_conf->charset;
        if ("" == $charset) {
            $charset = "utf8mb4";
        }

        echo "ENGINE=InnoDB\n";
        echo "DEFAULT CHARSET={$charset}\n";
        echo "COLLATE {$charset}_general_ci\n";
        echo "COMMENT='{$model->table_title} 表定义';";
    }

    /**
     * @inheritDoc
     */
    function ccTable_reset($model)
    {
        // TODO: Implement ccTable_reset() method.
        _db_comment("Delete Table   t_{$model->table_name}", true);
        echo "DROP TABLE IF EXISTS `t_{$model->table_name}`;\n";
    }

    /**
     * @inheritDoc
     */
    function ccProc($model)
    {
        if ($model->add_enable) {
            self::cAdd($model);
        }

        if ($model->delete_enable) {
            self::cDelete($model);
        }

        if ($model->update_enable) {
            self::cUpdate($model);
        }

        if ($model->fetch_enable) {
            self::cFetch($model);
        }

        if ($model->list_enable) {
            self::cList($model);
        }


    }


    /**
     * @inheritDoc
     * 只有一种插入
     * 创建存储过程-添加
     * @param MyModel $model
     */
    function cAdd($model)
    {
        $proc_name = self::_procHeader($model, "add", "插入数据", "add");

        $ii = 0;
        $a_temp = array();
        foreach ($model->table_fields as $key => $field) {
            if (!in_array($key, $model->add_keys)) {
                continue;
            }
            $ii++;
            $a_temp[] = self::_procParam($field);
        }
        if ($model->add_will_return_new_id) {
            $ii++;
            $a_temp[] = "INOUT `v_new_id` INT";
        }
        self::_procBegin($a_temp);

        echo "DECLARE m_new_id INT;\n";

        echo "INSERT INTO `t_{$model->table_name}` \n(\n";

        $ii = 0;
        $a_temp = array();
        foreach ($model->table_fields as $key => $field) {
            if ($key == "id") {
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
        foreach ($model->table_fields as $key => $field) {
            if ($key == "id") {
                continue;
            }
            $ii++;
            /* @var MyField $field */

            if (!in_array($key, $model->add_keys)) {
                //部分预置值
                switch ($key) {
                    case "flag":
                        $a_temp[] = "'n'";
                        break;

                    case "state":
                        if ($field->default_value != "") {
                            if ($field['type'] == "int") {
                                $a_temp[] = "{$field->default_value}";
                            } else {
                                $a_temp[] = "'{$field->default_value}'";
                            }
                        } else {
                            if ($field['type'] == "int") {
                                $a_temp[] = "0";
                            } else {
                                $a_temp[] = "'n'";
                            }
                        }
                        break;

                    case "ctime":
                    case "utime":
                        $a_temp[] = "NOW()";
                        break;

                    default:
                        if ($field->default_value != "") {
                            if ($field['type'] == "int") {
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
                $prefix = self::_procGetKeyPrefix($field['type']);
                $param_key = "{$prefix}_{$key}";
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

        if ($model->add_will_return_new_id) {
            echo "SELECT m_new_id INTO v_new_id;\n";
            echo "SELECT m_new_id AS i_new_id;\n";
        }

        self::_procEnd($model, $proc_name);
    }

    /**
     * @inheritDoc
     * 有多种更新
     * 创建存储过程-更新
     * @param MyModel $model
     */
    function cUpdate($model)
    {

        foreach ($model->update_confs as $update_name => $a_update_conf) {
            $s_update_title = $a_update_conf['update_title'];//更新的标题
            $a_update_keys = $a_update_conf['update_keys'];//更新的内容
            $a_update_by = $a_update_conf['update_by'];//更新依据
            $limit = $a_update_conf['limit'];//更新依据

            $proc_name = self::_procHeader($model, $update_name, $update_title, "update");


            $ii = 0;
            $a_temp = array();
            foreach ($update_keys as $key) {
                $ii++;
                $a_temp[] = self::_procParam($field, "u");
            }

            $jj = 0;
            foreach ($update_by as $key) {
                $jj++;
                $a_temp[] = self::_procParam($field, "w");
            }
            $a_temp[] = "INOUT `v_affected_rows` INT";

            self::_procBegin($a_temp);

            _mysql_comment("input u count {$ii} w count {$jj}");

            echo "DECLARE m_affected_rows INT;\n";
            echo "DECLARE s_affected_rows VARCHAR(12);\n";
            echo "UPDATE `t_{$model['table_name']}` SET ";

            $ii = 0;
            $a_temp = array();
            foreach ($update_keys as $key) {
                $ii++;
                $p_type = $model->table_fields[$key]['type'];
                $prefix = self::_procGetKeyPrefix($p_type);
                $key2 = "{$prefix}_u_$key";
                $a_temp[] = "`{$key}` = {$key2}";
            }

            if (isset($model->table_fields["utime"]) && !in_array("utime", $update_keys)) {
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

            $jj = 0;
            $a_temp = array();
            $a_temp[] = " 1 = 1";
            foreach ($update_by as $key) {
                $jj++;

                $p_type = $model->table_fields[$key]['type'];
                $prefix = self::_procGetKeyPrefix($p_type);
                $key2 = "{$prefix}_w_$key";
                $a_temp[] = "`{$key}` = {$key2}";
            }

            //只有正常数据才能更新
            if (isset($model->table_fields['flag'])) {
                $a_temp[] = "`flag` = 'n'";
            }

            echo implode("\n" . _tab(1) . "AND ", $a_temp);

            if ($limit > 0) {
                echo "\n";
                echo "LIMIT {$limit};\n";
            }
            _mysql_comment("query u count {$ii} w count {$jj}");


            echo "SET m_affected_rows = ROW_COUNT();\n";
            //echo "COMMIT;\n";
            echo "SET s_affected_rows = CONCAT( '' , m_affected_rows);\n";
            echo "CALL p_debug('{$proc_name}', s_affected_rows);\n";

            echo "SELECT m_affected_rows INTO v_affected_rows;\n";
            echo "SELECT m_affected_rows AS i_affected_rows;\n";


            self::_procEnd($model, $proc_name);
        }
    }

    /**
     * @inheritDoc
     * 创建存储过程-删除
     * @param MyModel $model
     */
    function cDelete($model)
    {
        $a_delete_confs = _model_get_ok_delete($model);
        foreach ($a_delete_confs as $delete_name => $a_delete_conf) {
            $a_delete_by = $a_delete_conf['delete_by'];// 删除依据
            $limit = $a_delete_conf['limit'];// 删除依据
            $proc_name = self::_procHeader($model, $delete_name, $delete_title, "delete");


            $jj = 0;
            foreach ($a_delete_by as $key) {
                $jj++;
                $a_temp[] = self::_procParam($field, "w");
            }
            $a_temp[] = "INOUT `v_affected_rows` INT";

            self::_procBegin($a_temp);

            _mysql_comment("input  w count {$jj}");

            echo "DECLARE m_affected_rows INT;\n";
            echo "DECLARE s_affected_rows VARCHAR(12);\n";

            echo "DELETE FROM `t_{$model['table_name']}` WHERE ";


            $jj = 0;
            $a_temp = array();
            $a_temp[] = " 1 = 1";
            foreach ($a_delete_by as $key) {
                $jj++;

                $p_type = $model->table_fields[$key]['type'];
                $prefix = self::_procGetKeyPrefix($p_type);
                $key2 = "{$prefix}_w_$key";
                $a_temp[] = "`{$key}` = {$key2}";
            }


            echo implode("\n" . _tab(1) . "AND ", $a_temp);

            if ($limit > 0) {
                echo "\n";
                echo "LIMIT {$limit};\n";
            }
            _mysql_comment("query w count {$jj}");


            echo "SET m_affected_rows = ROW_COUNT();\n";
            //echo "COMMIT;\n";
            echo "SET s_affected_rows = CONCAT( '' , m_affected_rows);\n";
            echo "CALL p_debug('{$proc_name}', s_affected_rows);\n";

            echo "SELECT m_affected_rows INTO v_affected_rows;\n";
            echo "SELECT m_affected_rows AS i_affected_rows;\n";

            self::_procEnd($model, $proc_name);
        }
    }

    /**
     * @inheritDoc
     * 创建存储过程-查询一个
     * @param MyModel $model
     */
    function cFetch($model)
    {
        /**
         * 默认主键查询
         */
        $fetch_by = _model_get_ok_fetch_by($model);
        if (count($fetch_by) > 0) {
            _mysql_create_proc_fetch($model, "default", "默认主键查询", $fetch_by);
            /**
             * 其他可能的主键查询单条语句
             * fetch_by_other=>
             *      ----fetch_name  => fetch_title
             *                      => fetch_by
             */
            $a_fetch_by_other = _model_get_ok_other_fetch_by($model);

            foreach ($a_fetch_by_other as $fetch_name => $a_vv) {
                $fetch_title = $a_vv['fetch_title'];
                $fetch_by = $a_vv['fetch_by'];
                $proc_name = self::_procHeader($model, $fetch_name, $fetch_title, "fetch");


                $ii = 0;
                $a_temp = array();
                foreach ($fetch_by as $key) {
                    $ii++;
                    $a_temp[] = self::_procParam($field);
                }
                self::_procBegin($a_temp);

                echo "SELECT * FROM `t_{$model['table_name']}` WHERE ";

                $ii = 0;
                $a_temp = array();
                $a_temp[] = " 1 = 1";
                foreach ($fetch_by as $key) {
                    $ii++;
                    $prefix = self::_procGetKeyPrefix($model["table_fields"][$key]['type']);
                    $a_temp[] = "`{$key}` = `{$param_key}`";
                }

                if (isset($model->table_fields['flag'])) {
                    $ii++;
                    $a_temp[] = "`flag`='n'";
                }

                echo implode("\n" . _tab(1) . "AND ", $a_temp);
                echo "\n";
                echo "LIMIT 1;\n";
                //echo "CALL p_debug('{$proc_name}', '1');\n";

                self::_procEnd($model, $proc_name);
            }
        }

    }

    /**
     * @inheritDoc
     * 创建存储过程-查询多个、聚合、统计
     * @param MyModel $model
     */
    function cList($model)
    {
        // TODO: Implement ccList() method.
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
                $prefix = self::_procGetKeyPrefix($p_type);

                if ($p_type == "int") {
                    echo "IF {$prefix}_{$key} >= 0 THEN\n";
                    echo _tab(1) . "SET @sql = CONCAT( @sql, ' AND `{$key}` =  \'', {$prefix}_{$key}, '\' ' );\n";
                    echo "END IF;\n";
                } else {
                    echo "IF {$prefix}_{$key} != '' THEN\n";
                    echo _tab(1) . "SET @sql = CONCAT( @sql, ' AND `{$key}` =  \'', {$prefix}_{$key}, '\' ' );\n";
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
                        echo _tab(1) . "SET @sql = CONCAT( @sql, ' AND (\n";
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
                        echo _tab(1) . "SET @sql = CONCAT( @sql, ' AND (";
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
                        echo _tab(1) . "SET @sql = CONCAT( @sql, ' AND (";
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
                        echo "SET @sql = CONCAT( @sql, ' AND (";
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
                        echo "SET @sql = CONCAT( @sql, ' AND (";
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
                            echo _tab(1) . "SET @sql = CONCAT( @sql, ' {$key} IN(', s_{$cond}_{$key}, ') ');\n";
                        } else {
                            echo _tab(1) . "SET @sql_v = REPLACE(s_{$cond}_{$key}, '|', '\',\'');\n";
                            echo _tab(1) . "SET @sql = CONCAT( @sql, ' {$key} IN(\'',@sql_v,'\') ');\n";
                        }
                        echo "END IF;\n";
                        break;

                    case "notin":
                        $key = $my_list->$kv;
                        //$a_temp[] = "IN `s_{$cond}_{$key}` VARCHAR ( 9999 )";
                        $ii++;
                        echo "IF s_{$cond}_{$key} != '' THEN\n";
                        if ($key == "int") {
                            echo _tab(1) . "SET @sql = CONCAT( @sql, ' {$key} NOT IN(', s_{$cond}_{$key}, ') ');\n";
                        } else {
                            echo _tab(1) . "SET @sql_v = REPLACE(s_{$cond}_{$key}, '|', '\',\'');\n";
                            echo _tab(1) . "SET @sql = CONCAT( @sql, ' {$key} NOT IN(\'',@sql_v,'\') ');\n";
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
                        echo "SET @sql = CONCAT( @sql, ' AND (";
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
            echo "SET @sql = CONCAT( @sql, ' AND `flag` = \'n\'');\n";
        }

        //仅查询数量
        if ($count_only) {
            return;
        }


        if (!$my_list->list_has_group && $my_list->list_has_pager) {
            if ($my_list->list_pager_type != "normal") {
                $kkk = $my_list->cursor_offset_key;
                if ($my_list->list_order_dir == "" || $my_list->list_order_dir == "DESC") {
                    echo "SET @sql = CONCAT( @sql, ' AND {$kkk} < ',i_cursor_from,' ');\n";
                } else {
                    echo "SET @sql = CONCAT( @sql, ' AND {$kkk} > ',i_cursor_from,' ');\n";
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
                    echo "SET @sql = CONCAT( @sql, ' ORDER BY ',s_order_by,' ',s_order_dir);\n";
                } else {
                    if ($my_list->list_order_dir == "") {
                        echo "SET @sql = CONCAT( @sql, ' ORDER BY {$my_list->list_order_by} ',s_order_dir);\n";
                        //echo "SET @sql = CONCAT( @sql, ' ORDER BY ',s_order_by,' {$my_list->list_order_dir}');\n";
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
                    echo "SET @sql = CONCAT( @sql, ' ORDER BY {$s_temp}');\n";
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
                    echo "SET @sql = CONCAT( @sql, ' LIMIT  ', m_offset, ',',m_length);\n";
                } else {
                    //页码,TODO 偏移量  大于数id
                    echo "SET @sql = CONCAT( @sql, ' LIMIT  ', i_cursor_offset);\n";
                }
            }
        }
        return;
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

        echo "SET @sql = 'SELECT COUNT(`{$my_list->list_count_key}`) AS i_count FROM`t_{$model['table_name']}` WHERE 1=1 '; \n";

        _mysql_proc_list_sql($model, $my_list, true);
        echo "CALL p_debug('{$proc_name}', @sql);\n";
        echo "PREPARE stmt FROM @sql;\n";
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


        echo "SET @sql = 'SELECT ";

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
        echo "CALL p_debug('{$proc_name}', @sql);\n";
        echo "PREPARE stmt FROM @sql;\n";
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
}