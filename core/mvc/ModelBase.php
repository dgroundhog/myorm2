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

    public $final_package = "";//最终包名字
    /**
     * 包含应用层和前端
     * 输出目录
     * @var string
     */
    public $odir_package = "";//包主目录
    public $odir_resource = "";//资源目录
    public $odir_webapp = "";//jsp目录或者独立http目录

    public $odir_beans = "";//数据结构
    public $odir_config = "";//配置
    public $odir_controllers = "";//控制器
    public $odir_models = "";//模型驱动
    public $odir_views = "";//视图或者UI
    //

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
        SeasLog::info("package11111");
        SeasLog::error($app->package);
        SeasLog::error($this->arch_conf->mvc);


        if(!is_ok_app_package($app->package)){
            $app->package = "default";
        }
        SeasLog::info("package22222");
        SeasLog::error($app->package);

        if($this->arch_conf->mvc==Constant::MVC_JAVA_SERVLET){
            //src
            //..../main/
            //......../java   主目录
            //............/__PACKAGE__  自定义包名
            //................/enums
            //................/helper
            //................/res
            //................/servlet
            //................/thread
            //................/ui
            //......../resource 资源
            //......../webapp jsp目录
            //............/WEB-INF web.xml目录

            $output_main = $output_root . DS . "src". DS . "main";

            $this->final_package  = str_replace("\\",".",$app->package);

            $s_package_dirs = str_replace(".",DS,$this->final_package );




            //包主目录
            $this->odir_package  = $output_main . DS . "java". DS . $s_package_dirs;
            //资源目录
            $this->odir_resource = $output_main . DS . "resource";
            $this->odir_webapp = $output_main . DS . "webapp";

            $this->odir_beans = $this->odir_package. DS . "beans";//数据结构
            $this->odir_config = $this->odir_package. DS . "config";//配置
            $this->odir_controllers = $this->odir_package. DS . "controllers";//控制器
            $this->odir_models = $this->odir_package. DS . "models";//模型驱动
            $this->odir_views = $this->odir_webapp. DS . "WEB-INF".DS . "templates";//视图或者UI

        }
        if($this->arch_conf->mvc==Constant::MVC_PHP_PHALCON){
            //src
            //..../app
            //......../beans
            //......../config
            //......../controllers
            //......../models
            //......../views
            //..../public
            //......../css
            //......../img
            //......../js

            $this->final_package = str_replace(".","\\",$app->package);



            $output_main = $output_root . DS . "src";
            //包主目录
            $this->odir_package  = $output_main . DS . "app";
            //资源目录
            $this->odir_resource = $output_main . DS . "resource";//php 不用这个目录
            $this->odir_webapp = $output_main . DS . "public";//
            $this->odir_beans = $this->odir_package. DS . "beans";//数据结构
            $this->odir_config = $this->odir_package. DS . "config";//配置
            $this->odir_controllers = $this->odir_package. DS . "controllers";//控制器
            $this->odir_models = $this->odir_package. DS . "models";//模型驱动
            $this->odir_views = $this->odir_package. DS . "views";//视图或者UI
        }

        dir_create($this->odir_package);
        dir_create($this->odir_resource);
        dir_create($this->odir_webapp);
        dir_create($this->odir_beans);
        dir_create($this->odir_config);
        dir_create($this->odir_controllers);
        dir_create($this->odir_models);
        dir_create($this->odir_views);


    }

    /**
     * 找到当前的构建机器
     * @param MyApp $app
     * @return ModelBase
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
                $mm = new PhpPhalconModel($app);
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
    abstract function ccBean(MyModel $model);

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