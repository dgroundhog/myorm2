<div class="modal fade" id="modal_edit_app_db">
    <!--编辑配置 -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">新建/编辑数据资源</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group row">
                        <label for="sel_db_driver" class="col-sm-2 col-form-label">数据驱动</label>
                        <div class="col-sm-10">
                            <select id="sel_db_driver" class="form-control form-control-sm select2">
                                <?php foreach (Constant::$a_build_db as $key => $value) { ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_db_source" class="col-sm-2 col-form-label">来源</label>
                        <div class="col-sm-10">
                            <select id="sel_db_source" class="form-control form-control-sm select2">
                                <?php foreach (Constant::$a_build_db_source as $key => $value) { ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_db_host" class="col-sm-2 col-form-label">主机</label>
                        <div class="col-sm-10">
                            <input id="txt_db_host"
                                   class="form-control form-control-sm"
                                   type="text"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_db_port" class="col-sm-2 col-form-label">端口</label>
                        <div class="col-sm-10">
                            <input id="txt_db_port"
                                   class="form-control form-control-sm"
                                   type="text"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_db_database" class="col-sm-2 col-form-label">数据库名字</label>
                        <div class="col-sm-10">
                            <input id="txt_db_database"
                                   class="form-control form-control-sm"
                                   type="text"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_db_user" class="col-sm-2 col-form-label">账号</label>
                        <div class="col-sm-10">
                            <input id="txt_db_user"
                                   class="form-control form-control-sm"
                                   type="text"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_db_passwd" class="col-sm-2 col-form-label">口令</label>
                        <div class="col-sm-10">
                            <input id="txt_db_passwd"
                                   class="form-control form-control-sm"
                                   type="text"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sel_db_charset" class="col-sm-2 col-form-label">数据库编码</label>
                        <div class="col-sm-10">
                            <select id="sel_db_charset" class="form-control form-control-sm select2">
                                <?php foreach (Constant::$a_build_db_charset as $key => $value) { ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_db_uri" class="col-sm-2 col-form-label">直接字符串</label>
                        <div class="col-sm-10">
                            <input id="txt_db_uri"
                                   class="form-control form-control-sm"
                                   type="text"/>
                        </div>
                    </div>


                    <input type="hidden" id="txt_db_uuid"/>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_save_db">保存</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>