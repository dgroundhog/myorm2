<?php

/**
 * 构建数据库的基类
 * Class DbBase
 */
abstract class DbBase implements CcBase
{
    /**
     * 应用配置
     * @var MyApp
     */
    public $curr_app = null;

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
     * @param MyApp $db 主应用
     */
    public function __construct(MyApp $app)
    {
        $output_root = $app->path_output;
        $this->curr_app = $app;
        $this->db_conf = $app->getCurrDb();

        if (!file_exists($output_root)) {
            mkdir($output_root);
        }

        $output_1 = $output_root . DS . "doc";
        if (!file_exists($output_1)) {
            mkdir($output_1);
        }

        $output_2 = $output_1 . DS . "sql";
        if (!file_exists($output_2)) {
            mkdir($output_2);
        }

        $output_3 = $output_2 . DS . $this->curr_app->driver;
        if (!file_exists($output_3)) {
            mkdir($output_3);
        }
        //~/doc/sql/mysql56   eg
        $this->db_output = $output_3;

    }

    /**
     * 找到当前的构建机器
     * @param MyApp $app
     * @return DbBase
     */
    public static function findCc(MyApp $app)
    {
        $dbc = null;
        $db_conf = $app->getCurrDb();
        switch ($db_conf->driver) {
            case Constant::DB_MYSQL_56:
            case Constant::DB_MYSQL_57:
            case Constant::DB_MYSQL_80:
                $dbc = new DbMysql($app);
                break;
            default:

        }
        return $dbc;
    }

    /**
     * 创建初始化结构
     * @return mixed
     */
    abstract function ccInitDb();


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