<?php 
    include "include/db.php";
    include_once "include/oturum_kontrol.php";

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $sth = $conn->prepare('SELECT id, marka, firma_unvani, adresi, ilce_id, sehir_id, ulke_id, sektor_id, cep_tel, 
    sabit_hat, e_mail, yetkili_adi,
    yetkili_cep, yetkili_mail, yetkili_gorev, aciklama, vergi_dairesi, vergi_numarasi, 
    musteri_temsilcisi_id FROM musteri WHERE id=:id AND firma_id = :firma_id');
    $sth->bindParam('id', $id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $musteri = $sth->fetch(PDO::FETCH_ASSOC);

    if(empty($musteri))
    {
        //header("Location: index.php");
        require_once "include/yetkisiz.php";
        die();
    }
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
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>
                        <i class="fa-solid fa-user"></i>
                        Müşteri Bilgileri Güncelleme
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
                    <form class="row g-3 needs-validation" action="musteri_db_islem.php" method="POST">
                        
                        <input type="hidden" name="id" value="<?php echo $musteri['id']; ?>">
                        <div class="form-floating col-md-6">
                            <input type="text" class="form-control" name="marka" id="marka" 
                                value="<?php echo $musteri['marka'];?>" required>
                            <label for="marka" class="form-label">Marka</label>
                        </div>
                        <div class="form-floating col-md-6">
                            <input type="text" class="form-control" name="firma_unvani" id="firma_unvani" 
                                value="<?php echo $musteri['firma_unvani'] ?>" required>
                            <label for="firma_unvani" class="form-label">Firma Ünvanı</label>
                        </div>
                        <div class="form-floating col-md-12">
                            <input type="text" class="form-control" name="adresi" value="<?php echo $musteri['adresi']; ?>" id="adresi" required>
                            <label for="adresi" class="form-label">Adresi</label>
                        </div>
                        <?php 
                            $sth = $conn->prepare('SELECT id, baslik FROM ulkeler ORDER BY baslik ');
                            $sth->execute();
                            $ulkeler = $sth->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="form-floating col-md-4">
                            <select class="form-select form-select-lg js-example-basic-single" id="ulke_id" name="ulke_id" required>
                                <option value="223">Türkiye</option>
                                <?php foreach ($ulkeler as $ulke) { ?>
                                    <option value="<?php echo $ulke['id']; ?>" <?php echo $ulke['id'] == $musteri['ulke_id'] ? 'selected':''; ?>><?php echo $ulke['baslik']; ?></option>
                                <?php }?>
                            </select>
                            <label for="ulke_id" class="form-label">Ülke</label>
                        </div>

                        <?php 
                            $sth = $conn->prepare('SELECT id, baslik FROM sehirler WHERE `ulke_id` = :ulke_id');
                            $sth->bindParam('ulke_id', $musteri['ulke_id']);
                            $sth->execute();
                            $sehirler = $sth->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="form-floating col-md-4">
                            <select class="form-select form-select-lg js-example-basic-single" id="sehir_id" name="sehir_id" required>
                                <?php foreach ($sehirler as  $sehir) { ?>
                                    <option value="<?php echo $sehir['id']; ?>" <?php echo $sehir['id'] == $musteri['sehir_id'] ? 'selected':'';  ?>><?php echo $sehir['baslik']; ?></option>
                                <?php }?>
                            </select>
                            <label for="sehir_id" class="form-label">Şehir</label>
                        </div>

                        <?php 
                            $sth = $conn->prepare('SELECT id, baslik FROM ilceler WHERE `sehir_id` = :sehir_id');
                            $sth->bindParam('sehir_id', $musteri['sehir_id']);
                            $sth->execute();
                            $ilceler = $sth->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="form-floating col-md-4">
                            <select class="form-select form-select-lg js-example-basic-single" id="ilce_id" name="ilce_id" required>
                                <?php foreach ($ilceler as  $ilce) { ?>
                                    <option value="<?php echo $ilce['id']; ?>" <?php echo $ilce['id'] == $musteri['ilce_id'] ? 'selected':'';  ?>><?php echo $ilce['baslik']; ?></option>
                                <?php }?>
                            </select>
                            <label for="ilce_id" class="form-label">İlçe</label>
                        </div>

                        <div class="form-floating col-md-4">
                            <?php 
                                $sth = $conn->prepare('SELECT id, sektor_adi FROM sektorler WHERE firma_id =:firma_id ORDER BY sektor_adi ASC ');
                                $sth->bindParam('firma_id', $_SESSION['firma_id']);
                                $sth->execute();
                                $sektorler = $sth->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <select class="form-select" name="sektor_id" id="sektor_id" required>
                                <?php foreach ($sektorler as $sektor) { ?>
                                    <option value="<?php echo $sektor['id']; ?>" <?php echo $sektor['id'] == $musteri['sektor_id'] ? 'selected' :''?>><?php echo $sektor['sektor_adi']; ?></option>
                                <?php } ?>
                            </select>
                            <label for="sektor_id" class="form-label">Sektör</label>
                        </div>
                        <div class="form-floating col-md-4">
                            <input type="text" class="form-control" name="cep_tel" value="<?php echo $musteri['cep_tel']; ?>" id="cep_tel" required>
                            <label for="cep_tel"  class="form-label">Cep Telefonu</label>
                        </div>
                        <div class="form-floating col-md-4">
                            <input type="text" class="form-control" name="sabit_hat"  value="<?php echo $musteri['sabit_hat']; ?>" id="sabit_hat" required>
                            <label for="sabit_hat" class="form-label">Sabit Hat</label>
                        </div>
                        <div class="form-floating col-md-4">
                            <input type="text" class="form-control" name="e_mail" value="<?php echo $musteri['e_mail']; ?>" id="e_mail" required>
                            <label for="e_mail" class="form-label">E-mail</label>
                        </div>
                        <div class="form-floating col-md-4">
                            <input type="text" class="form-control" name="vergi_numarasi" value="<?php echo $musteri['vergi_numarasi']; ?>" id="vergi_numarasi" required>
                            <label for="vergi_numarasi" class="form-label">Vergi Numarası</label>
                        </div>
                        <div class="form-floating col-md-4">
                            <input type="text" class="form-control" name="vergi_dairesi" id="vergi_dairesi" value="<?php echo $musteri['vergi_dairesi']; ?>" required>
                            <label for="vergi_dairesi" class="form-label">Vergi Dairesi</label>
                        </div>
                        <?php  
                            $sth = $conn->prepare('SELECT id, ad, soyad FROM personeller 
                                WHERE yetki_id IN(2,4) AND firma_id = :firma_id');
                            $sth->bindParam('firma_id', $_SESSION['firma_id']);
                            $sth->execute();
                            $personeller = $sth->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="form-floating col-md-4 mb-2">
                            <select class="form-select" id="musteri_temsilcisi_id" name="musteri_temsilcisi_id" required>
                                <?php foreach ($personeller as $personel) { ?>
                                    <option value="<?php echo $personel['id']; ?>" <?php echo $personel['id'] == $musteri['musteri_temsilcisi_id'] ? 'selected': ''; ?>>
                                        <?php echo $personel['ad'].' '.$personel['soyad']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <label for="musteri_temsilcisi_id" class="form-label">Musteri Temsilcisi</label>
                        </div>
                        <hr class="divider">
                        <h4 >Yetkili</h4>
                        <div class="form-floating col-md-3">
                            <input type="text" class="form-control" name="yetkili_adi" id="yetkili_adi" value="<?php echo $musteri['yetkili_adi']; ?>" required>
                            <label for="yetkili_adi" class="form-label">Adı Soyadı</label>
                        </div>
                        <div class="form-floating col-md-3">
                            <input type="text" class="form-control" name="yetkili_cep" value="<?php echo $musteri['yetkili_cep']; ?>" id="yetkili_cep" required>
                            <label for="yetkili_cep" class="form-label">Cep Telefonu</label>
                        </div>
                            <div class="form-floating col-md-3">
                            <input type="text" class="form-control" name="yetkili_mail" id="yetkili_mail" value="<?php echo $musteri['yetkili_mail']; ?>" required>
                            <label for="yetkili_mail" class="form-label">E-mail</label>
                        </div>
                        <div class="form-floating col-md-3">
                            <select class="form-select" name="yetkili_gorev" id="yetkili_gorev"   required>
                                <option value="Firma Sahibi"    <?php echo $musteri['yetkili_gorev'] == 'Firma Sahibi' ? 'selected' :'';?>>Firma Sahibi</option>
                                <option value="Müdür"           <?php echo $musteri['yetkili_gorev'] == 'Müdür' ? 'selected' :'';?>>Müdür</option>
                                <option value="Satın Alma"      <?php echo $musteri['yetkili_gorev'] == 'Satın Alma' ? 'selected' :'';?>>Satın Alma</option>
                            </select>
                            <label for="yetkili_gorev" class="form-label">Görevi</label>
                        </div>
                        <div class="form-floating col-md-10">
                            <input type="text" class="form-control" name="aciklama" id="aciklama" value="<?php echo $musteri['aciklama']; ?>" required>
                            <label for="aciklama" class="form-label">Açıklama</label>
                        </div>
                        <div class="mt-2">
                            <button class="btn btn-warning" type="submit" 
                                name="musteri_guncelle"
                                data-bs-toggle="tooltip" 
                                data-bs-placement="bottom" 
                                data-bs-title="Güncelle"
                            >
                                <i class="fa-regular fa-pen-to-square"></i> GÜNCELLE
                            </button>
                            <a href="index.php" class="btn btn-danger" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="bottom" 
                                data-bs-title="Sil"
                            >
                                <i class="fa-regular fa-rectangle-xmark"></i> İPTAL
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php include "include/scripts.php"; ?>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>                                   

        <script>
            $(function(){

                $('.js-example-basic-single').select2({
                    theme: 'bootstrap-5'
                });

                $("#ulke_id").change(function(){
                    const ulke_id = $(this).val();

                    $.ajax({
                        url         : "ulke_il_ilce_kontrol.php?ulke_id=" + ulke_id,
                        dataType    : "JSON",
                        success     : function(sehirler){
                            let sehirler_HTML = "";

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
                            let ilceler_HTML =  "";
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

            function il_getir()
            {
                const ulke_id = "<?php echo $musteri['ulke_id']; ?>";

                $.ajax({
                    url         : "ulke_il_ilce_kontrol.php?ulke_id=" + ulke_id,
                    dataType    : "JSON",
                    success     : function(sehirler){
                        let sehirler_HTML = "";

                        for(const sehir of sehirler)
                        {
                            sehirler_HTML += `
                                <option value="${sehir.id}">${sehir.baslik}</option>
                            `;
                        }
                        $("#sehir_id").html(sehirler_HTML);
                    }
                });
            }

            function ilce_getir()
            {
                const sehir_id = "<?php echo $musteri['sehir_id']; ?>";

                $.ajax({
                    url         : "ulke_il_ilce_kontrol.php?sehir_id=" + sehir_id,
                    dataType    : "JSON",
                    success     : function(ilceler){
                        let ilceler_HTML =  "";
                        for(const ilce of ilceler)
                        {
                            ilceler_HTML += `
                                <option value="${ilce.id}">${ilce.baslik}</option>
                            `;
                        }

                        $("#ilce_id").html(ilceler_HTML);
                    }

                });
            }
        </script>
    </body>
</html>
