<?php 

require_once "include/db.php";
require_once "include/oturum_kontrol.php";
#require_once 'vendor/autoload.php';

use Dompdf\Dompdf; 

function array2DStringToInt($arr){
    $sonuc = [];
    foreach ($arr as $deger) {
        $sonuc[] = array_map('intval', $deger);
    }
    return $sonuc;
}



#planlama ekleme veya kaydet
if(isset($_POST['planlama_ekle_kaydet']))
{
    //echo "<pre>"; print_r($_POST); exit;

    $alt_urun_sayisi    = intval($_POST['alt_urun_sayisi']);
    $islem              = $_POST['planlama_ekle_kaydet'];
    $siparis_id         = intval($_POST['siparis_id']);

    for($i = 1; $i <= $alt_urun_sayisi; $i++){

        $sql = "SELECT veriler,tip_id FROM `siparisler` WHERE id = :id";
        $sth = $conn->prepare($sql);
        $sth->bindParam("id",$siparis_id);
        $sth->execute();
        $siparis = $sth->fetch(PDO::FETCH_ASSOC);

        $asama_sayisi           =   isset($_POST["alt_urun_{$i}"]['departman']) ? count($_POST["alt_urun_{$i}"]['departman']) : 0;
        
        $departmanlar           =   isset($_POST["alt_urun_{$i}"]['departman']) ? 
                                    array_reverse(array_map('intval',$_POST["alt_urun_{$i}"]['departman'])) : []; 

        $adetler                =   isset($_POST["alt_urun_{$i}"]['adet']) ? 
                                    array_reverse(array_map('intval',$_POST["alt_urun_{$i}"]['adet'])) : [];
        
        $sureler                =   isset($_POST["alt_urun_{$i}"]['sure']) ? 
                                    array_reverse(array_map('floatval',$_POST["alt_urun_{$i}"]['sure'])) : [];

        $detaylar               =   isset($_POST["alt_urun_{$i}"]['detay']) ? array_reverse($_POST["alt_urun_{$i}"]['detay']) : [];

        $makinalar              =   isset($_POST["alt_urun_{$i}"]['makina']) ? 
                                    array_reverse(array_map('intval',$_POST["alt_urun_{$i}"]['makina'])) : [];
        
        $alt_arsiv              =   isset($_POST["alt_urun_{$i}"]['alt_arsiv']) ? $_POST["alt_urun_{$i}"]['alt_arsiv'] :[];
        $alt_arsiv              =   array2DStringToInt(array_reverse($alt_arsiv));

        
        $stok_kalemler          = isset($_POST["alt_urun_{$i}"]['stok_kalem']) ? $_POST["alt_urun_{$i}"]['stok_kalem'] : [];
        $stok_kalemler          = array2DStringToInt(array_reverse($stok_kalemler));

        $stok_alt_kalemler      = isset($_POST["alt_urun_{$i}"]['stok_alt_kalem']) ? $_POST["alt_urun_{$i}"]['stok_alt_kalem'] : [];
        $stok_alt_kalemler      = array2DStringToInt(array_reverse($stok_alt_kalemler));

        $stok_alt_depo_adetler  = isset($_POST["alt_urun_{$i}"]['stok_alt_depo_adet']) ? $_POST["alt_urun_{$i}"]['stok_alt_depo_adet'] : [];
        $stok_alt_depo_adetler  = array2DStringToInt(array_reverse($stok_alt_depo_adetler));

        $stok_alt_depolar       = isset($_POST["alt_urun_{$i}"]['stok_alt_depo']) ? $_POST["alt_urun_{$i}"]['stok_alt_depo'] : [];
        $stok_alt_depolar       = array2DStringToInt(array_reverse($stok_alt_depolar));

        $fason_durumlar         = isset($_POST["alt_urun_{$i}"]['fason_durum']) ? 
                                array_reverse(array_map('intval', $_POST["alt_urun_{$i}"]['fason_durum'])):[];

        $fason_tedarikciler     = isset($_POST["alt_urun_{$i}"]['fason_tedarikci']) ? 
                                array_reverse(array_map('intval',$_POST["alt_urun_{$i}"]['fason_tedarikci'])) : [];

        $uretilecek_adet        = preg_replace("/[^0-9]/",'',$_POST["alt_urun_{$i}"]['uretilecek_adet']);
        $isim                   = trim($_POST["alt_urun_{$i}"]['isim']);

        $grup_kodu              = uniqid();
        $eksik_stok_varmi       = false;
        //Rezervasyonlara ekle
        foreach ($stok_alt_depolar as $asama_index => $stok_alt_depolar_idler) {
            foreach ($stok_alt_depolar_idler as $stok_alt_depo_id_index => $stok_alt_depo_id) {
                //0 seçilmemiş
                //-1 eksik stok
                if(!in_array($stok_alt_depo_id, [-1, 0])){
                    $sql = "INSERT INTO uretim_reservasyon(firma_id, grup_kodu, mevcut_asama, stok_alt_depo_id, miktar) 
                            VALUES(:firma_id, :grup_kodu, :mevcut_asama, :stok_alt_depo_id, :miktar);";
                    $sth = $conn->prepare($sql);
                    $sth->bindParam("firma_id", $_SESSION['firma_id']);
                    $sth->bindParam("grup_kodu", $grup_kodu);
                    $sth->bindParam("mevcut_asama", $asama_index);
                    $sth->bindParam("stok_alt_depo_id", $stok_alt_depo_id);
                    $sth->bindParam("miktar", $stok_alt_depo_adetler[$asama_index][$stok_alt_depo_id_index]);
                    $sth->execute();

                }else if($stok_alt_depo_id == -1) {
                    $eksik_stok_varmi = true;
                }
            }
            
        }

        $sql = "INSERT INTO planlama(firma_id, siparis_id, alt_urun_id, grup_kodu, sira, isim, asama_sayisi, uretilecek_adet, departmanlar, 
                adetler,orijinal_adetler,
                sureler,detaylar,makinalar,arsiv_altlar,stok_kalemler, stok_alt_kalemler,stok_alt_depo_adetler,stok_alt_depolar,
                fason_durumlar, fason_tedarikciler, planlama_durum,tekil_kod, eksik_stok_varmi)
                VALUES(:firma_id, :siparis_id, :alt_urun_id, :grup_kodu, :sira, :isim, :asama_sayisi, :uretilecek_adet, :departmanlar, 
                :adetler,:orijinal_adetler,
                :sureler, :detaylar, :makinalar,:arsiv_altlar,:stok_kalemler, :stok_alt_kalemler, :stok_alt_depo_adetler, :stok_alt_depolar,
                :fason_durumlar, :fason_tedarikciler, :planlama_durum, :tekil_kod, :eksik_stok_varmi)";
        
        $sth = $conn->prepare($sql);
        $sth->bindParam("firma_id",             $_SESSION['firma_id']);
        $sth->bindParam("siparis_id",           $siparis_id);
        $sth->bindParam("alt_urun_id",          $i);
        $sth->bindParam("grup_kodu",            $grup_kodu);
        $sth->bindValue("sira",                 99);
        $sth->bindParam("isim",                 $isim);
        $sth->bindParam("asama_sayisi",         $asama_sayisi);
        $sth->bindParam("uretilecek_adet",      $uretilecek_adet);
        $sth->bindValue("departmanlar",         json_encode($departmanlar));
        $sth->bindValue("orijinal_adetler",     json_encode($adetler));
        $sth->bindValue("adetler",              json_encode($adetler));
        $sth->bindValue("sureler",              json_encode($sureler));
        $sth->bindValue("detaylar",             json_encode($detaylar,JSON_UNESCAPED_UNICODE));
        $sth->bindValue("makinalar",            json_encode($makinalar));
        $sth->bindValue("arsiv_altlar",         json_encode($alt_arsiv));
        $sth->bindValue("stok_kalemler",        json_encode($stok_kalemler));
        $sth->bindValue("stok_alt_kalemler",    json_encode($stok_alt_kalemler));
        $sth->bindValue("stok_alt_depo_adetler",json_encode($stok_alt_depo_adetler));
        $sth->bindValue("stok_alt_depolar",     json_encode($stok_alt_depolar));
        $sth->bindValue("fason_durumlar",       json_encode($fason_durumlar));
        $sth->bindValue("fason_tedarikciler",   json_encode($fason_tedarikciler));
        $sth->bindValue("planlama_durum",       $islem == 'ekle' ?  'evet' : 'yarım_kalmıs');
        $sth->bindValue("tekil_kod",            uniqid());
        $sth->bindValue("eksik_stok_varmi",     $eksik_stok_varmi ? 'var':'yok');
        $durum = $sth->execute();

        $veriler = json_decode($siparis['veriler'], true);
        if($siparis['tip_id'] == TEK_URUN){
            $veriler['miktar']  = $uretilecek_adet;
            $veriler['isim']    = $isim;
        }else if(in_array($siparis['tip_id'], [GRUP_URUN_TEK_FIYAT,GRUP_URUN_AYRI_FIYAT])){
            $veriler[$i-1]['miktar']  = $uretilecek_adet;
            $veriler[$i-1]['isim']    = $isim;
        }

        $sql = "UPDATE siparisler SET veriler = :veriler WHERE id = :siparis_id";
        $sth = $conn->prepare($sql);
        $sth->bindValue("veriler",    json_encode($veriler));
        $sth->bindParam("siparis_id", $siparis_id);
        $sth->execute();

        
        //gelen makinalar kullanıldı
        $makinalar_birlestir = implode(',', $makinalar);
        if(!empty($makinalar_birlestir)){
            $sql = "UPDATE `makinalar` SET `kullanildi_mi` = 'evet' WHERE id IN({$makinalar_birlestir});";
            $sth = $conn->prepare($sql);
            $sth->execute();
        }
    }

    if($durum)
    {
        $_SESSION['durum'] = 'success';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarılı';
        header("Location: planlama.php");
    }else 
    {
        $_SESSION['durum'] = 'error';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarısız';
        header("Location: planla_siparis.php?siparis_id={$siparis_id}");
    }
    exit;
}

