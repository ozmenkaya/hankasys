<?php 
require_once "include/db.php";
require_once "include/oturum_kontrol.php";


//gönderimi bitmiş siparişler log
if(isset($_POST['islem']) && $_POST['islem'] == 'gonderimi_bitmis_siparis_log'){
    $siparis_id = $_POST['siparis_id'];
    $sql = "SELECT teslim_edilenler.teslim_adedi AS adet,teslim_edilenler.tarih, 'teslim' AS durum,
            planlama.isim,siparisler.isin_adi
            FROM `teslim_edilenler` 
            JOIN planlama ON planlama.siparis_id = teslim_edilenler.siparis_id
            JOIN siparisler ON siparisler.id = teslim_edilenler.siparis_id
            WHERE teslim_edilenler.siparis_id = :siparis_id";

    $sth = $conn->prepare($sql);
    $sth->bindParam('siparis_id', $siparis_id);
    $sth->execute();
    $teslim_edilen_loglar = $sth->fetchAll(PDO::FETCH_ASSOC);

 

    
    $sql = "SELECT uretilen_adetler.uretilen_adet AS adet,uretilen_adetler.bitis_tarihi AS tarih, 'uretim' AS durum,
            planlama.isim,siparisler.isin_adi
            FROM `uretilen_adetler` 
            JOIN planlama ON planlama.id = uretilen_adetler.planlama_id
            JOIN siparisler ON siparisler.id = planlama.siparis_id
            WHERE siparisler.id = :siparis_id";

    $sth = $conn->prepare($sql);
    $sth->bindParam('siparis_id', $siparis_id);
    $sth->execute();
    $uretilen_loglar = $sth->fetchAll(PDO::FETCH_ASSOC);

    $loglar = array_merge($uretilen_loglar, $teslim_edilen_loglar);
    array_multisort( array_column($loglar, "tarih"), SORT_ASC, $loglar );


    echo json_encode([
        'loglar' => $loglar
    ]);
    exit;

}
#echo json_encode($_POST);
if(isset($_POST['islem']) && $_POST['islem'] == 'siparisler_ve_log')
{
    $siparis_id = $_POST['siparis_id'];
    $sql = "SELECT planlama.id, planlama.isim, planlama.biten_urun_adedi, planlama.teslim_edilen_urun_adedi, 
            birimler.ad AS birim_ad
            FROM `planlama` 
            JOIN `siparisler` ON `siparisler`.id = planlama.siparis_id
            JOIN birimler ON birimler.id = siparisler.birim_id
            WHERE planlama.siparis_id = :siparis_id AND planlama.firma_id = :firma_id";

    $sth = $conn->prepare($sql);
    $sth->bindParam('siparis_id', $siparis_id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $planlamalar = $sth->fetchAll(PDO::FETCH_ASSOC);


    $sql = "SELECT teslim_edilenler.teslim_adedi AS adet,teslim_edilenler.tarih, 'teslim' AS durum,
            planlama.isim,siparisler.isin_adi
            FROM `teslim_edilenler` 
            JOIN planlama ON planlama.siparis_id = teslim_edilenler.siparis_id
            JOIN siparisler ON siparisler.id = teslim_edilenler.siparis_id
            WHERE teslim_edilenler.siparis_id = :siparis_id";

    $sth = $conn->prepare($sql);
    $sth->bindParam('siparis_id', $siparis_id);
    $sth->execute();
    $teslim_edilen_loglar = $sth->fetchAll(PDO::FETCH_ASSOC);

 

    
    $sql = "SELECT uretilen_adetler.uretilen_adet AS adet,uretilen_adetler.bitis_tarihi AS tarih, 'uretim' AS durum,
            planlama.isim,siparisler.isin_adi
            FROM `uretilen_adetler` 
            JOIN planlama ON planlama.id = uretilen_adetler.planlama_id
            JOIN siparisler ON siparisler.id = planlama.siparis_id
            WHERE siparisler.id = :siparis_id";

    $sth = $conn->prepare($sql);
    $sth->bindParam('siparis_id', $siparis_id);
    $sth->execute();
    $uretilen_loglar = $sth->fetchAll(PDO::FETCH_ASSOC);
    
    $loglar = array_merge($uretilen_loglar, $teslim_edilen_loglar);
    array_multisort( array_column($loglar, "tarih"), SORT_ASC, $loglar );

    echo json_encode([
        'planlamalar'   => $planlamalar,
        'loglar'        => $loglar
    ]);
    exit;
}


//teslim edilecekleri kaydet
//echo "<pre>"; print_r($_POST);
if(isset($_POST['teslim_et']))
{
    $teslim_edilecekler = $_POST['teslim_edilecekler'];
    $planlanma_idler    = $_POST['planlanma_idler'];
    $siparis_id         = $_POST['siparis_id'];
    foreach ($teslim_edilecekler as $index => $teslim_edilecek_adet) {
        //teslim_edilenler tablosuna ekle
        $sql = "INSERT INTO teslim_edilenler(siparis_id, planlama_id, personel_id, teslim_adedi) 
        VALUES(:siparis_id, :planlama_id, :personel_id, :teslim_adedi);";
        $sth = $conn->prepare($sql);
        $sth->bindParam("siparis_id", $siparis_id);
        $sth->bindParam("planlama_id", $planlanma_idler[$index]);
        $sth->bindParam("personel_id", $_SESSION['personel_id']);
        $sth->bindParam("teslim_adedi", $teslim_edilecek_adet);
        $durum = $sth->execute();

        if($durum){
            //planlama tablosuna teslim edilen adetleri ekle
            $sql = "UPDATE planlama SET teslim_edilen_urun_adedi = teslim_edilen_urun_adedi + :teslim_edilen_urun_adedi  
            WHERE id = :id;";
            $sth = $conn->prepare($sql);
            $sth->bindParam('teslim_edilen_urun_adedi', $teslim_edilecek_adet);
            $sth->bindParam('id', $planlanma_idler[$index]);
            $durum = $sth->execute();
        }
    }

    foreach ($planlanma_idler as $planlanma_id) {
        $sth = $conn->prepare('SELECT planlama.teslim_edilen_urun_adedi,siparisler.adet
        FROM planlama 
        JOIN siparisler ON siparisler.id = planlama.siparis_id
        WHERE planlama.id =:id');
        $sth->bindParam('id', $planlanma_id);
        $sth->execute();
        $planlama = $sth->fetch(PDO::FETCH_ASSOC);

        //tümü teslim edildi mi?
        if($planlama['teslim_edilen_urun_adedi'] >= $planlama['adet']){
            $sql = "UPDATE planlama SET teslim_durumu = 'bitti'  WHERE id = :id;";
            $sth = $conn->prepare($sql);
            $sth->bindParam('id', $planlanma_id);
            $durum = $sth->execute();
        }
    }


    //alt ürünlerin hepsi iade edilmi mi?
    $sth = $conn->prepare('SELECT teslim_durumu FROM planlama WHERE siparis_id=:siparis_id');
    $sth->bindParam('siparis_id', $siparis_id);
    $sth->execute();
    $planlamalar = $sth->fetchAll(PDO::FETCH_ASSOC);

    $tum_teslim_edildi_mi = true;
    foreach ($planlamalar as $planlama) {
        if($planlama['teslim_durumu'] == 'bitmedi'){
            $tum_teslim_edildi_mi = false;
            break;
        }
    }

    if($tum_teslim_edildi_mi){
        $sql = "UPDATE siparisler SET islem = 'teslim_edildi'  WHERE id = :id;";
        $sth = $conn->prepare($sql);
        $sth->bindParam('id', $siparis_id);
        $durum = $sth->execute();
    }

    header("Location: depo.php"); exit;
}