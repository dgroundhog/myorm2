<div class="modal fade" id="modal_edit_field">
    <!--编辑配置 -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">新建/编辑字段</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">

                    <div class="form-group row">
                        <label for="txt_field_name" class="col-sm-2 col-form-label">字段名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="txt_field_name"
                                   placeholder="字段名"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="txt_field_title" class="col-sm-2 col-form-label">字段标题</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="txt_field_title"
                                   placeholder="字段标题"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_field_type" class="col-sm-2 col-form-label">类型</label>
                        <div class="col-sm-10">
                            <select id="sel_field_type" class="form-control form-control-sm select2">
                                <?php foreach (Constant::$a_build_db_field_type as $key => $value) { ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_field_size" class="col-sm-2 col-form-label">长度</label>
                        <div class="col-sm-10">
                            <input id="txt_field_size"
                                   class="form-control form-control-sm"
                                   type="text"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_field_default_val" class="col-sm-2 col-form-label">默认值</label>
                        <div class="col-sm-10">
                            <input id="txt_field_default_val"
                                   class="form-control form-control-sm"
                                   type="text"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_field_auto_inc" class="col-sm-2 col-form-label">自动增长</label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="txt_field_auto_inc" checked data-bootstrap-switch
                                   data-off-color="danger" data-on-color="success">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_field_required" class="col-sm-2 col-form-label">允许空值</label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="txt_field_required" checked data-bootstrap-switch
                                   data-off-color="danger" data-on-color="success">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_field_memo" class="col-sm-2 col-form-label">帮助提示</label>
                        <div class="col-sm-10">
                            <input id="txt_field_memo"
                                   class="form-control form-control-sm"
                                   value=""
                                   type="text"/>

                            <div class="form-control-static">
                                就是我这里的提示
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_field_position" class="col-sm-2 col-form-label">排序</label>
                        <div class="col-sm-10">
                            <input id="txt_field_position"
                                   class="form-control form-control-sm"
                                   value="255"
                                   type="text"/>

                            <div class="form-control-static">
                                0~255有效
                            </div>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="sel_field_input_by" class="col-sm-2 col-form-label">输入方法</label>
                        <div class="col-sm-10">
                            <select id="sel_field_input_by" class="form-control form-control-sm select2">
                                <?php foreach (Constant::$a_build_db_field_input as $key => $value) { ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_field_hash" class="col-sm-2 col-form-label">字典值</label>
                        <div class="col-sm-10">
                            <textarea id="txt_field_hash"
                                      class="form-control">
                            </textarea>
                            <div class="form-control-static">
                              KV对用逗号分隔。多对KV值之间用；分隔。比如：  1,甲方;2,乙方
                            </div>

                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_field_filter" class="col-sm-2 col-form-label">数据验证</label>
                        <div class="col-sm-10">
                            <select id="sel_field_filter" class="form-control form-control-sm select2">
                                <?php foreach (Constant::$a_build_db_field_filter as $key => $value) { ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_field_regexp" class="col-sm-2 col-form-label">自定义正则表式</label>
                        <div class="col-sm-10">
                            <textarea id="txt_field_regexp"
                                      class="form-control">

                            </textarea>

                        </div>
                    </div>


                    <input type="hidden" id="txt_field_model_id"/>
                    <input type="hidden" id="txt_field_uuid"/>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_save_field">保存</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>