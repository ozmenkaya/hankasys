<?php 
    require_once "include/db.php";
    require_once "include/oturum_kontrol.php";
    
    $stok_alt_kalem_id  = intval($_GET['stok_alt_kalem_id']);
    $stok_id            = intval($_GET['stok_id']);

    $sth = $conn->prepare('SELECT stok_alt_kalemler.*, birimler.ad AS birim_ad FROM stok_alt_kalemler 
    LEFT JOIN birimler ON birimler.id = stok_alt_kalemler.birim_id 
    WHERE stok_alt_kalemler.id = :id AND stok_alt_kalemler.firma_id = :firma_id');
    $sth->bindParam('id', $stok_alt_kalem_id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $stok_alt_kalem = $sth->fetch(PDO::FETCH_ASSOC);

    $sth = $conn->prepare('SELECT stok_kalem FROM stok_kalemleri WHERE id = :id');
    $sth->bindParam('id', $stok_id);
    $sth->execute();
    $stok_kalem = $sth->fetch(PDO::FETCH_ASSOC);


    if(empty($stok_kalem) || empty($stok_alt_kalem))
    {
        require_once "include/yetkisiz.php";
        die();
    }

    $sql = "SELECT SUM(tuketim_miktari) AS toplam_tuketim_miktari, SUM(fire_miktari) AS toplam_fire_miktar FROM `stok_alt_depolar_kullanilanlar` 
            WHERE stok_alt_kalem_id = :stok_alt_kalem_id ";
    $sth = $conn->prepare($sql);
    $sth->bindParam('stok_alt_kalem_id', $stok_alt_kalem['id']);
    $sth->execute();
    $toplam_tuketim = $sth->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
    <head>
        <?php require_once "include/head.php";?>
        <title>Hanka Sys SAAS</title> 
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    </head>
    <body>
        <?php 
            require_once "include/header.php";
            require_once "include/sol_menu.php";
        ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-secondary border-2">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="text-danger fw-bold">
                                <?php echo $stok_kalem['stok_kalem'];?>
                            </h5>
                            <div>
                                <div class="d-flex justify-content-end"> 
                                    <div class="btn-group justify-content-end" role="group" aria-label="Basic example">
                                        <a href="stok.php" 
                                            class="btn btn-secondary"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="bottom" 
                                            data-bs-title="Geri Dön"
                                        >
                                            <i class="fa-solid fa-arrow-left"></i>
                                        </a>
                                        <button data-bs-toggle="modal" 
                                            data-bs-target="#stok-alt-depo-ekle" 
                                            class="btn btn-primary align-self-end" 
                                            data-bs-placement="bottom" 
                                            data-bs-title="Ekle"
                                            type="button"
                                        >
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php 
                                $veriler = json_decode($stok_alt_kalem['veri'], true);
                            ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group">
                                        <li class="list-group-item active fw-bold" aria-current="true">STOK BİLGİLERİ</li>
                                        <?php foreach ($veriler as $stok_alt_kalem_adi => $veri) { ?>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span class="badge bg-secondary fs-6"><?php echo $stok_alt_kalem_adi; ?></span>
                                                <span>
                                                    <?php echo $veri;?>
                                                </span>
                                            </li>
                                        <?php }?>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group">
                                        <li class="list-group-item active fw-bold" aria-current="true">STOK BİLGİLERİ</li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span class="badge bg-secondary fs-6">Gelen Stok</span> 
                                            <span>
                                                <?php echo number_format($stok_alt_kalem['toplam_stok']);?>
                                                <?php echo $stok_alt_kalem['birim_ad']; ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span class="badge bg-secondary fs-6">Tüketilen Stok</span> 
                                            <span>
                                                <?php echo number_format($toplam_tuketim['toplam_tuketim_miktari']);?>
                                                <?php echo $stok_alt_kalem['birim_ad']; ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span class="badge bg-secondary fs-6">Fire Stok</span> 
                                            <span>
                                                <?php echo number_format($toplam_tuketim['toplam_fire_miktar']);?>
                                                <?php echo $stok_alt_kalem['birim_ad']; ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span class="badge bg-secondary fs-6">Toplam Stok</span> 
                                            <span>
                                                <?php echo number_format($stok_alt_kalem['toplam_stok']- $toplam_tuketim['toplam_tuketim_miktari'] - $toplam_tuketim['toplam_fire_miktar']);?>
                                                <?php echo $stok_alt_kalem['birim_ad']; ?>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="card border-secondary border-2">
                        <div class="card-header">
                            <h5>
                                <i class="fa-sharp fa-solid fa-layer-group"></i> Gelen Stoklar
                            </h5>
                        </div>
                        <div class="card-body">
                            <table id="myTable" class="table table-hover" >
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Stok Kodu</th>
                                        <th>Fatura No</th>
                                        <th class="text-end">Adet</th>
                                        <th class="text-end">Kullanılan Adet</th>
                                        <th class="text-end">Maliyet</th>
                                        <th>Tedarikçi</th>
                                        <th>Tarih</th>
                                        <th class="text-end">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $sth = $conn->prepare('SELECT stok_alt_depolar.id, stok_alt_depolar.`adet`,stok_alt_depolar.`stok_kodu`,
                                                            stok_alt_depolar.birim_id,stok_alt_depolar.qr_kod,`stok_alt_depolar`.`siparis_no`,
                                                            stok_alt_depolar.`maliyet`,stok_alt_depolar.`fatura_no`,stok_alt_depolar.ekleme_tarihi,
                                                            `stok_alt_depolar`.`para_cinsi`,`stok_alt_depolar`.`kullanilan_adet`,
                                                            tedarikciler.firma_adi, birimler.ad AS birim_ad
                                                            FROM stok_alt_depolar 
                                                            JOIN tedarikciler ON stok_alt_depolar.tedarikci_id = tedarikciler.id 
                                                            JOIN birimler ON birimler.id = stok_alt_depolar.birim_id
                                                            WHERE stok_alt_depolar.stok_alt_kalem_id = :stok_alt_kalem_id
                                                            ORDER BY stok_alt_depolar.id DESC
                                                        ');
                                    $sth->bindParam('stok_alt_kalem_id', $stok_alt_kalem_id);
                                    $sth->execute();
                                    $stok_alt_depolar = $sth->fetchAll(PDO::FETCH_ASSOC);
                                    ?>
                                    <?php foreach ($stok_alt_depolar as $key => $stok_alt_depo) { ?>
                                        <?php 
                                            $para_cinsi = '<i class="fa-solid fa-turkish-lira-sign"></i>';
                                            if($stok_alt_depo['para_cinsi'] == 'DOLAR')      $para_cinsi = '<i class="fa-solid fa-dollar-sign"></i>';
                                            if($stok_alt_depo['para_cinsi'] == 'EURO')       $para_cinsi = '<i class="fa-solid fa-euro-sign"></i>';
                                            if($stok_alt_depo['para_cinsi'] == 'POUND')      $para_cinsi = '<i class="fa-solid fa-sterling-sign"></i>';
                                        ?>
                                        <tr>
                                            <td class="table-primary">
                                                <?php echo $key + 1; ?>
                                            </td>
                                            <td class="table-secondary">
                                                <?php echo $stok_alt_depo['stok_kodu']; ?>
                                            </td>
                                            <td>
                                                <?php echo empty($stok_alt_depo['fatura_no']) ? '<b class="fw-bold text-danger">GİRİLMEDİ</b>':$stok_alt_depo['fatura_no']; ?> - 
                                                <?php if($stok_alt_depo['siparis_no']){?>
                                                    <b class="text-success">
                                                        <?php echo $stok_alt_depo['siparis_no']; ?> (S.Özel ve Tekrar Sipariş)</b>
                                                <?php } ?>
                                            </td>
                                            <th class="text-end table-success">
                                                <?php echo number_format($stok_alt_depo['adet']); ?>
                                                <?php echo $stok_alt_depo['birim_ad']; ?>
                                            </th>
                                            <th class="text-end table-primary">
                                                <?php echo number_format($stok_alt_depo['kullanilan_adet']); ?>
                                                <?php echo $stok_alt_depo['birim_ad']; ?>
                                            </th>
                                            <td class="text-end">
                                                <?php echo number_format($stok_alt_depo['maliyet'], 2, ',', '.'); ?> 
                                                <?php echo $para_cinsi;?>
                                            </td>
                                            <td><?php echo $stok_alt_depo['firma_adi']; ?></td>
                                            
                                            <td><?php echo date('d-m-Y H:i:s', strtotime($stok_alt_depo['ekleme_tarihi'])); ?></td>
                                            
                                            <td> 
                                                <div class="d-flex justify-content-end"> 
                                                    <div class="btn-group" role="group">
                                                        <button 
                                                            data-href="dosyalar/qr-code/<?php echo $stok_alt_depo['qr_kod']; ?>"
                                                            data-stok-kodu="<?php echo $stok_alt_depo['stok_kodu']; ?>"
                                                            data-fatura-no="<?php echo $stok_alt_depo['fatura_no']; ?>"
                                                            href="javascript:;" 
                                                            class="btn btn-secondary qr-kod-pdf-modal-goster"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="bottom" 
                                                            data-bs-title="QR Kod"
                                                        >
                                                            <i class="fa-solid fa-qrcode"></i>
                                                        </button>
                                                        <button 
                                                            class="btn btn-danger stok-alt-depo-dusme"
                                                            data-stok-id="<?php echo $_GET['stok_id']; ?>"
                                                            data-stok-alt-depo-id="<?php echo $stok_alt_depo['id']; ?>"
                                                            data-birim-id="<?php echo $stok_alt_depo['birim_id']; ?>"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="bottom" 
                                                            data-bs-title="Stoktan Düşme"
                                                        >
                                                            <i class="fa-solid fa-minus"></i>
                                                        </button>
                                                        <a href="stok_alt_depolar_guncelle.php?id=<?php echo $stok_alt_depo['id']; ?>&stok_id=<?php echo $_GET['stok_id']; ?>" 
                                                            class="btn btn-warning"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="bottom" 
                                                            data-bs-title="Güncelle"
                                                        >
                                                            <i class="fa-regular fa-pen-to-square"></i>
                                                        </a>
                                                        <a href="stok_alt_depolar_db_islem.php?islem=stok_alt_depo_sil&id=<?php echo $stok_alt_depo['id']; ?>&stok_alt_kalem_id=<?php echo $_GET['stok_alt_kalem_id']?>&stok_id=<?php echo $stok_id;?>" 
                                                            onClick="return confirm('Silmek İstediğinize Emin Misiniz?')" 
                                                            class="btn btn-danger"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="bottom" 
                                                            data-bs-title="Sil"
                                                        >
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>

                <div class="col-md-12 mt-3">
                    <div class="card border-secondary border-2">
                        <div class="card-header">
                            <h5>
                                <i class="fa-sharp fa-solid fa-layer-group"></i> Kullanılan Stoklar
                            </h5>
                        </div>
                        <div class="card-body">
                            <table id="myTable" class="table table-hover" >
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Stok Kodu</th>
                                        <th class="text-end">Tüketim Miktari</th>
                                        <th class="text-end">Fire</th>
                                        <th class="text-end">Açıklama</th>
                                        <th class="text-end">Tarih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $sth = $conn->prepare('SELECT stok_alt_depolar_kullanilanlar.tuketim_miktari,
                                                            stok_alt_depolar_kullanilanlar.fire_miktari,stok_alt_depolar_kullanilanlar.tarih,
                                                            `stok_alt_depolar`.`stok_kodu`, stok_alt_depolar_kullanilanlar.aciklama
                                                            FROM stok_alt_depolar_kullanilanlar 
                                                            JOIN stok_alt_depolar ON stok_alt_depolar.id = stok_alt_depolar_kullanilanlar.stok_alt_depo_id
                                                            WHERE stok_alt_depolar_kullanilanlar.stok_alt_kalem_id = :stok_alt_kalem_id ORDER BY stok_alt_depolar_kullanilanlar.id DESC');
                                    $sth->bindParam('stok_alt_kalem_id', $stok_alt_kalem_id);
                                    $sth->execute();
                                    $stok_alt_depolar_kullanilanlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                                    ?>
                                    <?php foreach ($stok_alt_depolar_kullanilanlar as $key => $stok_alt_depolar_kullanilan) { ?>
                                        <tr>
                                            <td class="table-primary"><?php echo $key + 1; ?></td>
                                            <td class="table-secondary"><?php echo $stok_alt_depolar_kullanilan['stok_kodu']; ?></td>
                                            <td class="text-end"><?php echo number_format($stok_alt_depolar_kullanilan['tuketim_miktari']); ?></td>
                                            <td class="text-end">
                                                <?php echo number_format($stok_alt_depolar_kullanilan['fire_miktari']); ?>
                                            </td>
                                            <td class="text-end">
                                                <?php echo $stok_alt_depolar_kullanilan['aciklama']; ?>
                                            </td>
                                            <td class="text-end">
                                                <?php echo date('d-m-Y H:i:s', strtotime($stok_alt_depolar_kullanilan['tarih'])); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

        <!-- Stok Ekleme Moda -->
        <div class="modal fade" id="stok-alt-depo-ekle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog  modal-lg">
                <div class="modal-content">
                    <form action="stok_alt_depolar_db_islem.php" method="POST">
                        <input type="hidden" name="stok_alt_kalem_id" value="<?php echo $_GET['stok_alt_kalem_id']; ?>">
                        <input type="hidden" name="stok_id" value="<?php echo $_GET['stok_id']; ?>">
                        <input type="hidden" name="stok_kodu" 
                            value="<?php echo str_replace(['Ğ','Ü','Ş','İ','Ö','Ç'],['G','U','S','I','O','C'],
                                                            mb_strtoupper(mb_substr($stok_kalem['stok_kalem'],0,3))); ?>">
                        <div class="modal-header">
                            <h5 class="modal-title">Stok Ekle</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger fw-bold d-flex justify-content-between border-3">
                                <span>
                                    1- Stok Eklenirken Takip İçin 
                                    <span class="text-decoration-underline fst-italic">QR</span> Kod Oluşacaktır.
                                </span>
                                <span>
                                    <i class="fa-solid fa-qrcode"></i>
                                </span>
                            </div>
                            
                            <div class="form-floating col-md-12">
                                <input type="number" class="form-control" name="adet" id="adet" required >
                                <label for="adet" class="form-label">Miktar</label>
                            </div>
                            <?php if(empty($stok_alt_depolar )){?>
                                <?php 
                                    $sth = $conn->prepare('SELECT * FROM birimler ORDER BY ad ');
                                    $sth->execute();
                                    $birimler = $sth->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <div class="form-floating col-md-12 mt-2">
                                    <select class="form-select" id="birim" name="birim_id" required>
                                        <option selected disabled value="">Seç...</option>
                                        <?php foreach ($birimler as $birim) { ?>
                                            <option value="<?php echo $birim['id']; ?>"><?php echo $birim['ad']; ?></option>
                                        <?php }?>
                                    </select>
                                    <label for="birim_id" class="form-label">Birim</label>
                                </div>
                            <?php }else{?> 
                                <input type="hidden" name="birim_id" value="<?php echo $stok_alt_depolar[0]['birim_id']?>">
                            <?php } ?>
                            
                            <div class="row g-3 mt-1">
                                <div class="form-floating col-md-6">
                                    <select class="form-select" id="para_cinsi" name="para_cinsi" required>
                                        <option selected disabled value="">Seçiniz</option>
                                        <option value="TL">TL</option>
                                        <option value="DOLAR">DOLAR</option>
                                        <option value="EURO">EURO</option>
                                        <option value="POUND">POUND</option>
                                    </select>
                                    <label for="para_cinsi" class="form-label">Para Cinsi</label>
                                </div>

                                <div class="form-floating col-md-6">
                                    <input type="number" class="form-control" name="maliyet" id="maliyet" required step="0.001">
                                    <label for="maliyet" class="form-label">Toplam Maliyet</label>
                                </div>
                            </div>

                            <div class="form-floating col-md-12 mt-2">
                                <input type="text" class="form-control" name="fatura_no" id="fatura_no" >
                                <label for="fatura_no" class="form-label">Fatura No</label>
                            </div>
                            <?php 
                                $sth = $conn->prepare('SELECT id, firma_adi FROM tedarikciler 
                                WHERE firma_id = :firma_id AND fason = "hayır" ORDER BY firma_adi ASC');
                                $sth->bindParam('firma_id', $_SESSION['firma_id']);
                                $sth->execute();
                                $tedarikciler = $sth->fetchAll(PDO::FETCH_ASSOC);
                            ?>    
                            <div class="form-floating col-md-12 mt-2">
                                <select name="tedarikci_id" id="tedarikci_id" class="form-control" required> 
                                    <option value="" selected disabled>Seçiniz</option>
                                    <?php foreach ($tedarikciler as $index => $tedarikci) { ?>
                                        <option value="<?php echo $tedarikci['id'];?>">
                                            <?php echo ($index + 1).' - '.$tedarikci['firma_adi']; ?>
                                        </option>
                                    <?php }?>
                                </select>
                                <label for="tedarikci_id" class="form-label">Tedarikçi</label>
                            </div>

                            <div class="form-floating col-md-12 mt-2">
                                <?php 
                                    $sql = "SELECT siparisler.stok_alt_depo_kod,siparisler.siparis_no, siparisler.isin_adi,
                                            musteri.marka FROM `siparisler` 
                                            JOIN musteri ON musteri.id = siparisler.musteri_id
                                            WHERE siparisler.firma_id = :firma_id ORDER BY siparisler.isin_adi ASC";
                                    $sth = $conn->prepare($sql);
                                    $sth->bindParam('firma_id', $_SESSION['firma_id']);
                                    $sth->execute();
                                    $siparisler = $sth->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                
                                <select class="form-select form-select-lg" id="stok_alt_depo_kod" name="stok_alt_depo_kod">
                                    <option value="">Seçiniz</option>    
                                    <?php foreach ($siparisler as $index => $siparis) { ?>
                                        <option value="<?php echo $siparis['stok_alt_depo_kod']; ?>">
                                            <?php echo ($index + 1).' - '.$siparis['siparis_no'].' - '.$siparis['marka'].' - '.$siparis['isin_adi'];?>
                                        </option>  
                                    <?php }?>
                                </select>
                                <label for="stok_alt_depo_kod" class="form-label">Siparişler</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="stok_alt_depo_ekle">
                                <i class="fa-regular fa-square-plus"></i> KAYDET
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fa-regular fa-rectangle-xmark"></i> İPTAL
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Stok Alt Depo Düşme -->
        <div class="modal fade" id="stok-alt-depo-dusme" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog  modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-minus fs-4"></i> Alt Stok Depo Düşme
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="stok_alt_depolar_db_islem.php" method="POST" id="stok-dusme-form">
                            <input type="hidden" name="stok_id" id="stok-id">
                            <input type="hidden" name="stok_alt_depo_id" id="stok-alt-depo-id">
                            <input type="hidden" name="birim_id" id="birim-id">
                            <div class="form-floating col-md-12 mb-2">
                                <input type="number" class="form-control" name="adet" id="dusme-adeti" required >
                                <label for="dusme-adeti" class="form-label">Miktar</label>
                            </div>  
                            <div class="form-floating col-md-12 mb-2">
                                <textarea class="form-control" name="aciklama" id="aciklama" style="height: 100px"></textarea>
                                <label for="aciklama">Açıklama</label>
                            </div>   
                            
                            <button type="submit" class="btn btn-primary" name="stok_alt_depo_dusme" id="stok-dusme-button">
                                <i class="fa-regular fa-paper-plane"></i> KAYDET
                            </button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa-regular fa-rectangle-xmark"></i> KAPAT
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--  QR Kod  Modal -->
        <div class="modal fade" id="stok-qr-kod-pdf-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                            <i class="fa-regular fa-file-pdf"></i> Stok Bilgi ve QR Kodu
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="stok-qr-kod-pdf-modal-body">
                        
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
            include_once "include/scripts.php"; 
            include_once "include/uyari_session_oldur.php";
        ?>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(function(){
                //QR Kod  Modalda Göster
                $(".qr-kod-pdf-modal-goster").click(function(){
                    const pdfURL = $(this).data('href');
                    const stokKodu = $(this).data('stok-kodu');
                    const faturaNo = $(this).data('fatura-no');
                    $("#stok-qr-kod-pdf-modal-body").html(`
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <ul class="list-group">
                                    <li class="list-group-item active fw-bold" aria-current="true">STOK BİLGİ</li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="fw-bold">Stok Kodu</span>
                                        <span>${stokKodu}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="fw-bold">Fatura No</span>
                                        <span>${faturaNo ? faturaNo : '<b class="text-danger fw-bold">GİRİLMEDİ</b>'}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-3 offset-md-1">
                                <div class="ratio ratio-1x1">
                                    <img src="${pdfURL}" class="rounded img-thumbnai object-fit-fill" >
                                </div>
                            </div>
                        </div>
                    `);
                    $("#stok-qr-kod-pdf-modal").modal('show');
                });

                $('.js-example-basic-single').select2({
                    theme: 'bootstrap-5'
                });

                //Stok Alt Depo Düşme Modal Açma
                $(".stok-alt-depo-dusme").click(function(){
                    const stokId = $(this).data('stok-id');
                    const stokAltDepoId = $(this).data('stok-alt-depo-id');
                    const birimId = $(this).data('birim-id');

                    $("#stok-id").val(stokId);
                    $("#stok-alt-depo-id").val(stokAltDepoId);
                    $("#birim-id").val(birimId);

                    $("#stok-alt-depo-dusme").modal('show');
                });

                //Stok Alt Depo Düşme Button Disable
                $("#stok-dusme-form").submit(function(){
                    $("#stok-dusme-button").addClass('disabled');
                    return true;
                });
            });
        </script>
    </body>
</html>
