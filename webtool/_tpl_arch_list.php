<?php echo '<script id="tpl_arch_list" type="text/x-jsmart-tmpl">' ?>
    {foreach $arch_list as $i => $arch}

    <tr>
        <td
        title="创建({$arch.ctime})更新({$arch.utime})"
        >{$arch.name}</td>

        <td>{$arch.mvc}</td>
        <td>{$arch.ui}</td>
        <td>
            ({$arch.has_restful})
            {if $arch.has_restful==1}
            启用
            {else}
            无
            {/if}
        </td>
        <td>
            ({$arch.has_doc})
            {if $arch.has_doc==1}
            启用
            {else}
            无
            {/if}
        </td>

        <td>
            ({$arch.has_test})
            {if $arch.has_test==1}
            启用
            {else}
            无
            {/if}
        </td>
        <td class="text-right py-0 align-middle">
            <div class="btn-group btn-group-sm">
                <a href="###" class="btn btn-success"
                   onclick="javascript:App.dt.project.archCopy('{$arch.uuid}');"
                ><i class="fas fa-copy"></i> 复制</a>
                <a href="###" class="btn btn-info"
                   onclick="javascript:App.dt.project.archEdit('{$arch.uuid}');"
                ><i class="fas fa-edit"></i> 编辑</a>
                <a href="###" class="btn btn-danger"
                   onclick="javascript:App.dt.project.archDrop('{$arch.uuid}');"
                ><i class="fas fa-trash"></i> 删除</a>
            </div>
        </td>
    </tr>

    {/foreach}
<?php echo '</script>'; ?>