<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_update_state.php");


/**
 * java抽象类--更新状态
 *
 * @param $model
 */
function java_mysql_update_state($model)
{

    if (!_java_db_header($model, "update_state")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("更新状态");
    _java_abs_update_state_param_comment($model);
    _java_comment_footer();
    echo "public int updateState(";
    $i_param = _java_abs_update_state_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    $s_qm = _question_marks($i_param);
    echo _tab(2) . "int rRet = 0;\n";
    _java_query_header("mysql");

    echo _tab(4) . "String sql = \"{CALL `p_{$table_name}_update_state`({$s_qm},?)}\";\n";
    echo _tab(4) . "st = conn.prepareCall(sql);\n";

    $ii = 0;
    $ii++;
    echo _tab(4) . _java_statement_param("state", $model['table_fields']['state']['type'], $ii);
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        echo _tab(4) . _java_statement_param($key, $model['table_fields'][$key]['type'], $ii);
    }
    if (isset($model['table_fields']['op_id2'])) {
        $ii++;
        echo _tab(4) . _java_statement_param("op_id2", "varchar", $ii);
    }
    $ii++;
    echo _tab(4) . "st.registerOutParameter({$ii}, Types.INTEGER);\n";
    echo _tab(4) . "rs = st.executeQuery();\n";
    echo _tab(4) . "rRet = st.getInt({$ii});\n";
    echo _tab(4) . "logger.debug(\"call p_{$table_name}_update_state--\" + rRet);\n";


    _java_query_footer("mysql");
    echo _tab(2) . "return rRet;\n";
    echo _tab(1) . "}";


    _java_db_footer($model, "update_state");
}