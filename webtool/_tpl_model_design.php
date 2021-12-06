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
                                <i class="fa fa-cubes"></i> ({$model_inc++} / {$model_total}) 模型设计 <small>{$model.name} - {$model.title}</small>
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
                        <h3 class="card-title">模型字段配置 <small></small></h3>
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
                                <th>创建时间</th>
                                <th>全局</th>
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
                            <tbody class="m_sort_field_list" title="{$model.uuid}">
                            {foreach $model.field_list as $i => $field}
                            <tr class="field_row" title="{$field.uuid}">
                                <td style="cursor: move;">
                                    <i class="fa fa-sort" ></i>
                                    {$field.ctime}
                                </td>
                                <td>{if $field.is_global==1}全局{else}私有{/if}</td>
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
                                        {if $field.is_global !=1}
                                        <a href="###" class="btn btn-info"
                                           onclick="javascript:App.dt.project.fieldEdit('{$field.uuid}','{$model.uuid}');"
                                        ><i class="fas fa-edit"></i> 改</a>
                                        {/if}

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
                        <h3 class="card-title">索引列表 <small></small></h3>
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
                                <th>创建时间</th>
                                <th>类型</th>
                                <th>名称</th>
                                <th>字段列表</th>
                                <th>备注</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach $model.idx_list as $i => $idx}

                            <tr>
                                <td>{$idx.ctime}</td>
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
                        <h3 class="card-title">CURD列表 <small></small></h3>
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
                                <th>创建时间</th>
                                <th>类型</th>
                                <th>名称</th>
                                <th>操作字段</th>
                                <th>聚合字段</th>
                                <th>分组键</th>
                                <th>条件列表</th>
                                <th>排序键</th>
                                <th>排序方向</th>
                                <th>备注</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            {assign var="model_field_list" value=$model.field_list}
                            {foreach $model.fun_list as $i => $fun}

                            <tr>
                                <td>{$fun.ctime}</td>
                                <td>{$fun.type}</td>
                                <td>{$fun.name}</td>

                                <td>
                                    {if $fun.type=='DELETE'}
                                    全局
                                    {elseif $fun.type=='ADD' && $fun.return_all==1}
                                    * (全部)
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
                                    WHERE TODO
                                </td>

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


                                <td>{$idx.memo}</td>

                                <td class="text-right py-0 align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <a href="###" class="btn btn-info"
                                           onclick="javascript:App.dt.project.modelFunEdit('{$model.uuid}','{$fun.uuid}');"
                                        ><i class="fas fa-edit"></i> 改</a>
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