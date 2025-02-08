<?php 
    include "include/db.php";
    include "include/oturum_kontrol.php";

    $sth = $conn->prepare('SELECT siparisler.id, siparisler.siparis_no, siparisler.isin_adi, siparisler.termin, 
                            siparisler.fiyat, siparisler.adet,
                            musteri.marka, CONCAT_WS(" ", personeller.ad, personeller.soyad) AS personel_ad_soyad
                            FROM siparisler 
                            JOIN musteri ON siparisler.musteri_id = musteri.id
                            JOIN personeller ON personeller.id  = siparisler.musteri_temsilcisi_id
                            WHERE siparisler.firma_id = :firma_id AND  onay_baslangic_durum = "evet" AND islem != "iptal"
                            ORDER BY siparisler.id DESC
                                                    ');
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $siparisler = $sth->fetchAll(PDO::FETCH_ASSOC);

    $islemdeki_siparis_sayisi = 0;
    $planlanmamis_siparis_sayisi = 0;
    $onay_bekleyen_siparis_sayisi = 0;

    foreach ($siparisler as $siparis) {
        $sth = $conn->prepare('SELECT planlama_durum,onay_durum FROM planlama 
            WHERE firma_id = :firma_id AND siparis_id = :siparis_id');
        $sth->bindParam('firma_id', $_SESSION['firma_id']);
        $sth->bindParam('siparis_id', $siparis['id']);
        $sth->execute();
        $planlama = $sth->fetch(PDO::FETCH_ASSOC);

        if(!isset($planlama['planlama_durum']) || in_array($planlama['planlama_durum'], ['hayır','yarım_kalmıs'])){ 
            $planlanmamis_siparis_sayisi++;
        }elseif(isset($planlama['onay_durum']) && $planlama['onay_durum'] == 'hayır' ){
            $onay_bekleyen_siparis_sayisi++;
        }elseif(isset($planlama['onay_durum']) && $planlama['onay_durum'] == 'evet' ){
            $islemdeki_siparis_sayisi++;
        }
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
                        <i class="fa-solid fa-list-check"></i> Planlamalar
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
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active position-relative fw-bold" id="nav-tab-onaylanmayan" data-bs-toggle="tab" 
                                data-bs-target="#nav-onaylanmayan" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                                Planlamayı Bekleyen
                                <span class="position-absolute top-0 start-70 translate-middle badge rounded-pill bg-danger fs-6">
                                    <?php echo $planlanmamis_siparis_sayisi;?>
                                    <span class="visually-hidden">Planlamayı Bekleyen</span>
                                </span>
                            </button>

                            <button class="nav-link position-relative fw-bold" id="nav-tab-onay-bekleyen" data-bs-toggle="tab" 
                                data-bs-target="#nav-onay-bekleyen" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                                Onay Bekleyenler 
                                <span class="position-absolute top-0 start-70 translate-middle badge rounded-pill bg-success fs-6">
                                    <?php echo $onay_bekleyen_siparis_sayisi; ?>
                                    <span class="visually-hidden">Onay Bekleyenler </span>
                                </span>
                            </button>

                            <button class="nav-link position-relative fw-bold" id="nav-tab-onaylanan" data-bs-toggle="tab" 
                                data-bs-target="#nav-onaylanan" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                                İşlemdekiler
                                <span class="position-absolute top-0 start-70 translate-middle badge rounded-pill bg-info fs-6">
                                    <?php echo $islemdeki_siparis_sayisi; ?>
                                    <span class="visually-hidden">İşlemdekiler</span>
                                </span>
                            </button>
                        </div>
                    </nav>
                    <div class="tab-content mt-3" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-onaylanmayan" role="tabpanel" 
                            aria-labelledby="nav-tab-onaylanmayan" tabindex="0">
                            <div class="table-responsive">
                                <table id="myTable" class="table table-hover table-striped">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Sıra</th>
                                            <th>Sipariş No</th>
                                            <th>İşin Adı</th>
                                            <th>Müşteri</th>
                                            <th>Müşteri Temsilcisi</th>
                                            <th>Termin</th>
                                            <th class="text-end">Adet</th>
                                            <th class="text-end">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sira = 0; ?>
                                        <?php foreach ($siparisler  as $siparis) { ?>
                                            <?php 
                                                $sth = $conn->prepare('SELECT planlama_durum FROM planlama 
                                                    WHERE firma_id = :firma_id AND siparis_id = :siparis_id');
                                                $sth->bindParam('firma_id', $_SESSION['firma_id']);
                                                $sth->bindParam('siparis_id', $siparis['id']);
                                                $sth->execute();
                                                $planlama = $sth->fetch(PDO::FETCH_ASSOC);
                                                //print_r($planlama_durumu);
                                            ?>
                                            <?php if(!isset($planlama['planlama_durum']) || in_array($planlama['planlama_durum'], ['hayır','yarım_kalmıs'])  ){ ?>
                                                <tr>
                                                    <th class="table-primary"><?php echo ++$sira;?></th>
                                                    <th class="table-secondary"><?php echo $siparis['siparis_no'];?></th>
                                                    <td><?php echo $siparis['isin_adi']; ?></td>
                                                    <td><?php echo $siparis['marka']; ?></td>
                                                    <td><?php echo $siparis['personel_ad_soyad']; ?></td>
                                                    <td><?php echo date('d-m-Y',strtotime($siparis['termin'])); ?></td>
                                                    <td class="text-end">
                                                        <?php echo number_format($siparis['adet'],0,'',','); ?> Adet
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-end"> 
                                                            <div class="btn-group" role="group">
                                                                <?php if(in_array(PLANLAMA, $_SESSION['sayfa_idler']) && 
                                                                    (!isset($planlama['planlama_durum']) || $planlama['planlama_durum'] == 'hayır')){  ?>
                                                                    <a href="planla_siparis.php?siparis_id=<?php echo $siparis['id']; ?>" 
                                                                        class="btn btn-sm btn-success"
                                                                        data-bs-toggle="tooltip"
                                                                        data-bs-placement="bottom" 
                                                                        data-bs-title="Planla"
                                                                    >
                                                                        <i class="fa-solid fa-list-check"></i>
                                                                    </a>
                                                                <?php }else if(in_array(PLANLAMA, $_SESSION['sayfa_idler']) && 
                                                                        (!isset($planlama['planlama_durum']) || $planlama['planlama_durum'] == 'yarım_kalmıs' ) ){ ?> 
                                                                    <a href="planla_siparis_duzenle.php?siparis_id=<?php echo $siparis['id']; ?>" 
                                                                        class="btn btn-sm btn-warning" 
                                                                        data-bs-toggle="tooltip" 
                                                                        data-bs-placement="bottom" 
                                                                        data-bs-title="Güncelle"
                                                                    >
                                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                                    </a>
                                                                <?php }?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-onay-bekleyen" role="tabpanel" 
                            aria-labelledby="nav-tab-onaylanan" tabindex="1">
                            <div class="table-responsive">
                                <table id="myTable" class="table table-hover table-striped" >
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Sıra</th>
                                            <th>Sipariş No</th>
                                            <th>Müşteri</th>
                                            <th>İşin Adı</th>
                                            <th>Müşteri Temsilcisi</th>
                                            <th>Termin</th>
                                            <th class="text-end">Adet</th>
                                            <th class="text-end">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sira = 0; ?>
                                        <?php foreach ($siparisler  as $siparis) { ?>
                                            <?php 
                                                $sth = $conn->prepare('SELECT onay_durum,planlama_durum FROM planlama 
                                                    WHERE firma_id = :firma_id AND siparis_id = :siparis_id');
                                                $sth->bindParam('firma_id', $_SESSION['firma_id']);
                                                $sth->bindParam('siparis_id', $siparis['id']);
                                                $sth->execute();
                                                $planlama = $sth->fetch(PDO::FETCH_ASSOC);
                                                //print_r($planlama_durumu);
                                            ?>
                                            <?php if(isset($planlama['onay_durum']) && $planlama['onay_durum'] == 'hayır' 
                                                    && $planlama['planlama_durum'] == 'evet'){ ?>
                                                <tr>
                                                    <th class="table-primary"><?php echo ++$sira; ?></th>
                                                    <th class="table-secondary"><?php echo $siparis['siparis_no'];?></th>
                                                    <td><?php echo $siparis['marka']; ?></td>
                                                    <td><?php echo $siparis['isin_adi']; ?></td>
                                                    <td><?php echo $siparis['personel_ad_soyad']; ?></td>
                                                    <td><?php echo date('d-m-Y',strtotime($siparis['termin'])); ?></td>
                                                    <td class="text-end">
                                                        <?php echo number_format($siparis['adet'],0,'',','); ?> Adet
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="d-flex justify-content-end"> 
                                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                                <a href="planlama_db_islem.php?islem=planlama-pdf&siparis_id=<?php echo $siparis['id']; ?>" 
                                                                    class="btn btn-secondary" 
                                                                    data-bs-toggle="tooltip" 
                                                                    data-bs-placement="bottom" 
                                                                    data-bs-title="Planlama PDF"
                                                                >
                                                                    <i class="fa-regular fa-file-pdf"></i>
                                                                </a>
                                                                <?php  if(in_array(PLANLAMA, $_SESSION['sayfa_idler'])){   ?>
                                                                    <a href="planla_siparis_duzenle.php?siparis_id=<?php echo $siparis['id']; ?>" 
                                                                        class="btn btn-success" name="siparis_guncelle"
                                                                        data-bs-toggle="tooltip" 
                                                                        data-bs-placement="bottom" 
                                                                        data-bs-title="Planlamayı Onayla"
                                                                    >
                                                                        <i class="fa-regular fa-circle-check"></i>
                                                                    </a>
                                                                <?php } ?>  
                                                                <a href="planla_siparis_duzenle.php?siparis_id=<?php echo $siparis['id']; ?>" 
                                                                    class="btn btn-warning" 
                                                                    data-bs-toggle="tooltip" 
                                                                    data-bs-placement="bottom" 
                                                                    data-bs-title="Güncelle"
                                                                >
                                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                                </a>  
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-onaylanan" role="tabpanel" 
                            aria-labelledby="nav-tab-onaylanan" tabindex="1">
                            <div class="table-responsive">
                                <table id="myTable" class="table table-hover table-striped" >
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Sıra</th>
                                            <th>Sipariş No</th>
                                            <th>Müşteri</th>
                                            <th>İşin Adı</th>
                                            <th>Müşteri Temsilcisi</th>
                                            <th>Termin</th>
                                            <th class="text-end">Adet</th>
                                            <th class="text-end">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sira = 0; ?>
                                        <?php foreach ($siparisler  as $siparis) { ?>

                                            <?php 
                                                $sth = $conn->prepare('SELECT onay_durum FROM planlama 
                                                    WHERE firma_id = :firma_id AND siparis_id = :siparis_id');
                                                $sth->bindParam('firma_id', $_SESSION['firma_id']);
                                                $sth->bindParam('siparis_id', $siparis['id']);
                                                $sth->execute();
                                                $planlama = $sth->fetch(PDO::FETCH_ASSOC);
                                                //print_r($planlama_durumu);
                                            ?>
                                            <?php if(isset($planlama['onay_durum']) && $planlama['onay_durum'] == 'evet'){ ?>
                                                <tr>
                                                    <th class="table-primary"><?php echo ++$sira;?></th>
                                                    <th class="table-secondary"><?php echo $siparis['siparis_no'];?></th>
                                                    <td><?php echo $siparis['marka']; ?></td>
                                                    <td><?php echo $siparis['isin_adi']; ?></td>
                                                    <td><?php echo $siparis['personel_ad_soyad']; ?></td>
                                                    <td><?php echo date('d-m-Y',strtotime($siparis['termin'])); ?></td>
                                                    <td class="text-end">
                                                        <?php echo number_format($siparis['adet'],0,'',','); ?> Adet
                                                    </td>
                                                    <td class="text-end">
                                                        <?php //if(isset($_SESSION['sayfa_yetki_46']) && $_SESSION['sayfa_yetki_46'] == 1){  ?>
                                                            <a href="planla_siparis_duzenle.php?siparis_id=<?php echo $siparis['id']; ?>" class="btn btn-warning" 
                                                                name="siparis_guncelle"
                                                                data-bs-toggle="tooltip" 
                                                                data-bs-placement="bottom" 
                                                                data-bs-title="Planı Düzenle"
                                                            >
                                                                <i class="fa-regular fa-pen-to-square"></i>
                                                            </a>
                                                        <?php //}?>    
                                                    </td>
                                                </tr>
                                            <?php }?>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
            
        </div>
        <?php include_once "include/scripts.php"; ?>
        <?php include_once "include/uyari_session_oldur.php"; ?>
    </body>
</html>

