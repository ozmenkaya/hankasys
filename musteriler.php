<?php 
    include "include/db.php";
    include_once "include/oturum_kontrol.php";

    if(!in_array(MUSTERI_GOR, $_SESSION['sayfa_idler']))
    {
        require_once "include/yetkisiz.php";
        die();
    }
    

    if($_SESSION['yetki_id'] == ADMIN_YETKI_ID)
    {
        $sth = $conn->prepare('SELECT musteri.id, musteri.marka, musteri.firma_unvani, musteri.yetkili_mail,musteri.yetkili_cep,
        personeller.ad, personeller.soyad  
        FROM musteri LEFT JOIN personeller ON musteri.musteri_temsilcisi_id = personeller.id 
        WHERE  musteri.firma_id = :firma_id');
        $sth->bindParam('firma_id', $_SESSION['firma_id']);
        $sth->execute();
    }
    else 
    {
        $sth = $conn->prepare('SELECT musteri.id, musteri.marka, musteri.firma_unvani, musteri.yetkili_mail,musteri.yetkili_cep,
        personeller.ad, personeller.soyad  
        FROM musteri LEFT JOIN personeller ON musteri.musteri_temsilcisi_id = personeller.id 
        WHERE musteri.musteri_temsilcisi_id = :musteri_temsilcisi_id 
            AND musteri.firma_id = :firma_id ');
        $sth->bindParam('musteri_temsilcisi_id', $_SESSION['personel_id']);
        $sth->bindParam('firma_id', $_SESSION['firma_id']);
        $sth->execute();
    }

    $musteriler = $sth->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="tr">
    <head>
        <title>Hanka Sys SAAS</title> 
        <?php require_once "include/head.php";?>
    </head>
    <body>
        <?php require_once "include/header.php";?>
        <?php require_once "include/sol_menu.php";?>
        <div class="container-fluid">
            <div class="card border-secondary border-2">
                <div class="card-header d-md-flex justify-content-between border-secondary">
                    <h5>
                        <i class="fa-solid fa-users"></i> Müşteriler 
                        <span class="text-primary fw-bold fs-6">(<?php echo count($musteriler); ?> Kişi)</span>
                    </h5>
                    <h5>
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span class="text-primary fw-bold fs-6" id="is-sayisi"></span>
                    </h5>
                    <div>
                        <div class="d-md-flex justify-content-end"> 
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <a href="javascript:window.history.back();" 
                                    class="btn btn-secondary"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" 
                                    data-bs-title="Geri Dön"
                                >
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>
                                <a href="musteri_db_islem.php?islem=musteri_excel" 
                                    class="btn btn-success"
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="bottom" 
                                    data-bs-title="Excel"
                                >
                                    <i class="fa-regular fa-file-excel"></i>
                                </a>
                                <?php if(in_array(MUSTERI_OLUSTUR, $_SESSION['sayfa_idler'])){ ?>
                                    <a href="musteri_ekle.php" class="btn btn-primary" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="bottom" 
                                        data-bs-title="Müşteri Ekle"
                                    >
                                        <i class="fa-solid fa-user-plus"></i>
                                    </a>
                                <?php }?>
                            </div>
                        </div>
                    </div>	
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="myTable" class="table table-hover table-striped">
                            <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Marka</th>
                                <th>Firma Ünvanı</th>
                                <th>M.Temsilcisi</th>
                                <th><i class="fa-regular fa-envelope"></i> Y. Email</th>
                                <th><i class="fa-solid fa-phone"></i> Y. Tel</th>
                                <th class="text-end">İş</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php $toplam_is_sayisi = 0; ?>
                                <?php foreach($musteriler as $key=>$musteri){ ?>
                                    <?php 
                                        $sth = $conn->prepare("SELECT COUNT(*) AS toplam_is FROM siparisler 
                                                WHERE musteri_id = {$musteri['id']} AND firma_id =:firma_id AND islem != 'iptal'");
                                        $sth->bindParam('firma_id', $_SESSION['firma_id']);
                                        $sth->execute();
                                        $siparis = $sth->fetch(PDO::FETCH_ASSOC);    
                                    ?>
                                    <tr>
                                        <th class="table-primary"><?php echo $key + 1; ?> </th>
                                        <td><?php echo $musteri['marka']; ?></td>
                                        <td><?php echo $musteri['firma_unvani']; ?></td>
                                        <td><?php echo $musteri['ad'].' '.$musteri['soyad']; ?></td>
                                        <td>
                                            
                                            <i class="fa-regular fa-envelope"></i> 
                                            <?php echo $musteri['yetkili_mail']; ?>
                                            
                                        </td>
                                        <th>
                                            <a href="tel:<?php echo $musteri['yetkili_mail']; ?>" 
                                                class="badge bg-secondary p-1 fw-bold fs-6 text-decoration-none">
                                                <i class="fa-solid fa-phone"></i>
                                                <?php echo $musteri['yetkili_cep'] ; ?>
                                            </a>
                                        </th>
                                        <th class="text-end">
                                            <?php echo $siparis['toplam_is'] ; ?> Adet
                                        </th>
                                        <td>
                                            <div class="d-flex justify-content-end"> 
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <?php if(in_array(SIPARIS_EKLE, $_SESSION['sayfa_idler'])){ ?>
                                                        <a href="siparis_ekle.php?musteri_id=<?php echo $musteri['id']; ?>" 
                                                            type="button" 
                                                            class="btn btn-primary"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="bottom" 
                                                            data-bs-title="Sipariş Ekle"
                                                        >
                                                            <i class="fa-solid fa-plus"></i>
                                                        </a>
                                                    <?php } ?>
                                                    <?php if(in_array(SIPARIS_GOR, $_SESSION['sayfa_idler'])){ ?>
                                                        <a href="siparis.php?musteri_id=<?php echo $musteri['id']; ?>" type="button" 
                                                            class="btn btn-secondary"
                                                            class="btn btn-warning"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="bottom" 
                                                            data-bs-title="Sipariş Listesi"
                                                            >
                                                            <i class="fa-solid fa-table-list"></i>
                                                        </a>
                                                    <?php } ?>

                                                    <?php if(in_array(MUSTERI_GUNCELLE, $_SESSION['sayfa_idler'])){ ?>
                                                        <a href="musteri_guncelle.php?id=<?php echo $musteri['id']; ?>" type="button" 
                                                            class="btn btn-warning"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="bottom" 
                                                            data-bs-title="Güncelle"
                                                        >
                                                            <i class="fa-regular fa-pen-to-square"></i>
                                                        </a>
                                                    <?php } ?>
                                                    <?php if(in_array(MUSTERI_SIL, $_SESSION['sayfa_idler'])){ ?>
                                                        <?php if($siparis['toplam_is'] == 0){?>
                                                            <a href="musteri_db_islem.php?islem=musteri_sil&id=<?php echo $musteri['id']; ?>" 
                                                                onClick="return confirm('Silmek İstediğinize Emin Misiniz?')" 
                                                                class="btn btn-danger"
                                                                data-bs-toggle="tooltip" 
                                                                data-bs-placement="bottom" 
                                                                data-bs-title="Sil"
                                                            >
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </a>
                                                        <?php }else{ ?>
                                                            <button
                                                                class="btn btn-danger disabled"
                                                                data-bs-toggle="tooltip" 
                                                                data-bs-placement="bottom" 
                                                                data-bs-title="Sil"
                                                            >
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                        <?php }?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </td> 
                                    </tr>
                                    <?php $toplam_is_sayisi += $siparis['toplam_is']; ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php 
            include_once "include/scripts.php"; 
            include_once "include/uyari_session_oldur.php";
        ?>
        <script>
            $(function(){
                $("#is-sayisi").text(`Toplam : (<?php echo $toplam_is_sayisi; ?> İş)`);
            });
        </script>

    </body>
</html>
