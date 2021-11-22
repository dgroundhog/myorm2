<?php

class Constant
{

    /**
     * 支持的开发语言
     */
    const   LANG_PHP = "php";
    const   LANG_JAVA = "java";
    const   LANG_CPP = "cpp";

    /**
     * 允许的mvc方案，目前先默认一下，后续再扩展
     * @var string[][]
     */
    public static $a_allow_mvc = array(
        Constant::LANG_PHP => array("phalcon"),
        Constant::LANG_JAVA => array("servlet"),
        Constant::LANG_CPP => array("qhttp")
    );

    const   MVC_PHP_PHALCON = "php";
    const   MVC_JAVA_SERVLET = "servlet";
    const   MVC_CPP_QHTTP = "qhttp";


    /**
     * 一组驱动常量
     */
    const   DB_MYSQL = "mysql";
    const   DB_SQLITE = "sqlite";
    const   DB_ORACLE = "oracle";


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
}