//planlamayı onayla 
if(isset($_POST['planlama_onay_guncelle'])){
    //echo "<pre>"; print_r($_POST);
    $alt_urun_sayisi    = $_POST['alt_urun_sayisi'];
    $islem              = $_POST['planlama_onay_guncelle'];
    $siparis_id         = $_POST['siparis_id'];

    $sql = "SELECT veriler,tip_id FROM `siparisler` WHERE id = :id";
    $sth = $conn->prepare($sql);
    $sth->bindParam("id",$siparis_id);
    $sth->execute();
    $siparis = $sth->fetch(PDO::FETCH_ASSOC);

    for($i = 1; $i <= $alt_urun_sayisi; $i++){
        $onay_durum  = $planlama_durum = 'evet';

        if($islem == 'yarım_kalmıs'){
            $onay_durum     = 'hayır';
            $planlama_durum = 'yarım_kalmıs';
        }else if($islem == 'guncelle'){
            $onay_durum     = 'hayır';
            $planlama_durum = 'evet';
        }

        if($_POST["alt_urun_{$i}"]['planlama_id'] != 0){ //güncelle
            
            $grup_kodu              = $_POST["alt_urun_{$i}"]['grup_kodu'];

            $departmanlar           = isset($_POST["alt_urun_{$i}"]['departman']) ?
                                    array_reverse(array_map('intval',$_POST["alt_urun_{$i}"]['departman'])) : []; 

            $adetler                = isset($_POST["alt_urun_{$i}"]['adet']) ? 
                                    array_reverse(array_map('intval',$_POST["alt_urun_{$i}"]['adet'])) : [];

            $asama_sayisi           = isset($_POST["alt_urun_{$i}"]['departman']) ? 
                                    count($_POST["alt_urun_{$i}"]['departman']) : 0;

            $sureler                = isset($_POST["alt_urun_{$i}"]['sure']) ?
                                    array_reverse(array_map('floatval',$_POST["alt_urun_{$i}"]['sure'])) : [];

            $detaylar               = isset($_POST["alt_urun_{$i}"]['detay']) ? 
                                    array_reverse($_POST["alt_urun_{$i}"]['detay']) : [];

            $makinalar              = isset($_POST["alt_urun_{$i}"]['makina']) ? 
                                    array_reverse(array_map('intval',$_POST["alt_urun_{$i}"]['makina'])) : [];
            

            $alt_arsiv              = isset($_POST["alt_urun_{$i}"]['alt_arsiv']) ? $_POST["alt_urun_{$i}"]['alt_arsiv'] :[];
            $alt_arsiv              = array2DStringToInt(array_reverse($alt_arsiv));

            $stok_kalemler          = isset($_POST["alt_urun_{$i}"]['stok_kalem']) ? $_POST["alt_urun_{$i}"]['stok_kalem'] : [];
            $stok_kalemler          = array2DStringToInt(array_reverse($stok_kalemler));

            $stok_alt_kalemler      = isset($_POST["alt_urun_{$i}"]['stok_alt_kalem']) ? $_POST["alt_urun_{$i}"]['stok_alt_kalem'] : [];
            $stok_alt_kalemler      = array2DStringToInt(array_reverse($stok_alt_kalemler));

            $stok_alt_depo_adetler  = isset($_POST["alt_urun_{$i}"]['stok_alt_depo_adet']) ? $_POST["alt_urun_{$i}"]['stok_alt_depo_adet'] : [];
            $stok_alt_depo_adetler  = array2DStringToInt(array_reverse($stok_alt_depo_adetler));

            $stok_alt_depolar       = isset($_POST["alt_urun_{$i}"]['stok_alt_depo']) ? $_POST["alt_urun_{$i}"]['stok_alt_depo'] : [];
            $stok_alt_depolar       = array2DStringToInt(array_reverse($stok_alt_depolar));

            $fason_durumlar         = isset($_POST["alt_urun_{$i}"]['fason_durum'])  ? 
                                    array_reverse(array_map('intval', $_POST["alt_urun_{$i}"]['fason_durum'])) : [];
                                    
            $fason_tedarikciler     = isset($_POST["alt_urun_{$i}"]['fason_tedarikci']) ? 
                                    array_reverse(array_map('intval',$_POST["alt_urun_{$i}"]['fason_tedarikci'])) : [];

            $uretilecek_adet        = preg_replace("/[^0-9]/",'',$_POST["alt_urun_{$i}"]['uretilecek_adet']);
            $isim                   = trim($_POST["alt_urun_{$i}"]['isim']);

            $eksik_stok_varmi       = false;

            //print_r($stok_alt_depo_adetler);
            //Rezervasyonlara ekle
            foreach ($stok_alt_depolar as $asama_index => $stok_alt_depolar_idler) {
                foreach ($stok_alt_depolar_idler as $stok_alt_depo_id_index => $stok_alt_depo_id) {
                    //echo $stok_alt_depo_id."<br>";
                    // 0 seçilmemiş
                    //-1 eksik stok
                    if(!in_array($stok_alt_depo_id, [-1, 0])){
                        $sql = "SELECT id FROM `uretim_reservasyon`  
                                WHERE grup_kodu = :grup_kodu AND stok_alt_depo_id = :stok_alt_depo_id AND mevcut_asama = :mevcut_asama";
                        $sth = $conn->prepare($sql);
                        $sth->bindParam("grup_kodu", $grup_kodu);
                        $sth->bindParam("stok_alt_depo_id", $stok_alt_depo_id);
                        $sth->bindParam("mevcut_asama", $asama_index);
                        $sth->execute();
                        $reservasyon_varmi = $sth->fetch(PDO::FETCH_ASSOC);

                        //var_dump($reservasyon_varmi);

                        if(empty($reservasyon_varmi)){
                            $sql = "INSERT INTO uretim_reservasyon(firma_id, grup_kodu, mevcut_asama, stok_alt_depo_id, miktar) 
                                VALUES(:firma_id, :grup_kodu, :mevcut_asama, :stok_alt_depo_id, :miktar);";
                            $sth = $conn->prepare($sql);
                            $sth->bindParam("firma_id", $_SESSION['firma_id']);
                            $sth->bindParam("grup_kodu", $grup_kodu);
                            $sth->bindParam("mevcut_asama", $asama_index);
                            $sth->bindParam("stok_alt_depo_id", $stok_alt_depo_id);
                            $sth->bindParam("miktar", $stok_alt_depo_adetler[$asama_index][$stok_alt_depo_id_index]);
                            $sth->execute();
                        }else{
                            $sql = "UPDATE uretim_reservasyon SET miktar = :miktar 
                                    WHERE firma_id = :firma_id AND stok_alt_depo_id = :stok_alt_depo_id AND mevcut_asama = :mevcut_asama";
                            $sth = $conn->prepare($sql);
                            $sth->bindParam("miktar", $stok_alt_depo_adetler[$asama_index][$stok_alt_depo_id_index]);
                            $sth->bindParam("firma_id", $_SESSION['firma_id']);
                            $sth->bindParam("mevcut_asama", $asama_index);
                            $sth->bindParam("stok_alt_depo_id", $stok_alt_depo_id);
                            $sth->execute();
                        }
                    }else if($stok_alt_depo_id == -1){
                        $eksik_stok_varmi = true;
                        $sql = "DELETE FROM uretim_reservasyon
                                WHERE grup_kodu = :grup_kodu AND stok_alt_depo_id = :stok_alt_depo_id AND mevcut_asama = :mevcut_asama";
                        $sth = $conn->prepare($sql);
                        $sth->bindParam("grup_kodu", $grup_kodu);
                        $sth->bindParam("mevcut_asama", $asama_index);
                        $sth->bindParam("stok_alt_depo_id", $stok_alt_depo_id);
                        $sth->execute(); 
                    } 
                }
                
            }

            $sql = "UPDATE planlama SET alt_urun_id = :alt_urun_id, isim = :isim, asama_sayisi = :asama_sayisi,
                uretilecek_adet = :uretilecek_adet,departmanlar = :departmanlar, orijinal_adetler = :orijinal_adetler, adetler = :adetler, 
                sureler = :sureler, detaylar = :detaylar, makinalar = :makinalar, arsiv_altlar = :arsiv_altlar,stok_kalemler = :stok_kalemler,
                stok_alt_kalemler = :stok_alt_kalemler, stok_alt_depo_adetler = :stok_alt_depo_adetler,
                stok_alt_depolar = :stok_alt_depolar, fason_durumlar = :fason_durumlar, fason_tedarikciler = :fason_tedarikciler,
                onay_durum = :onay_durum, planlama_durum = :planlama_durum, eksik_stok_varmi = :eksik_stok_varmi
                WHERE id = :id AND siparis_id = :siparis_id AND firma_id = :firma_id;";

            $sth = $conn->prepare($sql);
            $sth->bindParam('alt_urun_id',          $i);
            $sth->bindParam('isim',                 $isim);
            $sth->bindParam('asama_sayisi',         $asama_sayisi);
            $sth->bindParam('uretilecek_adet',      $uretilecek_adet);
            $sth->bindValue("departmanlar",         json_encode($departmanlar));
            $sth->bindValue("orijinal_adetler",     json_encode($adetler));
            $sth->bindValue("adetler",              json_encode($adetler));
            $sth->bindValue("sureler",              json_encode($sureler));
            $sth->bindValue("detaylar",             json_encode($detaylar,JSON_UNESCAPED_UNICODE));
            $sth->bindValue("makinalar",            json_encode($makinalar));
            $sth->bindValue("arsiv_altlar",         json_encode($alt_arsiv));
            $sth->bindValue("stok_kalemler",        json_encode($stok_kalemler));
            $sth->bindValue("stok_alt_kalemler",    json_encode($stok_alt_kalemler));
            $sth->bindValue("stok_alt_depo_adetler",json_encode($stok_alt_depo_adetler));
            $sth->bindValue("stok_alt_depolar",     json_encode($stok_alt_depolar));
            $sth->bindValue("fason_durumlar",       json_encode($fason_durumlar));
            $sth->bindValue("fason_tedarikciler",   json_encode($fason_tedarikciler));
            $sth->bindValue("onay_durum",           $onay_durum);
            $sth->bindValue("planlama_durum",       $planlama_durum);
            $sth->bindParam('id',                   $_POST["alt_urun_{$i}"]['planlama_id']);
            $sth->bindParam('siparis_id',           $siparis_id);
            $sth->bindParam('firma_id',             $_SESSION['firma_id']);
            $sth->bindValue("eksik_stok_varmi",     $eksik_stok_varmi ? 'var':'yok');
            $durum = $sth->execute();

            $veriler = json_decode($siparis['veriler'], true);
            if($siparis['tip_id'] == TEK_URUN){
                $veriler['miktar']  = $uretilecek_adet;
                $veriler['isim']    = $isim;
            }else if(in_array($siparis['tip_id'], [GRUP_URUN_TEK_FIYAT,GRUP_URUN_AYRI_FIYAT])){
                $veriler[$i-1]['miktar']  = $uretilecek_adet;
                $veriler[$i-1]['isim']    = $isim;
            }

            $sql = "UPDATE siparisler SET veriler = :veriler WHERE id = :siparis_id";
            $sth = $conn->prepare($sql);
            $sth->bindValue("veriler",    json_encode($veriler));
            $sth->bindParam("siparis_id", $siparis_id);
            $sth->execute();

        }
        else{ // ekle yeni alt ürün
            $sql = "INSERT INTO planlama(firma_id, siparis_id, alt_urun_id, isim, asama_sayisi, uretilecek_adet, departmanlar,
            adetler, sureler, detaylar, makinalar, arsiv_altlar, stok_alt_kalemler,
            stok_alt_depo_adetler, stok_alt_depolar, fason_durumlar, fason_tedarikciler, onay_durum, planlama_durum, tekil_kod) 
            VALUES(:firma_id, :siparis_id, :alt_urun_id, :isim, :asama_sayisi, :uretilecek_adet, :departmanlar, 
            :adetler, :sureler, :detaylar, :makinalar, :arsiv_altlar, :stok_alt_kalemler,
            :stok_alt_depo_adetler, :stok_alt_depolar, :fason_durumlar, :fason_tedarikciler, :onay_durum, :planlama_durum, :tekil_kod)";

            $asama_sayisi           = count($_POST["alt_urun_{$i}"]['departman']);
            $departmanlar           = array_reverse(array_map('intval',$_POST["alt_urun_{$i}"]['departman'])); 
            $adetler                = array_reverse(array_map('intval',$_POST["alt_urun_{$i}"]['adet']));
            
            $sureler                = array_reverse(array_map('floatval',$_POST["alt_urun_{$i}"]['sure']));
            $detaylar               = array_reverse($_POST["alt_urun_{$i}"]['detay']);
            $makinalar              = array_reverse(array_map('intval',$_POST["alt_urun_{$i}"]['makina']));
            $alt_arsiv              = array2DStringToInt(array_reverse($_POST["alt_urun_{$i}"]['alt_arsiv']));
            $stok_alt_kalemler      = array2DStringToInt(array_reverse($_POST["alt_urun_{$i}"]['stok_alt_kalem']));
            $stok_alt_depo_adetler  = array2DStringToInt(array_reverse($_POST["alt_urun_{$i}"]['stok_alt_depo_adet']));
            $stok_alt_depolar       = array2DStringToInt(array_reverse($_POST["alt_urun_{$i}"]['stok_alt_depo']));
            $fason_durumlar         = array_reverse(array_map('intval', $_POST["alt_urun_{$i}"]['fason_durum']));
            $fason_tedarikciler     = array_reverse(array_map('intval',$_POST["alt_urun_{$i}"]['fason_tedarikci']));
        
            $sth = $conn->prepare($sql);
            $sth->bindParam("firma_id",             $_SESSION['firma_id']);
            $sth->bindParam("siparis_id",           $siparis_id);
            $sth->bindParam("alt_urun_id",          $i);
            $sth->bindParam("isim",                 $_POST["alt_urun_{$i}"]['isim']);
            $sth->bindParam("asama_sayisi",         $asama_sayisi);
            $sth->bindValue('uretilecek_adet',      preg_replace("/[^0-9]/",'',$_POST["alt_urun_{$i}"]['uretilecek_adet']));
            $sth->bindValue("departmanlar",         json_encode($departmanlar));
            $sth->bindValue("adetler",              json_encode($adetler));
            $sth->bindValue("sureler",              json_encode($sureler));
            $sth->bindValue("detaylar",             json_encode($detaylar,JSON_UNESCAPED_UNICODE));
            $sth->bindValue("makinalar",            json_encode($makinalar));
            $sth->bindValue("arsiv_altlar",         json_encode($alt_arsiv));
            $sth->bindValue("stok_alt_kalemler",    json_encode($stok_alt_kalemler));
            $sth->bindValue("stok_alt_depo_adetler",json_encode($stok_alt_depo_adetler));
            $sth->bindValue("stok_alt_depolar",     json_encode($stok_alt_depolar));
            $sth->bindValue("fason_durumlar",       json_encode($fason_durumlar));
            $sth->bindValue("fason_tedarikciler",   json_encode($fason_tedarikciler));
            $sth->bindValue("onay_durum",           $onay_durum);
            $sth->bindValue("planlama_durum",       $planlama_durum);
            $sth->bindValue("tekil_kod",            uniqid());
            $durum = $sth->execute();
        }
    }

    
    if($durum)
    {
        $_SESSION['durum'] = 'success';
        $_SESSION['mesaj'] = 'İşlem Başarılı';
        header("Location: planlama.php");
    }
    else 
    {
        $_SESSION['durum'] = 'error';
        $_SESSION['mesaj'] = 'İşlem Başarısız';
        header("Location: planla_siparis_duzenle.php?siparis_id={$siparis_id}");
    }
    exit;
}

