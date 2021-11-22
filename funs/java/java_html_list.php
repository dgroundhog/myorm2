<?php
if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_html.inc.php");

/**
 *列表页
 * @param $model
 */
function java_html_list($model)
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
        <?php if ($model['add_enable']) { ?>
            <span class="float-right">
            <a class="btn btn-sm btn-success"
               href='<?= $table_name ?>/edit'
                <?= "th:href=\"@{'/{$table_name}/add'}\"" ?> >
                <i class="fa fa-plus"></i>
                添加 <?= $model['table_title'] ?>
             </a>
        </span>
        <?php } ?>

        <i class="fa fa-fw fa-<?= $model['table_icon'] ?>"></i> <?= $model['table_title'] ?> 列表
    </h2>

    <div class="row mb-2">
        <div class="col">
            <div class="card">
                <div class="card-header bg-white font-weight-bold">
                    <i class="fa fa-filter"></i> 搜索条件
                </div>
                <div class="card-body">
                    <form method="get" enctype="application/x-www-form-urlencoded" action=""
                          th:action="@{'/<?= $table_name ?>/list'}"
                    >
                        <div class="form-row">
                            <?php
                            foreach ($model['list_by'] as $key) {
                                $uc_key = ucfirst($key);
                                ?>
                                <div class="col">
                                    <label for="ipt_<?= $key ?>"><?= $model['table_fields'][$key]['name'] ?></label>
                                    <?php
                                    if (!isset($model['keys_by_select']) || !in_array($key, $model['keys_by_select'])) {
                                        ?>
                                        <input type="text" class="form-control mr-sm-1 col"
                                               id="ipt_<?= $key ?>"
                                               name="<?= $key ?>"
                                               placeholder="<?= $model['table_fields'][$key]['name'] ?>"
                                            <?= "th:value=\"\${curr_{$key}}\"" ?> />

                                    <?php } else {
                                        ?>

                                        <select class="form-control"
                                                name="<?= $key ?>"
                                                id="ipt_<?= $key ?>">
                                            <option value="">不限</option>
                                            <option <?= "th:each=\"_Id,_Value:\${m{$uc_key}List}\"" ?>
                                                    th:value="${_Value.current.key}"
                                                    th:text="${_Value.current.value}"
                                                <?= "th:selected=\"\${_Value.current.key}==\${curr_{$key}}\"" ?>
                                            ></option>
                                        </select>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            if (isset($model["list_kw"]) && count($model["list_kw"]) > 0) {
                                ?>
                                <div class="col">
                                    <label for="iptKw">关键字</label>
                                    <input type="text" class="form-control mr-sm-2"
                                           id="iptKw"
                                           name="kw"
                                           placeholder="关键字"
                                        <?= "th:value=\"\${curr_kw}\"" ?> />
                                </div>
                                <?php

                            }

                            if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {
                                ?>
                                <div class="col">
                                    <label for="date_from">开始日</label>
                                    <div class="input-group mr-sm-2">
                                        <div class="input-group-prepend"
                                             id="h_date_from">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                        <input type="text"
                                               class="form-control"
                                               id="date_from"
                                               value=""
                                               name="date_from"
                                               placeholder="开始日期"
                                            <?= "th:value=\"\${curr_date_from}\"" ?> />

                                    </div>
                                </div>
                                <div class="col">
                                    <label for="date_to">结束日</label>
                                    <div class="input-group mr-sm-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="h_date_to"><i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                        <input type="text"
                                               class="form-control"
                                               id="date_to"
                                               name="date_to"
                                               value=""
                                               placeholder="结束日起"
                                            <?= "th:value=\"\${curr_date_to}\"" ?> />

                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="col">
                                <label>&nbsp;</label>
                                <div class="">
                                    <button type="submit" class="btn btn-primary ">
                                        <i class="fa fa-search"></i>
                                        查询
                                    </button>
                                    <a class="btn btn-secondary" href="###" th:href="@{'/<?= $table_name ?>/list'}">
                                        <i class="fa fa-list"></i>
                                        重置搜索
                                    </a>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">

            <div class="card">
                <div class="card-body">
                    <div>
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="thead-light">
                            <tr class="">

                                <?php

                                foreach ($model['table_fields'] as $key => $a_info) {
                                    if ($a_info['type'] == "longblob" || $a_info['type'] == "blob" || $a_info['type'] == "longtext") {
                                        continue;
                                    }
                                    if ($key == "flag") {
                                        continue;
                                    }
                                    ?>
                                    <th><?= $a_info["name"] ?></th>
                                    <?php
                                }
                                ?>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr th:each="oneRow : ${vList}">
                                <?php

                                foreach ($model['table_fields'] as $key => $a_info) {
                                    if ($a_info['type'] == "longblob" || $a_info['type'] == "blob" || $a_info['type'] == "longtext") {
                                        continue;
                                    }
                                    if ($key == "flag") {
                                        continue;
                                    }
                                    $_row = "th:text=\"\${oneRow.{$key}}\"";
                                    $_row2 = "[[\${oneRow.{$key}}]]";
                                    ?>
                                    <td <?= $_row ?> ><?=$a_info['name']?> <?= $_row2 ?></td>
                                    <?php
                                }


                                $ii = 0;
                                $fetch_by = "";
                                foreach ($model['fetch_by'] as $key) {
                                    $ii++;
                                    $fetch_by = $fetch_by . _java_html_op_url_param($key, "oneRow", $ii);
                                }
                                ?>
                                <td>
                                    <a class="btn btn-sm btn-success"
                                        <?= "th:href=\"@{'/{$table_name}/detail'({$fetch_by})}\"" ?>
                                       href='<?= $table_name ?>/edit'>
                                        <i class="fa fa-info"></i>
                                        详情
                                    </a>

                                    <?php if ($model['update_enable']) { ?>
                                        <a class="btn btn-sm btn-primary"
                                            <?= "th:href=\"@{'/{$table_name}/edit'({$fetch_by})}\"" ?>
                                           href='<?= $table_name ?>/edit'>
                                            <i class="fa fa-edit"></i>
                                            编辑
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-center" th:utext="${pager_html}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    java_html_footer1();
    ?>
    <script type="text/javascript">


        $(function () {

            $('.select2').select2();





            <?php
            if (isset($model["list_has_date"]) && $model["list_has_date"] != false) {
            ?>$("#date_from").datepicker({
                language: 'zh-CN',
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd'
            });
            $("#date_to").datepicker({
                language: 'zh-CN',
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd'
            });
            <?php
            }
            ?>
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