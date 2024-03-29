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
                                <label for="txt_project_ctime" class="col-sm-2 col-form-label">更新时间</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control form-control-sm"
                                           id="txt_project_ctime"
                                           readonly="readonly"/>
                                </div>
                                <div class="col-sm-5">
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

                            <button type="button" class="btn btn-danger btn_sync_project">
                                <i class="fa fa-save"></i> 同步服务器
                            </button>

                            <button type="button" class="btn btn-info">
                                <i class="fa fa-file"></i> 关联文档
                            </button>


                            <button type="button" class="btn btn-success float-right" id="btn_add_app">
                                <i class="fa fa-plus"></i> 创建新版本
                            </button>
                        </div>

                    </div>


                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa fa fa-cogs"></i>
                                模型样板
                            </h3>

                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <div class="card-body">
                            <button type="button" class="btn btn-info"
                                    onclick="App.dt.data.ccModel_Admin();"
                            >
                                <i class="fa fa-key"></i> 创建基本管理员
                            </button>

                            <button type="button" class="btn btn-info"
                                    onclick="App.dt.data.ccModel_Group();"
                            >
                                <i class="fa fa-key"></i> 创建人员分组
                            </button>

                            <button type="button" class="btn btn-info"
                                    onclick="App.dt.data.ccModel_User();"
                            >
                                <i class="fa fa-key"></i> 创建人员
                            </button>
                        </div>
                    </div>
                </form>
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa fa-cogs"></i>
                            构建当前版本
                        </h3>

                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class="card-body">
                        <div class="row">

                            <div class="col">
                                <select id="sel_build_mvc" class="form-control form-control-sm select2">

                                </select>
                            </div>
                            <div class="col">
                                <select id="sel_build_db" class="form-control form-control-sm select2">

                                </select>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-primary" id="btn_build">
                                    <i class="fa fa-flag"></i> 构建模型
                                </button>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-danger" id="btn_build_all">
                                    <i class="fa fa-flag"></i> 构建全部
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
                                <button type="button" class="btn btn-tool" id="btn_delete_app">
                                    <i class="fas fa-trash"></i> 删除
                                </button>

                                <button type="button" class="btn btn-tool" id="btn_edit_app">
                                    <i class="fas fa-edit"></i> 编辑
                                </button>

                                <button type="button" class="btn btn-tool" id="btn_clone_app">
                                    <i class="fas fa-copy"></i> 复制
                                </button>

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
                                <label class="col-sm-2 col-form-label">包名/命名空间</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control form-control-sm txt_app_package"
                                           readonly="readonly"
                                           placeholder="a.b.c"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label  class="col-sm-2 col-form-label">图片</label>
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
                                            <textarea class="form-control form-control-sm txt_app_memo" rows="2"
                                                      readonly="readonly "></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">更新时间</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control form-control-sm txt_app_ctime"
                                           readonly="readonly"/>
                                </div>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control form-control-sm txt_app_utime"
                                           readonly="readonly"/>
                                </div>
                            </div>


                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">

                        </div>

                    </div>
                </form>


            </div>
            <!-- /.col-md-6 -->

        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</session>