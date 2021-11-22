<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_list_all.php");


/**
 * java抽象类--查询列表
 *
 * @param $model
 */
function java_mysql_list_all($model)
{

    if (!_java_db_header($model, "list_all")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    _java_comment_header("查询列表，结构为hash map");
    _java_abs_list_all_param_comment($model);
    _java_comment_footer();

    echo "public Vector<HashMap> listAll(";

    $i_param = _java_abs_list_all_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    $s_qm = _question_marks($i_param);

    echo _tab(2) . "Vector<HashMap> vList = new Vector<>();\n";
    _java_query_header("mysql","read");
    echo _tab(4) . "String sql = \"{CALL `p_{$table_name}_list_all`({$s_qm})}\";\n";
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

    if (isset($model["list_has_order"]) && $model["list_has_order"] != false) {

        $ii++;
        echo _tab(4) . "st.setString({$ii}, v_order_by); \n";

        $ii++;
        echo _tab(4) . "st.setString({$ii}, v_order_dir); \n";

    }


    echo _tab(4) . "rs = st.executeQuery();\n";
    echo _tab(4) . "while (rs.next()) {\n";
    echo _tab(5) . "HashMap<String , String> _mOne = new HashMap<>();\n";
    echo _tab(5) . "for (Map.Entry<String, String> entry : mRowMap.entrySet()) {\n";
    echo _tab(6) . "_mOne.put(entry.getKey(), rs.getString(entry.getValue())); \n";
    echo _tab(5) . "}\n";
    echo _tab(5) . "vList.add(_mOne);\n";
    echo _tab(4) . "}\n";

    echo _tab(4) . "logger.debug(\"call p_{$table_name}_list_all--done\");\n";

    _java_query_footer("mysql","read");
    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";


    _java_comment_header("查询列表 结构为bean");
    _java_abs_list_all_param_comment($model);
    _java_comment_footer();
    echo "public Vector<{$uc_table}Bean> listBeanAll(";
    _java_abs_list_all_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";


    echo _tab(2) . "Vector<{$uc_table}Bean> vList = new Vector<>();\n";
    echo _tab(2) . "{$uc_table}Bean {$table_name}Bean;\n";

    _java_query_header("mysql", "read");
    echo _tab(4) . "String sql = \"{CALL `p_{$table_name}_list_all`({$s_qm})}\";\n";
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

    if (isset($model["list_has_order"]) && $model["list_has_order"] != false) {

        $ii++;
        echo _tab(4) . "st.setString({$ii}, v_order_by); \n";

        $ii++;
        echo _tab(4) . "st.setString({$ii}, v_order_dir); \n";

    }


    echo _tab(4) . "rs = st.executeQuery();\n";
    echo _tab(4) . "while (rs.next()) {\n";
    echo _tab(5) . "{$table_name}Bean = new {$uc_table}Bean();\n";
    foreach ($model['table_fields'] as $key => $field) {
        echo _tab(5) . _java_result_bean($key, $field['type'], $table_name);
    }
    echo _tab(5) . "vList.add({$table_name}Bean);\n";
    echo _tab(4) . "}\n";

    echo _tab(4) . "logger.debug(\"call p_{$table_name}_list_all--done\");\n";

    _java_query_footer("mysql", "read");
    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "list_all");
}