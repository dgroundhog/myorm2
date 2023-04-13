<?php echo '<script id="tpl_model_where" type="text/x-jsmart-tmpl">' ?>
    {if $where0 && $where0.uuid}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                {if $where0.type eq 'AND'}
                <img src="img/and-gate.png" style="height: 24px;width: 24px"/>
                与 {else}
                <img src="img/or-gate.png" style="height: 24px;width: 24px"/>
                或 {/if}
                组合</h3>
            <div class="card-tools">

                <button type="button" class="btn btn-tool "
                        onclick="App.dt.project.modelFunCondEdit('','0','');"
                >
                    <i class="fas fa-plus-circle"></i> 条件
                </button>
                <button type="button" class="btn btn-tool "
                        onclick="App.dt.project.modelFunCondEdit('AND');"
                >
                    <i class="fas fa-plus-circle"></i>
                    <img src="img/and-gate.png" style="height: 24px;width: 24px"/>
                    [与]子嵌套
                </button>
                <button type="button" class="btn btn-tool "
                        onclick="App.dt.project.modelFunCondEdit('OR');"
                >
                    <i class="fas fa-plus-circle"></i>
                    <img src="img/or-gate.png" style="height: 24px;width: 24px"/>
                    [或]子嵌套
                </button>

                <button type="button" class="btn btn-tool "
                        onclick="App.dt.project.modelFunWhereDrop();"
                >
                    <i class="fas fa-minus-circle"></i> 移除
                </button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body p-1">

            <div class="card">
                <div class="card-body p-1">
                    <table class="table table-striped table-sm">
                        <tbody id="cond_sort_0">
                        {foreach $where0.cond_list as $i => $cond0}
                        {assign var="cond_field" value=$cond0.field}
                        {if $cond0.is_sub_where eq 0}
                        <tr class="cond_row" title="{$cond0.uuid}">
                            <td style="cursor: move;">
                                <i class="fa fa-sort"></i>
                                {$cond0.position}
                            </td>
                            <td>
                                {foreach $model_field_list as $ii => $iff4}
                                {if $cond_field eq $iff4.uuid} {$iff4.name}{/if}
                                {/foreach}
                            </td>
                            <td>{$cond0.type}</td>
                            <td>{$cond0.v1_type}</td>
                            <td>{$cond0.v1}</td>
                            <td>{$cond0.v2_type}</td>
                            <td>{$cond0.v2}</td>
                            <td class="text-right py-0 align-middle">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-info btn-sm"
                                            onclick="App.dt.project.modelFunCondEdit('{$where0.uuid}','{$cond0.uuid}');"
                                    ><i class="fas fa-edit"></i> 改
                                    </button>


                                    <div class="btn-group">
                                        <button type="button" class="btn btn-danger dropdown-toggle dropdown-icon"
                                                data-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-trash"></i> 删
                                        </button>
                                        <div class="dropdown-menu" style="">
                                            <a class="dropdown-item" href="###">-</a>
                                            <a class="dropdown-item" href="###">--</a>
                                            <a class="dropdown-item" href="###">---</a>
                                            <a class="dropdown-item"
                                               onclick="App.dt.project.modelFunCondDrop('{$where0.uuid}','{$cond0.uuid}');"
                                               href="####">确认删除</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        {else}
                        <tr class="cond_row" title="{$cond0.uuid}">
                            <td style="cursor: move;">
                                <i class="fa fa-sort"></i>
                                {$cond0.position}
                            </td>
                            <td colspan="7">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            {if $cond0.type_sub_where eq 'AND'}
                                            <img src="img/and-gate.png" style="height: 24px;width: 24px"/>
                                            与 {else}
                                            <img src="img/or-gate.png" style="height: 24px;width: 24px"/>
                                            或 {/if} 子嵌套组合</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool "
                                                    onclick="App.dt.project.modelFunCondEdit('{$cond0.uuid}','1','');"
                                            >
                                                <i class="fas fa-plus-circle"></i> 增加子条件
                                            </button>

                                            <button type="button" class="btn btn-tool "
                                                    onclick="App.dt.project.modelFunCondDrop('{$cond0.uuid}','0');"
                                            >
                                                <i class="fas fa-minus-circle"></i> 移除
                                            </button>
                                        </div>
                                        <!-- /.card-tools -->
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body p-1" id="cond_sort_1">

                                        <div class="card">
                                            <div class="card-body p-1">
                                                <table class="table table-striped table-sm">
                                                    {assign var="sub_cond_list" value=$cond0.cond_list}
                                                    {foreach $sub_cond_list as $ii => $cond1}
                                                    {assign var="cond_field" value=$cond1.field}
                                                    <tr>
                                                        <td>
                                                            {foreach $model_field_list as $ii => $iff4}
                                                            {if $cond_field eq $iff4.uuid} {$iff4.name}{/if}
                                                            {/foreach}
                                                        </td>
                                                        <td>{$cond1.type}</td>
                                                        <td>{$cond1.v1_type}</td>
                                                        <td>{$cond1.v1}</td>
                                                        <td>{$cond1.v2_type}</td>
                                                        <td>{$cond1.v2}</td>

                                                        <td class="text-right py-0 align-middle">
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                        onclick="App.dt.project.modelFunCondEdit('{$cond0.uuid}','1','{$cond1.uuid}');"
                                                                ><i class="fas fa-edit"></i> 改
                                                                </button>


                                                                <div class="btn-group">
                                                                    <button type="button"
                                                                            class="btn btn-danger btn-sm dropdown-toggle dropdown-icon"
                                                                            data-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                        <i class="fas fa-trash"></i> 删
                                                                    </button>
                                                                    <div class="dropdown-menu" style="">
                                                                        <a class="dropdown-item" href="###">-</a>
                                                                        <a class="dropdown-item" href="###">--</a>
                                                                        <a class="dropdown-item" href="###">---</a>
                                                                        <a class="dropdown-item"
                                                                           onclick="App.dt.project.modelFunCondDrop('{$cond0.uuid}','1','{$cond1.uuid}');"
                                                                           href="####">确认删除</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    {/foreach}
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <!-- /.card-footer -->
                                </div>
                            </td>
                        </tr>
                        {/if}
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
        <!-- /.card-footer -->
    </div>
    <!-- /.card -->
    {/if}
<?php echo '</script>'; ?>