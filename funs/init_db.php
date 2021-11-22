<?php

/**
 * 初始化数据库
 *
 * @param array $db_conf
 * @return void
 */

function init_db($db_conf)
{
    $db_type = $db_conf["db"];
    $user = $db_conf["user"];
    $passwd = $db_conf["passwd"];
    $host = $db_conf["host"];
    $version = $db_conf["version"];
    $db_name = $db_conf["database"];
    $charset = $db_conf["charset"];

    if ($db_type == "mysql") {

        echo "-- ----------------------------\n";
        echo "-- init mysql user {$user} and database {$db_name} \n";
        echo "-- should run as super user \n";
        echo "-- ----------------------------\n";
        if ($version == "5.6") {
            echo "-- ----------------------------\n";
            echo "-- for mysql5.6 \n";
            echo "-- ----------------------------\n";
            // echo "CREATE USER IF NOT EXISTS '{$user}'@'{$host}' IDENTIFIED BY '{$passwd}';\n";
            echo "CREATE DATABASE IF NOT EXISTS `{$db_name}`  CHARACTER SET {$charset} COLLATE {$charset}_general_ci;\n";
            echo "GRANT ALL PRIVILEGES ON `{$user}\\_%`.* TO '{$user}'@'{$host}' IDENTIFIED BY '{$passwd}';\n";
            echo "GRANT select ON mysql.proc TO '{$user}'@'{$host}' IDENTIFIED BY '{$passwd}';\n";
            echo "FLUSH PRIVILEGES;\n";
        } else {
            // echo "CREATE USER IF NOT EXISTS '{$user}'@'{$host}' IDENTIFIED BY '{$passwd}';\n";
            echo "CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET {$charset} COLLATE {$charset}_general_ci;\n";
            echo "GRANT ALL PRIVILEGES ON `{$user}\\_%`.* TO '{$user}'@'{$host}' IDENTIFIED BY '{$passwd}';\n";
            echo "GRANT select ON mysql.proc TO '{$user}'@'{$host}' IDENTIFIED BY '{$passwd}';\n";

        }
        echo "FLUSH PRIVILEGES;\n";
        echo "-- ----------------------------\n";
        echo "-- for events \n";
        echo "-- ----------------------------\n";
        echo "SET GLOBAL event_scheduler = ON;\n";
        echo "-- ----------------------------\n";
        echo "-- for other funs \n";
        echo "-- ----------------------------\n";


    }
    if ($db_type == "oracle") {
        // TODO
    }


}