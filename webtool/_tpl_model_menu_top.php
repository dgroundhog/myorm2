<?php echo '<script id="tpl_model_menu_top" type="text/x-jsmart-tmpl">' ?>
    <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>


    <li class="nav-item d-none d-sm-inline-block">
        <a href="#basic" class="nav-link">基本信息</a>
    </li>

    <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="#sess_conf">架构配置</a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="#sess_db">数据库配置</a>
    </li>

    <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="#sess_g_fields">公共字段</a>
    </li>

    <li class="nav-item d-none d-sm-inline-block">
        <a href="#session_model" class="nav-link">模型设计</a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">接口和测试 </a>
    </li>
　
    　　　

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
            模型列表
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            {foreach $model_list as $i => $model}
            <a class="dropdown-item" href="#model_{$model.uuid}" >{$model.name}|{$model.title}</a>
            {/foreach}
        </div>
    </li>

<?php echo '</script>'; ?>