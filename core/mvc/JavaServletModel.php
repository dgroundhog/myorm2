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

    function cList(MyModel $model, MyFun $fun,$count_only)
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


}