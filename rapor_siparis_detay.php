<?php
    require_once "include/db.php";
    require_once "include/oturum_kontrol.php";

    $siparis_id = isset($_GET['siparis-id']) ? intval($_GET['siparis-id']) : 0;
    $sql = 'SELECT siparisler.siparis_no, siparisler.isin_adi, siparisler.tarih,siparisler.termin,
    siparisler.fiyat, siparisler.para_cinsi,siparisler.adet,
    musteri.marka
    FROM `siparisler` 
    JOIN musteri ON musteri.id = siparisler.musteri_id
    WHERE siparisler.id = :id AND  siparisler.firma_id = :firma_id';
    //AND siparisler.islem = "tamamlandi"
    $sth = $conn->prepare($sql);
    $sth->bindParam('id', $siparis_id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $siparis = $sth->fetch(PDO::FETCH_ASSOC);


    //echo "<pre>"; print_r($siparis_planlamalari); exit;

    if(empty($siparis)){
        include_once "include/yetkisiz.php";
        die();
    }

    $sql = "SELECT id FROM `planlama` 
            WHERE siparis_id = :siparis_id AND firma_id = :firma_id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('siparis_id', $siparis_id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $planlamalar = $sth->fetchAll(PDO::FETCH_ASSOC);

    $planlamalar_idler              = array_column($planlamalar, 'id');
    $planlamalar_idler_birlestir    = implode(',', $planlamalar_idler);

    //print_r($planlamalar_idler);exit;

?>
<!DOCTYPE html>
<html lang="tr">
    <head>
        <title>Hanka Sys SAAS</title> 
        <?php require_once "include/head.php";?>
    </head>
    <body>
        <?php 
            require_once "include/header.php";
            require_once "include/sol_menu.php";
        ?>
        <div class="container-fluid">
            <div class="card border-secondary border-2">
                <div class="card-header">
                    <h5>
                        <i class="fa-solid fa-flag-checkered"></i> 
                        Sipariş Kodu: <b class="text-danger"><?php echo $siparis['siparis_no']; ?></b>  / 
                        Müşteri İsmi: <b><?php echo $siparis['marka']; ?></b> / 
                        İşin Adı: <b><?php echo $siparis['isin_adi']; ?></b>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <?php 
                                $teslim_tarihi = date('Y-m-d H:i:s');
                                $startTime = new DateTime($siparis['termin']);
                                $endTime = new DateTime($teslim_tarihi);
                                
                                $interval = $startTime->diff($endTime);
                                $daysDifference = $interval->format('%a');
                                
                            ?>
                            <div class="p-3 text-white bg-primary rounded-3 ">
                                <h4 class="p-1">
                                    <?php echo abs($daysDifference); ?> Gün 
                                    <?php echo $daysDifference >= 0 ?  'Erken' : 'Geç';?> Teslim Edildi
                                </h4>
                                <div class="row">
                                    <div class="col-md-5 fw-bold">
                                        <i class="fa-regular fa-calendar-days"></i> Sipariş Tarihi:
                                    </div>
                                    <div class="col-md-7 text-end">
                                        <h6><?php echo date('d-m-Y', strtotime($siparis['tarih'])); ?></h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 fw-bold">
                                        <i class="fa-regular fa-calendar-days"></i> Termin Tarihi:
                                    </div>
                                    <div class="col-md-7 text-end">
                                        <h6><?php echo date('d-m-Y', strtotime($siparis['termin'])); ?></h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 fw-bold">
                                        <i class="fa-regular fa-calendar-days"></i> Teslim Tarihi:
                                    </div>
                                    <div class="col-md-7 text-end">
                                        <h6><?php echo date('d-m-Y', strtotime($teslim_tarihi)); ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <?php 
                                $para_cinsi = '<i class="fa-solid fa-turkish-lira-sign"></i>';
                                if($siparis['para_cinsi'] == 'DOLAR')      $para_cinsi = '<i class="fa-solid fa-dollar-sign"></i>';
                                if($siparis['para_cinsi'] == 'EURO')       $para_cinsi = '<i class="fa-solid fa-euro-sign"></i>';
                                if($siparis['para_cinsi'] == 'POUND')      $para_cinsi = '<i class="fa-solid fa-sterling-sign"></i>';
                            ?>
                            <?php 
                                $maliyet    = 100;
                                $fire       = 200;
                                $toplam     = $siparis['fiyat'] - ($maliyet + $fire);
                            ?>
                            <div class="p-3 text-white bg-success rounded-3">
                                
                                <h4 class="p-1">
                                    <?php echo number_format(abs($toplam),2).' '.$para_cinsi;?>  
                                    <?php echo $toplam >= 0 ?  ' Kar' : ' Zarar'; ?>

                                    (<?php echo  number_format((abs($toplam)/$siparis['fiyat'])*100);?> %)
                                </h4>
                                <div class="row">
                                    
                                    <div class="col-md-5 fw-bold">
                                        <i class="fa-solid fa-wallet"></i> Satış Fiyatı:
                                    </div>
                                    <div class="col-md-7 text-end">
                                        <h6><?php echo number_format($siparis['fiyat'],2).' '.$para_cinsi; ?></h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 fw-bold">
                                        <i class="fa-solid fa-wallet"></i> Maliyet:
                                    </div>
                                    <div class="col-md-7 text-end">
                                        <h6><?php echo number_format($maliyet,2).' '.$para_cinsi; ?></h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 fw-bold">
                                        <i class="fa-solid fa-wallet"></i> Fire Maliyet:
                                    </div>
                                    <div class="col-md-7 text-end">
                                        <h6><?php echo number_format($fire,2).' '.$para_cinsi; ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <?php 
                                $sql = "SELECT SUM(uretilecek_adet) AS uretilecek_adet, 
                                        SUM(teslim_edilen_urun_adedi) AS teslim_edilen_urun_adedi,
                                        SUM(biten_urun_adedi) AS biten_urun_adedi
                                        FROM `planlama`
                                        WHERE siparis_id = :siparis_id AND aktar_durum = 'orijinal'";

                                $sth = $conn->prepare($sql);
                                $sth->bindParam("siparis_id", $siparis_id);
                                $sth->execute();
                                $planlama_uretilen_teslim_edilen = $sth->fetch(PDO::FETCH_ASSOC);

                                $uretilecek_adet    = $planlama_uretilen_teslim_edilen['uretilecek_adet'];
                                $teslim_adeti       = $planlama_uretilen_teslim_edilen['teslim_edilen_urun_adedi'];
                                $biten_urun_adedi   = $planlama_uretilen_teslim_edilen['biten_urun_adedi'];
                            ?>
                            <div class="p-3 text-white bg-primary rounded-3">
                                <h4 class="p-1">
                                    <?php echo number_format(abs($uretilecek_adet-$biten_urun_adedi));?>  
                                    <?php if($uretilecek_adet == $biten_urun_adedi  ){ ?>
                                        Tam Üretildi
                                    <?php }elseif($uretilecek_adet-$biten_urun_adedi > 0 ){ ?>
                                        Fazla Üretildi
                                    <?php }else{ ?>
                                        Eksik Üretildi
                                    <?php } ?>
                                </h4>
                                <div class="row">
                                    <div class="col-md-5 fw-bold">
                                        <i class="fa-solid fa-arrow-down-1-9"></i> Sipariş Adedi:
                                    </div>
                                    <div class="col-md-7 text-end">
                                        <h6><?php echo number_format($uretilecek_adet); ?></h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 fw-bold">
                                        <i class="fa-solid fa-arrow-down-1-9"></i> Üretilen Adet:
                                    </div>
                                    <div class="col-md-7 text-end">
                                        <h6><?php echo number_format($biten_urun_adedi); ?></h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 fw-bold">
                                        <i class="fa-solid fa-arrow-down-1-9"></i> Teslim Adedi:
                                    </div>
                                    <div class="col-md-7 text-end">
                                        <h6><?php echo number_format($teslim_adeti, 0); ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row d-flex  mb-4">
                        <div class="col-md-4">
                            <?php 
                                $uretim_yemek_mola_log_gecen_sure   = 0;
                                $uretim_mola_log_gecen_sure         = 0;
                                $uretim_ariza_log_gecen_sure        = 0;
                                $uretim_bakim_log_gecen_sure        = 0;
                                $uretim_toplanti_log_gecen_sure     = 0;

                                if(!empty($planlamalar_idler_birlestir)){
                                    $sql = "SELECT SUM(TIMESTAMPDIFF(SECOND,baslatma_tarihi,bitis_tarihi)) AS toplam_sure
                                        FROM `uretim_yemek_mola_log` 
                                        WHERE planlama_id IN({$planlamalar_idler_birlestir})";
                                    $sth = $conn->prepare($sql);
                                    $sth->execute();
                                    $uretim_yemek_mola_log = $sth->fetch(PDO::FETCH_ASSOC);
                                    $uretim_yemek_mola_log_gecen_sure = empty($uretim_yemek_mola_log['toplam_sure']) ? 0 : $uretim_yemek_mola_log['toplam_sure'];

                                    $sql = "SELECT SUM(TIMESTAMPDIFF(SECOND,baslatma_tarihi,bitis_tarihi)) AS toplam_sure
                                        FROM `uretim_mola_log` 
                                        WHERE planlama_id IN({$planlamalar_idler_birlestir})";
                                    $sth = $conn->prepare($sql);
                                    $sth->execute();
                                    $uretim_mola_log = $sth->fetch(PDO::FETCH_ASSOC);
                                    $uretim_mola_log_gecen_sure = empty($uretim_mola_log['toplam_sure']) ? 0 : $uretim_mola_log['toplam_sure'];

                                    $sql = "SELECT SUM(TIMESTAMPDIFF(SECOND,baslatma_tarihi,bitis_tarihi)) AS toplam_sure
                                        FROM `uretim_ariza_log` 
                                        WHERE planlama_id IN({$planlamalar_idler_birlestir})";
                                    $sth = $conn->prepare($sql);
                                    $sth->execute();
                                    $uretim_ariza_log = $sth->fetch(PDO::FETCH_ASSOC);
                                    $uretim_ariza_log_gecen_sure = empty($uretim_ariza_log['toplam_sure']) ? 0 : $uretim_ariza_log['toplam_sure'];

                                    $sql = "SELECT SUM(TIMESTAMPDIFF(SECOND,baslatma_tarihi,bitis_tarihi)) AS toplam_sure
                                        FROM `uretim_bakim_log` 
                                        WHERE planlama_id IN({$planlamalar_idler_birlestir})";
                                    $sth = $conn->prepare($sql);
                                    $sth->execute();
                                    $uretim_bakim_log = $sth->fetch(PDO::FETCH_ASSOC);
                                    $uretim_bakim_log_gecen_sure = empty($uretim_bakim_log['toplam_sure']) ? 0 : $uretim_bakim_log['toplam_sure'];

                                    $sql = "SELECT SUM(TIMESTAMPDIFF(SECOND,baslatma_tarihi,bitis_tarihi)) AS toplam_sure
                                        FROM `uretim_toplanti_log` 
                                        WHERE planlama_id IN({$planlamalar_idler_birlestir})";
                                    $sth = $conn->prepare($sql);
                                    $sth->execute();
                                    $uretim_toplanti_log = $sth->fetch(PDO::FETCH_ASSOC);
                                    $uretim_toplanti_log_gecen_sure = empty($uretim_toplanti_log['toplam_sure']) ? 0 : $uretim_toplanti_log['toplam_sure'];
                                }
                                
                                $uretim_mola_log_gecen_sure_hh_mm_ss        = $uretim_mola_log_gecen_sure == 0          ?  '00:00:00':secondToHHMMSS($uretim_mola_log_gecen_sure);
                                $uretim_yemek_mola_log_gecen_sure_hh_mm_ss  = $uretim_yemek_mola_log_gecen_sure == 0    ?  '00:00:00':secondToHHMMSS($uretim_yemek_mola_log_gecen_sure);
                                
                                $uretim_ariza_log_gecen_sure_hh_mm_ss       = $uretim_ariza_log_gecen_sure == 0         ?  '00:00:00':secondToHHMMSS($uretim_ariza_log_gecen_sure);
                                $uretim_bakim_log_gecen_sure_hh_mm_ss       = $uretim_bakim_log_gecen_sure == 0         ?  '00:00:00':secondToHHMMSS($uretim_bakim_log_gecen_sure);
                                $uretim_toplanti_log_gecen_sure_hh_mm_ss    = $uretim_toplanti_log_gecen_sure == 0      ?  '00:00:00':secondToHHMMSS($uretim_toplanti_log_gecen_sure);
                            
                            ?>
                            <div class="p-3 text-white bg-primary rounded-3">
                                <h4 class="p-2">
                                    <?php 
                                        $plansiz_toplam_durma_hh_mm_ss = timeToSeconds(secondToHHMMSS($uretim_ariza_log_gecen_sure));
                                        $plansiz_toplam_durma_hh_mm_ss += timeToSeconds(secondToHHMMSS($uretim_bakim_log_gecen_sure));
                                        $plansiz_toplam_durma_hh_mm_ss += timeToSeconds(secondToHHMMSS($uretim_toplanti_log_gecen_sure));
                                        
                                    ?> 
                                    <b>Plansız Toplam Durma(<?php echo secondsToTime($plansiz_toplam_durma_hh_mm_ss); ?>)</b>
                                </h4>
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <h5 class="fw-bold">
                                            <?php echo $uretim_toplanti_log_gecen_sure_hh_mm_ss;?>
                                        </h5>
                                        <p class="fw-bold"> 
                                            Toplantı <br>Süresi
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h5 class="fw-bold">
                                            <?php echo $uretim_ariza_log_gecen_sure_hh_mm_ss;?>
                                        </h5>
                                        <p class="fw-bold"> 
                                            Arıza <br>Süresi
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h5 class="fw-bold">
                                            <?php   
                                                echo $uretim_bakim_log_gecen_sure_hh_mm_ss;
                                            ?>
                                        </h5>
                                        <p class="fw-bold"> 
                                            Bakım <br> Süresi
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <?php 
                                $sql = "SELECT TIMESTAMPDIFF(SECOND,MIN(baslatma_tarih),MAX(bitirme_tarihi)) AS gecen_sure, 
                                        MIN(baslatma_tarih) AS baslatma_tarih 
                                        FROM `uretim_islem_tarihler` 
                                        WHERE planlama_id IN({$planlamalar_idler_birlestir})";
                                $sth = $conn->prepare($sql);
                                $sth->execute();
                                $gecen_sure = $sth->fetch(PDO::FETCH_ASSOC);
                                //print_r($gecen_sure);
                            ?>
                            <div class="p-3 text-white bg-success rounded-3">
                                <h4 class="p-2">
                                    Toplam Üretim Süresi
                                </h4>
                                <div class="row mb-3 ps-2">
                                    <div class="col-md-6">
                                        <h4>
                                            <?php   
                                                if(empty($gecen_sure['gecen_sure'])){
                                                    $toplam_brut_hh_mm_ss = secondToHHMMSS(strtotime(date('Y-m-d H:i:s')) - strtotime($gecen_sure['baslatma_tarih']));
                                                    $totalSeconds = strtotime(date('Y-m-d H:i:s')) - strtotime($gecen_sure['baslatma_tarih']) - ($uretim_mola_log_gecen_sure + $uretim_yemek_mola_log_gecen_sure + 
                                                                                            $uretim_ariza_log_gecen_sure + $uretim_bakim_log_gecen_sure);
                                                }else{
                                                    $toplam_brut_hh_mm_ss = secondToHHMMSS($gecen_sure['gecen_sure']);
                                                    $totalSeconds = $gecen_sure['gecen_sure'] - ($uretim_mola_log_gecen_sure + $uretim_yemek_mola_log_gecen_sure + 
                                                                                            $uretim_ariza_log_gecen_sure + $uretim_bakim_log_gecen_sure);
                                                }
                                                echo secondsToTime($totalSeconds); 
                                            ?>  
                                        </h4>
                                        <p class="fw-bold">Net Süre</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h4>
                                            <?php   
                                                echo $toplam_brut_hh_mm_ss;
                                            ?>
                                        </h4>
                                        <p class="fw-bold"> 
                                            Brüt Süre
                                        </p>
                                    </div>
                                </div>     
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 text-white bg-primary rounded-3">
                                <?php 
                                    $sql = "SELECT SUM(fire_miktari) AS fire_miktari 
                                            FROM `stok_alt_depolar_kullanilanlar` WHERE  planlama_id IN({$planlamalar_idler_birlestir})";
                                    $sth = $conn->prepare($sql);
                                    $sth->execute();
                                    $stok_alt_depolar_kullanilanlar_fire_adet = $sth->fetch(PDO::FETCH_ASSOC);


                                    $sql = "SELECT SUM(uretirken_verilen_fire_adet) AS uretirken_verilen_fire_adet 
                                            FROM `uretilen_adetler` WHERE firma_id = :firma_id AND planlama_id IN({$planlamalar_idler_birlestir})";
                                    $sth = $conn->prepare($sql);
                                    $sth->bindParam('firma_id', $_SESSION['firma_id']);
                                    $sth->execute();
                                    $uretirken_verilen_fire_adet = $sth->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <h4 class="p-2">
                                    Toplam Fire
                                </h4>
                                <div class="row ps-2 mb-2">
                                    <div class="col-md-5 fw-bold">Stok Fire:</div>
                                    <div class="col-md-7 text-end">
                                        <h6>
                                            <?php echo number_format($stok_alt_depolar_kullanilanlar_fire_adet['fire_miktari']);?> Adet
                                        </h6>
                                    </div>
                                </div>
                                <div class="row ps-2 mb-4">
                                    <div class="col-md-5 fw-bold">Ürün Fire:</div>
                                    <div class="col-md-7 text-end">
                                        <h6>
                                            <?php echo number_format($uretirken_verilen_fire_adet['uretirken_verilen_fire_adet']);?> Adet
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row d-flex  mb-4">
                        <div class="col-md-4">
                            <div class="p-3 text-white bg-primary rounded-3">
                                <?php 
                                    $planli_toplam_durma_hh_mm_ss = timeToSeconds(secondToHHMMSS($uretim_yemek_mola_log_gecen_sure));
                                    $planli_toplam_durma_hh_mm_ss += timeToSeconds(secondToHHMMSS($uretim_mola_log_gecen_sure));
                                ?> 
                                <h4 class="p-2">
                                    <b>Planlı Toplam Durma(<?php echo secondsToTime($planli_toplam_durma_hh_mm_ss); ?>)</b>
                                </h4>
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <h5 class="fw-bold">
                                            <?php echo $uretim_mola_log_gecen_sure_hh_mm_ss;?>
                                        </h5>
                                        <p class="fw-bold"> 
                                            Mola<br> Süresi
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h5 class="fw-bold">
                                            <?php echo $uretim_yemek_mola_log_gecen_sure_hh_mm_ss;?>
                                        </h5>
                                        <p class="fw-bold"> 
                                            Yemek<br>Süresi
                                        </p>
                                    </div>
                                </div>
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
