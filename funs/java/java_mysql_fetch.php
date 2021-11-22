<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_fetch.php");


/**
 * java模型类--获取一条数据
 *
 * @param $model
 */
function java_mysql_fetch($model)
{

    if (!_java_db_header($model, "fetch")) {
        return;
    }
    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("取出数据为map");
    _java_abs_fetch_param_comment($model);
    _java_comment_footer();


    echo "public HashMap fetch(";
    $i_param = _java_abs_fetch_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    $s_qm = _question_marks($i_param);

    echo _tab(2) . "HashMap<String, String> mRet = new HashMap<>();\n";
    _java_query_header("mysql");
    echo _tab(4) . "String sql = \"{CALL `p_{$table_name}_fetch`({$s_qm})}\";\n";
    echo _tab(4) . "st = conn.prepareCall(sql);\n";

    $ii = 0;
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        echo _tab(4) . _java_statement_param($key, $model['table_fields'][$key]['type'], $ii);
    }
    echo _tab(4) . "rs = st.executeQuery();\n";
    echo _tab(4) . "while (rs.next()) {\n";
    echo _tab(5) . "for (Map.Entry<String, String> entry : mRowMap.entrySet()) {\n";
    echo _tab(6) . "mRet.put(entry.getKey(), rs.getString(entry.getValue())); \n";
    echo _tab(5) . "}\n";
    echo _tab(5) . "break;\n";
    echo _tab(4) . "}\n";

    echo _tab(4) . "logger.debug(\"call p_{$table_name}_fetch--done\");\n";

    _java_query_footer("mysql");
    echo _tab(2) . "return mRet;\n";
    echo _tab(1) . "}";


    $uc_table = ucfirst($model['table_name']);
    _java_comment_header("取出数据为Bean");
    _java_abs_fetch_param_comment($model);
    _java_comment_footer();
    echo "public {$uc_table}Bean fetchBean(";
    _java_abs_fetch_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    echo _tab(2) . "{$uc_table}Bean {$table_name}Bean = new {$uc_table}Bean();\n";

    _java_query_header("mysql", "read");
    echo _tab(4) . "String sql = \"{CALL `p_{$table_name}_fetch`({$s_qm})}\";\n";
    echo _tab(4) . "st = conn.prepareCall(sql);\n";

    $ii = 0;
    foreach ($model['fetch_by'] as $key) {
        $ii++;
        echo _tab(4) . _java_statement_param($key, $model['table_fields'][$key]['type'], $ii);
    }
    echo _tab(4) . "rs = st.executeQuery();\n";
    echo _tab(4) . "while (rs.next()) {\n";

    foreach ($model['table_fields'] as $key => $field) {
        echo _tab(5) . _java_result_bean($key, $field['type'], $table_name);
    }
    echo _tab(5) . "break;\n";
    echo _tab(4) . "}\n";

    echo _tab(4) . "logger.debug(\"call p_{$table_name}_fetch--done\");\n";


    _java_query_footer("mysql", "read");


    echo _tab(2) . "return {$table_name}Bean;\n";


    echo _tab(1) . "}";


    _java_db_footer($model, "fetch");
}