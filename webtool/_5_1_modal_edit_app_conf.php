<div class="modal fade" id="modal_edit_app_conf">
    <!--编辑配置 -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">新建/编辑应用配置</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group row">
                        <label for="sel_app_mvc" class="col-sm-2 col-form-label">开发技术</label>
                        <div class="col-sm-10">
                            <select id="sel_app_mvc" class="form-control form-control-sm select2">
                                <?php foreach (Constant::$a_build_mvc as $key => $value) {?>
                                <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_app_mvc" class="col-sm-2 col-form-label">前端UI</label>
                        <div class="col-sm-10">
                            <select id="sel_app_ui" class="form-control form-control-sm select2">
                                <?php foreach (Constant::$a_build_ui as $key => $value) {?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_app_mvc" class="col-sm-2 col-form-label">启用restful</label>
                        <div class="col-sm-10">
                            <input type="checkbox" name="my-checkbox" checked data-bootstrap-switch
                                   data-off-color="danger" data-on-color="success">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_app_mvc" class="col-sm-2 col-form-label">启用文档</label>
                        <div class="col-sm-10">
                            <input type="checkbox" name="my-checkbox" checked data-bootstrap-switch
                                   data-off-color="danger" data-on-color="success">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_app_mvc" class="col-sm-2 col-form-label">启用测试</label>
                        <div class="col-sm-10">
                            <input type="checkbox" name="my-checkbox" checked data-bootstrap-switch
                                   data-off-color="danger" data-on-color="success">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_save_conf">保存</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>