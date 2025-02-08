<?php 
    require_once "include/db.php";
    include_once "include/oturum_kontrol.php";

    $arsiv_id = intval($_GET['arsiv_id']);
    $sth = $conn->prepare('SELECT id, arsiv FROM arsiv_kalemler WHERE id=:id AND firma_id = :firma_id');
    $sth->bindParam('id', $arsiv_id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $arsiv_kalem = $sth->fetch(PDO::FETCH_ASSOC);

    if(empty($arsiv_kalem))
    {
        include "include/yetkisiz.php";
        exit;
    }

?>
<!DOCTYPE html>
<html lang="tr">
    <head>
        <?php require_once "include/head.php";?>
        <title>Hanka Sys SAAS</title>
    </head>
    <body>
        <?php require_once "include/header.php";?>
        <?php require_once "include/sol_menu.php";?>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>
                        <i class="fa-regular fa-folder-open"></i>
                        Arşive Alt Ekle - Arşiv Adı: <b><?php echo $arsiv_kalem['arsiv']; ?></b>
                    </h5>
                    <div>
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
                </div>
                <div class="card-body">
                    <form class="row g-3 needs-validation" action="arsiv_alt_db_islem.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="arsiv_id" value="<?php echo $arsiv_id; ?>">
                        
                        <div class="form-floating col-md-4">
                            <input type="text" class="form-control" id="kod" name="kod" value="<?php echo random_int(100_000, 999_999);?>" />
                            <label for="kod" class="form-label">Kod</label>
                        </div>
                        
                        <div class="form-floating col-md-4">
                            <?php 
                            $sth = $conn->prepare('SELECT id, marka FROM musteri WHERE firma_id = :firma_id ORDER BY marka ');
                            $sth->bindParam('firma_id', $_SESSION['firma_id']);
                            $sth->execute();
                            $musteri = $sth->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            
                            <select class="form-select" id="musteri_id" name="musteri_id" required>
                                <option selected disabled value="">Seç...</option>
                                <?php foreach ($musteri as $muster) { ?>
                                    <option value="<?php echo $muster['id']; ?>"><?php echo $muster['marka']; ?></option>
                                <?php }?>
                            </select>
                            <label for="musteri_id" class="form-label">Müşteri</label>
                        </div>
                        
                        <div class="form-floating col-md-4">
                            <select class="form-select" id="siparis_id" name="siparis_id" required>
                                <option selected disabled value="">Seç...</option>
                            </select>
                            <label for="siparis_id" class="form-label">İşin Adı</label>
                        </div>
                
                        <div class="form-floating col-md-4">
                            <input type="text" class="form-control" id="ebat" name="ebat" required />
                            <label for="ebat" class="form-label">Ebat</label>
                        </div>
                        <div class="form-floating col-md-4">
                            <input type="number" class="form-control" id="adet" name="adet" required />
                            <label for="adet" class="form-label">Adet</label>
                        </div>
                        <div class="form-floating col-md-4">
                            <input type="text" class="form-control" id="detay" name="detay" required />
                            <label for="detay" class="form-label">Detay</label>
                        </div>
                        
                        <div class="form-floating col-md-4">
                            <input type="text" class="form-control" id="fatura_no" name="fatura_no" required />
                            <label for="fatura_no" class="form-label">Fatura No</label>
                        </div>
                        <div class="form-floating col-md-4">
                            <input type="number" class="form-control" id="maliyet" name="maliyet" step="0.001" required />
                            <label for="maliyet" class="form-label">Maliyet</label>
                        </div>
                        <div class="form-floating col-md-4">
                            <?php 
                            $sth = $conn->prepare('SELECT id, firma_adi FROM tedarikciler WHERE firma_id = :firma_id AND fason = "hayır" 
                                ORDER BY firma_adi ');
                            $sth->bindParam('firma_id', $_SESSION['firma_id']);
                            $sth->execute();
                            $tedarikciler = $sth->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            
                            <select class="form-select" id="tedarikci_id" name="tedarikci_id" required>
                                <option selected disabled value="">Seç...</option>
                                <?php foreach ($tedarikciler as $tedarikci) { ?>
                                    <option value="<?php echo $tedarikci['id']; ?>"><?php echo $tedarikci['firma_adi']; ?></option>
                                <?php }?>
                            </select>
                            <label for="tedarikci_id" class="form-label">Tedarikçi</label>
                        </div>


                        <div class="form-floating col-md-4">
                            <select class="form-select" id="durum" name="durum" required>
                                <option selected disabled value="">Seçiniz</option>
                                <option value="uretimde">Üretimde</option>
                                <option value="arsivde">Arşivde</option>
                                <option value="fasonda">Fasonda</option>
                                <option value="fabrika_icinde_kullanmakta">Fabrika İçinde Kullanmakta</option>
                            </select>
                            <label for="durum" class="form-label">Durum</label>
                        </div>
                        <div class="form-floating col-md-8">
                            <input type="text" class="form-control" id="aciklama" name="aciklama" required />
                            <label for="aciklama" class="form-label">Açıklama</label>
                        </div>
                

                        <div class="form-floating col-md-12">
                            <input type="file" class="form-control" id="dosya" name="dosya[]" multiple />
                            <label for="dosya" class="form-label">Dosya yükle</label>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button class="btn btn-primary" type="submit" name="arsiv_alt_ekle">
                                    <i class="fa-regular fa-square-plus"></i> KAYDET
                                </button>
                                <a href="arsiv_alt.php?arsiv_id=<?php echo $arsiv_kalem['id']?> " class="btn btn-secondary" type="submit">
                                    <i class="fa-regular fa-rectangle-xmark"></i> İPTAL
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php 
            include "include/scripts.php";
            include "include/uyari_session_oldur.php";
        ?>
        <script>
            $(function(){

                $("#musteri_id").change(function(){
                    const musteri_id = $(this).val();

                    $.ajax({
                        url         : "siparis_db_islem.php?islem=siparis-getir&musteri_id=" + musteri_id,
                        dataType    : "JSON",
                        success     : function(siparisler){
                            let siparisler_HTML = "<option selected disabled>Seç...</option>";

                            for(const siparis of siparisler)
                            {
                                siparisler_HTML += `
                                    <option value="${siparis.id}">${siparis.isin_adi}</option>
                                `;
                            }
                            $("#siparis_id").html(siparisler_HTML);
                        }
                    });

                });
                
            });
        </script>
    </body>
</html>
