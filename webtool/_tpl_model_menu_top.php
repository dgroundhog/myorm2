<?php echo '<script id="tpl_model_menu_top" type="text/x-jsmart-tmpl">' ?>
    <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    {foreach $model_list as $i => $model}
    <li class="nav-item d-none d-sm-inline-block">
        <a href="#model_{$model.uuid}" class="nav-link">{$model.name}|{$model.title}</a>
    </li>
    {/foreach}
<?php echo '</script>'; ?>