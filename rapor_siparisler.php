<?php
    require_once "include/db.php";
    require_once "include/oturum_kontrol.php";
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
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>
                                <i class="fa-solid fa-flag-checkered"></i> Bitmiş Sipariş Raporları
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="myTable" class="table table-hover" >
                                    <thead class="table-primary">
                                        <tr>
                                            <th>#</th>
                                            <th>Sipariş No</th>
                                            <th>İşin Adı</th>
                                            <th>Müşteri</th>
                                            <th>Müşteri Temsilcisi</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        
                                            $sth = $conn->prepare('SELECT siparisler.id, siparisler.siparis_no, siparisler.isin_adi, 
                                            musteri.marka,
                                            personeller.ad, personeller.soyad
                                            FROM siparisler 
                                            JOIN musteri ON musteri.id = siparisler.musteri_id
                                            JOIN personeller ON personeller.id = siparisler.musteri_temsilcisi_id
                                            WHERE siparisler.firma_id = :firma_id AND siparisler.islem = "tamamlandi" ORDER BY siparisler.id DESC');
                                            $sth->bindParam('firma_id', $_SESSION['firma_id']);
                                            $sth->execute();
                                            $tamamlanmis_siparisler = $sth->fetchAll(PDO::FETCH_ASSOC);
                                        ?>
                                        <?php foreach ($tamamlanmis_siparisler as $key => $siparis) { ?>
                                            <tr>
                                                <th><?php echo $key + 1; ?></th>
                                                <td class="table-primary"><?php echo $siparis['siparis_no']; ?></td>
                                                <td><?php echo $siparis['isin_adi']; ?></td>
                                                <td><?php echo $siparis['marka']; ?></td>
                                                <td><?php echo $siparis['ad'].' '.$siparis['soyad']; ?></td>
                                                <td class="text-end">
                                                    <a href="rapor_siparis_detay.php?siparis-id=<?php echo $siparis['id'];?>" class="btn btn-sm btn-success">
                                                        <i class="fa-solid fa-flag-checkered"></i>
                                                    </a>
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
