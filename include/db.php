<?php 
include_once  "sabitler.php";
include_once  "function.php";
date_default_timezone_set('Europe/Istanbul');
setcookie('url', getBaseUrl().$_SERVER['REQUEST_URI'], time() + SESSION_SURESI, "/"); // 86400 = 1 day
if(BAKIM_MOD){
    header("Location:include/bakim_mod.php");
    exit;
}

session_name("PNL");
ini_set('session.cookie_lifetime', SESSION_SURESI);
ini_set('session.gc_maxlifetime', SESSION_SURESI);

session_start();
session_regenerate_id();



if(isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], DEBUG_IPLER))
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}


#veritabanı bağlantı
try{    
    $conn = new PDO(
        'mysql:host=195.201.104.228;port=3306;dbname=panelhankasys_mes;charset=utf8mb4',
        'panelhankasys_pars',
        '+fAy9+]CoCe.',
        [
            PDO::ATTR_DEFAULT_FETCH_MODE    =>  PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE               =>  PDO::ERRMODE_EXCEPTION,
        ]
    );
}catch (PDOException $e){
    header("Location:db_hata.php");
    exit;
}

?>