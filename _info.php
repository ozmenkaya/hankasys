<?php 
    date_default_timezone_set('Europe/Istanbul');
    require_once "include/db.php";
    require_once "include/oturum_kontrol.php";
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
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fa-solid fa-server"></i> Server Bilgi
                    </h5>
                </div>
                <div class="card-body text-center">
                    <?php 
                        echo ini_get("session.gc_maxlifetime")."<br>";
                        echo date('d-m-Y H:i:s');
                        phpinfo()
                    ?>
                </div>
            </div>
        </div>
        <?php 
            include_once "include/scripts.php"; 
        ?>
    </body>
</html>
