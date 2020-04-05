<?php

/**
 * 构建数据库的基类
 * Class DbBase
 */
abstract class DbBase extends CcBase
{


    /**
     * 创建初始化结构
     * @param MyDb $db
     * @return mixed
     */
    abstract function ccInitDb($db);


    /**
     * 创建表结构sql
     * @param MyModel $model
     * @return mixed
     */
    abstract function ccTable($model);

    /**
     * 创建删除表结构sql
     * @param MyModel $model
     * @return mixed
     */
    abstract function ccTable_reset($model);

    /**
     * 创建存储过程sql
     * @param MyModel $model
     */
    abstract function ccProc($model);




}