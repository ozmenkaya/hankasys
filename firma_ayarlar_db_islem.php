<?php 
include "include/db.php";
include "include/oturum_kontrol.php";

//echo "<pre>"; print_r($_POST);
//echo "<pre>"; print_r($_FILES); exit;
//Ayarları Kaydet
if(isset($_POST['ayar_kaydet'])){
    $siparis_no_baslangic_kodu          = trim($_POST['siparis_no_baslangic_kodu']);
    $static_ip_varmi                    = $_POST['static_ip_varmi'];
    $makina_ekran_ipler                 = trim($_POST['makina_ekran_ipler']);
    $eksik_uretimde_onay_isteme_durumu  = $_POST['eksik_uretimde_onay_isteme_durumu'];
    $arsiv_getirme                      = $_POST['arsiv_getirme'];
    $stoga_geri_gonderme_durumu         = isset($_POST['stoga_geri_gonderme_durumu']) ? 'evet' : 'hayır';

    $sql = "UPDATE firmalar SET siparis_no_baslangic_kodu = :siparis_no_baslangic_kodu, 
            makina_ekran_ipler = :makina_ekran_ipler, 
            eksik_uretimde_onay_isteme_durumu = :eksik_uretimde_onay_isteme_durumu, 
            static_ip_varmi = :static_ip_varmi, arsiv_getirme = :arsiv_getirme,
            stoga_geri_gonderme_durumu = :stoga_geri_gonderme_durumu
            WHERE id = :id";
    $sth = $conn->prepare($sql);
    $sth->bindParam("siparis_no_baslangic_kodu", $siparis_no_baslangic_kodu);
    $sth->bindParam("makina_ekran_ipler", $makina_ekran_ipler);
    $sth->bindParam("eksik_uretimde_onay_isteme_durumu", $eksik_uretimde_onay_isteme_durumu);
    $sth->bindParam("static_ip_varmi", $static_ip_varmi);
    $sth->bindParam("arsiv_getirme", $arsiv_getirme);
    $sth->bindParam("stoga_geri_gonderme_durumu", $stoga_geri_gonderme_durumu);
    $sth->bindParam("id", $_SESSION['firma_id']);
    $durum = $sth->execute();

    if(isset($_FILES['logo']) && !empty($_FILES['logo']['name']))
    {
        $hedef_klasor   = "dosyalar/logo/";
        $dosya_adi      = pathinfo($_FILES['logo']["name"], PATHINFO_FILENAME)."-".random_int(1000, 99999);
        $dosya_uzanti   = pathinfo($_FILES['logo']["name"], PATHINFO_EXTENSION);
        $dosya_adi      = preg_replace("/\s+/","-", $dosya_adi);
        $logo = "{$dosya_adi}.{$dosya_uzanti}";

        move_uploaded_file($_FILES["logo"]["tmp_name"], $hedef_klasor.$logo);

        $sql = "UPDATE firmalar SET logo = :logo  WHERE id = :id;";
        $sth = $conn->prepare($sql);
        $sth->bindParam('logo', $logo);
        $sth->bindParam('id', $_SESSION['firma_id']);
        $durum = $sth->execute();
        $_SESSION['logo'] = $logo;
    }

    if($durum)
    {
        $_SESSION['durum'] = 'success';
        $_SESSION['mesaj'] = 'İşlem Başarılı';
    }
    else 
    {
        $_SESSION['durum'] = 'error';
        $_SESSION['mesaj'] = 'İşlem Başarısız';
    }

    header("Location: firma_ayarlar.php");
    exit;
}