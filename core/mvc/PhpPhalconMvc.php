<?php
if (!defined("MVC_ROOT")) {
    define('MVC_ROOT', realpath(dirname(__FILE__)));
}

include_once(MVC_ROOT . "/MvcBase.php");

class PhpPhalconMvc extends MvcBase
{

    function cAdd(MyModel $model, MyFun $fun)
    {
        // TODO: Implement cAdd() method.
    }

    function cUpdate(MyModel $model, MyFun $fun)
    {
        // TODO: Implement cUpdate() method.
    }

    function cDelete(MyModel $model, MyFun $fun)
    {
        // TODO: Implement cDelete() method.
    }

    function cFetch(MyModel $model, MyFun $fun)
    {
        // TODO: Implement cFetch() method.
    }

    function cList(MyModel $model, MyFun $fun)
    {
        // TODO: Implement cList() method.
    }

    function cCount(MyModel $model, MyFun $fun)
    {
        // TODO: Implement cCount() method.
    }

    function ccModel($model)
    {
        // TODO: Implement ccModel() method.
    }

    function ccTmpl($model)
    {
        // TODO: Implement ccTmpl() method.
    }

    function ccWeb($model)
    {
        // TODO: Implement ccWeb() method.
    }

    function ccApi($model)
    {
        // TODO: Implement ccApi() method.
    }

    function ccDoc($model)
    {
        // TODO: Implement ccDoc() method.
    }

    /**
     * 创建模型层
     * @param MyModel $model
     * @return mixed
     */
    function ccDb($model)
    {
        // TODO: Implement ccDoc() method.
    }

    /**
     * 创建模型层
     * @param MyModel $model
     * @return mixed
     */
    function ccBean(MyModel $model)
    {
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        SeasLog::info("创建PHP数据结构--{$model_name}");
        $_target = $this->odir_beans . DS . "{$uc_model_name}Bean.php";
        ob_start();

        _php_header();


        if ($this->final_package != "") {
            echo "namespace {$this->final_package};";
        }


        _php_comment("数据bean-{$model_name}[{$model->title}]");
        echo "class {$uc_model_name}Bean\n{\n";

        foreach ($model->field_list as $field) {
            /* @var MyField $field */

            $key = $field->name;
            _php_comment_header("{$field->title}", 1);
            switch ($field->type) {
                //bool
                case Constant::DB_FIELD_TYPE_BOOL :
                    echo _tab(1) . " * @var bool\n";
                    _php_comment_footer(1);
                    echo _tab(1) . "public \${$key} = false;\n";
                    break;
                //整型
                case Constant::DB_FIELD_TYPE_INT:
                case Constant::DB_FIELD_TYPE_LONGINT:
                    echo _tab(1) . " * @var int\n";
                    _php_comment_footer(1);
                    echo _tab(1) . "public \${$key} = 0;\n";
                    break;
                //blob
                case  Constant::DB_FIELD_TYPE_BLOB:
                case Constant::DB_FIELD_TYPE_LONGBLOB:
                    echo _tab(1) . " * @var string|object\n";
                    _php_comment_footer(1);
                    echo _tab(1) . "public \${$key} = null;\n";
                    break;

                default:
                    echo _tab(1) . " * @var string\n";
                    _php_comment_footer(1);
                    echo _tab(1) . "public \${$key} = \"\";\n";
                    break;
            }
        }

        _php_comment_header("TO_STRING", 1);
        echo _tab(1) . " * @return string\n";
        _php_comment_footer(1);
        echo _tab(1) . "public function toString(){ \n";
        echo _tab(2) . "return var_export(this,true);\n";
        echo _tab(1) . "}\n\n";

        echo "}";

        $cc_data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $cc_data);
    }
}