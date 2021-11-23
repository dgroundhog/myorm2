<?php
if (!defined("CC_ROOT")) {
    define('CC_ROOT', realpath(dirname(__FILE__)));
}
if (!defined("DS")) {
    define('DS', DIRECTORY_SEPARATOR);
}
include_once(CC_ROOT . "/_cc.inc.php");

/**
 * CRUD 解析器的抽象类
 *
 * Class CcBase
 */
interface MyBase
{


    /**
     * 初始化
     * @param $v1
     * @return mixed
     */
    function init($v1);

    /**
     * 输出字段结构为数组
     * @return array
     */
    function getAsArray();


    /**
     * 解析数组为对象
     * @param array $a_data
     * @return mixed
     */
    function parseToObj($a_data);


}