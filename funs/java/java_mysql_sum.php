<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_sum.php");


/**
 * java模型 统计
 *
 * @param $model
 */
function java_mysql_sum($model)
{

    if (!_java_db_header($model, "sum")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("统计");
    _java_abs_sum_param_comment($model);
    _java_comment_footer();

    echo _tab(1) . "public Vector<HashMap> sum(";
    $i_param = _java_abs_sum_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    $s_qm = _question_marks($i_param);
    echo _tab(2) . "Vector<HashMap> vList = new Vector<>();\n";
    _java_query_header("mysql");

    echo _tab(4) . "String sql = \"{CALL `p_{$table_name}_sum`({$s_qm})}\";\n";
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

    $ii++;
    echo _tab(4) . "st.setString({$ii}, v_group_by); \n";

    $ii++;
    echo _tab(4) . "st.setString({$ii}, v_order_by); \n";

    echo _tab(4) . "String[] a_group_by = v_group_by.split(DbMysql.SP);\n";
    echo _tab(4) . "int i_group_by = v_group_by.length();\n";

    echo _tab(4) . "rs = st.executeQuery();\n";
    echo _tab(4) . "while (rs.next()) {\n";
    echo _tab(5) . "HashMap<String , String> _mOne = new HashMap<>();\n";
    echo _tab(5) . "_mOne.put(\"i_sum\", rs.getString(\"i_sum\")); \n";
    echo _tab(5) . "if(i_group_by > 0) {\n";
    echo _tab(6) . "for (int ii=0; ii< a_group_by.length; ii++) {\n";
    echo _tab(7) . "_mOne.put(a_group_by[ii], rs.getString(a_group_by[ii])); \n";
    echo _tab(6) . "}\n";
    echo _tab(5) . "}\n";
    echo _tab(5) . "vList.add(_mOne);\n";
    echo _tab(4) . "}\n";


    echo _tab(4) . "logger.debug(\"call p_{$table_name}_sum--done\");\n";

    _java_query_footer("mysql");
    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "sum");

}