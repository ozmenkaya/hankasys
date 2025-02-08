<?php
#echo "<pre>"; print_r($_FILES);
#echo "<pre>"; print_r($_POST); 
#echo "<pre>"; print_r($_GET); exit;

require_once "include/db.php";
require_once "include/oturum_kontrol.php";
require_once "include/SimpleXLSXGen.php";

//sipariş resim sil
if(isset($_POST['islem']) && $_POST['islem'] == 'siparis_dosya_sil'){
    $id         = intval($_POST['id']);
    $resim_ad   = $_POST['resim_ad'];

    unlink("dosyalar/siparisler/{$resim_ad}");

    $sql = "DELETE FROM siparis_dosyalar WHERE id=:id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('id', $id);
    $durum = $sth->execute(); 

    echo json_encode(['durum' => $durum]);
}

if(isset($_POST['islem']) && $_POST['islem'] == 'tur_id_gore_siparis_form_getir'){
    $tur_id = $_POST['tur_id'];
    $sth = $conn->prepare('SELECT * FROM siparis_form WHERE firma_id = :firma_id');
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $siparis_formlar = $sth->fetchAll(PDO::FETCH_ASSOC);

    $siparis_formlar_sonuc = [];

    foreach ($siparis_formlar as $key => $siparis_form) {
        $tur_idler = json_decode($siparis_form['tur_idler'], true);
        if(in_array($tur_id, $tur_idler)){
            $siparis_formlar_sonuc[] = $siparis_form;
        }
    }
    echo json_encode(['siparis_formlar'=>$siparis_formlar_sonuc]);
}

