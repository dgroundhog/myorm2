<?php echo '<script id="tpl_model_design" type="text/x-jsmart-tmpl">' ?>

    {foreach $model_list as $i => $model}
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title">--模型设计 <small>{$model.name} - {$model.title}</small></h3>
                            <div class="card-tools">


                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>

                                <button type="button" class="btn btn-tool" data-card-widget="maximize"><i
                                            class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-header">
                            <h3 class="card-title">模型字段配置 <small></small></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" id="btn_edit_field2">
                                    <i class="fas fa-plus-circle"></i> 增加一个全局字段
                                </button>

                                <button type="button" class="btn btn-tool" id="btn_edit_field3">
                                    <i class="fas fa-plus"></i> 增加一个私有字段
                                </button>


                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>创建时间</th>
                                    <th>全局变量</th>
                                    <th>排序</th>
                                    <th>字段</th>
                                    <th>字段名</th>
                                    <th>类型</th>
                                    <th>大小</th>
                                    <th>自增</th>
                                    <th>默认值</th>
                                    <th>非空</th>
                                    <th>验证器</th>
                                    <th>正则</th>
                                    <th>输入方法</th>
                                    <th>枚举值</th>
                                    <th>提示</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach $field_list as $i => $field}

                                <tr>
                                    <td>{$field.ctime}</td>
                                    <td>{$field.is_global}</td>
                                    <td>{$field.position}</td>
                                    <td>{$field.name}</td>
                                    <td>{$field.title}</td>
                                    <td>{$field.type}</td>
                                    <td>{$field.size}</td>
                                    <td>{$field.auto_increment}</td>
                                    <td>{$field.default_value}</td>
                                    <td>{$field.required}</td>
                                    <td>{$field.filter}</td>
                                    <td>{$field.regexp}</td>
                                    <td>{$field.input_by}</td>
                                    <td>{$field.input_hash}</td>
                                    <td>{$field.memo}</td>

                                    <td class="text-right py-0 align-middle">
                                        <div class="btn-group btn-group-sm">
                                            <a href="###" class="btn btn-info"
                                               onclick="javascript:App.dt.project.fieldEdit('{$field.uuid}');"
                                            ><i class="fas fa-edit"></i> 改</a>
                                            <a href="###" class="btn btn-danger"
                                               onclick="javascript:App.dt.project.fieldDrop('{$field.uuid}');"
                                            ><i class="fas fa-trash"></i> 删</a>
                                        </div>
                                    </td>
                                </tr>

                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>


            </div>
        </div>

    {/foreach}

<?php echo '</script>'; ?>