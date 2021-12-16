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
     * @param $fun
     * @return mixed
     */
    function cAdd(MyModel $model, MyFun $fun);

    /**
     * 修改
     * @param MyModel $model
     * @param $fun
     * @return mixed
     */
    function cUpdate(MyModel $model, MyFun $fun);


    /**
     * 删除
     * @param MyModel $model
     * @param $fun
     * @return mixed
     */
    function cDelete(MyModel $model, MyFun $fun);


    /**
     * 读取一个
     * @param MyModel $model
     * @param $fun
     * @return mixed
     */
    function cFetch(MyModel $model, MyFun $fun);


    /**
     * 聚合查询、统计
     * @param $model
     * @param $fun
     * @return mixed
     */
    function cList(MyModel $model, MyFun $fun);


    /**
     * 聚合查询、统计数量
     * @param $model
     * @param $fun
     * @return mixed
     */
    function cCount(MyModel $model, MyFun $fun);

}