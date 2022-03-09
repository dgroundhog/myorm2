<?php

/**
 * 创建模型的基本操作
 * Class DbBase
 */
abstract class ModelBase implements CcBase
{
    /**
     * 应用配置
     * @var MyApp
     */
    public $curr_app = null;

    /**
     * 应用配置
     * @var MyArch
     */
    public $arch_conf = null;

    /**
     * 输出目录
     * @var string
     */
    public $app_output = "";
    //包含应用层和前端

    /**
     * 构造函数
     * @param MyApp $db 主应用
     */
    public function __construct(MyApp $app)
    {
        $output_root = $app->path_output;
        $this->curr_app = $app;
        $this->arch_conf = $app->getCurrArch();
        
        if (!file_exists($output_root)) {
            mkdir($output_root);
        }

        $output_1 = $output_root . DS . "app";
        if (!file_exists($output_1)) {
            mkdir($output_1);
        }

        $output_2 = $output_1 . DS . $app->lang;
        if (!file_exists($output_2)) {
            mkdir($output_2);
        }

        $output_3 = $output_2 . DS . $app->mvc;
        if (!file_exists($output_3)) {
            mkdir($output_3);
        }
        //~/app/java/servlet   eg
        $this->app_output = $output_3;
    }

    /**
     * 找到当前的构建机器
     * @param MyApp $app
     * @return DbBase
     */
    public static function findCc(MyApp $app)
    {
        $o_curr_arch = $app->getCurrArch();
        $mm = null;
        switch ($o_curr_arch->mvc) {
            case Constant::MVC_JAVA_SERVLET:
                $mm = new JavaServletModel($app);
                break;
            case Constant::MVC_PHP_PHALCON:
                //TODO
            default:
                break;
            default:

        }

        return $mm;
    }

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
}