<?php echo '<script id="tpl_model_menu" type="text/x-jsmart-tmpl">' ?>
    {foreach $model_list as $i => $model}

    <div class="d-flex">
        <a  class="btn btn-danger btn-block" href="#model_{$model.uuid}">
            <i class="fa fa-{$model.fa_icon}"></i>
            {$model.name} ｜ {$model.title} </a>
    </div>



    {/foreach}
<?php echo '</script>'; ?>