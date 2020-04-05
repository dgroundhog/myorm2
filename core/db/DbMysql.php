<?php


class DbMysql extends DbBase
{


    /**
     * 创建初始化结构
     * @param MyDb $db
     * @return mixed|void
     */
    function ccInitDb($db)
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
     * @inheritDoc
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
     *
     * 创建存储过程-添加
     * @param MyModel $model
     */
    function cAdd($model)
    {
        // TODO: Implement ccAdd() method.
    }

    /**
     * @inheritDoc
     * 创建存储过程-更新
     * @param MyModel $model
     */
    function cUpdate($model)
    {
        // TODO: Implement ccUpdate() method.
    }

    /**
     * @inheritDoc
     * 创建存储过程-删除
     * @param MyModel $model
     */
    function cDelete($model)
    {
        // TODO: Implement ccDelete() method.
    }

    /**
     * @inheritDoc
     * 创建存储过程-查询一个
     * @param MyModel $model
     */
    function cFetch($model)
    {
        // TODO: Implement ccFetch() method.

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
}