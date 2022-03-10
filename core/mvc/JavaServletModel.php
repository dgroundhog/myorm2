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

    /**
     * 创建模型
     * @param $model
     * @return mixed|void
     */
    function ccModel($model)
    {
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        SeasLog::info("创建JAV数据模型--{$model_name}");
        $_target = $this->odir_models . DS . "{$uc_model_name}Model.java";
        ob_start();

        echo "package  {$this->final_package}.models;\n";

        echo "import {$this->final_package}.beans.{$uc_model_name}Bean;\n";
        echo "import {$this->final_package}.model.ModelBase;\n";


        echo "import org.slf4j.Logger;\n";
        echo "import org.slf4j.LoggerFactory;\n";

        echo "import java.io.InputStream;\n";
        echo "import java.io.ByteArrayInputStream;\n";

        echo "import java.sql.CallableStatement;\n";
        echo "import java.sql.Connection;\n";
        echo "import java.sql.ResultSet;\n";
        echo "import java.sql.Types;\n";
        echo "import java.sql.SQLException;\n";

        echo "import java.util.HashMap;\n";
        echo "import java.util.Map;\n";
        echo "import java.util.Vector;\n";



        _java_comment("操作模型类--{$model->title}");
        echo "public class {$uc_model_name}Model extends ModelBase {\n";

        _java_comment("私有日志类", 1);
        echo _tab(1) . "private  static Logger logger = LoggerFactory.getLogger({$uc_model_name}Model.class);\n\n";

        _java_comment("基本数据字段映射,模型中的字段和数据库的字段的对英关系",1);
        echo _tab(1) ."public Map<String, String> mPlainRowMap = new HashMap<String, String>() {{\n";
        foreach ($model->field_list as $field) {
            /* @var MyField $field */

            $key = $field->name;
            echo _tab(2) . "put(\"{$key}\", \"{$key}\");//{$field->title}\n";
        }
        echo _tab(1) ."}};\n";

        _java_comment("数据类型", 1);
        echo _tab(1) . "{$uc_model_name}Bean bean;\n";

        _java_comment("获取bean", 1);
        echo _tab(1) . "public {$uc_model_name}Bean getBean() {\n";
        echo _tab(2) . "return bean;\n";
        echo _tab(1) . "}\n";

        _java_comment("设置bean", 1);
        echo _tab(1) . "public void setBean({$uc_model_name}Bean bean0) {\n";
        echo _tab(2) . "this.bean = bean0;\n";
        echo _tab(1) . "}\n";

        foreach ($model->field_list as $field) {
            /* @var MyField $field */
            if($field->input_hash != ""){
                $key = $field->name;
                $uc_key = ucfirst($key);
                _java_comment("基础字典-{$field->title}", 1);
                echo _tab(1) . "public static HashMap<String,String> get{$uc_key}_KV(){\n";
                echo _tab(2) . "HashMap<String,String> mList = new HashMap<String,String>();\n";
                $a_hash = explode(";",$field->input_hash );
                foreach ($a_hash as $s_kv){
                    $a_kv = explode(",",$s_kv);
                    echo _tab(2) . "mList.put(\"{$a_kv[0]}\",\"{$a_kv[1]}\");\n";
                }
                echo _tab(2) . "return mList;\n";
                echo _tab(1) . "}\n\n";
            }
        }
        echo "}";


        foreach ($model->fun_list as $o_fun) {

            /* @var MyFun $o_fun */
            $fun_type = $o_fun->type;

            switch ($fun_type) {
                case Constant::FUN_TYPE_ADD:
                    $this->cAdd($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_DELETE:
                    $this->cDelete($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_UPDATE:
                    $this->cUpdate($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_FETCH:
                    $this->cFetch($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_COUNT:
                    $this->cCount($model, $o_fun);
                    break;

                case Constant::FUN_TYPE_LIST_WITH_COUNT:
                case Constant::FUN_TYPE_LIST_WITH_AVG:
                case Constant::FUN_TYPE_LIST_WITH_SUM:
                case Constant::FUN_TYPE_LIST_WITH_MAX:
                case Constant::FUN_TYPE_LIST_WITH_MIN:
                case Constant::FUN_TYPE_LIST:
                default:
                    $this->cList($model, $o_fun);
                    break;
            }
        }



        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $data);

        $_target2 = $this->odir_models . DS . "{$uc_model_name}ModelX.java";
        ob_start();

        echo "package  {$this->final_package}.models;\n";
        _java_comment("自定义的操作模型类--{$model->title}");
        echo "public class {$uc_model_name}ModelX extends {$uc_model_name}Model {\n";
        echo "}";

        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target2, $data);
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
     * 创建数据结构
     * @param MyModel $model
     * @return mixed
     */
    function ccBean(MyModel $model)
    {
        $model_name = $model->name;
        $uc_model_name = ucfirst($model_name);
        SeasLog::info("创建JAVA数据结构--{$model_name}");
        $_target = $this->odir_beans . DS . "{$uc_model_name}Bean.java";
        ob_start();


        echo "package  {$this->final_package}.beans;\n\n";
        echo "import java.util.HashMap;\n";
        echo "import java.util.Map;\n";
        echo "import java.util.Vector;\n";
        echo "import java.io.Serializable;\n";

        _java_comment("数据bean-{$model->title}-{$model->name}");
        echo "public class {$uc_model_name}Bean implements Serializable {\n";

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
        _java_comment("获取bean2String", 1);
        echo _tab(1) . "public String toString() {\n";
        echo _tab(2) . "return \"{$uc_model_name}Bean [\"\n";
        $ii=0;
        foreach ($model->field_list as $field) {
            /* @var MyField $field */
            $key = $field->name;
            if($ii==0){
                echo _tab(5)."+ \"{$key} = \" +  {$key}\n";
            }
            else{
                echo _tab(5)."+ \", {$key} = \" +  {$key}\n";
            }
             $ii++;
        }
        echo _tab(4) . "+ \"]\";\n";


        echo _tab(1) . "}\n";
        echo "}";
        $cc_data = ob_get_contents();
        ob_end_clean();
        file_put_contents($_target, $cc_data);
    }

}