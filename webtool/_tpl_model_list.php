<?php echo '<script id="tpl_model_list" type="text/x-jsmart-tmpl">' ?>
    {foreach $model_list as $i => $model}

    <tr class="model_row" title="{$model.uuid}">
        <td style="cursor: move;">
            <i class="fa fa-sort"></i>
            {$model.position}
        </td>
        <td>{$model.name}</td>
        <td>{$model.title}</td>
        <td>{$model.table_name}</td>
        <td>{$model.primary_key}</td>
        <td>{$model.has_ui}</td>
        <td><i class="fa fa-fw  fa-{$model.fa_icon}"></i> ｜ {$model.fa_icon}</td>
        <td>{$model.field_list|count}</td>
        <td>{$model.idx_list|count}</td>
        <td>{$model.fun_list|count}</td>
        <td>{$model.memo}</td>

        <td class="text-right py-0 align-middle">
            <div class="btn-group btn-group-sm">
                <a href="###" class="btn btn-info"
                   onclick="javascript:App.dt.project.modelEdit('{$model.uuid}');"
                ><i class="fas fa-edit"></i> 编辑</a>
                <a href="###" class="btn btn-success"
                   onclick="javascript:App.dt.project.modelCopy('{$model.uuid}');"
                ><i class="fas fa-copy"></i> 复制</a>
                <a href="###" class="btn btn-danger"
                   onclick="javascript:App.dt.project.modelDrop('{$model.uuid}');"
                ><i class="fas fa-trash"></i> 删除</a>
            </div>
        </td>
    </tr>
    {/foreach}
<?php echo '</script>'; ?>