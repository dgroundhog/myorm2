<?php echo '<script id="tpl_ecode_list" type="text/x-jsmart-tmpl">' ?>
    {foreach $ecode_list as $code => $desc}
    <tr>


        <td>{$code}</td>
        <td>{$desc}</td>

        <td class="text-right py-0 align-middle">
            <div class="btn-group btn-group-sm">
                <a href="###" class="btn btn-info"
                   onclick="javascript:App.dt.project.ecodeEdit('{$code}');"
                ><i class="fas fa-edit"></i> 编辑</a>
                <a href="###" class="btn btn-danger"
                   onclick="javascript:App.dt.project.ecodeDrop('{$code}');"
                ><i class="fas fa-trash"></i> 删除</a>
            </div>
        </td>
    </tr>

    {/foreach}
<?php echo '</script>'; ?>