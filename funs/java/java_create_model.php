<?php
if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");


include_once JAVA_BASE . "/java_model_add.php";
include_once JAVA_BASE . "/java_model_count.php";
include_once JAVA_BASE . "/java_model_delete.php";
include_once JAVA_BASE . "/java_model_drop.php";
include_once JAVA_BASE . "/java_model_fetch.php";
include_once JAVA_BASE . "/java_model_list.php";
include_once JAVA_BASE . "/java_model_list_all.php";
include_once JAVA_BASE . "/java_model_list_by_ids.php";
include_once JAVA_BASE . "/java_model_sum.php";
include_once JAVA_BASE . "/java_model_update.php";
include_once JAVA_BASE . "/java_model_update_state.php";

/**
 * 建立java抽象类
 * @param $package
 * @param $model
 */
function java_create_model($package, $model)
{

    $uc_table = ucfirst($model['table_name']);

    echo "package  {$package}.model;\n";

    echo "import {$package}.bean.{$uc_table}Bean;\n";
    echo "import {$package}.model.MvcBase;\n";
    echo "import {$package}.db.DBFactory;\n";

    echo "import org.slf4j.Logger;\n";
    echo "import org.slf4j.LoggerFactory;\n";

    echo "import java.io.InputStream;\n";
    echo "import java.io.ByteArrayInputStream;\n";

    echo "import java.util.HashMap;\n";
    echo "import java.util.Map;\n";
    echo "import java.util.Vector;\n";


    _java_comment("java mysql 操作模型类--{$model['table_title']}");
    echo "public class {$uc_table}Model extends MvcBase {\n";

    _java_comment("日志类", 1);
    echo _tab(1) . "private  static Logger logger = LoggerFactory.getLogger({$uc_table}Model.class);\n\n";


    _java_comment("数据类型", 1);
    echo _tab(1) . "{$uc_table}Bean bean;\n";

    _java_comment("获取bean", 1);
    echo _tab(1) . "public {$uc_table}Bean getBean() {\n";
    echo _tab(2) . "return bean;\n";
    echo _tab(1) . "}\n";

    _java_comment("设置bean", 1);
    echo _tab(1) . "public void setBean({$uc_table}Bean bean0) {\n";
    echo _tab(2) . "this.bean = bean0;\n";
    echo _tab(1) . "}\n";


    if (isset($model['keys_by_select']) && count($model['keys_by_select']) > 0) {
        foreach ($model['keys_by_select'] as $key) {
            if (isset($model['table_fields'][$key])) {

                $uc_key = ucfirst($key);

                _java_comment("基础字典", 1);
                echo _tab(1) . "public static HashMap<String,String> get{$uc_key}List(){ \n";
                echo _tab(2) . "HashMap<String,String> mList = new HashMap<String,String>();\n";
                //state为习惯性有限数据
                if ($key == "state") {
                    if (isset($model['state_list']) && count($model['state_list']) > 0) {
                        foreach ($model["state_list"] as $v_state => $n_state) {
                            echo _tab(2) . "mList.put(\"{$v_state}\",\"{$n_state}\");\n";
                        }
                    }
                } else {
                    echo _tab(2) . "mList.put(\"foo\",\"bar\");\n";
                }
                echo _tab(2) . "return mList;\n";
                echo _tab(1) . "}\n\n";

            }
        }
    }

    java_model_add($model);
    java_model_count($model);
    java_model_delete($model);
    java_model_drop($model);
    java_model_fetch($model);
    java_model_list($model);
    java_model_list_all($model);
    java_model_list_basic($model);
    java_model_list_by_ids($model);
    java_model_sum($model);
    java_model_update($model);
    java_model_update_state($model);

    echo "}";
}