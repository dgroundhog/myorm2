<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_count.php");


/**
 * java模型--计数器
 *
 * @param $model
 */
function java_mysql_count($model)
{

    if (!_java_db_header($model, "count")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("计数");
    _java_abs_count_param_comment($model);
    _java_comment_footer();

    echo "public int count(";
    $i_param = _java_abs_count_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    $s_qm = _question_marks($i_param);
    echo _tab(2) . "int rRet = 0;\n";
    _java_query_header("mysql");

    echo _tab(4) . "String sql = \"{CALL `p_{$table_name}_count`({$s_qm})}\";\n";
    echo _tab(4) . "st = conn.prepareCall(sql);\n";


    $ii = 0;
    foreach ($model['list_by'] as $key) {
        $ii++;
        echo _tab(4) . _java_statement_param($key, $model['table_fields'][$key]['type'], $ii);
    }

    if (isset($model["list_kw"]) && count($model["list_kw"]) > 0) {
        $ii++;
        echo _tab(4) . "st.setString({$ii}, v_kw); \n";
    }

    if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {
        $ii++;
        echo _tab(4) . "st.setString({$ii}, v_date_from); \n";

        $ii++;
        echo _tab(4) . "st.setString({$ii}, v_date_to); \n";
    }

    echo _tab(4) . "rs = st.executeQuery();\n";

    echo _tab(4) . "while (rs.next()) {\n";
    echo _tab(5) . "rRet = rs.getInt(\"i_count\");\n";
    echo _tab(5) . "break;\n";
    echo _tab(4) . "}\n";

    echo _tab(4) . "logger.debug(\"call p_{$table_name}_count--\" + rRet);\n";

    _java_query_footer("mysql");
    echo _tab(2) . "return rRet;\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "count");

}