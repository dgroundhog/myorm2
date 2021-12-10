<?php

/**
 * 构建数据库的基类
 * Class DbBase
 */
abstract class DbBase implements CcBase
{
    /**
     * 数据库配置
     * @var MyDb
     */
    public $db_conf = null;

    /**
     * 输出目录
     * @var string
     */
    public $db_output = "";

    /**
     * 构造函数
     * @param MyDb $db  数据配置
     * @param string $path_output  输出的目录
     */
    public function __construct(MyDb $db, $output_root=".")
    {
        $this->db_conf = $db;


        if (!file_exists($output_root)) {
            mkdir($output_root);
        }


        $output_1 = $output_root .DIRECTORY_SEPARATOR. "doc";
        if (!file_exists($output_1)) {
            mkdir($output_1);
        }

        $output_2 = $output_1 . DIRECTORY_SEPARATOR."sql";
        if (!file_exists($output_2)) {
            mkdir($output_2);
        }

        $output_3 = $output_2 . DIRECTORY_SEPARATOR.$db->driver;
        if (!file_exists($output_3)) {
            mkdir($output_3);
        }
        //~/doc/sql/mysql   eg
        $this->db_output = $output_3;

    }


    /**
     * 创建初始化结构
     * @param MyDb $db
     * @return mixed
     */
    abstract function ccInitDb(MyDb $db);


    /**
     * 创建表结构sql
     * @param MyModel $model
     * @return mixed
     */
    abstract function ccTable(MyModel $model);

    /**
     * 创建删除表结构sql
     * @param MyModel $model
     * @return mixed
     */
    abstract function ccTable_reset(MyModel $model);

    /**
     * 创建存储过程sql
     * @param MyModel $model
     */
    abstract function ccProc(MyModel $model);





}