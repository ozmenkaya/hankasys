<?php

#echo "<pre>"; print_r($_POST); exit;
#echo "<pre>"; print_r($_GET);

require_once "include/db.php";
require_once "include/oturum_kontrol.php";

//excel çıkarma işlemi
if(isset($_GET['islem']) && $_GET['islem'] == 'arsiv_kalem_csv')
{

    //https://www.codexworld.com/export-data-to-csv-file-using-php-mysql/
    $delimiter = ","; 
    $filename = "arsiv_kalemler_" . date('Y-m-d-His') . ".csv"; 

    // Create a file pointer 
    $f = fopen('php://memory', 'w'); 


    $fields = ['SIRA','ARŞİV', 'DEPARTMAN']; 
    fputcsv($f, $fields, $delimiter); 

    $sth = $conn->prepare('SELECT arsiv_kalemler.*, departmanlar.departman FROM arsiv_kalemler 
    JOIN departmanlar  ON arsiv_kalemler.departman_id = departmanlar.id
    WHERE arsiv_kalemler.firma_id = :firma_id');
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $personeller = $sth->fetchAll(PDO::FETCH_ASSOC);


    foreach ($personeller as $key=> $personel) {
        $lineData = [
            $key+1, $personel['arsiv'], $personel['departman']
        ]; 
        fputcsv($f, $lineData, $delimiter); 
    }

    fseek($f, 0); 
     
    // Set headers to download file rather than displayed 
    header('Content-Type: text/csv'); 
    header('Content-Disposition: attachment; filename="' . $filename . '";'); 
     
    //output all remaining data on a file pointer 
    fpassthru($f); 

}


#arsiv kalem ekle
if(isset($_POST['arsiv_kalem_ekle']))
{
    $arsiv          = $_POST['arsiv'];
    $departman_id   = $_POST['departman_id'];
    
    $sql = "INSERT INTO arsiv_kalemler(firma_id, departman_id, arsiv) VALUES(:firma_id, :departman_id, :arsiv);";
    $sth = $conn->prepare($sql);
    $sth->bindParam("firma_id", $_SESSION['firma_id']);
    $sth->bindParam("departman_id", $departman_id);
    $sth->bindParam("arsiv", $arsiv);
    
    $durum = $sth->execute();

    if($durum == true)
    {
        #echo "<h2>Ekleme başarılı</h2>";
        $_SESSION['durum'] = 'success';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarılı';
        header('Location: arsiv_kalem.php');
    }
    else 
    {
        #echo "<h2>ekleme başarısız</h2>";
        $_SESSION['durum'] = 'error';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarısız';
        header('Location: arsiv_kalem.php');
    }
    die();
}


#Arsiv Kalem sil
if(isset($_GET['islem']) && $_GET['islem'] == 'arsiv_kalem_sil')
{
    $id = $_GET['id'];

    $sql = "DELETE FROM arsiv_kalemler WHERE id=:id AND firma_id = :firma_id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('id', $id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $durum = $sth->execute(); 
    
    
    if($durum == true)
    {
        #echo "<h2>Ekleme başarılı</h2>";
        $_SESSION['durum'] = 'basarili';
        $_SESSION['mesaj'] = 'Silme İşlemi Başarılı';
    }
    else 
    {
        $_SESSION['durum'] = 'basarisiz';
        $_SESSION['mesaj'] = 'Silme İşlemi Başarısız';
        #echo "<h2>ekleme başarısız</h2>";
    }
    header('Location: arsiv_kalem.php');
    die();
}


#Arsiv Kalem guncelle
if(isset($_POST['arsiv_kalem_guncelle']))
{
    $id                 = $_POST['id'];
    $arsiv              = $_POST['arsiv'];
    $departman_id       = $_POST['departman_id'];
    
    
    $sql = "UPDATE arsiv_kalemler SET arsiv = :arsiv, departman_id = :departman_id WHERE id = :id AND firma_id = :firma_id;";
    $sth = $conn->prepare($sql);
    $sth->bindParam("arsiv", $arsiv);
    $sth->bindParam("departman_id", $departman_id);
    $sth->bindParam("id", $id);
    $sth->bindParam("firma_id", $_SESSION['firma_id']);

    $durum = $sth->execute();

    if($durum == true)
    {
        $_SESSION['durum'] = 'basarili';
        $_SESSION['mesaj'] = 'Güncelleme İşlemi Başarılı';

        header("Location: arsiv_kalem.php");
    }
    else 
    {
        $_SESSION['durum'] = 'basarisiz';
        $_SESSION['mesaj'] = 'Güncelleme İşlemi Başarısız';
        header("Location: arsiv_kalem_guncelle.php?id={$id}");
    }
    die();
}