#siparis ekle
if(isset($_POST['siparis_ekle']))
{
    $musteri_id             = intval($_POST['musteri_id']);
    $veriler                = [];
    $tip_id                 = $_POST['tip'];
    $arsiv_kod              = random_int(100_000, 999_999);
    $stok_alt_depo_kod      = uniqid();
    $isin_adi               = trim($_POST['isin_adi']);
    $tur_id                 = $_POST['tur_id'];
    $teslimat_adresi        = trim($_POST['teslimat_adresi']);
    $ulke_id                = $_POST['ulke_id'];
    $sehir_id               = $_POST['sehir_id'];
    $ilce_id                = $_POST['ilce_id'];
    $termin                 = $_POST['termin'];
    $uretim                 = $_POST['uretim'];
    $vade                   = $_POST['vade'];
    $odeme_sekli_id         = $_POST['odeme_sekli_id'];
    $musteri_temsilcisi_id  = $_POST['musteri_temsilcisi_id'];
    $paketleme              = trim($_POST['paketleme']);
    $nakliye                = trim($_POST['nakliye']);
    $alt_urun_sayisi        = intval($_POST['alt_urun_sayisi']);


    $sql = 'SELECT siparis_no FROM `siparisler` WHERE firma_id = :firma_id ORDER BY id DESC';
    $sth = $conn->prepare($sql);
    $sth->bindParam("firma_id", $_SESSION['firma_id']);
    $sth->execute();
    $siparis_adedi = $sth->fetch(PDO::FETCH_ASSOC);
    $siparis_adedi = empty($siparis_adedi) ? 1 : preg_replace('/[^0-9]/', '',$siparis_adedi['siparis_no']) + 1;

    $sql = 'SELECT siparis_no_baslangic_kodu FROM `firmalar` WHERE id = :id';
    $sth = $conn->prepare($sql);
    $sth->bindParam("id", $_SESSION['firma_id']);
    $sth->execute();
    $firma_bilgi = $sth->fetch(PDO::FETCH_ASSOC);

    //Sipariş No Tekil Olmalı
    $siparis_no             = $firma_bilgi['siparis_no_baslangic_kodu'].str_pad($siparis_adedi,  4, "0", STR_PAD_LEFT);

    if($tip_id == GRUP_URUN_AYRI_FIYAT){  
        $fiyat      = 0;
        $adet       = intval($_POST['grup_ayri_fiyat_adet']);
        $birim_id   = intval($_POST['grup_ayri_fiyat_birim_id']);
        $para_cinsi = $_POST['grup_ayri_fiyat_para_cinsi'];

        for($i = 1; $i <= $alt_urun_sayisi; $i++){
            $veriler[]  = $_POST["grup_ayri_fiyat_alt_urun_{$i}"];
            $fiyat      += intval($_POST["grup_ayri_fiyat_alt_urun_{$i}"]['miktar']) * floatval($_POST["grup_ayri_fiyat_alt_urun_{$i}"]['birim_fiyat']);
        }
    } else if($tip_id == GRUP_URUN_TEK_FIYAT){
        $adet       = intval($_POST['grup_tek_fiyat_adet']);
        $birim_id   = intval($_POST['grup_tek_fiyat_birim_id']);
        $para_cinsi = $_POST['grup_tek_fiyat_para_cinsi'];
        $fiyat      = intval($_POST['grup_tek_fiyat_adet'])*floatval($_POST['grup_tek_fiyat_birim_fiyat']);
        for($i = 1; $i <= $alt_urun_sayisi; $i++){
            $_POST["grup_tek_fiyat_alt_urun_{$i}"]['kdv']           = $_POST['grup_tek_fiyat_kdv'];
            $_POST["grup_tek_fiyat_alt_urun_{$i}"]['birim_fiyat']   = floatval($_POST['grup_tek_fiyat_birim_fiyat']);
            $veriler[]                                              = $_POST["grup_tek_fiyat_alt_urun_{$i}"];
        }
    } else if($tip_id == TEK_URUN){
        $adet       = intval($_POST['tek_fiyat_adet']);
        $birim_id   = intval($_POST['tek_fiyat_birim_id']);
        $para_cinsi = $_POST['tek_fiyat_para_cinsi'];
        $fiyat      = intval($_POST['tek_fiyat_adet'])*floatval($_POST['tek_fiyat_birim_fiyat']);
        $veriler = [
            "kdv"           => $_POST['tek_fiyat_kdv'],
            "form"          => isset($_POST['tek_fiyat_form']) ? $_POST['tek_fiyat_form'] : null,
            'isim'          => $_POST['isin_adi'],
            'miktar'        => $adet,
            'numune'        => $_POST['tek_fiyat_numune'],
            'aciklama'      => trim($_POST['tek_fiyat_aciklama']),
            'birim_id'      => $birim_id,
            'birim_fiyat'   => floatval($_POST['tek_fiyat_birim_fiyat'])
        ];
    }else{
        exit("Yeni Bir Tip Eklemedi..");
    }


    $sql = "INSERT INTO siparisler(firma_id, musteri_id, siparis_no, veriler, tip_id, arsiv_kod,  isin_adi, tur_id, 
                        adet,birim_id,
                        teslimat_adresi, ulke_id, sehir_id, ilce_id, termin, uretim, vade, fiyat, para_cinsi, 
                        odeme_sekli_id, musteri_temsilcisi_id, paketleme, nakliye, stok_alt_depo_kod, takip_kodu) 
            VALUES(:firma_id, :musteri_id, :siparis_no, :veriler, :tip_id, :arsiv_kod, :isin_adi, :tur_id, :adet,:birim_id, :teslimat_adresi, 
            :ulke_id, :sehir_id, :ilce_id, :termin, :uretim, :vade, :fiyat, :para_cinsi, :odeme_sekli_id, 
            :musteri_temsilcisi_id, :paketleme, :nakliye, :stok_alt_depo_kod, :takip_kodu);";
    $sth = $conn->prepare($sql);
    $sth->bindParam("firma_id", $_SESSION['firma_id']);
    $sth->bindParam("musteri_id", $musteri_id);
    $sth->bindParam("siparis_no", $siparis_no);
    $sth->bindValue("veriler", json_encode($veriler));
    $sth->bindValue("tip_id", $tip_id);
    $sth->bindParam("arsiv_kod", $arsiv_kod);
    $sth->bindParam("isin_adi", $isin_adi);
    $sth->bindParam("tur_id", $tur_id);
    $sth->bindParam("adet", $adet);
    $sth->bindParam("birim_id", $birim_id);
    $sth->bindParam("teslimat_adresi", $teslimat_adresi);
    $sth->bindParam("ulke_id", $ulke_id);
    $sth->bindParam("sehir_id", $sehir_id);
    $sth->bindParam("ilce_id", $ilce_id);
    $sth->bindParam("termin", $termin);
    $sth->bindParam("uretim", $uretim);
    $sth->bindParam("vade", $vade);
    $sth->bindParam("fiyat", $fiyat);
    $sth->bindParam("para_cinsi", $para_cinsi);
    $sth->bindParam("odeme_sekli_id", $odeme_sekli_id);
    $sth->bindParam("musteri_temsilcisi_id", $musteri_temsilcisi_id);
    $sth->bindParam("paketleme", $paketleme);
    $sth->bindParam("nakliye", $nakliye);
    $sth->bindParam("stok_alt_depo_kod", $stok_alt_depo_kod);
    $sth->bindValue("takip_kodu", uuid4());
    $durum = $sth->execute();
    $siparis_id = $conn->lastInsertId();

    $hedef_klasor = "dosyalar/siparisler/";

    if($tip_id == GRUP_URUN_AYRI_FIYAT || $tip_id == GRUP_URUN_TEK_FIYAT){
        for($alt_urun_index = 1; $alt_urun_index <= $alt_urun_sayisi; $alt_urun_index++){
            
            if($tip_id == GRUP_URUN_AYRI_FIYAT)
                $dosyalar = $_FILES["grup_ayri_fiyat_alt_urun_{$alt_urun_index}"];
            else
                $dosyalar = $_FILES["grup_tek_fiyat_alt_urun_{$alt_urun_index}"];
                
            for($i = 0; $i < count($dosyalar['name']); $i++)
            {
                $dosya_adi     = pathinfo($dosyalar['name'][$i], PATHINFO_FILENAME)."_".random_int(1000, 99999);
                $dosya_uzanti  = pathinfo($dosyalar['name'][$i], PATHINFO_EXTENSION);

                $dosya_adi = "{$dosya_adi}.{$dosya_uzanti}";

                if (move_uploaded_file($dosyalar["tmp_name"][$i], $hedef_klasor.$dosya_adi)) 
                {
                    $sql = "INSERT INTO  siparis_dosyalar(siparis_id, alt_urun_index, ad) 
                            VALUES(:siparis_id, :alt_urun_index, :ad)";
                    $sth = $conn->prepare($sql);
                    $sth->bindParam("siparis_id", $siparis_id);
                    $sth->bindValue("alt_urun_index", $alt_urun_index-1);
                    $sth->bindParam("ad", $dosya_adi);
                    $durum = $sth->execute();
                }
            }

        }
    }else if($tip_id == TEK_URUN){
        if(isset($_FILES['tek_fiyat_dosya'])){
            $dosyalar = $_FILES['tek_fiyat_dosya'];
            for($i = 0; $i < count($dosyalar['name']); $i++)
            {
                $dosya_adi     = pathinfo($dosyalar['name'][$i], PATHINFO_FILENAME)."_".random_int(1000, 99999);
                $dosya_uzanti  = pathinfo($dosyalar['name'][$i], PATHINFO_EXTENSION);

                $dosya_adi = "{$dosya_adi}.{$dosya_uzanti}";

                if (move_uploaded_file($dosyalar["tmp_name"][$i], $hedef_klasor.$dosya_adi)) 
                {
                    $sql = "INSERT INTO  siparis_dosyalar(siparis_id, alt_urun_index, ad) 
                            VALUES(:siparis_id, :alt_urun_index, :ad)";
                    $sth = $conn->prepare($sql);
                    $sth->bindParam("siparis_id", $siparis_id);
                    $sth->bindValue("alt_urun_index", 0);
                    $sth->bindParam("ad", $dosya_adi);
                    $durum = $sth->execute();
                }
            }
        }
    }


    if($durum == true)
    {
        $_SESSION['durum'] = 'success';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarılı';
        header("Location: siparis.php?musteri_id={$musteri_id}");
    }
    else 
    {
        $_SESSION['durum'] = 'error';
        $_SESSION['mesaj'] = 'Ekleme İşlemi Başarısız';
        header("Location: siparis_ekle.php?musteri_id={$musteri_id}");
    }
    die();
}


