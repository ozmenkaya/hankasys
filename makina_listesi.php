<?php
    require_once "include/db.php";
    require_once "include/oturum_kontrol.php";

    $sql = "SELECT makinalar.id, makinalar.makina_adi, makinalar.makina_modeli, makinalar.makina_seri_no,
            departmanlar.departman,departmanlar.id AS departman_id
            FROM `makinalar` 
            JOIN makina_personeller ON makina_personeller.makina_id = makinalar.id 
            JOIN departmanlar ON departmanlar.id = makinalar.departman_id
            WHERE makinalar.firma_id = :firma_id AND makina_personeller.personel_id = :personel_id AND makinalar.durumu = 'aktif'";

    $sth = $conn->prepare($sql);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->bindParam('personel_id', $_SESSION['personel_id']);
    $sth->execute();
    $makinalar = $sth->fetchAll(PDO::FETCH_ASSOC);



    $sql = "SELECT id, asama_sayisi,mevcut_asama, makinalar, departmanlar, onay_durum, durum FROM `planlama` 
        WHERE firma_id = :firma_id AND aktar_durum = 'orijinal' ";
    $sth = $conn->prepare($sql);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $planlamalar = $sth->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT makina_ekran_ipler FROM `firmalar` WHERE id = :id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('id', $_SESSION['firma_id']);
    $sth->execute();
    $firma_ayar = $sth->fetch(PDO::FETCH_ASSOC);

    $makina_ekran_ipler = array_map('trim', explode("\n", $firma_ayar['makina_ekran_ipler']));
    $my_ip              = $_SERVER['REMOTE_ADDR'];
    //echo "<pre>";print_r($_SERVER);
    //echo $my_ip;
    //print_r($makina_ekran_ipler); exit;

    
?>
<!DOCTYPE html>
<html lang="tr">
    <head>
        <?php require_once "include/head.php";?>
        <title>Hanka Sys SAAS</title> 
        <style>
            a.disabled {
                cursor: no-drop;
            }
        </style>
    </head>
    <body>
        <?php //require_once "include/header.php";?>
        <div class="container">
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card border-secondary border-2">
                        <div class="card-body d-md-flex justify-content-between">
                            <h4 class="d-flex align-items-center gap-2">
                                <i class="fa-regular fa-circle-user"></i>
                                <span class="fw-bold">Hoş Geldin, </span>
                                <span class="badge bg-secondary"><?php echo $_SESSION['ad'].' '.$_SESSION['soyad']; ?></span> 
                            </h4>
                            <div>
                                <a class="btn btn-warning fw-bold" href="sifre_guncelle.php">
                                    <i class="fa-solid fa-lock fs-4"></i>
                                    Şifre Değiştir
                                </a>
                                <a href="makina_listesi.php" class="btn btn-secondary fw-bold">
                                    <i class="fa-solid fa-retweet fs-4"></i> Yenile
                                </a>
                                <a 
                                    class="btn btn-danger fw-bold" 
                                    href="login_kontrol.php?islem=cikis-yap"
                                    onClick="return confirm('Çıkmak İstediğinize Emin Misiniz?')"
                                >
                                    <i class="fa-solid fa-arrow-right-from-bracket fs-4"></i>
                                    ÇIKIŞ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <?php foreach ($makinalar as $makina) { ?>
                    <?php 
                        $is_sayisi = 0;
                        $aktif_is_varmi = false;
                        $planlama_id = 0;
                        foreach ($planlamalar as $planlama) {
                            $planla_makinalar   = json_decode($planlama['makinalar'], true);
                            $departmanlar       = json_decode($planlama['departmanlar'], true);

                            if(isset($planla_makinalar[$planlama['mevcut_asama']]) && 
                                isset($departmanlar[$planlama['mevcut_asama']]) && 
                                $planla_makinalar[$planlama['mevcut_asama']] == $makina['id'] && 
                                $departmanlar[$planlama['mevcut_asama']] == $makina['departman_id'] && 
                                $planlama['onay_durum'] == 'evet'){
                                $is_sayisi++;
                                if($planlama['durum'] == 'basladi') 
                                {
                                    $aktif_is_varmi = true;
                                    $planlama_id = $planlama['id'];
                                }
                            }

                        }  
                    ?>
                    <div class="col-md-6 mb-3">    
                        <div class="card border-secondary border-2">
                            <div class="card-header border-secondary border-2 d-flex justify-content-between">
                                <h5>
                                    <i class="fa-solid fa-building"></i>
                                    <?php echo $makina['departman']; ?>
                                </h5>
                                <div>
                                    <span class="fw-semibold fst-italic fs-6">İş: </span>
                                    <b class="text-danger fw-bold fs-5"><?php echo $is_sayisi; ?></b>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="list-group mb-2">
                                    <li class="list-group-item">
                                        <b>Ad:      </b> 
                                        <span class="text-decoration-underline"><?php echo $makina['makina_adi']; ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Model:   </b> 
                                        <span class="text-decoration-underline"><?php echo $makina['makina_modeli']; ?></span> 
                                    </li>
                                    <li class="list-group-item">
                                        <b>Seri No: </b>
                                        <span class="text-decoration-underline"><?php echo $makina['makina_seri_no']; ?></span>
                                    </li>
                                </ul>
                                
                                <div class="d-grid gap-2">
                                    <div class="btn-group btn-group-lg" role="group" aria-label="Basic example">
                                        <?php if($aktif_is_varmi){ ?>
                                            <a href="makina_is_ekran.php?planlama-id=<?php echo $planlama_id; ?>&makina-id=<?php echo $makina['id']; ?>" class="btn btn-primary">
                                                <i class="fa-solid fa-paper-plane"></i> İşe Git
                                            </a>
                                        <?php }?>

                                        <?php if( $is_sayisi != 0){?>
                                            <a href="makina_is_listesi.php?makina-id=<?php echo $makina['id']; ?>" class="btn btn-success">
                                                <i class="fa-solid fa-list"></i> İş Listesi
                                            </a>
                                        <?php }else { ?>
                                            <a href="javascript:;" class="btn btn-danger disabled">
                                                <i class="fa-solid fa-list"></i> İş Listesi
                                            </a>
                                        <?php } ?>
                                            
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }?>

                <?php if(empty($makinalar)){?>
                    <div class="col-md-12">
                        <div class="card border-danger border-2">
                            <div class="card-body">
                                <h4 class="text-danger fw-bold">
                                    1- Sizin Sorunluluğunuz Makina Bulunmuyor! (Admin İle İletişime Geçiniz)
                                </h4>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php 
            include_once "include/scripts.php";  
            include_once "include/uyari_session_oldur.php";
        ?>
    </body>
</html>
