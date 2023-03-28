<div class="modal fade" id="modal_edit_ecode">
    <!--编辑配置 -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">新建/编辑错误代码</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group row">
                        <label for="txt_ecode_name" class="col-sm-2 col-form-label">代码</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="txt_ecode_name"
                                   placeholder="E2xxxx"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_ecode_desc" class="col-sm-2 col-form-label">错误描述</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="txt_ecode_desc"
                                   placeholder="错误描述"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_save_ecode">保存</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>