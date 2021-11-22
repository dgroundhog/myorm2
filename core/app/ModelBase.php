<?php

/**
 * 创建模型的基本操作
 * Class DbBase
 */
abstract class ModelBase implements CcBase
{
    /**
     * 数据库配置
     * @var MyDbConf
     */
    public $db_conf = null;

    /**
     * 应用配置
     * @var MyAppConf
     */
    public $app_conf = null;

    /**
     * 输出目录
     * @var string
     */
    public $app_output = "";
    //包含应用层和前端

    /**
     * 创建模型层
     * @param MyModel $model
     * @return mixed
     */
    abstract function ccModel($model);


    /**
     * 创建模板层
     * @param MyModel $model
     * @return mixed
     */
    abstract function ccTmpl($model);

    /**
     * 创建网页层
     * @param MyModel $model
     * @return mixed
     */
    abstract function ccWeb($model);

    /**
     * 创建接口层
     * @param MyModel $model
     * @return mixed
     */
    abstract function ccApi($model);

    /**
     * 创建文档
     * @param MyModel $model
     */
    abstract function ccDoc($model);

    /**
     * 构造函数
     * @param MyDbConf $db  数据配置
     * @param string $output_root  输出的目录
     */
    public function __construct(MyAppConf $app,MyDbConf $db, $output_root=".")
    {
        $this->app_conf = $app;
        $this->db_conf = $db;
        if (!file_exists($output_root)) {
            mkdir($output_root);
        }

        $output_1 = $output_root .DIRECTORY_SEPARATOR. "app";
        if (!file_exists($output_1)) {
            mkdir($output_1);
        }

        $output_2 = $output_1 . DIRECTORY_SEPARATOR.$app->lang;
        if (!file_exists($output_2)) {
            mkdir($output_2);
        }

        $output_3 = $output_2 . DIRECTORY_SEPARATOR.$app->mvc;
        if (!file_exists($output_3)) {
            mkdir($output_3);
        }
        //~/app/java/servlet   eg
        $this->app_output  = $output_3;
    }
}