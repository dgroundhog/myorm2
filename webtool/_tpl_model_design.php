<?php echo '<script id="tpl_model_design" type="text/x-jsmart-tmpl">' ?>
    {assign var="model_total" value=$model_list|count}
    {assign var="model_inc" value=1}
    {foreach $model_list as $i => $model}
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <a name="model_{$model.uuid}">
                                <i class="fa fa-fw  fa-{$model.fa_icon}"></i>     ({$model_inc++} / {$model_total}) 模型设计 <small>{$model.name}
                                    - {$model.title}</small>
                            </a>
                        </h3>
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
                        <h3 class="card-title">1、模型字段配置 <small>{$model.name}
                                - {$model.title}</small></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool"
                                    onclick="javascript:App.dt.project.modelImportGlobalField('{$model.uuid}');"
                            >
                                <i class="fas fa-plus-circle"></i> 导入全局字段
                            </button>

                            <button type="button" class="btn btn-tool"
                                    onclick="javascript:App.dt.project.fieldEdit('','{$model.uuid}');"
                            >
                                <i class="fas fa-plus"></i> 增加私有字段
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-sm ">
                            <thead>
                            <tr>
                                <th>排序</th>
                                <th>全局</th>
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
                            <tbody class="m_sort_field_list" title="{$model.uuid}">
                            {foreach $model.field_list as $i => $field}
                            <tr class="field_row" title="{$field.uuid}">
                                <td style="cursor: move;">
                                    <i class="fa fa-sort"></i>
                                    {$field.position}
                                </td>
                                <td>{if $field.is_global==1}全局{else}私有{/if}</td>

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
                                        {if $field.is_global !=1}
                                        <a href="###" class="btn btn-info"
                                           onclick="javascript:App.dt.project.fieldEdit('{$field.uuid}','{$model.uuid}');"
                                        ><i class="fas fa-edit"></i> 改</a>
                                        {/if}

                                        <a href="###" class="btn btn-success"
                                           onclick="javascript:App.dt.project.fieldCopy('{$field.uuid}','{$model.uuid}');"
                                        ><i class="fas fa-copy"></i> 复</a>

                                        <a href="###" class="btn btn-danger"
                                           onclick="javascript:App.dt.project.fieldDrop('{$field.uuid}','{$model.uuid}');"
                                        ><i class="fas fa-trash"></i> 删</a>
                                    </div>
                                </td>
                            </tr>

                            {/foreach}
                            </tbody>
                        </table>
                    </div>

                    <hr class="mb-2">

                    <div class="card-header">
                        <h3 class="card-title">2-索引列表 <small>{$model.name}
                                - {$model.title}</small></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool "
                                    onclick="javascript:App.dt.project.modelIndexEdit('{$model.uuid}','');"
                            >
                                <i class="fas fa-plus-circle"></i> 增加普通/唯一索引
                            </button>

                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-sm ">
                            <thead>
                            <tr>
                                <th>序</th>
                                <th>类型</th>
                                <th>名称</th>
                                <th>字段列表</th>
                                <th>备注</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody class="m_sort_index_list" title="{$model.uuid}">
                            {foreach $model.idx_list as $i => $idx}

                            <tr class="idx_row" title="{$idx.uuid}">
                                <td style="cursor: move;">
                                    <i class="fa fa-sort"></i>
                                    {$idx.position}
                                </td>
                                <td>{$idx.type}</td>
                                <td>{$idx.name}</td>
                                <td>
                                    {foreach $idx.field_list as $ii => $iff}
                                    <strong>{$iff.name}, </strong>
                                    {/foreach}

                                </td>
                                <td>{$idx.memo}</td>
                                <td class="text-right py-0 align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <a href="###" class="btn btn-info"
                                           onclick="javascript:App.dt.project.modelIndexEdit('{$model.uuid}','{$idx.uuid}');"
                                        ><i class="fas fa-edit"></i> 改</a>
                                        <a href="###" class="btn btn-danger"
                                           onclick="javascript:App.dt.project.modelIndexDrop('{$model.uuid}','{$idx.uuid}');"
                                        ><i class="fas fa-trash"></i> 删</a>
                                    </div>
                                </td>
                            </tr>

                            {/foreach}
                            </tbody>
                        </table>
                    </div>


                    <hr class="mb-2">

                    <div class="card-header">
                        <h3 class="card-title">3-方法列表 <small>{$model.name}
                                - {$model.title}</small></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool "
                                    onclick="javascript:App.dt.project.modelFunEdit('{$model.uuid}','');"
                            >
                                <i class="fas fa-plus-circle"></i> 增加一个函数
                            </button>

                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-sm ">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>类型</th>
                                <th>名称</th>
                                <th>操作字段</th>
                                <th>聚合字段</th>
                                <th>分组键</th>
                                <th>条件列表</th>
                                <th>模糊查询</th>
                                <th>分页</th>
                                <th>页大小</th>
                                <th>排序键</th>
                                <th>排序方向</th>
                                <th>备注</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody class="m_sort_fun_list" title="{$model.uuid}">
                            {assign var="model_field_list" value=$model.field_list}
                            {foreach $model.fun_list as $i => $fun}

                            <tr class="fun_row" title="{$fun.uuid}">
                                <td style="cursor: move;">
                                    <i class="fa fa-sort"></i>
                                    {$fun.position}
                                </td>
                                <td>{$fun.type}</td>
                                <td>{$fun.name}</td>

                                <td>
                                    {if $fun.type=='DELETE'}
                                    忽略
                                    {elseif $fun.all_field==1}
                                    (全部)*
                                    {else}
                                    {foreach $fun.field_list as $ii => $iff}
                                    <strong>{$iff.name}, </strong><br/>
                                    {/foreach}
                                    {/if}
                                </td>


                                <td>
                                    {if $fun.group_field eq '@@'}
                                    无
                                    {else}
                                    {assign var="fun_group_field" value=$fun.group_field}
                                    {foreach $model_field_list as $ii => $iff2}
                                    {if $fun_group_field eq $iff2.uuid}
                                    <strong>{$iff2.name}</strong>
                                    {/if}
                                    {/foreach}
                                    {/if}
                                </td>

                                <td>
                                    {if $fun.group_by=='-1'}
                                    无
                                    {else}
                                    {assign var="fun_group_by" value=$fun.group_by}
                                    {foreach $model_field_list as $ii => $iff3}
                                    {if $fun_group_by eq $iff3.uuid}
                                    <strong>{$iff3.name}</strong>
                                    {/if}
                                    {/foreach}
                                    {/if}
                                </td>

                                <td>
                                    {if $fun.where && $fun.where.uuid}
                                    {assign var="where0" value=$fun.where}
                                    <div class="card">
                                        <div class="card-header">
                                            <span>-
                                                {if $where0.type eq 'OR'}
                                                <img src="img/or-gate.png" style="height: 24px;width: 24px"/> 或
                                                {else}
                                                <img src="img/and-gate.png" style="height: 24px;width: 24px"/> 与
                                                {/if}
                                                组合
                                             {$where0.cond_list|count}
                                            </span>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body p-0">
                                            <div class="card" style="margin-bottom:0px">
                                                <div class="card-body p-1">
                                                    <table class="table table-striped table-sm">
                                                        {foreach $where0.cond_list as $i => $cond}
                                                        {assign var="cond_field" value=$cond.field}
                                                        <tr>
                                                            <td>
                                                                {if $cond_field eq '##'}
                                                                聚合新健
                                                                {else}
                                                                {foreach $model_field_list as $ii => $iff4}
                                                                {if $cond_field eq $iff4.uuid} {$iff4.name}{/if}
                                                                {/foreach}
                                                                {/if}
                                                            </td>
                                                            <td>{$cond.type}</td>
                                                            <td>{$cond.v1_type}</td>
                                                            <td>{$cond.v1}</td>
                                                            <td>{$cond.v2_type}</td>
                                                            <td>{$cond.v2}</td>
                                                        </tr>
                                                        {/foreach}
                                                        {foreach $where0.where_list as $i => $where1}
                                                        <tr>
                                                            <td class="p-1" colspan="6">
                                                                <div class="card" style="margin-bottom:0px">
                                                                    <div class="card-header">
                                                                        <span>- -
                                                                            {if $where1.type eq 'AND'}
                                                                            <img src="img/and-gate.png" style="height: 24px;width: 24px"/> 与
                                                                            {else}
                                                                            <img src="img/or-gate.png" style="height: 24px;width: 24px"/>或
                                                                            {/if}
                                                                            子嵌套组合 {$where1.cond_list|count}
                                                                        </span>

                                                                        <!-- /.card-tools -->
                                                                    </div>
                                                                    <!-- /.card-header -->
                                                                    <div class="card-body p-1">
                                                                        <div class="card" style="margin-bottom:0px">
                                                                            <div class="card-body p-1">
                                                                                <table class="table table-striped table-sm">
                                                                                    {assign var="sub_cond_list" value=$where1.cond_list}
                                                                                    {foreach $sub_cond_list as $ii => $cond}
                                                                                    {assign var="cond_field" value=$cond.field}
                                                                                    <tr>
                                                                                        <td>
                                                                                            {if $cond_field eq '##'}
                                                                                            聚合新健
                                                                                            {else}
                                                                                                {foreach $model_field_list  as $ii => $iff4}
                                                                                                    {if $cond_field eq $iff4.uuid}
                                                                                                        {$iff4.name}
                                                                                                    {/if}
                                                                                                {/foreach}
                                                                                            {/if}
                                                                                        </td>
                                                                                        <td>{$cond.type}</td>
                                                                                        <td>{$cond.v1_type}</td>
                                                                                        <td>{$cond.v1}</td>
                                                                                        <td>{$cond.v2_type}</td>
                                                                                        <td>{$cond.v2}</td>
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
                                    {/if}


                                </td>

                                <td>{$fun.has_kw}</td>
                                <td>{$fun.pager_enable}</td>
                                <td>{$fun.pager_size}</td>

                                <td>
                                    {if $fun.order_enable=='0'}
                                    不排序
                                    {else}
                                    {assign var="fun_order_by" value=$fun.order_by}
                                    {if $fun_order_by eq '@@'}
                                    外部输入
                                    {else if $fun_order_by eq '##'}
                                    聚合的结果健
                                    {else}
                                    {foreach $model_field_list as $ii => $iff4}
                                    {if $fun_order_by eq $iff4.uuid} {$iff4.name}{/if}
                                    {/foreach}
                                    {/if}
                                    {/if}
                                </td>
                                <td>
                                    {if $fun.order_dir=='@@'}
                                    外部输入
                                    {else}
                                    {$fun.order_dir}
                                    {/if}
                                </td>


                                <td>{$fun.memo}</td>

                                <td class="text-right py-0 align-middle">
                                    <div class="btn-group btn-group-sm">

                                        <a href="###" class="btn btn-info"
                                           onclick="javascript:App.dt.project.modelFunEdit('{$model.uuid}','{$fun.uuid}');"
                                        ><i class="fas fa-edit"></i> 改</a>

                                        <a href="###" class="btn btn-success"
                                           onclick="javascript:App.dt.project.modelFunCopy('{$model.uuid}','{$fun.uuid}');"
                                        ><i class="fas fa-copy"></i> 复</a>

                                        <a href="###" class="btn btn-danger"
                                           onclick="javascript:App.dt.project.modelFunDrop('{$model.uuid}','{$fun.uuid}');"
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