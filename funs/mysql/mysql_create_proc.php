<?php
/**
 * Created by IntelliJ IDEA.
 * User: dengqianzhong
 * Date: 2019-02-22
 * Time: 20:09
 */

if (!defined("MYSQL_PROC")) {
    define('MYSQL_PROC', realpath(dirname(__FILE__)));
}
include_once(MYSQL_PROC . "/mysql_base.ini.php");


include_once MYSQL_PROC . "/mysql_create_table.php";
include_once MYSQL_PROC . "/mysql_create_proc_add.php";
include_once MYSQL_PROC . "/mysql_create_proc_delete.php";
include_once MYSQL_PROC . "/mysql_create_proc_update.php";
include_once MYSQL_PROC . "/mysql_create_proc_fetch.php";
include_once MYSQL_PROC . "/mysql_create_proc_list.php";


function mysql_create_proc($my_model)
{

    mysql_create_proc_add($my_model);
    mysql_create_proc_delete($my_model);
    mysql_create_proc_update($my_model);
    mysql_create_proc_fetch($my_model);
    mysql_create_proc_list($my_model);

}




