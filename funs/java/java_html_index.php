<?php
if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_html.inc.php");

/**
 * 入口页
 * @param $model
 */
function java_html_index($model)
{
    $tpl_debug = false;
    if ($tpl_debug) {
        ?>
        <!doctype html>
        <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:th="http://www.thymeleaf.org">
        <body>
        <?php
    }

    $table_name = $model['table_name'];
    $uc_table = ucfirst($table_name);

    java_html_header();
    ?>

    <h2 class="mb-4">
        <i class="fa fa-fw fa-<?= $model['table_icon'] ?>"></i> <?= $model['table_title'] ?> 入口页 TODO
    </h2>


    <div class="row mb-2">
        <div class="col-md-12">

        </div>
    </div>


    <?php
    java_html_footer1();
    ?>
    <script type="text/javascript">


        $(function () {


        });
    </script>
    <?php
    java_html_footer2();

    if ($tpl_debug) {
        ?>
        </body>
        </html>
        <?php
    }
}