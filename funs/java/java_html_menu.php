<?php
if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_html.inc.php");

/**
 * 详情页
 * @param $model
 */
function java_html_menu($a_models)
{

    ?>
    <!doctype html>
    <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:th="http://www.thymeleaf.org">
    <body>


    <li th:fragment="admin_menu_auto">
        <a href="#sm_menu_auto" data-toggle="collapse">
            <i class="fa fa-fw fa-cube"></i> 未分类自动菜单
        </a>

        <ul id="sm_menu_auto" class="list-unstyled collapse">
            <?php
            foreach ($a_models as $table => $model) {
                $table_name = $model["table_name"];
                ?>
                <li>
                    <a href="###"
                        <?= "th:href=\"@{'/{$table_name}/add'}\"" ?> >
                        新建 <?= $model["table_title"]; ?>
                    </a>
                </li>
                <li>
                    <a href="###"
                        <?= "th:href=\"@{'/{$table_name}/list'}\"" ?> >
                        管理 <?= $model["table_title"]; ?>
                    </a>
                </li>

            <?php } ?>
        </ul>
    </li>


    </body>
    </html>
    <?php

}