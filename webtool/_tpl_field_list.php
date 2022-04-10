<?php echo '<script id="tpl_field_list" type="text/x-jsmart-tmpl">' ?>
    {foreach $field_list as $i => $field}
    <tr class="field_row" title="{$field.uuid}">
        <td style="cursor: move;">
            <i class="fa fa-sort" ></i>
            {$field.position}
        </td>

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
                <a href="###" class="btn btn-success"
                   onclick="javascript:App.dt.project.fieldCopy('{$field.uuid}');"
                ><i class="fas fa-copy"></i> 复</a>
                <a href="###" class="btn btn-danger"
                   onclick="javascript:App.dt.project.fieldDrop('{$field.uuid}');"
                ><i class="fas fa-trash"></i> 删</a>
            </div>
        </td>
    </tr>

    {/foreach}
<?php echo '</script>'; ?>