<?php
/**
 * 删除oracle 表格
 *
 * @param $model
 */
function oracle_reset_table($model)
{

    echo "-- ----------------------------\n";
    echo "-- Delete Table   t_{$model['table_name']} \n";
    echo "-- ----------------------------\n";
    echo "DROP TABLE IF EXISTS `t_{$model['table_name']}`;\n";

}