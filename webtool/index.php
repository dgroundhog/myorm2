<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/ShangHai');
if (!defined("DS")) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined("WT_ROOT")) {
    define('WT_ROOT', realpath(dirname(__FILE__)));
}
define('MEM_DISK_SPEED_UP', "D:");
//可以删除掉
//if (!defined("MEM_DISK_SPEED_UP")) {
//    //SeasLog::setBasePath(WT_ROOT . DS . ".." . DS . "logs");
//    SeasLog::setBasePath("D:/xampp/apache/logs");
//}
//else{
//    SeasLog::setBasePath( MEM_DISK_SPEED_UP . DS ."logs");
//}
include_once(WT_ROOT . "/../core/Constant.php");
include_once(WT_ROOT . "/../core/MyProject.php");
include_once(WT_ROOT . "/../core/MyApp.php");
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Starter</title>
    <link rel="icon" href="./img/RC30.gif" type="image/x-icon"/>
    <link rel="shortcut icon" href="./img/RC30.gif" type="image/x-icon"/>
    <!-- jquery-ui -->
    <link rel="stylesheet" href="./vendor/jquery-ui/jquery-ui.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="./vendor/local-google-font/local.google.fonts.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="./vendor/fontawesome-free/css/all.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="./vendor/toastr/toastr.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="./vendor/select2/css/select2.min.css">
    <link rel="stylesheet" href="./vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Bootstrap-Fileinput -->
    <link rel="stylesheet" href="./vendor/bootstrap-fileinput/css/fileinput.min.css">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="./vendor/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="./vendor/adminlte-3.2.0/css/adminlte.min.css">
</head> 
<body class="hold-transition sidebar-mini layout-navbar-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <?php include("./_1_navbar.php"); ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include("./_2_sidebar.php"); ?>
    <!-- /Main Sidebar Container -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <?php include("./_3_content_header.php"); ?>
        <!-- /.content-header -->
        <!--1.1-1.2 基本信息 -->
        <?php include("./_4_session_basic.php"); ?>

        <!--1.3 应用基本配置 -->
        <?php include("./_5_session_arch.php"); ?>

        <!--1.4 数据库基本配置 -->
        <?php include("./_6_session_db.php"); ?>

        <!--1.5 全局字段 -->
        <?php include("./_7_session_field.php"); ?>

        <!--1.6 模型 -->
        <?php include("./_8_session_model.php"); ?>

        <!--1.6-x 模型设计 -->
        <session class="content" id="model_design"></session>

    </div><!-- /.container-fluid -->
    <!-- /.content-wrapper -->

    <!-- back-to-top-->
    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
    </a>
    <!-- /back-to-top-->
    <!-- Control Sidebar -->
    <?php include("./_9_sidebar_right.php"); ?>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- To the right -->
        <div class="float-right d-none d-sm-inline">
            Anything you want
        </div>
        <!-- Default to the left -->
        <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
    </footer>

    <?php include("./_4_1_modal_edit_app_info.php"); ?>
    <?php include("./_5_1_modal_edit_app_arch.php"); ?>
    <?php include("./_6_1_modal_edit_app_db.php"); ?>
    <?php include("./_7_1_modal_edit_field.php"); ?>
    <?php include("./_8_1_modal_edit_model.php"); ?>
    <?php include("./_8_2_modal_edit_index.php"); ?>
    <?php include("./_8_3_modal_import_global_field.php"); ?>
    <?php include("./_8_4_modal_edit_fun.php"); ?>

</div>
<!-- ./wrapper -->

<!-- tmpl -->
<?php include("./_tpl_project_menu_list.php"); ?>
<?php include("./_tpl_arch_list.php"); ?>
<?php include("./_tpl_db_list.php"); ?>
<?php include("./_tpl_field_list.php"); ?>
<?php include("./_tpl_model_list.php"); ?>
<?php include("./_tpl_model_design.php"); ?>
<?php include("./_tpl_model_menu.php"); ?>
<?php include("./_tpl_model_menu_top.php"); ?>
<?php include("./_tpl_model_where.php"); ?>
<!-- /tmpl -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="./vendor/jquery/jquery.min.js"></script>
<!-- jquery-ui -->
<script src="./vendor/jquery-ui/jquery-ui.min.js"></script>
<!-- Bootstrap 4 -->
<script src="./vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Toastr -->
<script src="./vendor/toastr/toastr.min.js"></script>
<!-- Select2 -->
<script src="./vendor/select2/js/select2.full.min.js"></script>
<!-- bootbox -->
<script src="./vendor/bootbox-5.5.2/js/bootbox.all.min.js"></script>
<!-- Bootstrap Switch -->
<script src="./vendor/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- Jsmart -->
<script src="./vendor/jsmart-4.0.0/jsmart.min.js"></script>
<!-- bootstrap-fileinput -->
<script src="./vendor/bootstrap-fileinput/js/fileinput.min.js"></script>
<script src="./vendor/bootstrap-fileinput/js/locales/zh.js"></script>
<script src="./vendor/bootstrap-fileinput/themes/fas/theme.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="./vendor/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- AdminLTE App -->
<script src="./vendor/adminlte-3.2.0/js/adminlte.min.js"></script>


<script src="./js/app.js"></script>
<script src="js/struct.js?_t=<?php echo time();?>"></script>
<script src="./js/dt.js?_t=<?php echo time();?>"></script>

<script type="text/javascript">

    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2();
        //
        $("input[data-bootstrap-switch]").each(function () {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });
        //
        $('.duallistbox').bootstrapDualListbox({
            nonSelectedListLabel: '可选字段',
            selectedListLabel: '已选字段',
            filterTextClear: '展示所有',
            filterPlaceHolder: '过滤搜索',
            moveSelectedLabel: "添加",
            moveAllLabel: '添加所有',
            removeSelectedLabel: "移除",
            removeAllLabel: '移除所有',
            infoText: '共{0}个',
            infoTextFiltered: '搜索到{0}个 ,共{1}个',
            infoTextEmpty: '列表为空',
            selectorMinimalHeight: 150,
            moveOnSelect: false,
        });
        //
        App.dt.init();


    });

</script>

</body>
</html>
