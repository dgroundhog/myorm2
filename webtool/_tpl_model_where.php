<?php echo '<script id="tpl_model_where" type="text/x-jsmart-tmpl">' ?>
    {if $where0 && $where0.uuid}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa fa-tree"></i>
                {if $where0.type eq 'AND'} 与 {else} 或 {/if} 组合</h3>
            <div class="card-tools">

                <button type="button" class="btn btn-tool "
                        onclick="javascript:App.dt.project.modelFunCondEdit('{$where0.uuid}','');"
                >
                    <i class="fas fa-plus-circle"></i> 普通条件
                </button>
                <button type="button" class="btn btn-tool "
                        onclick="javascript:App.dt.project.modelFunWhereAdd('{$where0.uuid}','AND');"
                >
                    <i class="fas fa-plus-circle"></i> [与]子嵌套
                </button>
                <button type="button" class="btn btn-tool "
                        onclick="javascript:App.dt.project.modelFunWhereAdd('{$where0.uuid}','OR');"
                >
                    <i class="fas fa-plus-circle"></i> [或]子嵌套
                </button>

                <button type="button" class="btn btn-tool "
                        onclick="javascript:App.dt.project.modelFunWhereDrop('{$where0.uuid}');"
                >
                    <i class="fas fa-minus-circle"></i> 移除
                </button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body p-1" id="cond_sort_0">

            <div class="card">
                <div class="card-body p-1">
                    <table class="table table-striped table-sm">
                        {foreach $where0.cond_list as $i => $cond}
                        {assign var="cond_field" value=$cond.field}
                        <tr>
                            <td>
                                {foreach $model_field_list as $ii => $iff4}
                                {if $cond_field eq $iff4.uuid} {$iff4.name}{/if}
                                {/foreach}
                            </td>
                            <td>{$cond.type}</td>
                            <td>{$cond.v1_type}</td>
                            <td>{$cond.v1}</td>
                            <td>{$cond.v2_type}</td>
                            <td>{$cond.v2}</td>
                            <td class="text-right py-0 align-middle">
                                <div class="btn-group btn-group-sm">
                                    <button type="button"  class="btn btn-info btn-sm"
                                       onclick="javascript:App.dt.project.modelFunCondEdit('{$where0.uuid}','{$cond.uuid}');"
                                    ><i class="fas fa-edit"></i> 改</button>


                                    <div class="btn-group">
                                        <button type="button" class="btn btn-danger dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-trash"></i> 删
                                        </button>
                                        <div class="dropdown-menu" style="">
                                            <a class="dropdown-item" href="#">-</a>
                                            <a class="dropdown-item" href="#">--</a>
                                            <a class="dropdown-item" href="#">---</a>
                                            <a class="dropdown-item"
                                               onclick="javascript:App.dt.project.modelFunCondDrop('{$where0.uuid}','{$cond.uuid}');"
                                               href="####">确认删除</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        {/foreach}
                        {foreach $where0.where_list as $i => $where1}
                        <tr>
                            <td colspan="7">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fa fa-tree"></i>

                                            {if $where1.type eq 'AND'} 与 {else} 或 {/if} 子嵌套组合</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool "
                                                    onclick="javascript:App.dt.project.modelFunCondEdit('{$where1.uuid}','');"
                                            >
                                                <i class="fas fa-plus-circle"></i> 普通条件
                                            </button>


                                            <button type="button" class="btn btn-tool "
                                                    onclick="javascript:App.dt.project.modelFunWhereDrop('{$where1.uuid}');"
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
                                                    {assign var="sub_cond_list" value=$where1.cond_list}
                                                    {foreach $sub_cond_list as $ii => $cond}
                                                    {assign var="cond_field" value=$cond.field}
                                                    <tr>
                                                        <td>
                                                            {foreach $model_field_list as $ii => $iff4}
                                                            {if $cond_field eq $iff4.uuid} {$iff4.name}{/if}
                                                            {/foreach}
                                                        </td>
                                                        <td>{$cond.type}</td>
                                                        <td>{$cond.v1_type}</td>
                                                        <td>{$cond.v1}</td>
                                                        <td>{$cond.v2_type}</td>
                                                        <td>{$cond.v2}</td>

                                                        <td class="text-right py-0 align-middle">
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="button"  class="btn btn-info btn-sm"
                                                                   onclick="javascript:App.dt.project.modelFunCondEdit('{$where1.uuid}','{$cond.uuid}');"
                                                                ><i class="fas fa-edit"></i> 改</button>


                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                                                                        <i class="fas fa-trash"></i> 删
                                                                    </button>
                                                                    <div class="dropdown-menu" style="">
                                                                        <a class="dropdown-item" href="#">-</a>
                                                                        <a class="dropdown-item" href="#">--</a>
                                                                        <a class="dropdown-item" href="#">---</a>
                                                                        <a class="dropdown-item"
                                                                           onclick="javascript:App.dt.project.modelFunCondDrop('{$where1.uuid}','{$cond.uuid}');"
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
                        {/foreach}
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