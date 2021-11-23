<?php

class Constant
{

    /**
     * 支持的开发语言
     */
    const   LANG_PHP = "php";
    const   LANG_JAVA = "java";
    const   LANG_CPP = "cpp";

    const   UI_NULL = "no_ui";
    const   UI_BOOTADMIN_SIMPLE = "bootadmin_simple";
    const   UI_ADMINLTE_31 = "adminlte_31";
    const   UI_YAHOO_PURECSS = "yahoo_purecss";


    const   MVC_PHP_PHALCON = "php_phalcon";
    const   MVC_JAVA_SERVLET = "java_servlet";
    const   MVC_CPP_QHTTP = "cpp_qhttp";
    /**
     * 一组驱动常量
     */
    const   DB_MYSQL_56 = "mysql56";
    const   DB_MYSQL_57 = "mysql57";
    const   DB_MYSQL_80 = "mysql80";
    const   DB_SQLITE_30 = "sqlite3";
    const   DB_POSTGRESQL_96 = "postgresql96";

    /**
     * db source
     */
    const   DB_SOURCE_ENV = "env";
    const   DB_SOURCE_EMBED = "embed";

    /**
     * db charset
     */
    const   DB_CHARSET_GBK = "gbk";
    const   DB_CHARSET_UTF8 = "utf8";
    const   DB_CHARSET_UTF8MB4 = "utf8mb4";

    /**
     * 条件链接方式
     */
    const   WHERE_JOIN_AND = "and";
    const   WHERE_JOIN_OR = "or";
    /**
     * 条件链接方式
     */
    const   WHERE_TYPE_AND = "and";
    const   WHERE_TYPE_OR = "or";
    /**
     * 允许的mvc方案，目前先默认一下，后续再扩展
     * @var string[]
     */
    public static $a_build_mvc = array(
        Constant::MVC_PHP_PHALCON => "PHP_PHALCON",
        Constant::MVC_JAVA_SERVLET => "JAVA_SERVLET",
        Constant::MVC_CPP_QHTTP => "QT_QHTTP"
    );
    /**
     * 允许的db
     * @var string[]
     */
    public static $a_build_db = array(
        Constant::DB_MYSQL_56 => "MySql_5.6",
        Constant::DB_MYSQL_57 => "MySql_5.7",
        Constant::DB_MYSQL_80 => "MySql_8.0",
        Constant::DB_SQLITE_30 => "Sqlite_30",
        Constant::DB_POSTGRESQL_96 => "Postgresql_9.6"
    );

    /**
     * db 来源
     * @var string[]
     */
    public static $a_build_db_source = array(
        Constant::DB_SOURCE_ENV => "环境变量",
        Constant::DB_SOURCE_EMBED => "程序配置文件"
    );

    /**
     * db编码
     * @var string[]
     */
    public static $a_build_db_charset = array(
        Constant::DB_CHARSET_UTF8 => "UTF8编码",
        Constant::DB_CHARSET_UTF8MB4 => "UTF8MB4编码",
        Constant::DB_SOURCE_EMBED => "GBK编码"
    );


    /**
     * 允许的ui方案，
     * @var string[]
     */
    public static $a_build_ui = array(
        Constant::UI_NULL => "NO_UI",
        Constant::UI_BOOTADMIN_SIMPLE => "Bootadmin_simple",
        Constant::UI_ADMINLTE_31 => "Adminlte_3.1.0",
        Constant::UI_YAHOO_PURECSS => "Yahoo_purecss",
    );
}