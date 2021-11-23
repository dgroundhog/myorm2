<?php echo '<script id="tpl_db_list" type="text/x-jsmart-tmpl">' ?>
    {foreach $db_list as $i => $db}

    <tr>
        <td>{$db.utime}</td>
        <td>{$db.driver}</td>
        <td>{$db.source}</td>
        <td>{$db.host}</td>
        <td>{$db.port}</td>
        <td>{$db.database}</td>
        <td>{$db.user}</td>
        <td>{$db.password}</td>
        <td>{$db.charset}</td>
        <td>{$db.uri}</td>

        <td class="text-right py-0 align-middle">
            <div class="btn-group btn-group-sm">
                <a href="###" class="btn btn-info"
                   onclick="javascript:App.dt.project.dbEdit('{$db.uuid}');"
                ><i class="fas fa-edit"></i> 编辑</a>
                <a href="###" class="btn btn-danger"
                   onclick="javascript:App.dt.project.dbDrop('{$db.uuid}');"
                ><i class="fas fa-trash"></i> 删除</a>
            </div>
        </td>
    </tr>

    {/foreach}
<?php echo '</script>'; ?>