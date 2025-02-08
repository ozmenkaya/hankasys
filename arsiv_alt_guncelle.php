<?php 
    require_once "include/db.php";
    include_once "include/oturum_kontrol.php";

    $arsiv_alt_id = intval($_GET['arsiv_alt_id']);
    
    $sth = $conn->prepare('SELECT arsiv_altlar.*, arsiv_kalemler.arsiv, arsiv_kalemler.id AS arsiv_id FROM arsiv_kalemler JOIN arsiv_altlar  ON arsiv_altlar.arsiv_id = arsiv_kalemler.id
    WHERE arsiv_altlar.id=:arsiv_alt_id AND arsiv_altlar.firma_id = :firma_id');
    $sth->bindParam('arsiv_alt_id', $arsiv_alt_id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $arsiv_alt_kalem = $sth->fetch(PDO::FETCH_ASSOC);

    if(empty($arsiv_alt_kalem))
    {
        include "include/yetkisiz.php";
        exit;
    }

    //echo "<pre>"; print_R($arsiv_alt_kalem); exit;
    

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
                <div class="card-header">
                    <h5>
                        <i class="fa-regular fa-folder-open"></i>
                        Arşive Alt Güncelle - 
                        Arşiv Adı: <b class="text-danger fw-bold"><?php echo $arsiv_alt_kalem['arsiv']; ?></b>
                    </h5>
                </div>
                <div class="card-body">
                    <form class="row g-3 needs-validation" action="arsiv_alt_db_islem.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="arsiv_alt_id" value="<?php echo $arsiv_alt_kalem['id']; ?>">
                        <input type="hidden" name="arsiv_id" value="<?php echo $arsiv_alt_kalem['arsiv_id']; ?>">
                        
                        <div class="form-floating col-md-4">
                            <input type="text" class="form-control" id="kod" name="kod" value="<?php echo $arsiv_alt_kalem['kod'] ;?>"  />
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
                                <?php foreach ($musteri as $veri) { ?>
                                    <option value="<?php echo $veri['id']; ?>" <?php echo $veri['id'] == $arsiv_alt_kalem['musteri_id'] ? 'selected': ''; ?>><?php echo $veri['marka']; ?></option>
                                <?php }?>
                            </select>
                            <label for="musteri_id" class="form-label">Müşteri</label>
                        </div>
                        
                        <?php 
                            $sth = $conn->prepare('SELECT id, isin_adi FROM siparisler WHERE firma_id = :firma_id AND musteri_id = :musteri_id');
                            $sth->bindParam('firma_id', $_SESSION['firma_id']);
                            $sth->bindParam('musteri_id', $arsiv_alt_kalem['musteri_id']);
                            $sth->execute();
                            $siparisler = $sth->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="form-floating col-md-4">
                            <select class="form-select" id="siparis_id" name="siparis_id" required>
                                <?php foreach ($siparisler as $siparis) { ?>
                                    <option  value="<?php echo $siparis['id']; ?>" 
                                    <?php echo $siparis['id'] == $arsiv_alt_kalem['siparis_id'] ? 'selected':''; ?>>
                                        <?php echo $siparis['isin_adi']; ?>
                                    </option>
                                <?php }?>
                            </select>
                            <label for="siparis_id" class="form-label">İşin Adı</label>
                        </div>
                        
                        <div class="form-floating col-md-4">
                            <input type="text" class="form-control" id="ebat" name="ebat" value="<?php echo $arsiv_alt_kalem['ebat']; ?>" required />
                            <label for="ebat" class="form-label">Ebat</label>
                        </div>
                        <div class="form-floating col-md-4">
                            <input type="number" class="form-control" id="adet" name="adet" value="<?php echo $arsiv_alt_kalem['adet']; ?>" required />
                            <label for="adet" class="form-label">Adet</label>
                        </div>
                        <div class="form-floating col-md-4">
                            <input type="text" class="form-control" id="detay" name="detay" value="<?php echo $arsiv_alt_kalem['detay']; ?>" required />
                            <label for="detay" class="form-label">Detay</label>
                        </div>
                        
                        <div class="form-floating col-md-4">
                            <input type="text" class="form-control" id="fatura_no" name="fatura_no" value="<?php echo $arsiv_alt_kalem['fatura_no']; ?>" required />
                            <label for="fatura_no" class="form-label">Fatura No</label>
                        </div>
                        <div class="form-floating col-md-4">
                            <input type="number" class="form-control" id="maliyet" name="maliyet" value="<?php echo $arsiv_alt_kalem['maliyet']; ?>" step="0.001" required />
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
                                    <option value="<?php echo $tedarikci['id']; ?>" 
                                        <?php echo $tedarikci['id'] == $arsiv_alt_kalem['tedarikci_id'] ? 'selected':''; ?>>
                                        <?php echo $tedarikci['firma_adi']; ?>
                                    </option>
                                <?php }?>
                            </select>
                            <label for="tedarikci_id" class="form-label">Tedarikçi</label>
                        </div>


                        <div class="form-floating col-md-4">
                            <select class="form-select" id="durum" name="durum" required>
                                <option selected disabled value="">Seçiniz</option>
                                <option value="uretimde"    <?php echo $arsiv_alt_kalem['durum'] == 'uretimde' ? 'selected': ''; ?>>Üretimde</option>
                                <option value="arsivde"     <?php echo $arsiv_alt_kalem['durum'] == 'arsivde' ? 'selected': ''; ?>>Arşivde</option>
                                <option value="fasonda"     <?php echo $arsiv_alt_kalem['durum'] == 'fasonda' ? 'selected': ''; ?>>Fasonda</option>
                                <option value="fabrika_icinde_kullanmakta" <?php echo $arsiv_alt_kalem['durum'] == 'fabrika_icinde_kullanmakta' ? 'selected': ''; ?>>Fabrika İçinde Kullanmakta</option>
                                
                            </select>
                            <label for="durum" class="form-label">Durum</label>
                        </div>
                        <div class="form-floating col-md-8">
                            <input type="text" class="form-control" id="aciklama" name="aciklama" value="<?php echo $arsiv_alt_kalem['aciklama']; ?>" required />
                            <label for="aciklama" class="form-label">Açıklama</label>
                        </div>

                        <div class="form-floating col-md-12">
                            <input type="file" class="form-control" id="dosya" name="dosya[]" multiple />
                            <label for="dosya" class="form-label">Dosya yükle</label>
                        </div>

                        <?php 
                        
                            $sth = $conn->prepare('SELECT id, ad FROM arsiv_alt_dosyalar
                                WHERE firma_id = :firma_id AND arsiv_alt_id = :arsiv_alt_id');
                            $sth->bindParam('firma_id', $_SESSION['firma_id']);
                            $sth->bindParam('arsiv_alt_id', $arsiv_alt_kalem['id']);
                            $sth->execute();
                            $arsiv_dosyalar = $sth->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php if(!empty($arsiv_dosyalar)){ ?>
                            <div class="card">
                                <div class="card-body">
                                    <?php foreach ($arsiv_dosyalar as $arsiv_dosya) { ?>
                                        <span style="position:relative;display:inline-block">
                                            <a  class="btn btn-danger btn-sm resim-sil" 
                                                href="arsiv_alt_db_islem.php?islem=dosyasil&arsiv_alt_dosya_id=<?php echo $arsiv_dosya['id'];?>&arsiv_alt_id=<?php echo $arsiv_alt_kalem['id']?>&ad=<?php echo $arsiv_dosya['ad']; ?>" 
                                                onClick="return confirm('Silmek İstediğinize Emin Misiniz?')" 
                                                style="position:absolute;right:4px;top:4px"
                                            >
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                            <?php 
                                                $uzanti = pathinfo("dosyalar/arsivler/{$arsiv_dosya['ad']}", PATHINFO_EXTENSION);
                                            ?>
                                            <?php if($uzanti == 'pdf'){ ?>
                                                <a class="text-decoration-none pdf-modal-goster" data-href="dosyalar/arsivler/<?php echo $arsiv_dosya['ad'];?>" 
                                                    href="javascript:;"
                                                >
                                                    <img src="dosyalar/pdf.png" 
                                                        class="rounded img-thumbnai border border-secondary-subtle object-fit-fill" 
                                                        alt="" 
                                                        style="width:150px;height:150px;"
                                                        
                                                    > 
                                                </a>
                                            <?php }else{?>
                                                <a class="example-image-link" href="dosyalar/arsivler/<?php echo $arsiv_dosya['ad']; ?>" 
                                                    data-lightbox="example-set" data-title="<?php echo $arsiv_alt_kalem['kod']; ?>">
                                                    <img src="dosyalar/arsivler/<?php echo $arsiv_dosya['ad'];?>" 
                                                        class="rounded img-thumbnai border border-secondary-subtle object-fit-fill" alt="" 
                                                        style="width:150px;height:150px;object-fit:content;"
                                                    >   
                                                </a> 
                                            <?php } ?>

                                        </span>
                                    <?php }?>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button class="btn btn-warning" type="submit" name="arsiv_alt_guncelle">
                                    <i class="fa-regular fa-pen-to-square"></i> GÜNCELLE
                                </button>
                                <a href="arsiv_alt.php?arsiv_id=<?php echo $arsiv_alt_kalem['arsiv_id']?> " class="btn btn-secondary" type="submit">
                                    <i class="fa-regular fa-rectangle-xmark"></i> İPTAL
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Arşiv PDF Modal -->
        <div class="modal fade" id="arsiv-pdf-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                            <i class="fa-regular fa-file-pdf"></i> ARŞİV PDF
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="arsiv-pdf-modal-body">
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa-regular fa-rectangle-xmark"></i> KAPAT
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php 
            include "include/scripts.php";
            include "include/uyari_session_oldur.php";
        ?>
        <script>
            $(function(){
                $(".pdf-modal-goster").click(function(){
                    const pdfURL = $(this).data('href');
                    $("#arsiv-pdf-modal-body").html(`
                        <div class="ratio ratio-16x9">
                            <iframe src="${pdfURL}"  allowfullscreen></iframe>
                        </div>
                    `);
                    $("#arsiv-pdf-modal").modal('show');
                });
                

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
