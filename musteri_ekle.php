<?php
    include_once "include/db.php";
    include_once "include/oturum_kontrol.php";
    //echo "<pre>"; print_r($_SERVER); exit;

    
    if(!in_array(MUSTERI_OLUSTUR, $_SESSION['sayfa_idler'])){ 
        require_once "include/yetkisiz.php";
        die();
    }


    $sth = $conn->prepare('SELECT id, ad, soyad FROM personeller WHERE yetki_id IN(2,4) AND firma_id = :firma_id');
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $personeller = $sth->fetchAll(PDO::FETCH_ASSOC);


    $sth = $conn->prepare('SELECT id, sektor_adi FROM sektorler WHERE firma_id = :firma_id  ORDER BY sektor_adi ASC  ');
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $sektorler = $sth->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="tr">
<head>
    <?php require_once "include/head.php";?>
    <title>Hanka Sys SAAS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
</head>
<body>
    <?php require_once "include/header.php";?>
    <?php require_once "include/sol_menu.php";?>
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-md-12">
                <?php if(empty($personeller)){?>
                    <div class="alert alert-danger" role="alert">
                        <h5>
                            Müşteri Eklemek İçin Müşteri Temsilcisi veya Pazarlama Personeli Eklemeniz Gereklidir! 
                            <a href="personel_ekle.php" class="btn btn-primary btn-sm">Personel Ekle</a>
                        </h5>
                    </div>
                <?php } ?>

                <?php if(empty($sektorler)){?>
                    <div class="alert alert-danger" role="alert">
                        <h5>
                            Müşteri Eklemek İçin Sektor Gereklidir! 
                            <a href="sektor.php" class="btn btn-primary btn-sm">Sektor Ekle</a>
                            
                        </h5>
                    </div>
                <?php } ?>

                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>
                            <i class="fa-solid fa-user-plus"></i>
                            Müşteri Ekle
                        </h5>
                        <div>
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
                    <div class="card-body">
                        <form class="row g-3 needs-validation" action="musteri_db_islem.php" method="POST" id="musteri-ekle-form"> 
                            <div class="form-floating col-md-6">
                                <input type="text" class="form-control" name="marka" id="marka" required>
                                <label for="marka" class="form-label">Marka</label>
                            </div>
                            <div class="form-floating col-md-6">
                                <input type="text" class="form-control" name="firma_unvani" id="firma_unvani" required>
                                <label for="firma_unvani" class="form-label">Firma Ünvanı</label>
                            </div>
                            <div class="form-floating col-md-12">
                                <input type="text" class="form-control" name="adresi" id="adresi" required>
                                <label for="adresi" class="form-label">Adresi</label>
                            </div>

                            <?php 
                                $sth = $conn->prepare('SELECT id, baslik FROM ulkeler ORDER BY baslik ');
                                $sth->execute();
                                $ulkeler = $sth->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <div class="form-floating col-md-4">
                                <select class="form-select form-select-lg js-example-basic-single" id="ulke_id" 
                                    name="ulke_id" required>
                                    <option selected disabled value="">Ülke Seçiniz</option>
                                    <option value="223">Türkiye</option>
                                    <?php foreach ($ulkeler as $ulke) { ?>
                                        <option value="<?php echo $ulke['id']; ?>"><?php echo $ulke['baslik']; ?></option>
                                    <?php }?>
                                </select>
                                <label for="ulke_id" class="form-label">Ülke</label>
                            </div>
                            <div class="form-floating col-md-4">
                                <select class="form-select form-select-lg js-example-basic-single" id="sehir_id" name="sehir_id" required>
                                </select>
                                <label for="sehir_id" class="form-label">Şehir</label>
                            </div>
                            <div class="form-floating col-md-4">
                                <select class="form-select form-select-lg js-example-basic-single" id="ilce_id" name="ilce_id" required>
                                </select>
                                <label for="ilce_id" class="form-label">İlçe</label>
                            </div>

                            <div class="form-floating col-md-4">
                                <select class="form-select" name="sektor_id" id="sektor_id" required>
                                    <option selected disabled value="">Seçiniz</option>
                                    <?php foreach ($sektorler as $sektor) { ?>
                                        <option value="<?php echo $sektor['id']; ?>"><?php echo $sektor['sektor_adi']; ?></option>
                                    <?php } ?>
                                </select>
                                <label for="sektor_id" class="form-label">Sektör</label>
                            </div>
                            <div class="form-floating col-md-4">
                                <input type="text" class="form-control" name="cep_tel" id="cep_tel" required>
                                <label for="cep_tel" class="form-label">Cep Telefonu</label>
                            </div>
                            <div class="form-floating col-md-4">
                                <input type="text" class="form-control" name="sabit_hat" id="sabit_hat" required>
                                <label for="sabit_hat" class="form-label">Sabit Hat</label>
                            </div>
                            <div class="form-floating col-md-4">
                                <input type="text" class="form-control" name="e_mail" id="e_mail" required>
                                <label for="e_mail" class="form-label">E-mail</label>
                            </div>
                            <div class="form-floating col-md-4">
                                <input type="text" class="form-control" name="vergi_numarasi" id="vergi_numarasi" required>
                                <label for="vergi_numarasi" class="form-label">Vergi Numarası</label>
                            </div>
                            <div class="form-floating col-md-4">
                                <input type="text" class="form-control" name="vergi_dairesi" id="vergi_dairesi" required>
                                <label for="vergi_dairesi" class="form-label">Vergi Dairesi</label>
                            </div>

                            <div class="form-floating col-md-4 mb-2">
                                <select class="form-select" id="musteri_temsilcisi_id" name="musteri_temsilcisi_id" required>
                                    <option selected disabled value="">Seçiniz</option>
                                    <?php foreach ($personeller as $personel) { ?>
                                        <option value="<?php echo $personel['id']; ?>"><?php echo $personel['ad'].' '.$personel['soyad']; ?></option>
                                    <?php } ?>
                                </select>
                                <label for="musteri_temsilcisi_id" class="form-label">Musteri Temsilcisi</label>
                            </div>
                            <div class="border-bottom border-2 border-secondary"></div>

                            <h4 >Yetkili</h4>

                            
                            <div class="form-floating col-md-3">
                                <input type="text" class="form-control" name="yetkili_adi" id="yetkili_adi" required>
                                <label for="yetkili_adi" class="form-label">Adı Soyadı</label>
                            </div>
                            <div class="form-floating col-md-3">
                                <input type="text" class="form-control" name="yetkili_cep" id="yetkili_cep" required>
                                <label for="yetkili_cep" class="form-label">Cep Telefonu</label>
                            </div>
                                <div class="form-floating col-md-3">
                                <input type="text" class="form-control" name="yetkili_mail" id="yetkili_mail" required>
                                <label for="yetkili_mail" class="form-label">e-mail</label>
                            </div>
                            <div class="form-floating col-md-3">
                                <select class="form-select" name="yetkili_gorev" id="yetkili_gorev" required>
                                    <option selected disabled value="">Seçiniz</option>
                                    <option value="Firma Sahibi">Firma Sahibi</option>
                                    <option value="Müdür">Müdür</option>
                                    <option value="Satın Alma">Satın Alma</option>
                                </select>
                                <label for="yetkili_gorev" class="form-label">Görevi</label>
                            </div>
                            <div class="form-floating col-md-12">
                                <textarea name="aciklama" id="aciklama" class="form-control" style="height: 120px;"></textarea>
                                <label for="aciklama" class="form-label">Açıklama</label>
                            </div>

                            <div >
                                <button class="btn btn-primary" type="submit" name="musteri_ekle" id="musteri-ekle-button">
                                    <i class="fa-solid fa-paper-plane"></i> KAYDET
                                </button>
                                <a href="musteriler.php" class="btn btn-secondary">
                                    <i class="fa-regular fa-rectangle-xmark"></i> MÜŞTERİLER
                                </a>
                            </div>
                
                        </form>
                    </div>
                </div>
            </div>
        </div>    
    </div>