#tedarikçileri getir
if(isset($_GET['islem']) && $_GET['islem'] == 'tedarikcileri_getir'){
    $sth = $conn->prepare('SELECT * FROM tedarikciler WHERE firma_id = :firma_id AND fason = "evet"');
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $tedarikciler = $sth->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['tedarikciler' => $tedarikciler]);exit;
}

#departmana özel makinaları getir
if(isset($_GET['islem']) && $_GET['islem'] == 'departmanin_makinalari'){
    
    $sth = $conn->prepare('SELECT * FROM makinalar 
        WHERE firma_id = :firma_id AND departman_id = :departman_id AND durumu = "aktif"');
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->bindParam('departman_id', $_GET['departman_id']);
    $sth->execute();
    $makinalar = $sth->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['makinalar' => $makinalar]);exit;
}

if(isset($_GET['islem']) && $_GET['islem'] == 'departmanin_birimini_getir'){
    $sql = "SELECT birimler.ad FROM `departman_planlama` JOIN birimler ON birimler.id =  departman_planlama.birim_id
    WHERE departman_planlama.firma_id = :firma_id AND departman_planlama.departman_id = :departman_id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->bindParam('departman_id', $_GET['departman_id']);
    $sth->execute();
    $birim = $sth->fetch(PDO::FETCH_ASSOC);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['birim' => $birim]);exit;
}


