<?php
if (!defined("CC_ROOT")) {
    define('CC_ROOT', realpath(dirname(__FILE__)));
}

/**
 * CRUD 解析器的抽象类
 *
 * Class CcBase
 */
interface CcImpl
{

    /**
     * 增加
     * @param MyModel $model
     * @param MyFun $fun
     * @return int
     */
    function cAdd(MyModel $model, MyFun $fun);

    /**
     * 修改
     * @param MyModel $model
     * @param MyFun $fun
     * @return int
     */
    function cUpdate(MyModel $model, MyFun $fun);


    /**
     * 删除
     * @param MyModel $model
     * @param MyFun $fun
     * @return int
     */
    function cDelete(MyModel $model, MyFun $fun);


    /**
     * 读取一个
     * @param MyModel $model
     * @param MyFun $fun
     * @return int
     */
    function cFetch(MyModel $model, MyFun $fun);


    /**
     * 聚合查询、统计
     * @param MyModel $model
     * @param MyFun $fun
     * @return array
     */
    function cList(MyModel $model, MyFun $fun);

    /**
     * 单纯统计数量
     * @param MyModel $model
     * @param MyFun $fun
     * @return int
     */
    function cCount(MyModel $model, MyFun $fun);



}