</main>
<?php require_once "include/scripts.php";?>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(function(){

        $("#musteri-ekle-form").submit(function(){
            $("#musteri-ekle-button").addClass('disabled');
            return true;
        });

        $('.js-example-basic-single').select2({
            theme: 'bootstrap-5'
        });

        $("#ulke_id").change(function(){
            const ulke_id = $(this).val();

            $.ajax({
                url         : "ulke_il_ilce_kontrol.php?ulke_id=" + ulke_id,
                dataType    : "JSON",
                success     : function(sehirler){
                    let sehirler_HTML = "<option selected disabled>İl Seçiniz</option>";

                    for(const sehir of sehirler)
                    {
                        sehirler_HTML += `
                            <option value="${sehir.id}">${sehir.baslik}</option>
                        `;
                    }
                    $("#sehir_id").html(sehirler_HTML);
                }
            });

        });

        $("#sehir_id").change(function(){
            const sehir_id = $(this).val();

            $.ajax({
                url         : "ulke_il_ilce_kontrol.php?sehir_id=" + sehir_id,
                dataType    : "JSON",
                success     : function(ilceler){
                    let ilceler_HTML =  "<option selected disabled>İlçe Seçiniz</option>";
                    for(const ilce of ilceler)
                    {
                        ilceler_HTML += `
                            <option value="${ilce.id}">${ilce.baslik}</option>
                        `;
                    }

                    $("#ilce_id").html(ilceler_HTML);
                }

            });
        });
        
    });
</script>
</body>
</html>
