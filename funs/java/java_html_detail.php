<?php
if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_html.inc.php");

/**
 * 详情页
 * @param $model
 */
function java_html_detail($model)
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

        <span class="float-right">
            <a class="btn btn-sm btn-info"
               href='<?= $table_name ?>/edit'
                <?= "th:href=\"@{'/{$table_name}/list'}\"" ?> >
                <i class="fa fa-backward"></i>
                返回<?= $model['table_title'] ?>列表
             </a>
        </span>
        <i class="fa fa-fw fa-<?= $model['table_icon'] ?>"></i> <?= $model['table_title'] ?> 详情
    </h2>


    <div class="row mb-2">
        <div class="col-md-6">
            <form>
                <div class="card mb-4">
                    <div class="card-header bg-white font-weight-bold">
                        <i class="fa fa-info-circle"></i> 数据详情
                    </div>

                    <div class="card-body">


                        <?php

                        foreach ($model['table_fields'] as $key => $field) {

                            ?>
                            <div class="form-group row">
                                <label for="<?= $key ?>Id"
                                       class="col-sm-4 col-form-label"><?= $field['name'] ?>：</label>
                                <div class="col-sm-8">
                                    <input type="text"
                                           class="form-control"
                                           name="<?= $key ?>"
                                           id="<?= $key ?>Id"
                                           aria-describedby="<?= $key ?>Help"
                                           value=''
                                           readonly="readonly"
                                        <?= "th:value=\"\${mInfo.{$key}}\"" ?> />

                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <div class="card-footer">
                        <?php if ($model['update_enable']) { ?>
                            <?php
                            $ii = 0;
                            $fetch_by = "";
                            foreach ($model['fetch_by'] as $key) {

                                $ii++;
                                $fetch_by = $fetch_by . _java_html_op_url_param($key, "mInfo", $ii);
                            }
                            ?>

                            <a href='<?= $table_name ?>/edit'
                               class="btn btn-lg btn-info "
                                <?= "th:href=\"@{'/{$table_name}/edit'({$fetch_by})}\"" ?>>
                                <i class="fa fa-edit"></i>
                                编 辑
                            </a>

                        <?php } ?>
                    </div>

                </div>
            </form>
        </div>
    </div>


    <?php
    java_html_footer1();
    ?>
    <script type="text/javascript">
        ;

        $(function () {
            ;
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