<?php
/**
 * Created by IntelliJ IDEA.
 * User: dengqianzhong
 * Date: 2019-02-22
 * Time: 20:09
 */

if (!defined("ORACLE_PROC")) {
    define('ORACLE_PROC', realpath(dirname(__FILE__)));
}
include_once(ORACLE_PROC . "/oracle_base.ini.php");


include_once ORACLE_PROC . "/oracle_create_table.php";
include_once ORACLE_PROC . "/oracle_create_proc_add.php";
include_once ORACLE_PROC . "/oracle_create_proc_count.php";
include_once ORACLE_PROC . "/oracle_create_proc_delete.php";
include_once ORACLE_PROC . "/oracle_create_proc_drop.php";
include_once ORACLE_PROC . "/oracle_create_proc_fetch.php";
include_once ORACLE_PROC . "/oracle_create_proc_list.php";
include_once ORACLE_PROC . "/oracle_create_proc_list_all.php";
include_once ORACLE_PROC . "/oracle_create_proc_list_by_ids.php";
include_once ORACLE_PROC . "/oracle_create_proc_update.php";
include_once ORACLE_PROC . "/oracle_create_proc_update_state.php";


function oracle_create_proc($my_model, $db_conf)
{
    $my_model['db_conf'] = $db_conf;
    oracle_create_proc_add($my_model);
    oracle_create_proc_count($my_model);
    oracle_create_proc_delete($my_model);
    oracle_create_proc_drop($my_model);
    oracle_create_proc_fetch($my_model);
    oracle_create_proc_list($my_model);
    oracle_create_proc_list_all($my_model);
    oracle_create_proc_list_by_ids($my_model);
    oracle_create_proc_update($my_model);
    oracle_create_proc_update_state($my_model);
}




