<?php

/**
 * 创建模型的基本操作
 * Class DbBase
 */
abstract class MvcBase extends CcBase implements CcImpl
{

    public $final_package = "";//最终包名字
    /**
     * 包含应用层和前端
     * 输出目录
     * @var string
     */
    public $odir_package = "";//包主目录
    public $odir_resource = "";//资源目录
    public $odir_webapp = "";//jsp目录或者独立http目录

    public $odir_enums = "";//枚举数据
    public $odir_rest = "";//rest方法

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
        $this->curr_app = $app;
        $output_root = $app->path_output;

        $this->arch_conf = $app->getCurrArch();

        if (!file_exists($output_root)) {
            mkdir($output_root);
        }

        if (!is_ok_app_package($app->package)) {
            $app->package = "default";
        }

        if ($this->arch_conf->mvc == Constant::MVC_JAVA_SERVLET) {
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

            $output_main = $output_root . DS . "src" . DS . "main";
            $this->final_package = str_replace("\\", ".", $app->package);
            $s_package_dirs = str_replace(".", DS, $this->final_package);

            //包主目录
            $this->odir_package = $output_main . DS . "java" . DS . $s_package_dirs;
            $this->odir_resource = $output_main . DS . "resources";
            $this->odir_webapp = $output_main . DS . "webapp";
            $this->odir_beans = $this->odir_package . DS . "beans";//数据结构
            $this->odir_config = $this->odir_package . DS . "config";//配置
            $this->odir_rest = $this->odir_package . DS . "rest";//restful
            $this->odir_controllers = $this->odir_package . DS . "controllers";//控制器
            $this->odir_models = $this->odir_package . DS . "models";//模型驱动
            $this->odir_enums = $this->odir_package . DS . "enums";//错误码
            $this->odir_views = $this->odir_webapp . DS . "WEB-INF" . DS . "templates";//视图或者UI
            //TODO 配置文件应该写到这里

        }
        if ($this->arch_conf->mvc == Constant::MVC_PHP_PHALCON) {
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

            $this->final_package = str_replace(".", "\\", $app->package);


            $output_main = $output_root . DS . "src";
            //包主目录
            $this->odir_package = $output_main . DS . "app";
            //资源目录
            $this->odir_resource = $output_main . DS . "resources";//php 不用这个目录
            $this->odir_webapp = $output_main . DS . "public";//
            $this->odir_beans = $this->odir_package . DS . "beans";//数据结构
            $this->odir_config = $this->odir_package . DS . "config";//配置 java 不用这个目录
            $this->odir_controllers = $this->odir_package . DS . "controllers";//控制器
            $this->odir_rest = $this->odir_package . DS . "rest";//restful
            $this->odir_models = $this->odir_package . DS . "models";//模型驱动
            $this->odir_views = $this->odir_package . DS . "views";//视图或者UI
            $this->odir_enums = $this->odir_package . DS . "enums";//错误码
        }

        dir_create($this->odir_package);
        dir_create($this->odir_resource);
        dir_create($this->odir_webapp);
        dir_create($this->odir_beans);
        dir_create($this->odir_enums);
        dir_create($this->odir_rest);
        dir_create($this->odir_config);
        dir_create($this->odir_controllers);
        dir_create($this->odir_models);
        dir_create($this->odir_views);


    }

    /**
     * 找到当前的构建机器
     * @param MyApp $app
     * @return MvcBase
     */
    public static function findCc(MyApp $app, $with_web = false)
    {

        $o_curr_arch = $app->getCurrArch();
        $mm = null;
        switch ($o_curr_arch->mvc) {
            case Constant::MVC_JAVA_SERVLET:
                if ($with_web) {
                    $mm = new JavaServletMvcCtrl($app);
                } else {
                    $mm = new JavaServletMvc($app);
                }
                break;
            case Constant::MVC_PHP_PHALCON:
                if ($with_web) {
                    $mm = new PhpPhalconMvcCtrl($app);
                } else {
                    $mm = new PhpPhalconMvc($app);
                }
                break;
            default:
                break;
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
     * 创建restful 接口
     * @param MyModel $model
     * @return mixed
     */
    abstract function ccRestful($model);

    /**
     * 创建模板层
     * @param MyModel $model
     * @return mixed
     */
    abstract function ccTmpl($model);

    /**
     * 创建控制层
     * @param MyModel $model
     * @return mixed
     */
    abstract function ccCtrl($model);

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
     * 创建错误码
     * @param array $kv_list
     */
    abstract function ccEcode($kv_list);
}