//departmanin ve siparişin alt arşivlerini getirme
if(isset($_GET['islem']) && $_GET['islem'] == 'siparis_alt_arsiv'){
    $sql = "SELECT * FROM `arsiv_kalemler` WHERE firma_id = :firma_id AND departman_id = :departman_id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->bindParam('departman_id', $_GET['departman_id']);
    $sth->execute();
    $arsivler = $sth->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT arsiv_getirme FROM `firmalar` WHERE id = :id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('id', $_SESSION['firma_id']);
    $sth->execute();
    $firma_ayar = $sth->fetch(PDO::FETCH_ASSOC);



    foreach ($arsivler as $index=>$arsiv) {

        $sql = "SELECT * FROM `arsiv_altlar` WHERE arsiv_id = :arsiv_id ";
        if($firma_ayar['arsiv_getirme'] == 'siparise_ozel'){
            $sql .= " AND siparis_id = :siparis_id";
        }

        $sth = $conn->prepare($sql);
        $sth->bindParam('arsiv_id', $arsiv['id']);
        
        if($firma_ayar['arsiv_getirme'] == 'siparise_ozel'){
            $sth->bindParam('siparis_id', $_GET['siparis_id']);
        }
        $sth->execute();
        $alt_arsivler = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($alt_arsivler)){ 
            unset($arsivler[$index]);
        }
        else{
            $arsivler[$index]['alt_arsivler'] = $alt_arsivler;
        }
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['arsivler' => $arsivler]);exit;
}

