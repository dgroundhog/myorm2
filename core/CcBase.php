<?php
if (!defined("CC_ROOT")) {
    define('CC_ROOT', realpath(dirname(__FILE__)));
}
include_once(CC_ROOT . "/_cc.inc.php");

/**
 * CRUD 解析器的抽象类
 *
 * Class CcBase
 */
interface CcBase
{

    //有一个特性的输出目录
    

    /**
     * 增加
     * @param MyModel $model
     * @return mixed
     */
    function cAdd($model);

    /**
     * 修改
     * @param MyModel $model
     * @return mixed
     */
    function cUpdate($model);


    /**
     * 删除
     * @param MyModel $model
     * @return mixed
     */
    function cDelete($model);


    /**
     * 读取一个
     * @param MyModel $model
     * @return mixed
     */
    function cFetch($model);



    /**
     * 聚合查询、统计
     * @param $model
     * @return mixed
     */
    function cList($model);


    /**
     * 聚合查询、统计数量
     * @param $model
     * @return mixed
     */
    function cCount($model);

}