#siparis güncelle
if(isset($_POST['siparis_guncelle']))
{
    
    //echo "<pre>"; print_r($_POST); exit;
    //echo "<pre>"; print_r($_FILES); exit;

    $siparis_id             = $_POST['siparis_id'];
    $musteri_id             = $_POST['musteri_id'];
    $firma_id               = $_SESSION['firma_id'];
    $veriler                = [];
    $tip_id                 = $_POST['tip'];
    $isin_adi               = $_POST['isin_adi'];
    $tur_id                 = $_POST['tur_id'];
    $adet                   = 0;
    $birim_id               = 0;
    $teslimat_adresi        = $_POST['teslimat_adresi'];
    $ulke_id                = $_POST['ulke_id'];
    $sehir_id               = $_POST['sehir_id'];
    $ilce_id                = $_POST['ilce_id'];
    $termin                 = $_POST['termin'];
    $uretim                 = $_POST['uretim'];
    $vade                   = $_POST['vade'];
    $odeme_sekli_id         = $_POST['odeme_sekli_id'];
    $odeme_sekli_id         = $_POST['odeme_sekli_id'];
    $musteri_temsilcisi_id  = $_POST['musteri_temsilcisi_id'];
    $paketleme              = $_POST['paketleme'];
    $nakliye                = $_POST['nakliye'];
    $alt_urun_sayisi        = $_POST['alt_urun_sayisi'];
    $eski_tip_id            = $_POST['eski_tip_id'];

    if($eski_tip_id != $tip_id ){
        $sth = $conn->prepare('SELECT ad FROM siparis_dosyalar');
        $sth->execute();
        $siparis_dosyalar = $sth->fetchAll(PDO::FETCH_ASSOC);

        foreach ($siparis_dosyalar as $siparis_dosya) {
            unlink("dosyalar/siparisler/{$siparis_dosyalar['ad']}");
        }

        $sql = "DELETE FROM siparis_dosyalar WHERE siparis_id = :siparis_id";
        $sth = $conn->prepare($sql);
        $sth->bindParam('siparis_id', $siparis_id);
        $sth->execute(); 
    }

    
    if($tip_id == GRUP_URUN_AYRI_FIYAT){  
        $adet       = $_POST['grup_ayri_fiyat_adet'];
        $birim_id   = $_POST['grup_ayri_fiyat_birim_id'];
        $para_cinsi = $_POST['grup_ayri_fiyat_para_cinsi'];

        for($i = 1; $i <= $alt_urun_sayisi; $i++){
            $veriler[] = $_POST["grup_ayri_fiyat_alt_urun_{$i}"];
            $fiyat      += intval($_POST["grup_ayri_fiyat_alt_urun_{$i}"]['miktar']) * floatval($_POST["grup_ayri_fiyat_alt_urun_{$i}"]['birim_fiyat']);
        }
    }
    else if($tip_id == GRUP_URUN_TEK_FIYAT){
        $adet       = $_POST['grup_tek_fiyat_adet'];
        $birim_id   = $_POST['grup_tek_fiyat_birim_id'];
        $para_cinsi = $_POST['grup_tek_fiyat_para_cinsi'];
        $fiyat      = intval($_POST['grup_tek_fiyat_adet'])*floatval($_POST['grup_tek_fiyat_birim_fiyat']);
        for($i = 1; $i <= $alt_urun_sayisi; $i++){
            $_POST["grup_tek_fiyat_alt_urun_{$i}"]['kdv']           = $_POST['grup_tek_fiyat_kdv'];
            $_POST["grup_tek_fiyat_alt_urun_{$i}"]['birim_fiyat']   = $_POST['grup_tek_fiyat_birim_fiyat'];
            $veriler[]                                              = $_POST["grup_tek_fiyat_alt_urun_{$i}"];
        }
    }else if($tip_id == TEK_URUN){
        $adet       = $_POST['tek_fiyat_adet'];
        $birim_id   = $_POST['tek_fiyat_birim_id'];
        $para_cinsi = $_POST['tek_fiyat_para_cinsi'];
        $fiyat      = $_POST['tek_fiyat_adet']*$_POST['tek_fiyat_birim_fiyat'];
        $veriler = [
            "kdv"           => $_POST['tek_fiyat_kdv'],
            "form"          => $_POST['tek_fiyat_form'],
            'isim'          => $_POST['isin_adi'],
            'miktar'        => $adet,
            'numune'        => $_POST['tek_fiyat_numune'],
            'aciklama'      => $_POST['tek_fiyat_aciklama'],
            'birim_id'      => $birim_id,
            'birim_fiyat'   => $_POST['tek_fiyat_birim_fiyat']
        ];
    }

    $sql = "UPDATE siparisler SET veriler = :veriler, tip_id = :tip_id, isin_adi = :isin_adi,tur_id = :tur_id,
            adet = :adet, birim_id = :birim_id, teslimat_adresi = :teslimat_adresi,ulke_id = :ulke_id, sehir_id = :sehir_id,
            ilce_id = :ilce_id,termin = :termin, uretim = :uretim, vade = :vade, fiyat = :fiyat,
            nakliye = :nakliye, paketleme = :paketleme, para_cinsi = :para_cinsi,  odeme_sekli_id = :odeme_sekli_id,numune = :numune,
            musteri_temsilcisi_id = :musteri_temsilcisi_id
            WHERE id = :id AND firma_id = :firma_id;";

    $sth = $conn->prepare($sql);
    $sth->bindValue("veriler", json_encode($veriler));
    $sth->bindValue("tip_id", $tip_id);
    $sth->bindParam('isin_adi', $isin_adi);
    $sth->bindParam('tur_id', $tur_id);
    $sth->bindParam("adet", $adet);
    $sth->bindParam("birim_id", $birim_id);
    $sth->bindParam("teslimat_adresi", $teslimat_adresi);
    $sth->bindParam("ulke_id", $ulke_id);
    $sth->bindParam("sehir_id", $sehir_id);
    $sth->bindParam("ilce_id", $ilce_id);
    $sth->bindParam("termin", $termin);
    $sth->bindParam("uretim", $uretim);
    $sth->bindParam("vade", $vade);
    $sth->bindParam("fiyat", $fiyat);
    $sth->bindParam("para_cinsi", $para_cinsi);
    $sth->bindParam("odeme_sekli_id", $odeme_sekli_id);
    $sth->bindParam("numune", $numune);
    $sth->bindParam("musteri_temsilcisi_id", $musteri_temsilcisi_id);
    $sth->bindParam('nakliye', $nakliye);
    $sth->bindParam('paketleme', $paketleme);
    $sth->bindParam('id', $siparis_id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $durum = $sth->execute();

    //Planlaması varsa Üretilecek Adeti Güncelle (Sipariş Tekrarda Sıktı Oluyor)
    if($tip_id == TEK_URUN){
        $adet   = intval($_POST['tek_fiyat_adet']);
        $sql    = "UPDATE planlama SET uretilecek_adet = :uretilecek_adet WHERE siparis_id = :siparis_id";
        $sth    = $conn->prepare($sql);
        $sth->bindParam('uretilecek_adet', $adet);
        $sth->bindParam('siparis_id', $siparis_id);
        $sth->execute();
    }else if(in_array($tip_id, [GRUP_URUN_TEK_FIYAT, GRUP_URUN_AYRI_FIYAT])){
        for($alt_urun_index = 1; $alt_urun_index <= $alt_urun_sayisi; $alt_urun_index++){
            $adet   = $tip_id ==  GRUP_URUN_TEK_FIYAT ? 
                    intval($_POST["grup_tek_fiyat_alt_urun_{$alt_urun_index}"]['miktar']) :
                    intval($_POST["grup_ayri_fiyat_alt_urun_{$alt_urun_index}"]['miktar']);
            $sql    = "UPDATE planlama SET uretilecek_adet = :uretilecek_adet WHERE siparis_id = :siparis_id AND alt_urun_id = :alt_urun_id";
            $sth    = $conn->prepare($sql);
            $sth->bindParam('uretilecek_adet', $adet);
            $sth->bindParam('siparis_id', $siparis_id);
            $sth->bindParam('alt_urun_id', $alt_urun_index);
            $sth->execute();
        }
    }


    if($durum)
    {
        $sql = "SELECT * FROM siparisler WHERE id = :id";
        $sth = $conn->prepare($sql);
        $sth->bindParam('id', $siparis_id);
        $sth->execute();
        $siparis_eski_hali = $sth->fetch(PDO::FETCH_ASSOC);

        $sql = "INSERT INTO siparis_log(siparis_id, personel_id, islem, onceki_degerler, sonraki_degerler) 
            VALUES(:siparis_id, :personel_id, :islem, :onceki_degerler, :sonraki_degerler);";
        $sth = $conn->prepare($sql);
        $sth->bindParam("siparis_id", $siparis_id);
        $sth->bindParam("personel_id", $_SESSION['personel_id']);
        $sth->bindValue('islem', 'Güncelleme');
        $sth->bindValue('onceki_degerler', json_encode($siparis_eski_hali));
        $sth->bindValue('sonraki_degerler', json_encode($_POST));
        $durum = $sth->execute();

        $hedef_klasor = "dosyalar/siparisler/";
        if($tip_id == GRUP_URUN_AYRI_FIYAT || $tip_id == GRUP_URUN_TEK_FIYAT){
            for($alt_urun_index = 1; $alt_urun_index <= $alt_urun_sayisi; $alt_urun_index++){
                
                if($tip_id == GRUP_URUN_AYRI_FIYAT)
                    $dosyalar = $_FILES["grup_ayri_fiyat_alt_urun_{$alt_urun_index}"];
                else
                    $dosyalar = $_FILES["grup_tek_fiyat_alt_urun_{$alt_urun_index}"];
                    
                for($i = 0; $i < count($dosyalar['name']); $i++)
                {
                    $dosya_adi     = pathinfo($dosyalar['name'][$i], PATHINFO_FILENAME)."_".random_int(1000, 99999);
                    $dosya_uzanti  = pathinfo($dosyalar['name'][$i], PATHINFO_EXTENSION);
    
                    $dosya_adi = "{$dosya_adi}.{$dosya_uzanti}";
    
                    if (move_uploaded_file($dosyalar["tmp_name"][$i], $hedef_klasor.$dosya_adi)) 
                    {
                        $sql = "INSERT INTO  siparis_dosyalar(siparis_id, alt_urun_index, ad) 
                                VALUES(:siparis_id, :alt_urun_index, :ad)";
                        $sth = $conn->prepare($sql);
                        $sth->bindParam("siparis_id", $siparis_id);
                        $sth->bindValue("alt_urun_index", $alt_urun_index-1);
                        $sth->bindParam("ad", $dosya_adi);
                        $durum = $sth->execute();
                    }
                }
    
            }
        } else if($tip_id == TEK_URUN){
            if(isset($_FILES['tek_fiyat_dosya'])){
                $dosyalar = $_FILES['tek_fiyat_dosya'];
                for($i = 0; $i < count($dosyalar['name']); $i++)
                {
                    $dosya_adi      = pathinfo($dosyalar['name'][$i], PATHINFO_FILENAME)."_".random_int(1000, 99999);
                    $dosya_uzanti  = pathinfo($dosyalar['name'][$i], PATHINFO_EXTENSION);
    
                    $dosya_adi = "{$dosya_adi}.{$dosya_uzanti}";
    
                    if (move_uploaded_file($dosyalar["tmp_name"][$i], $hedef_klasor.$dosya_adi)) 
                    {
                        $sql = "INSERT INTO  siparis_dosyalar(siparis_id, alt_urun_index, ad) 
                                VALUES(:siparis_id, :alt_urun_index, :ad)";
                        $sth = $conn->prepare($sql);
                        $sth->bindParam("siparis_id", $siparis_id);
                        $sth->bindValue("alt_urun_index", 0);
                        $sth->bindParam("ad", $dosya_adi);
                        $durum = $sth->execute();
                    }
                }
            }
        }
    }

    

    if($durum == true)
    {
        $_SESSION['durum'] = 'success';
        $_SESSION['mesaj'] = 'Güncelleme İşlemi Başarılı';
        header("Location: siparis.php?musteri_id={$musteri_id}");
    }
    else 
    {
        $_SESSION['durum'] = 'error';
        $_SESSION['mesaj'] = 'Güncelleme İşlemi Başarısız';
        header("Location: siparis_guncelle.php?siparis_id={$siparis_id}");
    }
    die();

}

//sipariş tekrarla
if(isset($_GET['islem']) && $_GET['islem'] == 'siparis-tekrar')
{
    $siparis_id = intval($_GET['siparis-id']);

    $sth = $conn->prepare('SELECT * FROM siparisler WHERE id = :id AND firma_id = :firma_id');
    $sth->bindParam('id', $siparis_id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $siparis = $sth->fetch(PDO::FETCH_ASSOC);

    if(empty($siparis)){
        include_once "include/yetkisiz.php"; exit;
    }

    $sql = 'SELECT siparis_no FROM `siparisler` WHERE firma_id = :firma_id ORDER BY id DESC';
    $sth = $conn->prepare($sql);
    $sth->bindParam("firma_id", $_SESSION['firma_id']);
    $sth->execute();
    $siparis_adedi = $sth->fetch(PDO::FETCH_ASSOC);
    $siparis_adedi = empty($siparis_adedi) ? 1 : preg_replace('/[^0-9]/', '',$siparis_adedi['siparis_no']) + 1;

    $sql = 'SELECT siparis_no_baslangic_kodu FROM `firmalar` WHERE id = :id';
    $sth = $conn->prepare($sql);
    $sth->bindParam("id", $_SESSION['firma_id']);
    $sth->execute();
    $firma_bilgi = $sth->fetch(PDO::FETCH_ASSOC);

    $siparis_no     = $firma_bilgi['siparis_no_baslangic_kodu'].str_pad($siparis_adedi,  6, "0", STR_PAD_LEFT);


    $sql = 'SELECT * FROM planlama WHERE siparis_id = :siparis_id GROUP BY `planlama`.`grup_kodu`';
    $sth = $conn->prepare($sql);
    $sth->bindParam('siparis_id', $siparis['id']);
    $sth->execute();
    $farkli_group_kodu_olan_planlamalar = $sth->fetchAll(PDO::FETCH_ASSOC);
    //echo "<pre>"; print_r($farkli_group_kodu_olan_planlamalar);

    //siparişi Ekle
    $sql = "INSERT INTO siparisler(firma_id, musteri_id, siparis_no,siprais_tekrar_id, veriler,tip_id, arsiv_kod,  isin_adi, tur_id, 
                        adet,birim_id,
                        teslimat_adresi, ulke_id, sehir_id, ilce_id, termin, uretim, vade, fiyat, para_cinsi, 
                        odeme_sekli_id, numune, aciklama, musteri_temsilcisi_id, paketleme, nakliye, stok_alt_depo_kod, takip_kodu) 
            VALUES(:firma_id, :musteri_id, :siparis_no, :siprais_tekrar_id, :veriler,:tip_id, :arsiv_kod, :isin_adi, :tur_id,
                    :adet,:birim_id,:teslimat_adresi,:ulke_id, :sehir_id, :ilce_id, :termin, :uretim, :vade, :fiyat, :para_cinsi, 
                    :odeme_sekli_id, :numune, :aciklama, :musteri_temsilcisi_id, :paketleme, :nakliye, :stok_alt_depo_kod, :takip_kodu);";
    $sth = $conn->prepare($sql);
    $sth->bindParam("firma_id", $_SESSION['firma_id']);
    $sth->bindParam("musteri_id", $siparis['musteri_id']);
    $sth->bindParam("siparis_no", $siparis_no);
    $sth->bindParam("siprais_tekrar_id", $siparis['id']);
    $sth->bindParam("veriler", $siparis['veriler']);
    $sth->bindParam("tip_id", $siparis['tip_id']);
    $sth->bindParam("arsiv_kod", $siparis['arsiv_kod']);
    $sth->bindParam("isin_adi", $siparis['isin_adi']);
    $sth->bindParam("tur_id", $siparis['tur_id']);
    $sth->bindParam("adet", $siparis['adet']);
    $sth->bindParam("birim_id", $siparis['birim_id']);
    $sth->bindParam("teslimat_adresi", $siparis['teslimat_adresi']);
    $sth->bindParam("ulke_id", $siparis['ulke_id']);
    $sth->bindParam("sehir_id", $siparis['sehir_id']);
    $sth->bindParam("ilce_id", $siparis['ilce_id']);
    $sth->bindParam("termin", $siparis['termin']);
    $sth->bindParam("uretim", $siparis['uretim']);
    $sth->bindParam("vade", $siparis['vade']);
    $sth->bindParam("fiyat", $siparis['fiyat']);
    $sth->bindParam("para_cinsi", $siparis['para_cinsi']);
    $sth->bindParam("odeme_sekli_id", $siparis['odeme_sekli_id']);
    $sth->bindParam("numune", $siparis['numune']);
    $sth->bindParam("aciklama", $siparis['aciklama']);
    $sth->bindParam("musteri_temsilcisi_id", $siparis['musteri_temsilcisi_id']);
    $sth->bindParam("paketleme", $siparis['paketleme']);
    $sth->bindParam("nakliye", $siparis['nakliye']);
    $sth->bindParam("stok_alt_depo_kod", $siparis['stok_alt_depo_kod']);
    $sth->bindValue("takip_kodu", uuid4());
    $durum = $sth->execute();
    $eklenen_siparis_id = $conn->lastInsertId();

    $sth = $conn->prepare('SELECT * FROM `siparis_dosyalar` WHERE siparis_id = :siparis_id');
    $sth->bindParam('siparis_id', $siparis['id']);
    $sth->execute();
    $siparis_dosyalar = $sth->fetchAll(PDO::FETCH_ASSOC);

    //sipariş dosyaları Ekle
    foreach ($siparis_dosyalar as $siparis_dosya) {
        $sql = "INSERT INTO siparis_dosyalar(siparis_id, ad) VALUES(:siparis_id, :ad)";
        $sth = $conn->prepare($sql);
        $sth->bindParam("siparis_id", $eklenen_siparis_id);
        $sth->bindParam("ad", $siparis_dosya['ad']);
        $durum = $sth->execute();
    }
    
    foreach ($farkli_group_kodu_olan_planlamalar as $key => $planlama) {
        $toplam_adetler = array_fill(0, $planlama['asama_sayisi'], 0);
        $toplam_sureler = array_fill(0, $planlama['asama_sayisi'], 0);

        $sql = 'SELECT adetler,sureler,uretilecek_adet FROM planlama 
                WHERE siparis_id = :siparis_id AND `planlama`.`grup_kodu` = :grup_kodu AND aktar_durum = "orijinal"';
        $sth = $conn->prepare($sql);
        $sth->bindParam('siparis_id', $siparis['id']);
        $sth->bindParam('grup_kodu', $planlama['grup_kodu']);
        $sth->execute();
        $ayni_group_kodu_olan_planlamalar = $sth->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ayni_group_kodu_olan_planlamalar as $ayni_group_kodu_olan_planlama) {
            $adetler = json_decode($ayni_group_kodu_olan_planlama['adetler'], true);
            $sureler = json_decode($ayni_group_kodu_olan_planlama['sureler'], true);
            for($i = 0; $i < $planlama['asama_sayisi']; $i++){
                $toplam_adetler[$i] += $adetler[$i];
                $toplam_sureler[$i] += $sureler[$i];
            }
        }
        //echo "ayni_group_kodu_olan_planlamalar=>"; 
        //echo "<pre>"; print_r($ayni_group_kodu_olan_planlamalar);
        
        $sql = "INSERT INTO planlama(firma_id, siparis_id, alt_urun_id,grup_kodu, isim, asama_sayisi, mevcut_asama, uretilecek_adet, departmanlar, orijinal_adetler, adetler, 
                sureler, detaylar,makinalar,arsiv_altlar,stok_kalemler,stok_alt_kalemler, stok_alt_depo_adetler, stok_alt_depolar, fason_durumlar,
                fason_tedarikciler,notlar,planlama_durum,tekil_kod) 
                VALUES(:firma_id, :siparis_id, :alt_urun_id, :grup_kodu, :isim, :asama_sayisi, :mevcut_asama, :uretilecek_adet, :departmanlar,:orijinal_adetler, :adetler, 
                :sureler, :detaylar, :makinalar, :arsiv_altlar,:stok_kalemler, :stok_alt_kalemler, :stok_alt_depo_adetler, :stok_alt_depolar, :fason_durumlar, 
                :fason_tedarikciler, :notlar, :planlama_durum, :tekil_kod);";

        $sth = $conn->prepare($sql);
        $sth->bindParam("firma_id", $_SESSION['firma_id']);
        $sth->bindParam("siparis_id", $eklenen_siparis_id);
        $sth->bindParam("alt_urun_id", $planlama['alt_urun_id']);
        $sth->bindValue("grup_kodu", uniqid());
        $sth->bindParam("isim", $planlama['isim']);
        $sth->bindParam("asama_sayisi", $planlama['asama_sayisi']);
        $sth->bindValue("mevcut_asama", 0);
        $sth->bindValue("uretilecek_adet", end($toplam_adetler));
        $sth->bindParam("departmanlar", $planlama['departmanlar']);
        $sth->bindValue("orijinal_adetler", json_encode($toplam_adetler));
        $sth->bindValue("adetler", json_encode($toplam_adetler));
        $sth->bindValue("sureler", json_encode($toplam_sureler));
        $sth->bindParam("detaylar", $planlama['detaylar']);
        $sth->bindParam("makinalar", $planlama['makinalar']);
        $sth->bindParam("arsiv_altlar", $planlama['arsiv_altlar']);
        $sth->bindParam("stok_kalemler", $planlama['stok_kalemler']);
        $sth->bindParam("stok_alt_kalemler", $planlama['stok_alt_kalemler']);
        $sth->bindParam("stok_alt_depo_adetler", $planlama['stok_alt_depo_adetler']);
        $sth->bindParam("stok_alt_depolar", $planlama['stok_alt_depolar']);
        $sth->bindParam("fason_durumlar", $planlama['fason_durumlar']);
        $sth->bindParam("fason_tedarikciler", $planlama['fason_tedarikciler']);
        $sth->bindParam("notlar", $planlama['notlar']);
        $sth->bindValue("planlama_durum", 'evet');
        $sth->bindValue("tekil_kod", uniqid());
        $durum = $sth->execute();
        
    }

    //print_r($conn->errorInfo());
    //header("Location: siparis.php?musteri_id={$siparis['musteri_id']}");
    $_SESSION['islem'] = 'siparis-tekrar';
    header("Location: siparis_guncelle.php?siparis_id={$eklenen_siparis_id}");
    exit;
}

