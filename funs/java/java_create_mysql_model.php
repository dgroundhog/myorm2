<?php
if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");


include_once JAVA_BASE . "/java_mysql_add.php";
include_once JAVA_BASE . "/java_mysql_count.php";
include_once JAVA_BASE . "/java_mysql_delete.php";
include_once JAVA_BASE . "/java_mysql_drop.php";
include_once JAVA_BASE . "/java_mysql_fetch.php";
include_once JAVA_BASE . "/java_mysql_list.php";
include_once JAVA_BASE . "/java_mysql_list_all.php";
include_once JAVA_BASE . "/java_mysql_list_basic.php";
include_once JAVA_BASE . "/java_mysql_list_by_ids.php";
include_once JAVA_BASE . "/java_mysql_update.php";
include_once JAVA_BASE . "/java_mysql_sum.php";
include_once JAVA_BASE . "/java_mysql_update_state.php";

/**
 * 建立java抽象类
 * @param $package
 * @param $model
 */
function java_create_mysql_model($package, $model)
{

    $uc_table = ucfirst($model['table_name']);

    echo "package  {$package}.db.mysql;\n";


    echo "import {$package}.bean.{$uc_table}Bean;\n";
    echo "import {$package}.db.base.Db{$uc_table};\n";
    echo "import {$package}.db.DbMysql;\n";

    echo "import org.slf4j.Logger;\n";
    echo "import org.slf4j.LoggerFactory;\n";

    echo "import java.io.InputStream;\n";
    echo "import java.io.ByteArrayInputStream;\n";
    echo "import java.sql.CallableStatement;\n";
    echo "import java.sql.Connection;\n";
    echo "import java.sql.ResultSet;\n";
    echo "import java.sql.Types;\n";
    echo "import java.sql.SQLException;\n";
    echo "import java.util.HashMap;\n";
    echo "import java.util.Map;\n";
    echo "import java.util.Vector;\n";


    _java_comment("java mysql 操作模型类--{$model['table_title']}");
    echo "public class DbMysql{$uc_table} extends Db{$uc_table} {\n";

    echo "private  static Logger logger = LoggerFactory.getLogger(DbMysql{$uc_table}.class);\n\n";

    java_mysql_add($model);
    java_mysql_count($model);
    java_mysql_delete($model);
    java_mysql_drop($model);
    java_mysql_fetch($model);
    java_mysql_list($model);
    java_mysql_list_all($model);
    java_mysql_list_basic($model);
    java_mysql_list_by_ids($model);
    java_mysql_sum($model);
    java_mysql_update($model);
    java_mysql_update_state($model);

    echo "}";
}