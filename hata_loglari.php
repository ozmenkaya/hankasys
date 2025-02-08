<?php
    require_once "include/db.php";
    require_once "include/oturum_kontrol.php";

    if( $_SESSION['yetki_id'] != SUPER_ADMIN_YETKI_ID ){ 
        include "include/yetkisiz.php"; exit;
    }

    $log_dosyasi = $_SERVER['HOME'].'/logs/panel_hankasys_com.php.error.log';
    $loglar = [];
    if (file_exists($log_dosyasi) && $file = fopen($log_dosyasi, "r")) {
        while(!feof($file)) {  
            if(!empty(fgets($file)) )
                $loglar[] = fgets($file); 
        }
        fclose($file);
    }
    $loglar = array_reverse($loglar);
    
?>
<!DOCTYPE html>
<html lang="tr">
    <head>
        <title>Hanka Sys SAAS</title> 
        <?php require_once "include/head.php";?>
    </head>
    <body>
        <?php 
            require_once "include/header.php";
            require_once "include/sol_menu.php";
        ?>
        <div class="container-fluid">
            <div class="card border-secondary border-2">
                <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                    <h5>
                        <i class="fa-solid fa-bug fs-4"></i>
                        HATALAR
                    </h5>
                    <h6 class="badge bg-secondary fs-6 p-2">
                        <i class="fa-regular fa-file"></i>
                        <?php echo $log_dosyasi; ?>
                    </h6>
                    <h5>
                        <a href="hata_loglari.php" class="btn btn-secondary fw-bold">
                            <i class="fa-solid fa-retweet"></i>
                            <span id="geri-sayim">120</span> sn
                        </a>
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($loglar as $index => $log) { ?>
                            <li class="list-group-item <?php echo str_contains($log, 'Europe/Istanbul') ? 'list-group-item-danger fw-bold':'';?>">
                                <?php echo '<b>'.($index+1).' - </b>'.$log; ?>
                            </li>
                        <?php }?>
                    </ul>
                </div>
            </div>
        </div>
        <?php 
            include_once "include/scripts.php"; 
        ?>
        <script>
            let geriSayim = 120;
            setInterval(function(){
                if(geriSayim < 1 ) window.location.reload();
                $("#geri-sayim").text(--geriSayim);
            },1000)
        </script>
    </body>
</html>
