<?php
if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_base.ini.php");


/**
 * 建立java工厂类
 *
 * @param $package
 * @param $a_allow_dbs
 * @param $a_models
 */
function java_db_factory($package, $a_allow_dbs, $a_models)
{


    echo "package  {$package}.db;\n";

    echo "import  {$package}.Constants;\n";
    echo "import  {$package}.MyApp;\n";

    echo "import  {$package}.db.base.*;\n";
    foreach ($a_allow_dbs as $db => $db_conf) {
        echo "import  {$package}.db.{$db}.*;\n";
    }
    //echo "import  {$package}.db.mysql.*;\n";


    _java_comment("java db 工厂类");
    echo "abstract public class DBFactory {\n";

    echo _tab(1) . "public static UnormBase getUnorm() {\n";
    echo _tab(2) . "switch (MyApp.Settings.DB) {\n";
    foreach ($a_allow_dbs as $db => $db_conf) {
        $up_db = strtoupper($db);
        $uc_db = ucfirst($db);
        echo _tab(3) . "case Constants.DB_{$up_db}:\n";
        echo _tab(4) . "return new Unorm{$uc_db}();\n";
        //echo _tab(3) . "break;\n";
    }
    echo _tab(3) . "default:\n";
    echo _tab(4) . "return null;\n";
    //echo _tab(3) . "break;\n";
    echo _tab(2) . "}\n";
    echo _tab(1) . "}\n";


    foreach ($a_models as $table => $model) {
        $uc_table = ucfirst($model["table_name"]);

        echo _tab(1) . "public static Db{$uc_table} get{$uc_table}() {\n";
        echo _tab(2) . "switch (MyApp.Settings.DB) {\n";
        foreach ($a_allow_dbs as $db => $db_conf) {
            $up_db = strtoupper($db);
            $uc_db = ucfirst($db);
            echo _tab(3) . "case Constants.DB_{$up_db}:\n";
            echo _tab(4) . "return new Db{$uc_db}{$uc_table}();\n";
            //echo _tab(3) . "break;\n";
        }
        echo _tab(3) . "default:\n";
        echo _tab(4) . "return null;\n";
        //echo _tab(3) . "break;\n";
        echo _tab(2) . "}\n";
        echo _tab(1) . "}\n";
    }


    echo "}";
}
