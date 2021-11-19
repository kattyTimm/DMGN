<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">

        <title>ИС ДМГН</title>

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/favicon_180.png">
        <link rel="icon" type="image/png" href="img/favicon/favicon_32.png" sizes="32x32">
        <link rel="shortcut icon" href="img/favicon/favicon_32.ico">
        <meta name="msapplication-TileImage" content="img/favicon/favicon_144.png">
        <meta name="msapplication-TileColor" content="#FFFFFF">
        
        <!-- CSS -->
        <link href="/site_lib/lib/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        
        <link href="/pktbbase/css/bootstrap.min.css" rel="stylesheet">
        <link href="/pktbbase/css/mprogress-gr.css" rel="stylesheet">
        <link href="/pktbbase/css/colorbox.css" rel="stylesheet"/>
        <link href="/pktbbase/css/bootstrap4-toggle.min.css" rel="stylesheet">
        <link href="/pktbbase/css/bootstrap-datepicker3.css" rel="stylesheet">

        <link href="/pktbbase/css/_indx_root.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_comm.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_srch.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_fdb.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_fdbadm.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_help.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_bbmon.css" rel="stylesheet"/>
        
        <link href="/pktbbase/css/_indx_sout.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_drag.css" rel="stylesheet"/>
        
        <link href="css/index.css" rel="stylesheet">
    </head>
    <body spellcheck="false" ondrop="return false;" ondragover="return false;">
        
        <?php
        if (strlen(trim(session_id())) == 0) session_start();
        
        $current_browser = get_browser(null, true);
        if (strcasecmp($current_browser['browser'], 'ie') == 0 && intval($current_browser['majorver']) < 10) {
            echo "<div class='text-center align-middle' style='padding:20px;color:white;background:red;line-height:40px;'>Версии браузера Internet Explorer ниже 10 не поддерживаются!</div>";
            exit();
        }

        include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php';
        include_once 'php/assist.php';
        /*
        include_once $_SERVER['DOCUMENT_ROOT'] . '/dmgnPsAdm/php/db_depo.php';
        
        $db = new db_depo();
        $db->from_table_to_table();
          */
        ?>

        <nav class="navbar navbar-expand navbar-info bg-info y-fixtop">
            <span class="navbar-brand d-inline-block">
                    <img src='img/favicon/dmgn_nav_32.png' onclick="show_sideout();">
                   <span id="appttl"> &thinsp;<small class="y-llgray-text">ИС</small> ДМГН</span> <span class="d-inline-block y-ver y-info">Подвижной состав 1.00</span>
            </span>

            <!-- Navbar Search -->
            <div id="global_search" class="ml-auto">
            </div>
        </nav>



        <div class="page-content">
        </div>
        
        
         <div id="sideout" class="noselect">
            <div id="sideout_title">
               <span id="sideout_who" class="d-inline-block y-pad-lr10">&nbsp;</span>
            </div>
            <div id="sideout_body" class="p-0 position-relative" style="overflow-y:auto;">
       
            </div>
            <div id="sideout_footer">
            </div>
        </div>

        <footer class="sticky-footer y-flex-row-nowrap justify-content-between align-items-center y-modal-footer-bk">
            <div id='foot_left_info' class="y-gray-text y-mrg-l20"><i class="far fa-window-restore" style="color:#AFAF5F;"></i> &nbsp;<span id="app_partname">Подвижной состав</span></div>

            <div id='foot_info' class="y-lgray-text"></div>
            
            <div id='foot_right'>
                <div class="y-gray-text foot-copyright"> &nbsp;<?php echo assist::$copyright_str; ?> &nbsp;
                    <span id='foot_copyright_float'></span>
                    <span id='foot_bbmon_root' class='d-inline-block position-relative'></span>
                    <span id='foot_fdb_root' class='d-inline-block position-relative'></span>
                    <img id="foot_help" src='/pktbbase/img/help_32.png' class="y-cur-point" onclick="_help.help_start(this);" data-toggle='tooltip' title='Справка'>
                </div>
            </div>
        </footer>

        
        <div class="y-ajax-wait"></div>   <!-- Waiting cursor -->

        <div id="div_tmp"></div>
        <div id="div_tmpx"></div>
        
        <a id='a_download' href='javascript:;' class='d-none' download></a>
        
        <div id="pktb_appid" class="d-none" data-appnm="dmgnPs" data-applvl="U" data-applvl="O" data-oneid="131" data-helpid="131" ><?php echo _assbase::app_id('dmgnPs');?></div> <!--_assbase include via assist-->
        
        <?php _assbase::checkSttv('dmgnPs'); ?>

        <script src="/pktbbase/js/jquery.min.js"></script>
        <script src="/pktbbase/js/bootstrap.bundle.min.js"></script>

        <script src="/pktbbase/js/purl.min.js"></script>
        <script src="/pktbbase/js/jquery.noty.packaged.min.js"></script>
        <script src="/pktbbase/js/mprogress.min.js"></script>
        <script src="/pktbbase/js/bootstrap4-toggle.min.js"></script>
        <!--
        <script src="/pktbbase/js/md5.min.js"></script>
        <script src="/pktbbase/js/jquery.colorbox-min.js"></script>
        -->
        <script src="/pktbbase/js/bootstrap-datepicker.min.js"></script>
        <script src="/pktbbase/js/bootstrap-datepicker.ru.min.js" charset="UTF-8"></script>
        <script src="/pktbbase/js/jquery.touchSwipe.min.js"></script>

        <script src="/pktbbase/js/jquery.autocomplete.corrected-me.min.js"></script>

        <script src="js/main.js"></script>

    </body>
</html>
