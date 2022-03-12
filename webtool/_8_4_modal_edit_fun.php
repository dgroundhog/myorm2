<div class="modal fade " id="modal_edit_fun">
    <!--编辑配置 -->
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">新建/编辑CURD函数</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">

                        <form class="form-horizontal">

                            <div class="form-group row">
                                <label for="sel_fun_type" class="col-sm-2 col-form-label">函数类型</label>
                                <div class="col-sm-10">
                                    <select id="sel_fun_type" class="form-control form-control-sm select2">
                                        <?php foreach (Constant::$a_fun_type as $key => $value) { ?>
                                            <option value="<?= $key ?>">
                                                <?= $key ?> ｜ <?= $value ?>
                                            </option>
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
                                <label for="txt_fun_all_field" class="col-sm-2 col-form-label">使用全部*</label>
                                <div class="col-sm-10">
                                    <input id="txt_fun_all_field" type="checkbox" checked data-bootstrap-switch
                                           data-off-color="danger" data-on-color="success">
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
                                <label for="sel_fun_group_by" class="col-sm-2 col-form-label">分组健</label>
                                <div class="col-sm-10">
                                    <select id="sel_fun_group_by" class="form-control form-control-sm select2">

                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="txt_fun_pager_enable" class="col-sm-2 col-form-label">是否分页</label>
                                <div class="col-sm-10">
                                    <input id="txt_fun_pager_enable" type="checkbox" checked data-bootstrap-switch
                                           data-off-color="danger" data-on-color="success">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="txt_fun_pager_size" class="col-sm-2 col-form-label">分页大小</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control form-control-sm" id="txt_fun_pager_size"
                                           placeholder="默认20，或者外部输入"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="txt_fun_limit" class="col-sm-2 col-form-label">操作限制</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control form-control-sm" id="txt_fun_limit"
                                           placeholder="默认1，0表示不限制，或者外部输入"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="txt_fun_order_enable" class="col-sm-2 col-form-label">是否排序</label>
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

                            <div class="form-group row">
                                <label for="txt_fun_query_optimize" class="col-sm-2 col-form-label">大表优化</label>
                                <div class="col-sm-10">
                                    <input id="txt_fun_query_optimize" type="checkbox" checked data-bootstrap-switch
                                           data-off-color="danger" data-on-color="success">
                                </div>
                            </div>


                            <input type="hidden" id="txt_model_fun_mid" value=""/>
                            <input type="hidden" id="txt_model_fun_fid" value=""/>
                        </form>
                    </div>
                    <div class="col-6">

                        <div class="card">
                            <div class="card-body p-1">
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

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">WHERE组合查询条件</h3>
                                <div class="card-tools">
                                    <button class="btn btn-tool" type="button"
                                            onclick="javascript:App.dt.project.modelFunWhereAdd('','AND');">
                                        <i class="fas fa-plus-circle"></i>
                                        <img src="img/and-gate.png" style="height: 24px;width: 24px"/>
                                        与条件组合
                                    </button>
                                    <button class="btn btn-tool" type="button"
                                            onclick="javascript:App.dt.project.modelFunWhereAdd('','OR');">
                                        <i class="fas fa-plus-circle"></i>
                                        <img src="img/or-gate.png" style="height: 24px;width: 24px"/>
                                        或条件组合
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-1" id="block_where">

                            </div>


                            <hr/>


                            <div class="card-footer" id="block_edit_mode_conf">
                                <form class="form-horizontal ">
                                    <div class="form-group row" id="block_fun_cond_field">
                                        <label for="sel_fun_cond_field" class="col-sm-4 col-form-label">操作字段</label>
                                        <div class="col-sm-8">
                                            <select id="sel_fun_cond_field"
                                                    class="form-control form-control-sm select2">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="sel_fun_cond_type" class="col-sm-4 col-form-label">条件类型</label>
                                        <div class="col-sm-8">
                                            <select id="sel_fun_cond_type" class="form-control form-control-sm select2">
                                                <?php foreach (Constant::$a_cond_type as $key => $value) { ?>
                                                    <option value="<?= $key ?>">
                                                        <?= $key ?> ｜ <?= $value ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="sel_fun_cond_v1_type" class="col-sm-4 col-form-label">参数1类型</label>
                                        <div class="col-sm-8">
                                            <select id="sel_fun_cond_v1_type"
                                                    class="form-control form-control-sm select2">
                                                <?php foreach (Constant::$a_cond_val_type as $key => $value) { ?>
                                                    <option value="<?= $key ?>">
                                                        <?= $key ?> ｜ <?= $value ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="txt_fun_cond_v1" class="col-sm-4 col-form-label">参数1</label>
                                        <div class="col-sm-8">
                                <textarea class="form-control form-control-sm" rows="1"
                                          id="txt_fun_cond_v1"></textarea>
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label for="sel_fun_cond_v2_type" class="col-sm-4 col-form-label">参数2类型</label>
                                        <div class="col-sm-8">
                                            <select id="sel_fun_cond_v2_type"
                                                    class="form-control form-control-sm select2">
                                                <?php foreach (Constant::$a_cond_val_type as $key => $value) { ?>
                                                    <option value="<?= $key ?>">
                                                        <?= $key ?> ｜ <?= $value ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="txt_fun_cond_v2" class="col-sm-4 col-form-label">参数2</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm"
                                                   id="txt_fun_cond_v2"/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="btn_save_cond" class="col-sm-4 col-form-label"></label>
                                        <div class="col-sm-8">
                                            <button type="button" class="btn btn-success btn-sm" id="btn_save_cond">
                                                保存where条件
                                            </button>

                                            <button type="button" class="btn btn-success btn-sm" id="btn_save_having">
                                                保存having条件
                                            </button>
                                        </div>
                                    </div>

                                    <input type="hidden" id="txt_cond_uuid"/>
                                    <input type="hidden" id="txt_where_uuid"/>
                                </form>
                            </div>

                            <hr/>
                            <div class="card-header">
                                <h3 class="card-title">HAVING聚合后过滤（参数为聚合新键）</h3>
                                <div class="card-tools">
                                    <button class="btn btn-tool" type="button"
                                            onclick="javascript:App.dt.project.modelFunHavingEdit();">
                                        <i class="fas fa-edit"></i> 编辑
                                    </button>

                                </div>
                            </div>
                            <div class="card-body p-1" id="block_having">
                                <table class="table table-striped table-sm">
                                    <tr>
                                        <td>
                                            聚合健
                                        </td>
                                        <td id="txt_having_type">{$cond.type}</td>
                                        <td id="txt_having_v1_type">{$cond.v1_type}</td>
                                        <td id="txt_having_v1">{$cond.v1}</td>
                                        <td id="txt_having_v2_type">{$cond.v2_type}</td>
                                        <td id="txt_having_v2">{$cond.v2}</td>

                                        <td class="text-right py-0 align-middle">
                                            <div class="btn-group btn-group-sm" id="btn_drop_having">
                                                <button type="button"
                                                        class="btn btn-danger dropdown-toggle dropdown-icon"
                                                        data-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-trash"></i> 移除
                                                </button>
                                                <div class="dropdown-menu" style="">
                                                    <a class="dropdown-item" href="#">-</a>
                                                    <a class="dropdown-item" href="#">--</a>
                                                    <a class="dropdown-item" href="#">---</a>
                                                    <a class="dropdown-item"
                                                       onclick="javascript:App.dt.project.modelFunHavingDrop();"
                                                       href="####">确认移除</a>
                                                </div>

                                            </div>
                                        </td>
                                    </tr>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

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