//alt arsiv resimlerini getir
if(isset($_GET['islem']) && $_GET['islem'] == 'alt_arsiv_resim_getir'){
    $sql = "SELECT * FROM `arsiv_alt_dosyalar` WHERE firma_id = :firma_id AND arsiv_alt_id = :arsiv_alt_id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->bindParam('arsiv_alt_id', $_GET['arsiv_alt_id']);
    $sth->execute();
    $alt_arsiv_resimler = $sth->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['alt_arsiv_resimler' => $alt_arsiv_resimler]);exit;
}

//stoklari getir stoklari_getir
if(isset($_GET['islem']) && $_GET['islem'] == 'stoklari_getir'){
    $sql = "SELECT departman_planlama.stok
        FROM `departman_planlama` 
        WHERE firma_id = :firma_id AND `departman_id` = :departman_id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->bindParam('departman_id', $_GET['departman_id']);
    $sth->execute();
    $departman_stok             = $sth->fetch(PDO::FETCH_ASSOC);
    $departman_stoklar          = json_decode($departman_stok['stok'], true);
    $stoklar                    = [];
    $stok_alt_kalemler_sonuc    = [];

    foreach ($departman_stoklar as $key => $stok_id) {
        $sql = "SELECT id,stok_kalem FROM `stok_kalemleri`  WHERE id = :id";
        $sth = $conn->prepare($sql);
        $sth->bindParam('id', $stok_id);
        $sth->execute();
        $stok_kalem = $sth->fetch(PDO::FETCH_ASSOC);
        if($stok_kalem){
            $sql = "SELECT stok_alt_kalemler.id, stok_alt_kalemler.veri,stok_alt_kalemler.toplam_stok
                    FROM `stok_alt_kalemler` 
                    WHERE stok_alt_kalemler.firma_id = :firma_id 
                    AND stok_alt_kalemler.stok_id = :stok_id";
            $sth = $conn->prepare($sql);
            $sth->bindParam('firma_id', $_SESSION['firma_id']);
            $sth->bindParam('stok_id', $stok_id);
            $sth->execute();
            $stok_alt_kalemler = $sth->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($stok_alt_kalemler)){
                $stoklar[] = $stok_kalem;
                $stok_alt_kalemler_sonuc[] = $stok_alt_kalemler;
            }
        }
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'stoklar'           => $stoklar, 
        'stok_alt_kalemler' =>$stok_alt_kalemler_sonuc
    ]);
    exit;
}


