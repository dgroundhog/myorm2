<?php echo '<script id="tpl_conf_list" type="text/x-jsmart-tmpl">' ?>
    {foreach $conf_list as $i => $conf}

    <tr>
        <td>{$conf.utime}</td>
        <td>{$conf.mvc}</td>
        <td>{$conf.ui}</td>
        <td>
            ({$conf.has_restful})
            {if $conf.has_restful==1}
            启用
            {else}
            无
            {/if}
        </td>
        <td>
            ({$conf.has_doc})
            {if $conf.has_doc==1}
            启用
            {else}
            无
            {/if}
        </td>

        <td>
            ({$conf.has_test})
            {if $conf.has_test==1}
            启用
            {else}
            无
            {/if}
        </td>
        <td class="text-right py-0 align-middle">
            <div class="btn-group btn-group-sm">
                <a href="###" class="btn btn-info"
                   onclick="javascript:App.dt.project.confEdit('{$conf.uuid}');"
                ><i class="fas fa-edit"></i> 编辑</a>
                <a href="###" class="btn btn-danger"
                   onclick="javascript:App.dt.project.confDrop('{$conf.uuid}');"
                ><i class="fas fa-trash"></i> 删除</a>
            </div>
        </td>
    </tr>

    {/foreach}
<?php echo '</script>'; ?>