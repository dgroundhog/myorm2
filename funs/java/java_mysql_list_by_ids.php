<?php

if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_abs_list_by_ids.php");


/**
 * java抽象类--按照ID查询列表
 *
 * @param $model
 */
function java_mysql_list_by_ids($model)
{

    if (!$model['list_by_ids_enable'] || $model['list_by_ids_key'] == "" || !isset($model['table_fields'][$model['list_by_ids_key']])) {

        return;
    }

    $key = $model['list_by_ids_key'];
    $type = $model['table_fields'][$key]['type'];
    if ($type != "int" && $type != "char" && $type != "varchar") {
        return;
    }

    if (!_java_db_header($model, "list_by_ids")) {
        return;
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);


    _java_comment_header("根据一组ID查询列表，结构为hash map");
    _java_abs_list_by_ids_param_comment($model);
    _java_comment_footer();
    echo "public Vector<HashMap> listByIds(";
    $i_param = _java_abs_list_by_ids_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";

    $s_qm = _question_marks($i_param);

    echo _tab(2) . "Vector<HashMap> vList = new Vector<>();\n";
    _java_query_header("mysql", "read");
    echo _tab(4) . "String sql = \"{CALL `p_{$table_name}_list_by_ids`(?)}\";\n";
    echo _tab(4) . "st = conn.prepareCall(sql);\n";


    $ii = 0;
    if ($type == "int") {
        echo _tab(4) . "st.setString(1, v_values); \n";
    } else {
        echo _tab(4) . "String v_values_ok = v_values.replaceAll(\",\",\"|\"); \n";
        echo _tab(4) . "st.setString(1, v_values_ok); \n";
    }

    //echo _tab(4) . "st.setString(2, \"{$type}\"); \n";


    echo _tab(4) . "rs = st.executeQuery();\n";
    echo _tab(4) . "while (rs.next()) {\n";
    echo _tab(5) . "HashMap<String , String> _mOne = new HashMap<>();\n";
    echo _tab(5) . "for (Map.Entry<String, String> entry : mRowMap.entrySet()) {\n";
    echo _tab(6) . "_mOne.put(entry.getKey(), rs.getString(entry.getValue())); \n";
    echo _tab(5) . "}\n";
    echo _tab(5) . "vList.add(_mOne);\n";
    echo _tab(4) . "}\n";

    echo _tab(4) . "logger.debug(\"call p_{$table_name}_list_by_ids--done\");\n";

    _java_query_footer("mysql", "read");
    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";


    _java_comment_header("根据一组ID查询列表 结构为bean");
    _java_abs_list_by_ids_param_comment($model);
    _java_comment_footer();
    echo "public Vector<{$uc_table}Bean> listBeanByIds(";
    _java_abs_list_by_ids_param($model);
    echo _tab(1) . ")\n";
    echo _tab(1) . "{\n";


    echo _tab(2) . "Vector<{$uc_table}Bean> vList = new Vector<>();\n";
    echo _tab(2) . "{$uc_table}Bean {$table_name}Bean;\n";

    _java_query_header("mysql", "read");
    echo _tab(4) . "String sql = \"{CALL `p_{$table_name}_list_by_ids`({$s_qm})}\";\n";
    echo _tab(4) . "st = conn.prepareCall(sql);\n";


    $ii = 0;
    echo _tab(4) . "st.setString(1, v_values); \n";


    echo _tab(4) . "rs = st.executeQuery();\n";
    echo _tab(4) . "while (rs.next()) {\n";
    echo _tab(5) . "{$table_name}Bean = new {$uc_table}Bean();\n";
    foreach ($model['table_fields'] as $key => $field) {
        echo _tab(5) . _java_result_bean($key, $field['type'], $table_name);
    }
    echo _tab(5) . "vList.add({$table_name}Bean);\n";
    echo _tab(4) . "}\n";

    echo _tab(4) . "logger.debug(\"call p_{$table_name}_list_by_ids--done\");\n";

    _java_query_footer("mysql", "read");
    echo _tab(2) . "return vList;\n";
    echo _tab(1) . "}";

    _java_db_footer($model, "list_by_ids");
}