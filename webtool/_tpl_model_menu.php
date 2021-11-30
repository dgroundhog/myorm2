<?php echo '<script id="tpl_model_menu" type="text/x-jsmart-tmpl">' ?>
    {foreach $model_list as $i => $model}
    <div class="d-flex mb-2 ">
        <a  class="btn  btn-info btn-block text-left" href="#model_{$model.uuid}">
            <i class="fa fa-{$model.fa_icon}"></i>
            {$model.name} ｜ {$model.title} </a>
    </div>
    {/foreach}
<?php echo '</script>'; ?>