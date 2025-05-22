<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title><?=$_ENV['APP_NAME']?></title>
        <meta name="description" content="Sistema de gestão escolar" />
        <meta name="author" content="Mauricio Macedo" />
        <meta property="og:url" content="https://www.geeduc.com.br"/>
        <meta property="og:title" content="Mauricio Macedo - Sistemas de Gestão Escolar"/>
        <link rel="shortcut icon" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/assets/images/ico-geeduc.png"/>
            <!-- *************
                ************ CSS Files *************
            ************* -->
        <!-- Icomoon Font Icons css -->
        <link rel="stylesheet" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/assets/fonts/icomoon/style.css"/>

        <!-- Main CSS -->
        <link rel="stylesheet" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/assets/css/main.min.css" />
        <link rel="stylesheet" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/css/app.css" />
        <!-- *************
                ************ Vendor Css Files *************
            ************ -->

        <!-- Scrollbar CSS -->
        <link rel="stylesheet" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/assets/vendor/overlay-scroll/OverlayScrollbars.min.css" />

        <!-- Include stylesheet -->
        <!-- <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" /> -->

        <!-- Include Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <!-- Dropzone CSS -->
        <link rel="stylesheet" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/assets/vendor/dropzone/dropzone.min.css" />
        <!-- Date Range CSS -->
        <link rel="stylesheet" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/assets/vendor/daterange/daterange.css" />
    </head>
    <body>

        <?php 
            require_once('app-header.php');
            require_once('navbar.php');
        ?>

        <div class="app-body">
            <div class="container">
    <?php
        echo alertaBootstrap();
    ?>
    
