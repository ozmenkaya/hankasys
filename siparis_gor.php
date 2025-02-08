<?php 
    require_once "include/db.php";
    include_once "include/oturum_kontrol.php";

    $siparis_id = isset($_GET['siparis_id']) ? intval($_GET['siparis_id']) : 0;
    $sth = $conn->prepare('SELECT siparisler.*,turler.tur, odeme_tipleri.odeme_sekli, ulkeler.baslik AS ulke_adi,
        sehirler.baslik AS sehir_adi, ilceler.baslik AS ilce_adi
        FROM siparisler 
        JOIN turler ON turler.id = siparisler.tur_id  
        JOIN odeme_tipleri ON odeme_tipleri.id = siparisler.odeme_sekli_id
        JOIN ulkeler ON ulkeler.id = siparisler.ulke_id
        JOIN sehirler ON sehirler.id = siparisler.sehir_id
        JOIN ilceler ON ilceler.id = siparisler.ilce_id
        WHERE siparisler.id=:id');
    $sth->execute([':id' => $siparis_id]);
    $siparis = $sth->fetch(PDO::FETCH_ASSOC);
    
    if(empty($siparis))
    {
        include_once "include/yetkisiz.php";
        die();
    }

    $sth = $conn->prepare('SELECT ad FROM siparis_dosyalar WHERE siparis_id = :siparis_id');
    $sth->bindParam('siparis_id', $siparis['id']);
    $sth->execute();
    $siparis_dosyalar = $sth->fetchAll(PDO::FETCH_ASSOC);


    //echo "<pre>"; print_r(json_decode($siparis['veriler'],true)); exit;

    $sql = "SELECT * FROM `siparis_dosyalar` WHERE siparis_id = :siparis_id";
    $sth = $conn->prepare($sql);
    $sth->bindParam('siparis_id',$siparis_id);
    $sth->execute();
    $siparis_resimler = $sth->fetchAll(PDO::FETCH_ASSOC);

    $veriler = json_decode($siparis['veriler'], true);

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

        <div class="container-fluid mb-4">
            <div class="card mb-2">
                <div class="card-header d-flex justify-content-between">
                    <h5>
                        <i class="fa-solid fa-bag-shopping"></i> Sipariş Detay
                    </h5>
                    <div>
                        <div class="d-md-flex justify-content-end"> 
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
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <ul class="list-group mb-2">
                                <li class="list-group-item active fw-bold" aria-current="true">Sipariş Bilgileri</li>
                                <li class="list-group-item">
                                    <strong>İşin Adı :</strong><?php echo $siparis['isin_adi']; ?> 
                                </li>
                                <li class="list-group-item">
                                    <strong>Türü :</strong><?php echo $siparis['tur']; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Adet :</strong><?php echo number_format($siparis['adet'], 0, '','.'); ?>
                                </li>
                                <li class="list-group-item list-group-item-danger">
                                    <strong>Teslimat Adresi :</strong><?php echo $siparis['teslimat_adresi']; ?>
                                </li>
                                <li class="list-group-item list-group-item-danger">
                                    <strong>Teslimat Ülkesi :</strong><?php echo $siparis['ulke_adi']; ?>
                                </li>
                                <li class="list-group-item list-group-item-danger">
                                    <strong>Teslimat Şehri :</strong><?php echo $siparis['sehir_adi']; ?>
                                </li>
                                <li class="list-group-item list-group-item-danger">
                                    <strong>Teslimat İlçesi :</strong><?php echo $siparis['ilce_adi']; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Termin Tarihi :</strong><?php echo date('d-m-Y', strtotime($siparis['termin'])); ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Üretim Tarihi :</strong><?php echo date('d-m-Y',strtotime($siparis['uretim'])); ?>
                                </li>
                                <?php   
                                    $sth = $conn->prepare('SELECT personeller.ad, personeller.soyad FROM `siparisler` JOIN personeller ON siparisler.musteri_temsilcisi_id = personeller.id 
                                                            WHERE siparisler.id = :id');
                                    $sth->bindParam('id', $siparis['id']);
                                    $sth->execute();
                                    $musteri_temsilci = $sth->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <li class="list-group-item">
                                    <strong>Müşteri Temsilcisi :</strong><?php echo $musteri_temsilci['ad'].' '.$musteri_temsilci['soyad'] ; ?>
                                </li>
                                <?php   
                                    $sth = $conn->prepare('SELECT musteri.marka FROM `siparisler` 
                                                            JOIN musteri ON siparisler.musteri_id = musteri.id 
                                                            WHERE siparisler.id = :id');
                                    $sth->bindParam('id', $siparis['id']);
                                    $sth->execute();
                                    $musteri = $sth->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <li class="list-group-item">
                                    <strong>Müşteri : </strong><?php echo $musteri['marka'] ; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Vade Tarihi : </strong><?php echo date('d-m-Y', strtotime($siparis['vade'])); ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Fiyat :</strong><?php echo number_format($siparis['fiyat'], 2, ',','.'); ?> <?php echo $siparis['para_cinsi']; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Ödeme Şekli :</strong><?php echo $siparis['odeme_sekli']; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Paketleme  :</strong><?php echo $siparis['paketleme']; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Nakliye  :</strong><?php echo $siparis['nakliye']; ?>
                                </li>
                            </ul>
                        </div>
                        
                        <?php if($siparis['tip_id'] == TEK_URUN){?>
                            <div class="col-md-2">
                                <?php $veri = $veriler; ?>
                                <ul class="list-group mb-2">
                                    <li class="list-group-item list-group-item-success fw-bold" aria-current="true">1. Alt Ürün</li>
                                    <li class="list-group-item"><b>İsim:</b> <?php echo $veri['isim']; ?></li>
                                    <li class="list-group-item"><b>Miktar:</b> <?php echo number_format($veri['miktar'],0,'',','); ?></li>
                                    <li class="list-group-item"><b>Birim Fiyat:</b> <?php echo number_format($veri['birim_fiyat'],3,'.',','); ?></li>
                                    <li class="list-group-item"><b>KDV: </b> %<?php echo $veri['kdv']; ?></li>
                                    <?php 
                                        $sth = $conn->prepare('SELECT * FROM `birimler`  WHERE id = :id');
                                        $sth->bindParam('id', $veri['birim_id']);
                                        $sth->execute();
                                        $birim = $sth->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <li class="list-group-item"><b>Birim:</b> <?php echo $birim['ad']; ?></li>
                                    <li class="list-group-item">
                                        <b>Numune:</b> 
                                        <?php if($veri['numune'] == 0){?> 
                                            <span class="badge text-bg-success">VAR</span>
                                        <?php }else{?> 
                                            <span class="badge text-bg-success">YOK</span>
                                        <?php }?>
                                    </li>
                                    <li class="list-group-item"><b>Açıklama:</b> <?php echo $veri['aciklama']; ?></li>
                                    
                                    <?php foreach ($veri['form']as $key => $value) { ?>
                                        <?php if(!empty($value)){ ?>
                                            <li class="list-group-item list-group-item-warning">
                                                <b><?php echo $key; ?>:</b> <?php echo $value; ?>
                                            </li>
                                        <?php } ?>
                                    <?php }?>
                                </ul>
                                <div class="border border-secondary-subtle rounded p-1">
                                    <?php foreach ($siparis_resimler as $siparis_dosya) { ?>
                                        <a class="example-image-link" href="dosyalar/siparisler/<?php echo $siparis_dosya['ad']; ?>" 
                                                    data-lightbox="example-set" data-title="Sipariş Resimleri">
                                            <img src="dosyalar/siparisler/<?php echo $siparis_dosya['ad']; ?>" 
                                                class="rounded img-fluid border border-secondary-subtle mb-1 mt-1 object-fit-fill lightbox-resim" 
                                            >
                                        </a>
                                        <?php if(empty($siparis_resimler)){?>
                                            <h6 class="text-danger fw-bold">Dosya Yok</h6>
                                        <?php } ?>
                                    <?php } ?>
                                </div> 
                            </div>
                        <?php }else if(in_array($siparis['tip_id'], [GRUP_URUN_TEK_FIYAT, GRUP_URUN_AYRI_FIYAT])){?>
                            <?php foreach($veriler as $index => $veri){ ?>
                                <div class="col-md-2">
                                    <ul class="list-group mb-2">
                                        <li class="list-group-item list-group-item-success fw-bold" aria-current="true"><?php echo $index+1;?>. Alt Ürün</li>
                                        <li class="list-group-item"><b>KDV: </b> %<?php echo $veri['kdv']; ?></li>
                                        <li class="list-group-item"><b>İsim:</b> <?php echo $veri['isim']; ?></li>
                                        <li class="list-group-item"><b>Miktar:</b> <?php echo number_format($veri['miktar'],0,'',','); ?></li>
                                        <li class="list-group-item"><b>Birim Fiyat:</b> <?php echo number_format($veri['birim_fiyat'],3,'.',','); ?></li>
                                        <?php 
                                            $sth = $conn->prepare('SELECT * FROM `birimler`  WHERE id = :id');
                                            $sth->bindParam('id', $veri['birim_id']);
                                            $sth->execute();
                                            $birim = $sth->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                        <li class="list-group-item"><b>Birim:</b> <?php echo $birim['ad']; ?></li>
                                        <li class="list-group-item">
                                            <b>Numune:</b> 
                                            <?php if($veri['numune'] == 0){?> 
                                                <span class="badge text-bg-success">VAR</span>
                                            <?php }else{?> 
                                                <span class="badge text-bg-success">YOK</span>
                                            <?php }?>
                                        </li>
                                        <li class="list-group-item"><b>Açıklama:</b> <?php echo $veri['aciklama']; ?></li>
                                        
                                        <?php foreach ($veri['form'] as $key => $value) { ?>
                                            <?php if(!empty($value)){ ?>
                                                <li class="list-group-item list-group-item-warning">
                                                    <b><?php echo $key; ?>:</b> <?php echo $value; ?>
                                                </li>
                                            <?php } ?>
                                        <?php }?>
                                    </ul>

                                    <div class="border border-secondary-subtle rounded p-1">
                                        <?php $resim_varmi = false; ?>
                                        <?php foreach ($siparis_resimler as $siparis_dosya) { ?>
                                            <?php if($index == $siparis_dosya['alt_urun_index']){ ?>
                                                <?php $resim_varmi = true; ?>
                                                <a class="example-image-link-<?php echo $index; ?>" 
                                                        href="dosyalar/siparisler/<?php echo $siparis_dosya['ad']; ?>" 
                                                        data-lightbox="example-set-<?php echo $index; ?>" data-title="Sipariş Resimleri"
                                                >
                                                    <img src="dosyalar/siparisler/<?php echo $siparis_dosya['ad']; ?>" 
                                                        class="rounded img-fluid border border-secondary-subtle mb-1 mt-1 object-fit-fill lightbox-resim" 
                                                    >
                                                </a>
                                            <?php }?>
                                        <?php } ?> 
                                        <?php if(!$resim_varmi){?>
                                            <h6 class="text-danger fw-bold">Dosya Yok</h6>
                                        <?php }?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Log</h5>
                </div>
                <div class="card-body">
                    <table id="myTable" class="table table-hover" >
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>İşlem</th>
                                <th>Değerler</th>
                                <th class="text-end">Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $sth = $conn->prepare('SELECT islem, onceki_degerler,sonraki_degerler,islem_tarihi 
                                FROM siparis_log WHERE siparis_id = :siparis_id ORDER BY id DESC');
                                $sth->bindParam('siparis_id',  $siparis_id);
                                $sth->execute();
                                $siparis_loglar = $sth->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <?php foreach ($siparis_loglar as $key => $siparis_log) { ?>
                                <?php 
                                    $onceki_degerler    = json_decode($siparis_log['onceki_degerler'], true);
                                    $sonraki_degerler   = json_decode($siparis_log['sonraki_degerler'], true);
                                    //$onceki_degerler    = empty($onceki_degerler) ? [] : $onceki_degerler;
                                ?>
                                <tr>
                                    <th class="table-primary"><?php echo $key+1;?></th>
                                    <td><?php echo $siparis_log['islem']?></td>
                                    <td >
                                        <?php 
                                            //echo print_r($onceki_degerler)."<br>";
                                            //echo print_r($sonraki_degerler);
                                        ?>
                                        <?php foreach ($onceki_degerler as $key=>$onceki_deger) { ?>
                                            <?php if(isset($sonraki_degerler[$key]) && $sonraki_degerler[$key] != $onceki_deger){ ?>
                                                Önceki/Sonraki: <?php echo $key; ?> => <?php echo $onceki_deger;?>
                                                / <?php echo $sonraki_degerler[$key];?>
                                            <br>
                                            <?php }?>
                                        <?php }?>
                                    </td>
                                    <td class="text-end">
                                        <?php echo date('d-m-Y H:i:s', strtotime($siparis_log['islem_tarihi']));?>
                                    </td>
                                </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
        <?php include "include/scripts.php" ?>
        <script>
            $(function(){
                
            });
        </script>
    </body>
</html>