//excel çıkarma işlemi
if(isset($_GET['islem']) && $_GET['islem'] == 'siparis_excel')
{

    $musteri_id     = isset($_GET['musteri_id']) ? $_GET['musteri_id'] : 0;

    $sql = 'SELECT siparisler.id, siparisler.siparis_no, siparisler.isin_adi, 
    siparisler.termin, siparisler.fiyat, siparisler.adet, siparisler.islem,
    musteri.marka, CONCAT_WS(" ", personeller.ad, personeller.soyad) AS personel_ad_soyad
    FROM siparisler 
    JOIN musteri ON siparisler.musteri_id = musteri.id
    JOIN personeller ON personeller.id  = siparisler.musteri_temsilcisi_id
    WHERE siparisler.firma_id = :firma_id AND musteri.id = :musteri_id';

    if(isset($_GET['baslangic_tarihi']) && isset($_GET['bitis_tarihi'])){
        $baslangic_tarihi   = $_GET['baslangic_tarihi'];
        $bitis_tarihi       = $_GET['bitis_tarihi'];
        $sql .= " AND siparisler.tarih >= '{$baslangic_tarihi} 00:00:00' AND  siparisler.tarih <= '$bitis_tarihi 23:59:59' ";
    }

    $sth = $conn->prepare($sql);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->bindParam('musteri_id', $musteri_id);
    $sth->execute();
    $siparisler = $sth->fetchAll(PDO::FETCH_ASSOC);


    $excel_data = [
        ['SIRA','SİPARİŞ NO', 'İŞİN ADI', 'MÜŞTERİ', 'MÜŞTERİ TEMSİLCİSİ', 'TERMİN', 'ADET', 'FİYAT', 'DURUM']
    ];

    

    foreach ($siparisler as $key=> $siparis) {
        $durum = "YENİ";
        if($siparis['islem'] == 'islemde')          $durum = 'ONAYLANDI';
        else if($siparis['islem'] == 'tamamlandi')  $durum = 'BİTTİ';
        else if($siparis['islem'] == 'iptal')       $durum = 'İPTAL';
        $excel_data[] = [
            $key+1, 
            $siparis['siparis_no'], 
            $siparis['isin_adi'], 
            $siparis['marka'], 
            $siparis['personel_ad_soyad'], 
            $siparis['termin'], 
            number_format($siparis['adet'],0,'','.'), 
            number_format($siparis['fiyat'], 2, ',','.'),
            $durum
        ]; 
    }

    $xlsx = Shuchkin\SimpleXLSXGen::fromArray( $excel_data );
    $tarih = date('dmY');
    $xlsx->downloadAs("siparisler-{$tarih}.xlsx"); // or downloadAs('books.xlsx') or $xlsx_content = (string) $xlsx 

    exit;

}


#siparis sil
if(isset($_GET['islem']) && $_GET['islem'] == 'siparis_sil')
{
    $id         = intval($_GET['id']);
    $musteri_id = intval($_GET['musteri_id']);

    $sql = "DELETE FROM siparisler WHERE id=:id AND firma_id = :firma_id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('id', $id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $durum = $sth->execute(); 

    $sql = "DELETE FROM planlama WHERE siparis_id=:siparis_id AND firma_id = :firma_id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('siparis_id', $id);
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
    header("Location: siparis.php?musteri_id={$musteri_id}");
    die();
}


//ilgili müşterinin siparislerini ajax getirme
if(isset($_GET['islem']) && $_GET['islem'] == 'siparis-getir')
{
    $musteri_id = intval($_GET['musteri_id']);
    $sth = $conn->prepare('SELECT id, isin_adi FROM siparisler WHERE musteri_id = :musteri_id AND firma_id = :firma_id');
    $sth->bindParam('musteri_id', $musteri_id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $siparisler = $sth->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($siparisler);
    exit;
}

