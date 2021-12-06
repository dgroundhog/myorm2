<div class="modal fade" id="modal_edit_fun">
    <!--编辑配置 -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">新建/编辑CURD函数</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">

                    <div class="form-group row">
                        <label for="sel_fun_type" class="col-sm-2 col-form-label">函数类型</label>
                        <div class="col-sm-10">
                            <select id="sel_fun_type" class="form-control form-control-sm select2">
                                <?php foreach (Constant::$a_fun_type as $key => $value) { ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_fun_name" class="col-sm-2 col-form-label">函数名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="txt_fun_name"
                                   placeholder="新函数"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_fun_title" class="col-sm-2 col-form-label">函数标题</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="txt_fun_title"
                                   placeholder="新函数"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_fun_return_all" class="col-sm-2 col-form-label">返回全部字段*</label>
                        <div class="col-sm-10">
                            <input id="txt_fun_return_all" type="checkbox" checked data-bootstrap-switch
                                   data-off-color="danger" data-on-color="success">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_fun_field" class="col-sm-2 col-form-label">插入/更新/查询返回的字段</label>
                        <div class="col-sm-10">
                            <select id="sel_fun_field" class="duallistbox" multiple="multiple">
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
                        <label for="sel_fun_where" class="col-sm-2 col-form-label">条件TODO 这是里多级嵌套</label>
                        <div class="col-sm-10">

                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_fun_group_field" class="col-sm-2 col-form-label">被聚合健</label>
                        <div class="col-sm-10">
                            <select id="sel_fun_group_field" class="form-control form-control-sm select2">

                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_fun_group_by" class="col-sm-2 col-form-label">聚合分组健</label>
                        <div class="col-sm-10">
                            <select id="sel_fun_group_by" class="form-control form-control-sm select2">

                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_fun_order_enable" class="col-sm-2 col-form-label">查询是否排序</label>
                        <div class="col-sm-10">
                            <input id="txt_fun_order_enable" type="checkbox" checked data-bootstrap-switch
                                   data-off-color="danger" data-on-color="success">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_fun_order_by" class="col-sm-2 col-form-label">排序健</label>
                        <div class="col-sm-10">
                            <select id="sel_fun_order_by" class="form-control form-control-sm select2">

                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_fun_order_dir" class="col-sm-2 col-form-label">排序方向</label>
                        <div class="col-sm-10">
                            <select id="sel_fun_order_dir" class="form-control form-control-sm select2">
                                <option value="ASC">正序ASC</option>
                                <option value="DESC">倒叙DESC</option>
                                <option value="-1">外部输入</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="txt_fun_memo" class="col-sm-2 col-form-label">备注</label>
                        <div class="col-sm-10">
                                <textarea class="form-control form-control-sm" rows="3"
                                          id="txt_fun_memo"></textarea>
                        </div>
                    </div>


                    <input type="hidden" id="txt_model_fun_mid"  value=""/>
                    <input type="hidden" id="txt_model_fun_fid" value=""/>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary"
                        onclick="javascript:App.dt.project.modelFunSave();"
                >保存
                </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>