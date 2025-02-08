<?php 

require_once "include/db.php";
require_once "include/oturum_kontrol.php";

#Birim Ekle
if(isset($_POST['birim_ekle'])){
    #echo "<pre>"; print_r($_POST); exit;
    $birim = mb_strtoupper(trim($_POST['birim']));

    $sql = "INSERT INTO birimler(ad, firma_id) VALUES(:ad, :firma_id);";
    $sth = $conn->prepare($sql);
    $sth->bindParam("ad", $birim);
    $sth->bindParam("firma_id", $_SESSION['firma_id']);
    $durum = $sth->execute();

    if($durum){ 
        $_SESSION['durum'] = 'success';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarılı';
    }
    else 
    {
        $_SESSION['durum'] = 'error';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarısız';
    }
    header('Location: birimler.php');
    die();

}

#Birim Güncelle
if(isset($_POST['birim_guncelle'])){
    $id     = intval($_POST['id']);
    $birim  = mb_strtoupper(trim($_POST['birim']));

    $sql = "UPDATE birimler SET ad = :ad
            WHERE id = :id AND firma_id =:firma_id;";
    $sth = $conn->prepare($sql);
    $sth->bindParam("ad", $birim);
    $sth->bindParam("id", $id);
    $sth->bindParam("firma_id", $_SESSION['firma_id']);

    $durum = $sth->execute();

    if($durum == true)
    {
        $_SESSION['durum'] = 'success';
        $_SESSION['mesaj'] = 'Güncelle İşlemi Başarılı';
        header("Location: birimler.php");
    }
    else 
    {
        $_SESSION['durum'] = 'error';
        $_SESSION['mesaj'] = 'Güncelle İşlemi Başarısız';
        header("Location: birim_guncelle.php?id={$id}");
    }
    die();
}