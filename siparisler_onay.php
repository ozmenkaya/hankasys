<?php  
    require_once "include/db.php";
    require_once "include/oturum_kontrol.php";

    $sth = $conn->prepare('SELECT siparisler.id, siparisler.siparis_no, siparisler.isin_adi, siparisler.termin, 
                        siparisler.fiyat, siparisler.adet,siparisler.para_cinsi,
                        musteri.marka, CONCAT_WS(" ", personeller.ad, personeller.soyad) AS personel_ad_soyad
                        FROM siparisler 
                        JOIN musteri ON siparisler.musteri_id = musteri.id
                        JOIN personeller ON personeller.id  = siparisler.musteri_temsilcisi_id
                        WHERE siparisler.firma_id = :firma_id AND  onay_baslangic_durum = "hayır" AND islem != "iptal"
                        ORDER BY siparisler.id DESC');
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $onaylanmamis_siparisler = $sth->fetchAll(PDO::FETCH_ASSOC);

    $sth = $conn->prepare('SELECT siparisler.id, siparisler.siparis_no, siparisler.isin_adi, siparisler.termin, 
                    siparisler.fiyat, siparisler.adet,siparisler.para_cinsi,
                    musteri.marka, CONCAT_WS(" ", personeller.ad, personeller.soyad) AS personel_ad_soyad
                    FROM siparisler 
                    JOIN musteri ON siparisler.musteri_id = musteri.id
                    JOIN personeller ON personeller.id  = siparisler.musteri_temsilcisi_id
                    WHERE siparisler.firma_id = :firma_id AND onay_baslangic_durum = "evet" AND islem != "iptal" ORDER BY siparisler.id DESC');
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $onaylanmis_siparisler = $sth->fetchAll(PDO::FETCH_ASSOC);



    $sth = $conn->prepare('SELECT siparisler.id, siparisler.siparis_no, siparisler.isin_adi, siparisler.termin, 
                    siparisler.fiyat, siparisler.adet,siparisler.para_cinsi,
                    musteri.marka, CONCAT_WS(" ", personeller.ad, personeller.soyad) AS personel_ad_soyad
                    FROM siparisler 
                    JOIN musteri ON siparisler.musteri_id = musteri.id
                    JOIN personeller ON personeller.id  = siparisler.musteri_temsilcisi_id
                    WHERE siparisler.firma_id = :firma_id AND onay_baslangic_durum = "evet" AND islem = "tamamlandi" ORDER BY siparisler.id DESC');
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $bitmis_siparisler = $sth->fetchAll(PDO::FETCH_ASSOC);
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
                        <i class="fa-solid fa-bag-shopping"></i> Siparişler
                    </h5>
                    <div>
                        <div class="d-flex justify-content-end"> 
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <a href="javascript:window.history.back();" 
                                    class="btn btn-secondary"
                                    data-bs-target="#departman-ekle-modal"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" 
                                    data-bs-title="Geri Dön"
                                >
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>
                                <a href="siparis_db_islem.php?islem=siparis_excel" 
                                    class="btn btn-success"
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="bottom" 
                                    data-bs-title="Excel"
                                >
                                    <i class="fa-regular fa-file-excel"></i>
                                </a>
                            </div>
                        </div>
                    </div>	
                </div>
                <div class="card-body">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <?php if(in_array(YENI_SIPARIS_GOR, $_SESSION['sayfa_idler'])){  ?>
                                <button class="nav-link active position-relative fw-bold" id="nav-tab-onaylanmayan" data-bs-toggle="tab" 
                                    data-bs-target="#nav-onaylanmayan" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                                    Yeni Siparişler
                                    <span class="position-absolute top-0 start-70 translate-middle badge rounded-pill bg-danger">
                                        <?php echo count($onaylanmamis_siparisler); ?>
                                        <span class="visually-hidden">Yeni Siparişler</span>
                                    </span>
                                </button>
                            <?php }?>
                            <button class="nav-link position-relative fw-bold" id="nav-tab-onaylanan" data-bs-toggle="tab" 
                                data-bs-target="#nav-onaylanan" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                                Siparişler
                                <span class="position-absolute top-0 start-70 translate-middle badge rounded-pill bg-primary">
                                        <?php echo count($onaylanmis_siparisler ); ?>
                                        <span class="visually-hidden">Onaylanmış Siparişler</span>
                                    </span>
                            </button>
                            <button class="nav-link position-relative fw-bold" id="nav-tab-biten" data-bs-toggle="tab" 
                                data-bs-target="#nav-biten" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                                Biten Siparişler
                                <span class="position-absolute top-0 start-70 translate-middle badge rounded-pill bg-success">
                                        <?php echo count($bitmis_siparisler ); ?>
                                        <span class="visually-hidden">Biten Siparişler</span>
                                    </span>
                            </button>
                        </div>
                    </nav>

                    <div class="tab-content mt-3" id="nav-tabContent">
                        <?php if(in_array(YENI_SIPARIS_GOR, $_SESSION['sayfa_idler'])){  ?>
                            <div class="tab-pane fade show active" id="nav-onaylanmayan" role="tabpanel" 
                                aria-labelledby="nav-tab-onaylanmayan" tabindex="0">
                                <div class="table-responsive">
                                    <table id="myTable" class="table table-hover" >
                                        <thead class="table-primary">
                                            <tr>
                                                <th>#</th>
                                                <th>Sipariş No</th>
                                                <th>İşin Adı</th>
                                                <th>Müşteri</th>
                                                <th>M.Temsilcisi</th>
                                                <th>Termin</th>
                                                <th class="text-end">Adet</th>
                                                <th class="text-end">Fiyat</th>
                                                <th class="text-end">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($onaylanmamis_siparisler  as $key=>$onaylanmamis_siparis) { ?>
                                                <tr>
                                                    <td class="table-primary"><?php echo $key +1 ;?></td>
                                                    <th class="table-secondary"><?php echo $onaylanmamis_siparis['siparis_no'];?></th>
                                                    <td><?php echo $onaylanmamis_siparis['isin_adi']; ?></td>
                                                    <td><?php echo $onaylanmamis_siparis['marka']; ?></td>
                                                    <td><?php echo $onaylanmamis_siparis['personel_ad_soyad']; ?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($onaylanmamis_siparis['termin'])); ?></td>
                                                    <td class="text-end">
                                                        <?php echo number_format($onaylanmamis_siparis['adet'],0,'','.'); ?> Adet
                                                    </td>
                                                    <td class="text-end">
                                                        <?php 
                                                            $para_cinsi = '<i class="fa-solid fa-turkish-lira-sign"></i>';
                                                            if($onaylanmamis_siparis['para_cinsi'] == 'DOLAR')      $para_cinsi = '<i class="fa-solid fa-dollar-sign"></i>';
                                                            if($onaylanmamis_siparis['para_cinsi'] == 'EURO')       $para_cinsi = '<i class="fa-solid fa-euro-sign"></i>';
                                                            if($onaylanmamis_siparis['para_cinsi'] == 'POUND')      $para_cinsi = '<i class="fa-solid fa-sterling-sign"></i>';
                                                        ?>
                                                        <?php echo number_format($onaylanmamis_siparis['fiyat'], 2, ',', '.').' '.$para_cinsi; ?> 
                                                    </td>
                                                    <td >
                                                        <div class="d-flex justify-content-end"> 
                                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                                <?php if(in_array(YENI_SIPARIS_ONAY, $_SESSION['sayfa_idler'])){  ?>
                                                                    <a href="siparisler_admin_kontrol.php?siparis_id=<?php echo $onaylanmamis_siparis['id']; ?>" 
                                                                        class="btn btn-warning" 
                                                                        name="siparis_guncelle"
                                                                        data-bs-toggle="tooltip" 
                                                                        data-bs-placement="bottom" 
                                                                        data-bs-title="Kontrol"
                                                                    >
                                                                        <i class="fa-regular fa-circle-check"></i>
                                                                    </a>
                                                                <?php }?>
                                                                <?php if(in_array(SIPARIS_SIL, $_SESSION['sayfa_idler'])){  ?>
                                                                    <a href="siparisler_admin_db_islem.php?islem=iptal&siparis_id=<?php echo $onaylanmamis_siparis['id']; ?>" 
                                                                        class="btn  btn-danger" 
                                                                        onClick="return confirm('Silmek İstediğinize Emin Misiniz?')"
                                                                        data-bs-toggle="tooltip" 
                                                                        data-bs-placement="bottom" 
                                                                        data-bs-title="İptal"
                                                                    >
                                                                        <i class="fa-solid fa-ban"></i>
                                                                    </a>
                                                            </div>
                                                        </div>
                                                        <?php }?>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php }?>

                        <div class="tab-pane fade" id="nav-onaylanan" role="tabpanel" 
                            aria-labelledby="nav-tab-onaylanan" tabindex="1">
                            <div class="table-responsive">
                                <table id="myTable" class="table table-hover" >
                                    <thead class="table-primary">
                                        <tr>
                                            <th>#</th>
                                            <th>Sipariş No</th>
                                            <th>Müşteri</th>
                                            <th>İşin Adı</th>
                                            <th>M.Temsilcisi</th>
                                            <th>Termin</th>
                                            <th class="text-end">Adet</th>
                                            <th class="text-end">Fiyat</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($onaylanmis_siparisler  as $index=>$onaylanmis_siparis) { ?>
                                            <tr>
                                                <th class="table-primary"><?php echo $index+1;?></th>
                                                <td class="table-secondary"><?php echo $onaylanmis_siparis['siparis_no'];?></td>
                                                <td><?php echo $onaylanmis_siparis['marka']; ?></td>
                                                <td><?php echo $onaylanmis_siparis['isin_adi']; ?></td>
                                                <td><?php echo $onaylanmis_siparis['personel_ad_soyad']; ?></td>
                                                <td><?php echo date('d-m-Y',strtotime($onaylanmis_siparis['termin'])); ?></td>
                                                <td class="text-end">
                                                    <?php echo number_format($onaylanmis_siparis['adet'],0,'','.'); ?> Adet
                                                </td>
                                                <td class="text-end">
                                                    <?php 
                                                        $para_cinsi = '<i class="fa-solid fa-turkish-lira-sign"></i>';
                                                        if($onaylanmis_siparis['para_cinsi'] == 'DOLAR')      $para_cinsi = '<i class="fa-solid fa-dollar-sign"></i>';
                                                        if($onaylanmis_siparis['para_cinsi'] == 'EURO')       $para_cinsi = '<i class="fa-solid fa-euro-sign"></i>';
                                                        if($onaylanmis_siparis['para_cinsi'] == 'POUND')      $para_cinsi = '<i class="fa-solid fa-sterling-sign"></i>';
                                                    ?>
                                                    <?php echo number_format($onaylanmis_siparis['fiyat'], 2, ',', '.').' '.$para_cinsi; ?>
                                                </td>
                                                <td>
                                                    <div class="d-md-flex justify-content-end"> 
                                                        <div class="btn-group" role="group" aria-label="Basic example">
                                                            <?php if(in_array(TUM_SIPARISLERI_DUZENLE, $_SESSION['sayfa_idler'])){  ?>
                                                                <a href="siparisler_admin_kontrol.php?siparis_id=<?php echo $onaylanmis_siparis['id']; ?>" 
                                                                    class="btn  btn-warning" name="siparis_guncelle"
                                                                    data-bs-toggle="tooltip" 
                                                                    data-bs-placement="bottom" 
                                                                    data-bs-title="Güncelle"    
                                                                >
                                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                                </a>
                                                            <?php }?>
                                                            <?php if(in_array(SIPARIS_SIL, $_SESSION['sayfa_idler'])){  ?>
                                                                <a href="siparisler_admin_db_islem.php?islem=iptal&siparis_id=<?php echo $onaylanmis_siparis['id']; ?>" 
                                                                    class="btn  btn-danger" 
                                                                    onClick="return confirm('Silmek İstediğinize Emin Misiniz?')"
                                                                    data-bs-toggle="tooltip" 
                                                                    data-bs-placement="bottom" 
                                                                    data-bs-title="İptal"
                                                                >
                                                                    <i class="fa-solid fa-ban"></i>
                                                                </a>
                                                            <?php }?>
                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-biten" role="tabpanel" 
                            aria-labelledby="nav-tab-biten" tabindex="1">
                            <div class="table-responsive">
                                <table id="myTable" class="table table-hover" >
                                    <thead class="table-primary">
                                        <tr>
                                            <th>#</th>
                                            <th>Sipariş No</th>
                                            <th>İşin Adı</th>
                                            <th>Müşteri</th>
                                            <th>M.Temsilcisi</th>
                                            <th>Termin</th>
                                            <th class="text-end">Adet</th>
                                            <th class="text-end">Fiyat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bitmis_siparisler  as $index=>$bitmis_siparis) { ?>
                                            <tr>
                                                <th class="table-primary"><?php echo $index+1;?></th>
                                                <td class="table-secondary"><?php echo $bitmis_siparis['siparis_no'];?></td>
                                                <td><?php echo $bitmis_siparis['isin_adi']; ?></td>
                                                <td><?php echo $bitmis_siparis['marka']; ?></td>
                                                <td><?php echo $bitmis_siparis['personel_ad_soyad']; ?></td>
                                                <td><?php echo date('d-m-Y',strtotime($bitmis_siparis['termin'])); ?></td>
                                                <td class="text-end">
                                                    <?php echo number_format($bitmis_siparis['adet'],0,'','.'); ?> Adet
                                                </td>
                                                <td class="text-end">
                                                    <?php 
                                                        $para_cinsi = '<i class="fa-solid fa-turkish-lira-sign"></i>';
                                                        if($bitmis_siparis['para_cinsi'] == 'DOLAR')      $para_cinsi = '<i class="fa-solid fa-dollar-sign"></i>';
                                                        if($bitmis_siparis['para_cinsi'] == 'EURO')       $para_cinsi = '<i class="fa-solid fa-euro-sign"></i>';
                                                        if($bitmis_siparis['para_cinsi'] == 'POUND')      $para_cinsi = '<i class="fa-solid fa-sterling-sign"></i>';
                                                    ?>
                                                    <?php echo number_format($bitmis_siparis['fiyat'], 2, ',', '.').' '.$para_cinsi; ?>
                                                </td>
                                            </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php 
            include_once "include/scripts.php";
            include_once "include/uyari_session_oldur.php";
        ?>
    </body>
</html>

