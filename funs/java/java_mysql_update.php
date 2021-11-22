<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_update.php");


/**
 * java模型类--更新
 *
 * @param $model
 */
function java_mysql_update($model)
{

    if (!_java_db_header($model, "update")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("更新数据");
    _java_abs_update_param_comment($model);
    _java_comment_footer();
    echo "public int update(";
    $i_param = _java_abs_update_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    $s_qm = _question_marks($i_param);

    echo _tab(2) . "int rRet = 0;\n";
    _java_query_header("mysql");
    echo _tab(4) . "String sql = \"{CALL `p_{$table_name}_update`({$s_qm},?)}\";\n";
    echo _tab(4) . "st = conn.prepareCall(sql);\n";

    $ii = 0;
    foreach ($model['update_keys'] as $key) {
        $ii++;
        echo _tab(4) . _java_statement_param($key, $model['table_fields'][$key]['type'], $ii);
    }
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        echo _tab(4) . _java_statement_param($key, $model['table_fields'][$key]['type'], $ii);
    }
    $ii++;

    echo _tab(4) . "st.registerOutParameter({$ii}, Types.INTEGER);\n";
    echo _tab(4) . "rs = st.executeQuery();\n";
    echo _tab(4) . "rRet = st.getInt({$ii});\n";
    echo _tab(4) . "logger.debug(\"call p_{$table_name}_update--\" + rRet);\n";

    _java_query_footer("mysql");
    echo _tab(2) . "return rRet;\n";
    echo _tab(1) . "}";


    _java_comment_header("通过bean更新数据");
    echo "* @param v_{$model['table_name']}Bean\n";
    echo "* @return int\n";
    _java_comment_footer();
    echo "public int updateBean({$uc_table}Bean v_{$model['table_name']}Bean) \n";
    echo _tab(1) . "{\n";
    echo _tab(2) . "int rRet = 0;\n";
    _java_query_header("mysql");
    echo _tab(4) . "String sql = \"{CALL `p_{$table_name}_update`({$s_qm},?)}\";\n";
    echo _tab(4) . "st = conn.prepareCall(sql);\n";

    $ii = 0;

    foreach ($model['update_keys'] as $key) {
        $ii++;
        echo _tab(4) . _java_statement_param_bean($key, $model['table_fields'][$key]['type'], $ii, $table_name);
    }
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        echo _tab(4) . _java_statement_param_bean($key, $model['table_fields'][$key]['type'], $ii, $table_name);
    }

    $ii++;
    echo _tab(4) . "st.registerOutParameter({$ii}, Types.INTEGER);\n";
    echo _tab(4) . "rs = st.executeQuery();\n";
    echo _tab(4) . "rRet = st.getInt({$ii});\n";
    echo _tab(4) . "logger.debug(\"call p_{$table_name}_update--\" + rRet);\n";

    _java_query_footer("mysql");
    echo _tab(2) . "return rRet;\n";

    echo _tab(1) . "}";

    _java_db_footer($model, "update");
}