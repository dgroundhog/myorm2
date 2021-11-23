<session class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <form class="form-horizontal">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <a name="basic">
                                    <i class="fa fa fa-flag-o"></i>
                                </a>
                                1、基本信息 <small>项目1:应用版本N</small></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="txt_project_name" class="col-sm-2 col-form-label">项目ID</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control form-control-sm"
                                           id="txt_project_name"
                                           readonly="readonly"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="txt_project_title" class="col-sm-2 col-form-label">项目标题</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control form-control-sm"
                                           id="txt_project_title"
                                           placeholder="新项目"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="txt_project_memo" class="col-sm-2 col-form-label">备注</label>
                                <div class="col-sm-10">
                                            <textarea class="form-control form-control-sm" rows="3"
                                                      id="txt_project_memo"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="txt_project_ctime" class="col-sm-2 col-form-label">创建时间</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control form-control-sm"
                                           id="txt_project_ctime"
                                           readonly="readonly"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="txt_project_utime" class="col-sm-2 col-form-label">最后更新</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control form-control-sm"
                                           id="txt_project_utime"
                                           readonly="readonly"/>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="button" class="btn btn-primary" id="btn_save_project">
                                <i class="fa fa-check"></i> 保存项目
                            </button>
                            <button type="button" class="btn btn-success float-right" id="btn_add_app">
                                <i class="fa fa-plus"></i> 创建新版本
                            </button>
                        </div>

                    </div>

                </form>
            </div>

            <!-- /.col-md-6 -->
            <div class="col-lg-6">
                <form class="form-horizontal" enctype="multipart/form-data">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <a name="basic">
                                    <i class="fa fa fa-flag-o"></i>
                                </a>
                                2、
                                应用版本信息</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">应用版本</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control form-control-sm txt_app_name"
                                           readonly="readonly"
                                           placeholder="新项目"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">应用别称</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control form-control-sm txt_app_title"
                                           readonly="readonly"
                                           placeholder="新项目"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="app_file_logo" class="col-sm-2 col-form-label">图片</label>
                                <div class="col-sm-10">
                                    <table class="table table-sm ">
                                        <tbody>
                                        <tr>
                                            <td width="50%">LOGO</td>
                                            <td width="50%">ICON</td>
                                        </tr>
                                        <tr>
                                            <td scope="row"><img src="img/mustang.png"
                                                                 class="img-fluid img_logo_saved" alt="...">
                                            </td>
                                            <td scope="row"><img src="img/mustang.png"
                                                                 class="img-fluid img_icon_saved" alt="...">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="txt_app_memo" class="col-sm-2 col-form-label">备注</label>
                                <div class="col-sm-10">
                                            <textarea class="form-control form-control-sm txt_app_memo" rows="3"
                                                      readonly="readonly "></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">创建时间</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control form-control-sm txt_app_ctime"
                                           readonly="readonly"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">最后更新</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control form-control-sm txt_app_utime"
                                           readonly="readonly"/>
                                </div>
                            </div>

                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="button" class="btn btn-primary" id="btn_edit_app">
                                <i class="fa fa-edit"></i>
                                编辑
                            </button>
                            <button type="button" class="btn btn-success" id="btn_clone_app">
                                <i class="fa fa-copy"></i>
                                复制
                            </button>
                            <button type="button" class="btn btn-danger float-right" id="btn_delete_app">
                                <i class="fa fa-trash"></i>
                                删除
                            </button>
                        </div>

                    </div>
                </form>
            </div>
            <!-- /.col-md-6 -->

        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</session>