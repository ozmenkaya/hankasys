<?php

#echo "<pre>"; print_r($_POST); exit;
#echo "<pre>"; print_r($_GET);

require_once "include/db.php";

#stok kalem ekle
if(isset($_POST['stok_kalem_ekle']))
{
    $stok_kalem      = mb_strtoupper(trim($_POST['stok_kalem']));    
    
    $sql = "INSERT INTO stok_kalemleri(firma_id, stok_kalem) VALUES(:firma_id, :stok_kalem);";
    $sth = $conn->prepare($sql);
    $sth->bindParam("firma_id", $_SESSION['firma_id']);
    $sth->bindParam("stok_kalem", $stok_kalem);
    
    $durum = $sth->execute();

    if($durum == true)
    {
        #echo "<h2>Ekleme başarılı</h2>";
        $_SESSION['durum'] = 'success';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarılı';
        header('Location: stok_kalem.php');
    }
    else 
    {
        #echo "<h2>ekleme başarısız</h2>";
        $_SESSION['durum'] = 'error';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarısız';
        header('Location: stok_kalem.php');
    }
    die();
}


#stok kalem sil
if(isset($_GET['islem']) && $_GET['islem'] == 'stok_kalem_sil')
{
    $id = $_GET['id'];

    $sql = "DELETE FROM stok_kalemleri WHERE id=:id AND firma_id = :firma_id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('id', $id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $durum = $sth->execute(); 
    
    
    if($durum == true)
    {
        #echo "<h2>Ekleme başarılı</h2>";
        $_SESSION['durum'] = 'success';
        $_SESSION['mesaj'] = 'Silme İşlemi Başarılı';
    }
    else 
    {
        #echo "<h2>ekleme başarısız</h2>";
        $_SESSION['durum'] = 'error';
        $_SESSION['mesaj'] = 'Silme İşlemi Başarısız';
    }
    header('Location: stok_kalem.php');
    die();
}


#stok kalem guncelle
if(isset($_POST['stok_kalem_guncelle']))
{
    $id              = intval($_POST['id']);
    $stok_kalem      = mb_strtoupper(trim($_POST['stok_kalem']));
    

    
    $sql = "UPDATE stok_kalemleri SET stok_kalem = :stok_kalem WHERE id = :id AND firma_id = :firma_id;";
    $sth = $conn->prepare($sql);
    $sth->bindParam("stok_kalem", $stok_kalem);
    $sth->bindParam("firma_id", $_SESSION['firma_id']);
    $sth->bindParam("id", $id);
    $durum = $sth->execute();

    if($durum == true)
    {
        $_SESSION['durum'] = 'success';
        $_SESSION['mesaj'] = 'Güncelleme İşlemi Başarılı';
        header("Location: stok_kalem.php");
    }
    else 
    {
        $_SESSION['durum'] = 'error';
        $_SESSION['mesaj'] = 'Güncelleme İşlemi Başarısız';
        header("Location: stok_kalem_guncelle.php?id={$id}");
    }
    die();
}
