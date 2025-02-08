<?php 
require_once "include/db.php";
require_once 'vendor/autoload.php';

/*
echo "<pre>"; print_R($_SESSION);
echo "<pre>"; print_R($_POST);
echo "<pre>"; print_R($_FILES); exit;
*/

if(isset($_POST['detaydan-ekle'])){
    $ust_id         = trim($_POST['ust_id']);
    $baslik         = trim($_POST['baslik']);
    $icerik         = trim($_POST['icerik']);
    $ait_id         = trim($_POST['ait_id']);
    $onem_sirasi    = $_POST['onem_sirasi'];
    $sql = "INSERT INTO geri_bildirim(ust_id, kimden, kime, baslik, icerik, onem_sirasi) 
        VALUES(:ust_id,:kimden, :kime, :baslik, :icerik, :onem_sirasi);";

    $sth = $conn->prepare($sql);

    $sth->bindParam("ust_id",   $ust_id);
    $sth->bindParam("kimden",   $_SESSION['personel_id']);
    $sth->bindValue("kime",     $_SESSION['yetki_id'] == SUPER_ADMIN_YETKI_ID ? $_SESSION['personel_id'] : $ait_id);
    $sth->bindParam("baslik",   $baslik);
    $sth->bindParam("icerik",   $icerik);
    $sth->bindParam("onem_sirasi",   $onem_sirasi);
    $durum = $sth->execute();

    $geri_donusum_id = $conn->lastInsertId();

    $hedef_klasor = "dosyalar/geri-bildirim/";
    if(isset($_FILES['dosyalar']))
    {
        $dosyalar = $_FILES['dosyalar'];

        for($i = 0; $i < count($dosyalar['name']); $i++)
        {
            $dosya_adi      = pathinfo($dosyalar['name'][$i], PATHINFO_FILENAME)."_".random_int(1000, 99999);
            $dosya_uzanti   = pathinfo($dosyalar['name'][$i], PATHINFO_EXTENSION);

            $dosya_adi = "{$dosya_adi}.{$dosya_uzanti}";

            if (move_uploaded_file($dosyalar["tmp_name"][$i], $hedef_klasor.$dosya_adi)) 
            {
                $sql = "INSERT INTO  geri_bildirim_dosyalar(geri_donusum_id,ad) VALUES(:geri_donusum_id, :ad)";
                $sth = $conn->prepare($sql);
                $sth->bindParam("geri_donusum_id", $geri_donusum_id);
                $sth->bindParam("ad", $dosya_adi);
                $durum = $sth->execute();
            }
        }
    }

    if($durum == true)
    {
        $_SESSION['durum'] = 'success';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarılı';
    }
    else 
    {
        $_SESSION['durum'] = 'error';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarısız';
    }
    header("Location: geri_bildirim_detay.php?id={$ust_id}");
    die();

}

//ilk işlem açılımında
if(isset($_POST['ekle'])){
    $baslik     = trim($_POST['baslik']);
    $icerik     = trim($_POST['icerik']);
    $onem_sirasi     = trim($_POST['onem_sirasi']);
    $sql = "INSERT INTO geri_bildirim(kimden, kime, baslik, icerik, onem_sirasi) 
        VALUES(:kimden, :kime, :baslik, :icerik, :onem_sirasi);";
    $sth = $conn->prepare($sql);
    $sth->bindParam("kimden", $_SESSION['personel_id']);
    $sth->bindValue("kime", 1);
    $sth->bindParam("baslik", $baslik);
    $sth->bindParam("icerik", $icerik);
    $sth->bindParam("onem_sirasi", $onem_sirasi);
    $durum = $sth->execute();

    $geri_donusum_id = $conn->lastInsertId();

    $hedef_klasor = "dosyalar/geri-bildirim/";
    if(isset($_FILES['dosyalar']))
    {
        $dosyalar = $_FILES['dosyalar'];

        for($i = 0; $i < count($dosyalar['name']); $i++)
        {
            $dosya_adi      = pathinfo($dosyalar['name'][$i], PATHINFO_FILENAME)."_".random_int(1000, 99999);
            $dosya_uzanti   = pathinfo($dosyalar['name'][$i], PATHINFO_EXTENSION);

            $dosya_adi = "{$dosya_adi}.{$dosya_uzanti}";

            if (move_uploaded_file($dosyalar["tmp_name"][$i], $hedef_klasor.$dosya_adi)) 
            {
                $sql = "INSERT INTO  geri_bildirim_dosyalar(geri_donusum_id,ad) VALUES(:geri_donusum_id, :ad)";
                $sth = $conn->prepare($sql);
                $sth->bindParam("geri_donusum_id", $geri_donusum_id);
                $sth->bindParam("ad", $dosya_adi);
                $durum = $sth->execute();
            }
        }
    }

    if($durum == true)
    {
        $_SESSION['durum'] = 'success';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarılı';
    }
    else 
    {
        $_SESSION['durum'] = 'error';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarısız';
    }
    header("Location: geri_bildirim.php");
    die();
}