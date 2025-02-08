<?php
    require_once "include/db.php";
    require_once "include/oturum_kontrol.php";

    $sql = "SELECT * FROM `firmalar` WHERE id = :id";
    $sth = $conn->prepare($sql);
    $sth->bindParam("id", $_SESSION['firma_id']);
    $sth->execute();
    $firma_ayarlar = $sth->fetch(PDO::FETCH_ASSOC);
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
            <div class="card border-secondary border-2">
                <div class="card-header d-flex justify-content-between border-secondary">
                    <h5> 
                        <i class="fa-solid fa-gear"></i> Firma Ayar
                    </h5>
                    <div class="d-flex justify-content-end"> 
                        <div class="btn-group" role="group">
                            <a href="javascript:window.history.back();" 
                                class="btn btn-secondary"
                                data-bs-target="#departman-ekle-modal"
                                data-bs-toggle="tooltip"
                                data-bs-placement="bottom" 
                                data-bs-title="Geri Dön"
                            >
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="row g-3 needs-validation" id="firma-ayarlar-form" enctype="multipart/form-data"   action="firma_ayarlar_db_islem.php" method="POST" novalidate>
                        
                        <div class="form-floating col-md-6">
                            <input class="form-control" type="file" id="logo" name="logo" >           
                            <label for="logo" class="form-label">Logo</label>
                        </div>
                        <div class="form-floating col-md-6">
                            <?php $logo = $firma_ayarlar['logo'] != '' && file_exists("dosyalar/logo/{$firma_ayarlar['logo']}") ? $firma_ayarlar['logo'] : 'varsayilan.svg'; ?>
                            <a class="text-decoration-none example-image-link-0" 
                                href="dosyalar/logo/<?php echo $logo;?>" 
                                data-lightbox="example-set-0" data-title="">
                                <img class="object-fit-fill border rounded" 
                                    src="dosyalar/logo/<?php echo $logo;?>" 
                                    alt="<?php echo $firma_ayarlar['firma_adi']; ?>"  loading="lazy" 
                                    style="width:50px;height:50px"
                                >
                            </a>
                        </div>
                        <div class="form-floating col-md-6">
                            <input type="text" class="form-control" id="siparis_no_baslangic_kodu" name="siparis_no_baslangic_kodu" value="<?php echo $firma_ayarlar['siparis_no_baslangic_kodu'];?>" required>
                            <label for="siparis_no_baslangic_kodu" class="form-label">Sipariş No Başlangıç Kodu</label>
                        </div>

                        <div class="form-floating col-md-6">
                            <select class="form-select form-select-lg" id="static_ip_varmi"  name="static_ip_varmi" required>
                                <option value="var" <?php echo $firma_ayarlar['static_ip_varmi']== 'var' ? 'selected':'';?>>Var</option>
                                <option value="yok" <?php echo $firma_ayarlar['static_ip_varmi']== 'yok' ? 'selected':'';?>>Yok</option>
                            </select>
                            <label for="static_ip_varmi" class="form-label">Static İP Var mı?</label>
                        </div>


                        <div class="form-floating col-md-12" id="makina-ekran-ipler-satir" style="display:<?php echo $firma_ayarlar['static_ip_varmi']== 'yok' ? 'none':'' ?>">
                            <textarea class="form-control" name="makina_ekran_ipler" id="makina_ekran_ipler" 
                                style="height:200px" required><?php echo $firma_ayarlar['makina_ekran_ipler']; ?></textarea>
                            <label for="makina_ekran_ipler" class="form-label">Makina Ekrana Kontrol IP(ler)</label>
                            <div class="text-danger fw-bold"> * Birden fazla ise alt alta ekleyiniz!</div>
                        </div>

                        <div class="form-floating col-md-6">
                            <select class="form-select form-select-lg" id="eksik_uretimde_onay_isteme_durumu"  name="eksik_uretimde_onay_isteme_durumu" required>
                                <option value="evet" <?php echo $firma_ayarlar['eksik_uretimde_onay_isteme_durumu']== 'evet' ? 'selected':'';?>>Evet</option>
                                <option value="hayır" <?php echo $firma_ayarlar['eksik_uretimde_onay_isteme_durumu']== 'hayır' ? 'selected':'';?>>Hayır</option>
                            </select>
                            <label for="eksik_uretimde_onay_isteme_durumu" class="form-label">Eksik Mal Üretim Onay İsteme Durumu</label>
                        </div>
                        <div class="form-floating col-md-6">
                            <select class="form-select form-select-lg" id="arsiv_getirme"  name="arsiv_getirme" required>
                                <option value="siparise_ozel" <?php echo $firma_ayarlar['arsiv_getirme']== 'siparise_ozel' ? 'selected':'';?>>Siparişe Özel</option>
                                <option value="tumu" <?php echo $firma_ayarlar['arsiv_getirme']== 'tumu' ? 'selected':'';?>>Tüm Siparişte</option>
                            </select>
                            <label for="arsiv_getirme" class="form-label">Arşiv Getirme İşlemi</label>
                        </div>

                        <div class="form-floating col-md-12">
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input" type="checkbox" role="switch" name="stoga_geri_gonderme_durumu" 
                                    id="stoga_geri_gonderme_durumu" <?php echo $firma_ayarlar['stoga_geri_gonderme_durumu'] == 'evet' ? 'checked':''; ?>>
                                <label class="form-check-label" for="stoga_geri_gonderme_durumu">Stoğa Geri Gönderilecek Mi?</label>
                            </div>
                        </div>

                        <div>
                            <button class="btn btn-primary" type="submit" name="ayar_kaydet" id="firma-ayarlar-button">
                                <i class="fa-regular fa-paper-plane"></i> KAYDET
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php 
            include_once "include/scripts.php"; 
            include_once "include/uyari_session_oldur.php"; 
        ?>

        <script>
            $(function(){
                $("#firma-ayarlar-form").submit(function(){
                    $("#firma-ayarlar-button").addClass('disabled');
                    return true;
                });

                //Static IP varsa ip giriş aç
                $("#static_ip_varmi").change(function(){
                    const static_ip_varmi = $(this).val();
                    if(static_ip_varmi == 'var')    $("#makina-ekran-ipler-satir").show();
                    else                            $("#makina-ekran-ipler-satir").hide();
                });
                
            });

            


        </script>
    </body>
</html>
