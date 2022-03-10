<?php
if (!defined("MVC_ROOT")) {
    define('MVC_ROOT', realpath(dirname(__FILE__)));
}

include_once(MVC_ROOT . "/ModelBase.php");

/**
 * java servlet 模型
 */
class JavaServletModel extends ModelBase
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

    function cList(MyModel $model, MyFun $fun, $count_only)
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
    function ccBean($model)
    {
        $uc_table = ucfirst($model->name);

        echo "package  {$this->final_package}.beans;\n\n";

        echo "import java.util.HashMap;\n";
        echo "import java.util.Map;\n";
        echo "import java.util.Vector;\n";
        echo "import java.io.Serializable;\n";

        _java_comment("数据bean-{$model->title}-{$model->name}");
        echo "public class {$uc_table}Bean implements Serializable {\n";

        foreach ($model->field_list as $field) {
            /* @var MyField $field */
            _java_comment("{$field->title}", 1);
            $key = $field->name;
            switch ($field->type) {
                //bool
                case Constant::DB_FIELD_TYPE_BOOL :
                    echo _tab(1) . "public boolean {$key} = false;\n";
                    break;
                //整型
                case Constant::DB_FIELD_TYPE_INT:
                case Constant::DB_FIELD_TYPE_LONGINT:
                    echo _tab(1) . "public int {$key} = 0;\n";
                    break;
                //blob
                case  Constant::DB_FIELD_TYPE_BLOB:
                case Constant::DB_FIELD_TYPE_LONGBLOB:
                    echo _tab(1) . "public  byte[] {$key} = null;\n";
                    break;
                default:
                    echo _tab(1) . "public String {$key} = \"\";\n";
                    break;
            }
        }
        echo "}";
    }

}