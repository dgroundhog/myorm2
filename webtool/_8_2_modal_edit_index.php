<div class="modal fade" id="modal_edit_index">
    <!--编辑配置 -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">新建/编辑索引</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group row">
                        <label for="txt_index_name" class="col-sm-2 col-form-label">索引名称</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="txt_index_name"
                                   placeholder="新项目"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_index_type" class="col-sm-2 col-form-label">索引类型</label>
                        <div class="col-sm-10">
                            <select id="sel_index_type" class="form-control form-control-sm select2">
                                <?php foreach (Constant::$a_db_index_type as $key => $value) { ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_index_field" class="col-sm-2 col-form-label">索引字段</label>
                        <div class="col-sm-10">
                            <select id="sel_index_field" class="duallistbox" multiple="multiple">
                                <option selected>Alabama</option>
                                <option>Alaska</option>
                                <option>California</option>
                                <option>Delaware</option>
                                <option>Tennessee</option>
                                <option>Texas</option>
                                <option>Washington</option>
                            </select>
                        </div>
                    </div>



                    <div class="form-group row">
                        <label for="txt_index_memo" class="col-sm-2 col-form-label">备注</label>
                        <div class="col-sm-10">
                                <textarea class="form-control form-control-sm" rows="3"
                                          id="txt_index_memo"></textarea>
                        </div>
                    </div>


                    <input type="hidden" id="txt_model_index_mid" />
                    <input type="hidden" id="txt_model_index_iid" />
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_save_index">保存</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>