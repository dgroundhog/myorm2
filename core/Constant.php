<?php

class Constant
{

    /**
     * 支持的开发语言
     */
    const   LANG_PHP = "PHP";
    const   LANG_JAVA = "JAVA";
    const   LANG_CPP = "CPP";

    const   UI_NULL = "NO_UI";
    const   UI_BOOTADMIN_SIMPLE = "BOOTADMIN_SIMPLE";
    const   UI_ADMINLTE_31 = "ADMINLTE_31";
    const   UI_YAHOO_PURECSS = "YAHOO_PURECSS";


    const   MVC_PHP_PHALCON = "PHP_PHALCON";
    const   MVC_JAVA_SERVLET = "JAVA_SERVLET";
    const   MVC_CPP_QHTTP = "CPP_QHTTP";
    /**
     * 一组驱动常量
     */
    const   DB_MYSQL_56 = "MYSQL56";
    const   DB_MYSQL_57 = "MYSQL57";
    const   DB_MYSQL_80 = "MYSQL80";
    const   DB_SQLITE_30 = "SQLITE3";
    const   DB_POSTGRESQL_96 = "POSTGRESQL96";

    /**
     * db source
     */
    const   DB_SOURCE_ENV = "ENV";
    const   DB_SOURCE_EMBED = "EMBED";

    /**
     * db charset
     */
    const   DB_CHARSET_GBK = "GBK";
    const   DB_CHARSET_UTF8 = "UTF8";
    const   DB_CHARSET_UTF8MB4 = "UTF8MB4";

    /**
     * 条件链接方式
     */
    const   WHERE_JOIN_AND = "AND";
    const   WHERE_JOIN_OR = "OR";
    /**
     * 条件链接方式
     */
    const   WHERE_TYPE_AND = "AND";
    const   WHERE_TYPE_OR = "OR";
    /**
     * 允许的mvc方案，目前先默认一下，后续再扩展
     * @var string[]
     */
    public static $a_build_mvc = array(
        self::MVC_PHP_PHALCON => "PHP_PHALCON",
        self::MVC_JAVA_SERVLET => "JAVA_SERVLET",
        self::MVC_CPP_QHTTP => "QT_QHTTP"
    );
    /**
     * 允许的db
     * @var string[]
     */
    public static $a_build_db = array(
        self::DB_MYSQL_56 => "MySql_5.6",
        self::DB_MYSQL_57 => "MySql_5.7",
        self::DB_MYSQL_80 => "MySql_8.0",
        self::DB_SQLITE_30 => "Sqlite_30",
        self::DB_POSTGRESQL_96 => "Postgresql_9.6"
    );

    /**
     * db 来源
     * @var string[]
     */
    public static $a_build_db_source = array(
        self::DB_SOURCE_ENV => "环境变量",
        self::DB_SOURCE_EMBED => "程序配置文件"
    );

    /**
     * db编码
     * @var string[]
     */
    public static $a_build_db_charset = array(
        self::DB_CHARSET_UTF8 => "UTF8编码",
        self::DB_CHARSET_UTF8MB4 => "UTF8MB4编码",
        self::DB_SOURCE_EMBED => "GBK编码"
    );

    const DB_FIELD_TYPE_BOOL = "bool";
    const DB_FIELD_TYPE_CHAR = "char";
    const DB_FIELD_TYPE_VARCHAR = "string";
    const DB_FIELD_TYPE_TEXT = "text";
    const DB_FIELD_TYPE_LONGTEXT = "text";
    const DB_FIELD_TYPE_INT = "int";
    const DB_FIELD_TYPE_LONG = "longint";
    const DB_FIELD_TYPE_BLOB = "blob";
    const DB_FIELD_TYPE_LONGBLOB = "longblob";
    const DB_FIELD_TYPE_DATE = "date";
    const DB_FIELD_TYPE_DATETIME = "datetime";

