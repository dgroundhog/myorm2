<div class="modal fade" id="modal_edit_model">
    <!--编辑配置 -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">新建/编辑实体模型</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group row">
                        <label for="txt_model_name" class="col-sm-2 col-form-label">模型名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="txt_model_name"
                                   placeholder="新项目"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_model_title" class="col-sm-2 col-form-label">模型标题</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="txt_model_title"
                                   placeholder="模型标题"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_table_name" class="col-sm-2 col-form-label">数据库表名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="txt_table_name"
                                   placeholder="数据库表名"/>
                            <div class="form-control-static">数据库表名一般和模型名字一致，也可以加入前缀后缀</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_primary_key" class="col-sm-2 col-form-label">数据库主键</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="txt_primary_key"
                                   placeholder="id"/>

                            <div class="form-control-static">默认为自增的ID，有联合主键时也是ID做索引</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_model_has_ui" class="col-sm-2 col-form-label">包含UI</label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="txt_model_has_ui" checked data-bootstrap-switch
                                   data-off-color="danger" data-on-color="success"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_model_icon" class="col-sm-2 col-form-label">FA图标</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="txt_model_icon"
                                   placeholder="apple"/>

                            <div class="form-control-static">参考FontAwesome</div>
                        </div>
                    </div>



                    <div class="form-group row">
                        <label for="txt_model_memo" class="col-sm-2 col-form-label">备注</label>
                        <div class="col-sm-10">
                                <textarea class="form-control form-control-sm" rows="3"
                                          id="txt_model_memo"></textarea>
                        </div>
                    </div>


                    <input type="hidden" id="txt_model_uuid" />
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_save_model">保存</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>