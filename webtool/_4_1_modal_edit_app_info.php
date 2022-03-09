<div class="modal fade" id="modal_edit_app_info">
    <!--编辑配置 -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">新建/编辑应用版本信息</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" enctype="multipart/form-data">
                    <div class="form-group row">
                        <label for="txt_app_name" class="col-sm-2 col-form-label">应用版本</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm txt_app_name" id="txt_app_name"
                                   placeholder="新项目"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="txt_app_title" class="col-sm-2 col-form-label">应用别称</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm txt_app_title" id="txt_app_title"
                                   placeholder="新项目"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="txt_app_title" class="col-sm-2 col-form-label">包名/命名空间</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm txt_app_package" id="txt_app_package"
                                   placeholder="a.b.c"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="app_file_logo" class="col-sm-2 col-form-label">LOGO</label>
                        <div class="col-sm-10">
                            <table class="table table-sm ">
                                <tbody>
                                <tr>
                                    <td width="50%">已保存</td>
                                    <td width="50%">待保存</td>
                                </tr>
                                <tr>
                                    <td scope="row"><img id="img_logo_saved" src="img/mustang.png"
                                                         class="img-fluid img_logo_saved" alt="..."></td>
                                    <td scope="row"><img id="img_logo_free" src="img/mustang.png"
                                                         class="img-fluid" alt="..."></td>
                                </tr>
                                </tbody>
                            </table>
                            <input type="hidden" id="img_logo_id" value=""/>
                            <div class="file-loading">
                                <input id="app_file_logo" name="one_image" type="file" multiple/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="app_file_icon" class="col-sm-2 col-form-label">ICON</label>
                        <div class="col-sm-10">
                            <table class="table table-sm ">
                                <tbody>
                                <tr>
                                    <td width="50%">已存</td>
                                    <td width="50%">待存</td>
                                </tr>
                                <tr>
                                    <td scope="row"><img id="img_icon_saved" src="img/mustang.png"
                                                         class="img-fluid img_icon_saved" alt="..."></td>
                                    <td scope="row"><img id="img_icon_free" src="img/mustang.png"
                                                         class="img-fluid" alt="..."></td>
                                </tr>
                                </tbody>
                            </table>
                            <input type="hidden" id="img_icon_id" value=""/>
                            <div class="file-loading">
                                <input id="app_file_icon" name="one_image" type="file" multiple/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txt_app_memo" class="col-sm-2 col-form-label">备注</label>
                        <div class="col-sm-10">
                                <textarea class="form-control form-control-sm txt_app_memo" rows="3"
                                          id="txt_app_memo"></textarea>
                        </div>
                    </div>


                </form>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_save_app">保存</button>
            </div>
        </div>
    </div>
</div>