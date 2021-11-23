<?php echo '<script id="tpl_project_menu_list" type="text/x-jsmart-tmpl">' ?>
    {foreach $projects as $i => $project}
    <li class="nav-item menu_row_project" id="menu_row_project_{$project.name}">
        <a href="#" class="nav-link menu_row_project_a ">
            <i class="nav-icon fas fa-cogs"></i>
            <p>
                {$project.name}
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            {foreach $project.version_list as $j => $version}
            <li class="nav-item menu_row_app" id="menu_row_app_{$version.uuid}">
                <a href="#" class="nav-link menu_row_app_a"
                   onclick="javascript:App.dt.project.loadProject('{$project.name}','{$version.uuid}');">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{$version.name}</p>
                </a>
            </li>
            {/foreach}
        </ul>
    </li>
    {/foreach}
<?php echo '</script>'; ?>