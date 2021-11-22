<?php
if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_html.inc.php");

/**
 * 新建页
 * @param $model
 */
function java_html_new($model)
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

    java_html_header(true);
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
        <i class="fa fa-fw fa-<?= $model['table_icon'] ?>"></i> <?= $model['table_title'] ?> 编辑
    </h2>

    <div th:replace="_comm_ui::block_edit_msg"></div>


    <div class="row mb-2">
        <div class="col-md-6">
            <form method="post"
                  enctype="application/x-www-form-urlencoded"
                  action=""
                  id="form_main"
                  th:action="@{'/<?= $table_name ?>/save'}">
                <div class="card mb-4">
                    <div class="card-header bg-white font-weight-bold">
                        <i class="fa fa-info-circle"></i> 新建 信息
                    </div>

                    <div class="card-body">

                        <input type="hidden"
                               readonly="readonly"
                               name="form_token__"
                               value='s3c'
                            <?= "th:value=\"\${form_token__}\"" ?> />

                        <?php


                        foreach ($model['table_fields'] as $key => $field) {
                            if (!in_array($key, $model['add_keys'])) {
                                continue;
                            }
                            if ($key == "flag") {
                                continue;
                            }
                            if ($key == "op_id1" || $key == "op_id2") {
                                continue;
                            }
                            if ($key == "utime" || $key == "ctime") {
                                continue;
                            }
                            $uc_key = ucfirst($key);
                            ?>


                            <div class="form-group row">
                                <label for="<?= $key ?>Id"
                                       class="col-sm-4 col-form-label"><?= $field['name'] ?>：</label>
                                <div class="col-sm-8">

                                    <?php
                                    if ($field['type'] == "blob" || $field['type'] == "longblob") {
                                        ?>
                                        <input type="text"
                                               class="form-control"
                                               name="<?= $key ?>ForUpload"
                                               readonly="readonly"
                                               id="<?= $key ?>Id"
                                               aria-describedby="<?= $key ?>Help"
                                               placeholder=""
                                               value='0'/>
                                        <?php
                                    }
                                    if (isset($model['keys_by_select']) && in_array($key, $model['keys_by_select'])) {
                                        ?>

                                        <select class="form-control select2"
                                                name="<?= $key ?>"
                                                id="<?= $key ?>Id">
                                            <option value="">不限</option>
                                            <option <?= "th:each=\"_Id,_Value:\${m{$uc_key}List}\"" ?>
                                                    th:value="${_Value.current.key}"
                                                    th:text="${_Value.current.value}"
                                            ></option>
                                        </select>

                                    <?php } elseif ($field['type'] == "text") { ?>
                                        <textarea class="form-control"
                                                  name="<?= $key ?>"
                                                  id="<?= $key ?>Id"
                                                  rows="3"
                                        ></textarea>
                                        <?php
                                    } else {
                                        ?>
                                        <input type="text"
                                               class="form-control <?php if ($field['type'] == 'date') { ?>js_date_select<?php } ?>"
                                               name="<?= $key ?>"
                                               id="<?= $key ?>Id"
                                               aria-describedby="<?= $key ?>Help"
                                               value=''
                                            <?php
                                            if ($field["required"] == "1") {
                                                ?>
                                                required="required"
                                                <?php
                                            }
                                            if (isset($field["valid_rule"])) {
                                                ?>
                                                valid_rule="<?= $field['valid_rule'] ?>"
                                                valid_min="<?= $field['valid_min'] ?>"
                                                valid_max="<?= $field['valid_max'] ?>"
                                            <?php }
                                            ?>
                                        />
                                    <?php } ?>
                                    <small id="<?= $key ?>Help"
                                           class="help_text form-text text-muted"><?= $field['help'] ?>
                                    </small>
                                    <div class="invalid-feedback"></div>
                                    <div class="valid-feedback"></div>
                                </div>
                            </div>


                            <?php
                        }
                        ?>
                    </div>

                    <div class="card-footer">
                        <button type="button" id="btn_submit" class="btn btn-lg btn-primary btn-block">
                            <i class="fa fa-check"></i>
                            提交保存
                        </button>
                    </div>

                </div>
            </form>
        </div>

        <div class="col-md-6">

            <?php if (isset($model['upload_enable']) && $model['upload_enable']) { ?>
                <div th:replace="_comm_ui::block_upload_avatar"></div>
            <?php } ?>
        </div>


    </div>


    <?php
    java_html_footer1(true);
    ?>
    <script type="text/javascript">

        var _url_submit = "[[@{'/<?= $table_name ?>/ajax_save'}]]";
        var _url_upload = "[[@{'/common/upload'}]]";
        var _g_token = "[[${op_token__}]]";
        //TODO 头像还是文件？
        var _ajaxSubmit = false;

        function checkSubmit() {
            var _formId = "form_<?=$table_name?>";


            var finalRet = App.su.validate.check(_formId)

            if (finalRet) {
                var _form = $("#" + _formId);
                if (!_ajaxSubmit) {
                    _form.submit();
                } else {
                    //ajax 操作
                    //var _url_submit = _form.attr("action");
                    var _uuid = App.su.maths.uuid.create();
                    var _data = "_r=" + _uuid;
                    <?php
                    foreach ($model['table_fields'] as $key => $field) {
                    if (!in_array($key, $model['add_keys'])) {
                        continue;
                    }
                    ?>
                    _data += "&<?=$key?>=" + encodeURIComponent($("#<?=$key?>Id").val());
                    <?php } ?>
                    var _url = _url_submit + "?" + _uuid;

                    $.ajax({
                        type: 'POST',
                        url: _url,
                        data: _data,
                        dataType: 'json',
                        success: function (ret_ajax) {
                            if (ret_ajax.code == "ok") {
                                App.su.notice.succ("保存成功");
                                if (undefined != ret_ajax.url_redirect) {
                                    location.href = ret_ajax.url_redirect;
                                }

                            } else {
                                App.su.notice.err("保存失败", "请检查输入");
                            }
                        }
                    });
                }
            }
        }


        $(function () {

            var _main_from = $("#form_main");
            $("#btn_submit").click(function () {

                _main_from.find(".is-valid").removeClass("is-valid");
                _main_from.find(".is-invalid").removeClass("is-invalid");
                _main_from.find(".valid-feedback").empty();
                _main_from.find(".invalid-feedback").empty();

                var has_error = false;

                <?php
                foreach ($model['table_fields'] as $key => $field) {
                if (!in_array($key, $model['add_keys'])) {
                    continue;
                }
                if ($field["required"] != "1") {
                    continue;
                }

                ?>
                var _ipt_<?=$key?> = $("#<?=$key?>Id");
                var _val_<?=$key?> = _ipt_<?=$key?>.val();

                if (App.su.string.cnLength(_val_<?=$key?>) < 1) {
                    var _msg = "<?php echo $field['name']; ?>不能为空";
                    _ipt_<?=$key?>.parent().find(".invalid-feedback").html(_msg);
                    _ipt_<?=$key?>.addClass("is-invalid");
                    has_error = true;
                }
                <?php
                }
                ?>
                //TODO
                if (has_error) {
                    bootbox.alert("信息填写有误，请检查");
                    return false;
                }

                bootbox.confirm({
                    // title: "?",
                    message: "确认保存基本信息？",
                    buttons: {
                        cancel: {
                            label: '<i class="fa fa-times"></i> 取 消',
                            className: 'btn-danger'
                        },
                        confirm: {
                            label: '<i class="fa fa-check"></i> 确 认',
                            className: 'btn-success'
                        }
                    },
                    callback: function (result) {
                        if (result) {
                            _main_from.submit();
                        }
                    }
                });
            });

            <?php if (isset($model['upload_enable']) && $model['upload_enable']) { ?>
            //上传文件方法封装
            App.su.upload_helper.init_photo("avatar", g_url_upload);
            $("#btn_show_upload").click(function () {
                $("#block_upload_photo").show();
            });
            <?php } ?>

            $(".js_date_select").datepicker({
                language: 'zh-CN',
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd'
            });
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