    /**
     * db编码
     * @var string[]
     */
    public static $a_build_db_field_type = array(

        self::DB_FIELD_TYPE_VARCHAR => "字符串",
        self::DB_FIELD_TYPE_INT => "整形",
        self::DB_FIELD_TYPE_LONGBLOB => "长BLOB",
        self::DB_FIELD_TYPE_CHAR => "单字符",
        self::DB_FIELD_TYPE_TEXT => "普通文本",
        self::DB_FIELD_TYPE_LONGTEXT => "长文本",
        self::DB_FIELD_TYPE_LONG => "长整形",
        self::DB_FIELD_TYPE_BLOB => "BLOB",
        self::DB_FIELD_TYPE_BOOL => "布尔",
        self::DB_FIELD_TYPE_DATE => "日期",
        self::DB_FIELD_TYPE_DATETIME => "时间"
    );

    const DB_FIELD_FILTER_NULL = "NO_FILTER";
    const DB_FIELD_FILTER_INT = "INT";
    const DB_FIELD_FILTER_FLOAT = "FLOAT";
    const DB_FIELD_FILTER_BOOL = "BOOL";
    const DB_FIELD_FILTER_DOMAIN = "DOMAIN";
    const DB_FIELD_FILTER_EMAIL = "EMAIL";
    const DB_FIELD_FILTER_DATE = "DATE";
    const DB_FIELD_FILTER_DATETIME = "DATETIME";
    const DB_FIELD_FILTER_IP = "IP";
    const DB_FIELD_FILTER_MAC = "MAC";
    const DB_FIELD_FILTER_URL = "URL";
    const DB_FIELD_FILTER_REGEXP 	 = "REGEXP";

    /**
     * 验证功能
     * @var string[]
     */
    public static $a_build_db_field_filter = array(
    self::DB_FIELD_FILTER_NULL  => "不验证",
    self::DB_FIELD_FILTER_INT => "整数",
    self::DB_FIELD_FILTER_FLOAT  => "浮点数字",
    self::DB_FIELD_FILTER_BOOL => "布尔",
    self::DB_FIELD_FILTER_EMAIL  => "电子邮件",
    self::DB_FIELD_FILTER_DATE => "日期",
    self::DB_FIELD_FILTER_DATETIME  => "完整时间",
        self::DB_FIELD_FILTER_DOMAIN  => "域名",
    self::DB_FIELD_FILTER_IP => "IP-V4格式",
    self::DB_FIELD_FILTER_MAC  => "网卡MAC地址",
    self::DB_FIELD_FILTER_URL  => "互联网URL",
    self::DB_FIELD_FILTER_REGEXP  => "自定义正则表达式"
    );

    const DB_FIELD_INPUT_DEFAULT = "DEFAULT";
    const DB_FIELD_INPUT_UPLOAD_FILE = "UPLOAD_FILE ";
    const DB_FIELD_INPUT_UPLOAD_IMAGE = "UPLOAD_IMAGE";
    const DB_FIELD_INPUT_SELECT = "SELECT";
    const DB_FIELD_INPUT_RADIO	 = "RADIO";
    const DB_FIELD_INPUT_CHECKBOX 	 = "CHECKBOX";

    /**
     * 输入办法
     * @var string[]
     */
    public static $a_build_db_field_input = array(

        self::DB_FIELD_INPUT_DEFAULT => "默认输入",
    self::DB_FIELD_INPUT_UPLOAD_FILE  => "普通文件上传",
    self::DB_FIELD_INPUT_UPLOAD_IMAGE  => "图片上传",
    self::DB_FIELD_INPUT_SELECT  => "下拉框",
    self::DB_FIELD_INPUT_RADIO	 => "单选框",
    self::DB_FIELD_INPUT_CHECKBOX 	  => "复选框"
    );
    /**
     * 允许的ui方案，
     * @var string[]
     */
    public static $a_build_ui = array(
        self::UI_NULL => "NO_UI",
        self::UI_BOOTADMIN_SIMPLE => "Bootadmin_simple",
        self::UI_ADMINLTE_31 => "Adminlte_3.1.0",
        self::UI_YAHOO_PURECSS => "Yahoo_purecss",
    );
}