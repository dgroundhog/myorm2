<?php
if (!defined("CC_ROOT")) {
    define('CC_ROOT', realpath(dirname(__FILE__)));
}
include_once(CC_ROOT . "/base.inc.php");

/**
 * CRUD 解析器的抽象类
 *
 * Class CcBase
 */
abstract class CcBase
{

    /**
     * 增加
     * @param MyModel $model
     * @return mixed
     */
    abstract function cAdd($model);

    /**
     * 修改
     * @param MyModel $model
     * @return mixed
     */
    abstract function cUpdate($model);


    /**
     * 删除
     * @param MyModel $model
     * @return mixed
     */
    abstract function cDelete($model);


    /**
     * 读取一个
     * @param MyModel $model
     * @return mixed
     */
    abstract function cFetch($model);

    /**
     * 聚合查询、统计
     * @param $model
     * @return mixed
     */
    abstract function cList($model);


}