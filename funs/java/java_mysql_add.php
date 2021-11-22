<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_add.php");

/**
 * java模型类--添加
 *
 * @param $model
 */
function java_mysql_add($model)
{

    if (!_java_db_header($model, "add")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("插入数据");
    _java_abs_add_param_comment($model);
    _java_comment_footer();


    echo "public int add(";
    $i_param = _java_abs_add_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    $s_qm = _question_marks($i_param);

    echo _tab(2) . "int rRet = 0;\n";
    _java_query_header("mysql");
    echo _tab(4) . "String sql = \"{CALL `p_{$table_name}_add`({$s_qm},?)}\";\n";
    echo _tab(4) . "st = conn.prepareCall(sql);\n";

    $ii = 0;
    foreach ($model['table_fields'] as $key => $field) {
        if (!in_array($key, $model['add_keys'])) {
            continue;
        }
        $ii++;
        echo _tab(4) . _java_statement_param($key, $field['type'], $ii);
    }
    $ii++;
    echo _tab(4) . "st.registerOutParameter({$ii}, Types.INTEGER);\n";
    echo _tab(4) . "rs = st.executeQuery();\n";
    echo _tab(4) . "rRet = st.getInt({$ii});\n";
    echo _tab(4) . "logger.debug(\"call p_{$table_name}_add--\" + rRet);\n";

    _java_query_footer("mysql");
    echo _tab(2) . "return rRet;\n";
    echo _tab(1) . "}";

    _java_comment_header("插入数据--通过bean");
    echo "* @param v_{$model['table_name']}Bean\n";
    echo "* @return int\n";
    _java_comment_footer();
    echo "public int addBean({$uc_table}Bean v_{$table_name}Bean) \n";
    echo _tab(1) . "{\n";
    echo _tab(2) . "int rRet = 0;\n";
    _java_query_header("mysql");
    echo _tab(4) . "String sql = \"{CALL `p_{$table_name}_add`({$s_qm},?)}\";\n";
    echo _tab(4) . "st = conn.prepareCall(sql);\n";

    $ii = 0;
    foreach ($model['table_fields'] as $key => $field) {
        if (!in_array($key, $model['add_keys'])) {
            continue;
        }
        $ii++;
        echo _tab(4) . _java_statement_param_bean($key, $field['type'], $ii, $table_name);
    }
    $ii++;
    echo _tab(4) . "st.registerOutParameter({$ii}, Types.INTEGER);\n";
    echo _tab(4) . "rs = st.executeQuery();\n";
    echo _tab(4) . "rRet = st.getInt({$ii});\n";
    echo _tab(4) . "logger.debug(\"call p_{$table_name}_add--\" + rRet);\n";

    _java_query_footer("mysql");
    echo _tab(2) . "return rRet;\n";

    echo _tab(1) . "}";
    _java_db_footer($model, "add");


}