// stok alt depoları getir
if(isset($_GET['islem']) && $_GET['islem'] == 'stok_alt_depo_getir'){
    $siparis_id         = intval($_POST['siparis_id']);
    $stok_alt_kalem_id  = intval($_POST['stok_alt_kalem_id']);

    $sql = "SELECT stok_alt_depo_kod  FROM `siparisler`  WHERE id = :id AND firma_id = :firma_id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('id', $siparis_id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $siparis = $sth->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT stok_alt_depolar.id, (stok_alt_depolar.adet - stok_alt_depolar.kullanilan_adet) AS kalan_adet,
            stok_alt_depolar.fatura_no,stok_alt_depolar.siparis_no,`stok_alt_depolar`.`stok_kodu`,
            `tedarikciler`.`firma_adi`
            FROM `stok_alt_depolar` 
            JOIN `tedarikciler` ON `tedarikciler`.id = stok_alt_depolar.tedarikci_id
            WHERE stok_alt_depolar.stok_alt_kalem_id = :stok_alt_kalem_id 
            AND stok_alt_depolar.firma_id = :firma_id
            -- AND (stok_alt_depolar.stok_alt_depo_kod  = :stok_alt_depo_kod  OR stok_alt_depolar.stok_alt_depo_kod  IS NULL)
            AND (stok_alt_depolar.adet - stok_alt_depolar.kullanilan_adet) > 0
            ORDER BY stok_alt_depolar.stok_alt_depo_kod DESC";
    $sth = $conn->prepare($sql);
    $sth->bindParam('stok_alt_kalem_id', $stok_alt_kalem_id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    #$sth->bindParam('stok_alt_depo_kod', $siparis['stok_alt_depo_kod']);
    $sth->execute();
    $stok_alt_depolar = $sth->fetchAll(PDO::FETCH_ASSOC);

    //Firmanın Reservasyonları
    $sql = "SELECT `stok_alt_depo_id`, (SUM(`miktar`) - SUM(`kullanilan_miktar`)) reservasyon_miktari 
            FROM `uretim_reservasyon` 
            WHERE `firma_id` = :firma_id GROUP BY `stok_alt_depo_id`; ";
    $sth = $conn->prepare($sql);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $reservasyonlar = $sth->fetchAll(PDO::FETCH_ASSOC);

    foreach ($stok_alt_depolar as $index => $stok_alt_depo) {
        foreach ($reservasyonlar as $reservasyon) {
            if($reservasyon['stok_alt_depo_id'] == $stok_alt_depo['id']){
                $stok_alt_depolar[$index]['kalan_adet']  = $stok_alt_depo['kalan_adet'] - $reservasyon['reservasyon_miktari'];
                break;
            }
        }
    }

    $sql = "SELECT birimler.ad FROM `stok_alt_kalemler` 
            JOIN birimler ON birimler.id = stok_alt_kalemler.birim_id
            WHERE stok_alt_kalemler.id = :id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('id', $stok_alt_kalem_id);
    $sth->execute();
    $birim = $sth->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'stok_alt_depolar'  =>  $stok_alt_depolar, 
        'birim'             =>  $birim,
    ]); exit;
}

if(isset($_GET['islem']) && $_GET['islem'] == 'stok_alt_kalem_getir'){
    $sql = "SELECT * FROM `stok_alt_kalemler` WHERE stok_id = :stok_id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('stok_id', $_POST['stok_id']);
    $sth->execute();
    $stok_alt_kalemler = $sth->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM `stok_kalemleri` WHERE id = :id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('id', $_POST['stok_id']);
    $sth->execute();
    $stok_kalem = $sth->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'stok_alt_kalemler' =>$stok_alt_kalemler, 
        'stok_kalem'        =>$stok_kalem
    ]);exit;
}
