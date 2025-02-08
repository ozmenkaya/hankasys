<?php
    require_once "include/db.php";
    require_once "include/oturum_kontrol.php";
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
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fa-solid fa-laptop-code fs-4"></i> KOD NOTLAR
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    1- MAKİNA EKRANI
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <ol class="list-group list-group-numbered">
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto mb-2">
                                                <div class="fw-bold mb-3 text-success">
                                                    <i class="fa-regular fa-circle-play fa-2x"></i> İŞİ BAŞLAT
                                                </div>
                                                <p>
                                                    <mark class="p-2 rounded">
                                                        <b class="text-warning me-2"><i class="fa-regular fa-pen-to-square"></i>(update)</b>
                                                        planlama
                                                    </mark>
                                                    <ul class="list-group ms-3">
                                                        <li class="list-group-item fw-bold">
                                                            1- <span class="badge bg-secondary fw-bold fs-6 p-2 fst-italic">durum = 'basladi'</span>
                                                        </li>
                                                    </ul>

                                                    <?php 
                                                        $sql = "SHOW FULL COLUMNS FROM planlama;";
                                                        $sth = $conn->prepare($sql);
                                                        $sth->execute();
                                                        $kolonlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                                                        //echo "<pre>";print_r($kolonlar);
                                                    ?>
                                                    <div class="accordion ms-3 mt-2" id="accordion-planlama">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOnePlanlama" aria-expanded="true" aria-controls="collapseOne">
                                                                    <i class="fa-solid fa-list-check me-2"></i> 
                                                                    Planlama Tablosu
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOnePlanlama" class="accordion-collapse collapse" data-bs-parent="#accordion-planlama">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered border-primary table-striped border-2">
                                                                            <thead>
                                                                                <tr class="table-success">
                                                                                    <th>#</th>
                                                                                    <th>Field</th>
                                                                                    <th>Type</th>
                                                                                    <th>Collation</th>
                                                                                    <th>Null</th>
                                                                                    <th>Key</th>
                                                                                    <th>Default</th>
                                                                                    <th>Extra</th>
                                                                                    <th>Comment</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($kolonlar as $index => $kolon) { ?>
                                                                                    <tr>
                                                                                        <th><?php echo $index+1;?></th>
                                                                                        <th><?php echo $kolon['Field']?></th>
                                                                                        <td><?php echo $kolon['Type']?></td>
                                                                                        <td><?php echo $kolon['Collation']?></td>
                                                                                        <td><?php echo $kolon['Null']?></td>
                                                                                        <td><?php echo $kolon['Key']?></td>
                                                                                        <td><?php echo $kolon['Default']?></td>
                                                                                        <td><?php echo $kolon['Extra']?></td>
                                                                                        <td><?php echo $kolon['Comment']?></td>
                                                                                    </tr>
                                                                                <?php }?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </p>

                                                <p>
                                                    <mark class="p-2 rounded">
                                                        <b class="text-warning me-2"><i class="fa-regular fa-pen-to-square"></i>(update)</b>
                                                        siparisler
                                                        <b>[Aynı Siparişte Hiçbir İş Başlamamışsa]</b>
                                                    </mark>

                                                    <ul class="list-group ms-3">
                                                        <li class="list-group-item fw-bold">
                                                            1- <span class="badge bg-secondary fw-bold fs-6 p-2 fst-italic">islem = 'islemde'</span>
                                                        </li>
                                                    </ul>

                                                    <?php 
                                                        $sql = "SHOW FULL COLUMNS FROM siparisler;";
                                                        $sth = $conn->prepare($sql);
                                                        $sth->execute();
                                                        $kolonlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <div class="accordion ms-3 mt-2" id="accordion-siparisler">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOneSiparisler" aria-expanded="true" aria-controls="collapseOne">
                                                                    <i class="fa-solid fa-bag-shopping me-2"></i> Sipariş Tablosu
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOneSiparisler" class="accordion-collapse collapse" data-bs-parent="#accordion-siparisler">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered border-primary table-striped border-2">
                                                                            <thead>
                                                                                <tr class="table-success">
                                                                                    <th>#</th>
                                                                                    <th>Field</th>
                                                                                    <th>Type</th>
                                                                                    <th>Collation</th>
                                                                                    <th>Null</th>
                                                                                    <th>Key</th>
                                                                                    <th>Default</th>
                                                                                    <th>Extra</th>
                                                                                    <th>Comment</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($kolonlar as $index => $kolon) { ?>
                                                                                    <tr>
                                                                                        <th><?php echo $index+1;?></th>
                                                                                        <th><?php echo $kolon['Field']?></th>
                                                                                        <td><?php echo $kolon['Type']?></td>
                                                                                        <td><?php echo $kolon['Collation']?></td>
                                                                                        <td><?php echo $kolon['Null']?></td>
                                                                                        <td><?php echo $kolon['Key']?></td>
                                                                                        <td><?php echo $kolon['Default']?></td>
                                                                                        <td><?php echo $kolon['Extra']?></td>
                                                                                        <td><?php echo $kolon['Comment']?></td>
                                                                                    </tr>
                                                                                <?php }?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </p>

                                                <p>
                                                    <mark class="p-2 rounded mb-2">
                                                        <b class="text-success me-2"><i class="fa-solid fa-plus"></i>(insert)</b>
                                                        stok_alt_depolar_kullanilanlar
                                                    </mark>
                                                    <?php 
                                                        $sql = "SHOW FULL COLUMNS FROM stok_alt_depolar_kullanilanlar;";
                                                        $sth = $conn->prepare($sql);
                                                        $sth->execute();
                                                        $kolonlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <div class="accordion ms-3 mt-2" id="accordion-stok_alt_depolar_kullanilanlar">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOnestok_alt_depolar_kullanilanlar" aria-expanded="true" aria-controls="collapseOne">
                                                                    <i class="fa-sharp fa-solid fa-layer-group me-2"></i> Stok Alt Depolar Kullanılanlar Tablosu
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOnestok_alt_depolar_kullanilanlar" class="accordion-collapse collapse" data-bs-parent="#accordion-stok_alt_depolar_kullanilanlar">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered border-primary table-striped border-2">
                                                                            <thead>
                                                                                <tr class="table-success">
                                                                                    <th>#</th>
                                                                                    <th>Field</th>
                                                                                    <th>Type</th>
                                                                                    <th>Collation</th>
                                                                                    <th>Null</th>
                                                                                    <th>Key</th>
                                                                                    <th>Default</th>
                                                                                    <th>Extra</th>
                                                                                    <th>Comment</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($kolonlar as $index => $kolon) { ?>
                                                                                    <tr>
                                                                                        <th><?php echo $index+1;?></th>
                                                                                        <th><?php echo $kolon['Field']?></th>
                                                                                        <td><?php echo $kolon['Type']?></td>
                                                                                        <td><?php echo $kolon['Collation']?></td>
                                                                                        <td><?php echo $kolon['Null']?></td>
                                                                                        <td><?php echo $kolon['Key']?></td>
                                                                                        <td><?php echo $kolon['Default']?></td>
                                                                                        <td><?php echo $kolon['Extra']?></td>
                                                                                        <td><?php echo $kolon['Comment']?></td>
                                                                                    </tr>
                                                                                <?php }?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </p>

                                                <p>
                                                    <mark class="p-2 rounded mb-2">
                                                        <b class="text-success me-2"><i class="fa-solid fa-plus"></i>(insert)</b>
                                                        uretim_islem_tarihler
                                                    </mark>
                                                    <?php 
                                                        $sql = "SHOW FULL COLUMNS FROM uretim_islem_tarihler;";
                                                        $sth = $conn->prepare($sql);
                                                        $sth->execute();
                                                        $kolonlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <div class="accordion ms-3 mt-2" id="accordion-uretim_islem_tarihler">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOneuretim_islem_tarihler" aria-expanded="true" aria-controls="collapseOne">
                                                                    <i class="fa-solid fa-gears me-2"></i> Uretim Islem Tarihler Tablosu
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOneuretim_islem_tarihler" class="accordion-collapse collapse" data-bs-parent="#accordion-uretim_islem_tarihler">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered border-primary table-striped border-2">
                                                                            <thead>
                                                                                <tr class="table-success">
                                                                                    <th>#</th>
                                                                                    <th>Field</th>
                                                                                    <th>Type</th>
                                                                                    <th>Collation</th>
                                                                                    <th>Null</th>
                                                                                    <th>Key</th>
                                                                                    <th>Default</th>
                                                                                    <th>Extra</th>
                                                                                    <th>Comment</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($kolonlar as $index => $kolon) { ?>
                                                                                    <tr>
                                                                                        <th><?php echo $index+1;?></th>
                                                                                        <th><?php echo $kolon['Field']?></th>
                                                                                        <td><?php echo $kolon['Type']?></td>
                                                                                        <td><?php echo $kolon['Collation']?></td>
                                                                                        <td><?php echo $kolon['Null']?></td>
                                                                                        <td><?php echo $kolon['Key']?></td>
                                                                                        <td><?php echo $kolon['Default']?></td>
                                                                                        <td><?php echo $kolon['Extra']?></td>
                                                                                        <td><?php echo $kolon['Comment']?></td>
                                                                                    </tr>
                                                                                <?php }?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </p>

                                                <p>
                                                    <mark class="p-2 rounded mb-2">
                                                        <b class="text-success me-2"><i class="fa-solid fa-plus"></i>(insert)</b>
                                                        departman_form_degerler 
                                                        <b>[Form Varsa]</b>
                                                    </mark>
                                                    <?php 
                                                        $sql = "SHOW FULL COLUMNS FROM departman_form_degerler;";
                                                        $sth = $conn->prepare($sql);
                                                        $sth->execute();
                                                        $kolonlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <div class="accordion ms-3 mt-2" id="accordion-departman_form_degerler">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOnedepartman_form_degerler" aria-expanded="true" aria-controls="collapseOne">
                                                                    <i class="fa-solid fa-align-justify me-2"></i>  Departman Form Degerler Tablosu
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOnedepartman_form_degerler" class="accordion-collapse collapse" data-bs-parent="#accordion-departman_form_degerler">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered border-primary table-striped border-2">
                                                                            <thead>
                                                                                <tr class="table-success">
                                                                                    <th>#</th>
                                                                                    <th>Field</th>
                                                                                    <th>Type</th>
                                                                                    <th>Collation</th>
                                                                                    <th>Null</th>
                                                                                    <th>Key</th>
                                                                                    <th>Default</th>
                                                                                    <th>Extra</th>
                                                                                    <th>Comment</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($kolonlar as $index => $kolon) { ?>
                                                                                    <tr>
                                                                                        <th><?php echo $index+1;?></th>
                                                                                        <th><?php echo $kolon['Field']?></th>
                                                                                        <td><?php echo $kolon['Type']?></td>
                                                                                        <td><?php echo $kolon['Collation']?></td>
                                                                                        <td><?php echo $kolon['Null']?></td>
                                                                                        <td><?php echo $kolon['Key']?></td>
                                                                                        <td><?php echo $kolon['Default']?></td>
                                                                                        <td><?php echo $kolon['Extra']?></td>
                                                                                        <td><?php echo $kolon['Comment']?></td>
                                                                                    </tr>
                                                                                <?php }?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </p>
                                            </div>
                                        </li>

                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto mb-2">
                                                <div class="fw-bold mb-3 text-success">
                                                    <i class="fa-solid fa-mug-saucer fa-2x"></i> MOLA
                                                </div>
                                                <p>
                                                    <mark class="p-2 rounded">
                                                        <b class="text-success"><i class="fa-solid fa-plus"></i>(insert)</b>
                                                        uretim_mola_log
                                                    </mark>
                                                    <?php 
                                                        $sql = "SHOW FULL COLUMNS FROM uretim_mola_log;";
                                                        $sth = $conn->prepare($sql);
                                                        $sth->execute();
                                                        $kolonlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <div class="accordion ms-3 mt-2" id="accordion-uretim_mola_log">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOneuretim_mola_log" aria-expanded="true" aria-controls="collapseOne">
                                                                    <i class="fa-solid fa-mug-saucer me-2"></i> Mola Tablosu
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOneuretim_mola_log" class="accordion-collapse collapse" data-bs-parent="#accordion-uretim_mola_log">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered border-primary table-striped border-2">
                                                                            <thead>
                                                                                <tr class="table-success">
                                                                                    <th>#</th>
                                                                                    <th>Field</th>
                                                                                    <th>Type</th>
                                                                                    <th>Collation</th>
                                                                                    <th>Null</th>
                                                                                    <th>Key</th>
                                                                                    <th>Default</th>
                                                                                    <th>Extra</th>
                                                                                    <th>Comment</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($kolonlar as $index => $kolon) { ?>
                                                                                    <tr>
                                                                                        <th><?php echo $index+1;?></th>
                                                                                        <th><?php echo $kolon['Field']?></th>
                                                                                        <td><?php echo $kolon['Type']?></td>
                                                                                        <td><?php echo $kolon['Collation']?></td>
                                                                                        <td><?php echo $kolon['Null']?></td>
                                                                                        <td><?php echo $kolon['Key']?></td>
                                                                                        <td><?php echo $kolon['Default']?></td>
                                                                                        <td><?php echo $kolon['Extra']?></td>
                                                                                        <td><?php echo $kolon['Comment']?></td>
                                                                                    </tr>
                                                                                <?php }?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </p>
                                            </div>
                                        </li>

                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto mb-2">
                                                <div class="fw-bold mb-3 text-success">
                                                    <i class="fa-solid fa-utensils fa-2x"></i> YEMEK
                                                </div>
                                                <p>
                                                    <mark class="p-2 rounded">
                                                        <b class="text-success"><i class="fa-solid fa-plus"></i>(insert)</b>
                                                        uretim_yemek_mola_log
                                                    </mark>
                                                    <?php 
                                                        $sql = "SHOW FULL COLUMNS FROM uretim_yemek_mola_log;";
                                                        $sth = $conn->prepare($sql);
                                                        $sth->execute();
                                                        $kolonlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <div class="accordion ms-3 mt-2" id="accordion-uretim_yemek_mola_log">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOneuretim_yemek_mola_log" aria-expanded="true" aria-controls="collapseOne">
                                                                    <i class="fa-solid fa-utensils me-2"></i> Yemek Mola Tablosu
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOneuretim_yemek_mola_log" class="accordion-collapse collapse" data-bs-parent="#accordion-uretim_yemek_mola_log">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered border-primary table-striped border-2">
                                                                            <thead>
                                                                                <tr class="table-success">
                                                                                    <th>#</th>
                                                                                    <th>Field</th>
                                                                                    <th>Type</th>
                                                                                    <th>Collation</th>
                                                                                    <th>Null</th>
                                                                                    <th>Key</th>
                                                                                    <th>Default</th>
                                                                                    <th>Extra</th>
                                                                                    <th>Comment</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($kolonlar as $index => $kolon) { ?>
                                                                                    <tr>
                                                                                        <th><?php echo $index+1;?></th>
                                                                                        <th><?php echo $kolon['Field']?></th>
                                                                                        <td><?php echo $kolon['Type']?></td>
                                                                                        <td><?php echo $kolon['Collation']?></td>
                                                                                        <td><?php echo $kolon['Null']?></td>
                                                                                        <td><?php echo $kolon['Key']?></td>
                                                                                        <td><?php echo $kolon['Default']?></td>
                                                                                        <td><?php echo $kolon['Extra']?></td>
                                                                                        <td><?php echo $kolon['Comment']?></td>
                                                                                    </tr>
                                                                                <?php }?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </p>
                                                
                                            </div>
                                        </li>

                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto mb-2">
                                                <div class="fw-bold mb-3 text-success">
                                                    <i class="fa-solid fa-handshake fa-2x"></i> TOPLANTI
                                                </div>
                                                <p>
                                                    <mark class="p-2 rounded">
                                                        <b class="text-success"><i class="fa-solid fa-plus"></i>(insert)</b>
                                                        uretim_toplanti_log
                                                    </mark>
                                                    <?php 
                                                        $sql = "SHOW FULL COLUMNS FROM uretim_toplanti_log;";
                                                        $sth = $conn->prepare($sql);
                                                        $sth->execute();
                                                        $kolonlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <div class="accordion ms-3 mt-2" id="accordion-uretim_toplanti_log">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOneuretim_toplanti_log" aria-expanded="true" aria-controls="collapseOne">
                                                                    <i class="fa-solid fa-handshake me-2"></i> Toplantı Tablosu
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOneuretim_toplanti_log" class="accordion-collapse collapse" data-bs-parent="#accordion-uretim_toplanti_log">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered border-primary table-striped border-2">
                                                                            <thead>
                                                                                <tr class="table-success">
                                                                                    <th>#</th>
                                                                                    <th>Field</th>
                                                                                    <th>Type</th>
                                                                                    <th>Collation</th>
                                                                                    <th>Null</th>
                                                                                    <th>Key</th>
                                                                                    <th>Default</th>
                                                                                    <th>Extra</th>
                                                                                    <th>Comment</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($kolonlar as $index => $kolon) { ?>
                                                                                    <tr>
                                                                                        <th><?php echo $index+1;?></th>
                                                                                        <th><?php echo $kolon['Field']?></th>
                                                                                        <td><?php echo $kolon['Type']?></td>
                                                                                        <td><?php echo $kolon['Collation']?></td>
                                                                                        <td><?php echo $kolon['Null']?></td>
                                                                                        <td><?php echo $kolon['Key']?></td>
                                                                                        <td><?php echo $kolon['Default']?></td>
                                                                                        <td><?php echo $kolon['Extra']?></td>
                                                                                        <td><?php echo $kolon['Comment']?></td>
                                                                                    </tr>
                                                                                <?php }?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </p>
                                                
                                            </div>
                                        </li>

                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto mb-2">
                                                <div class="fw-bold mb-3 text-success">
                                                    <i class="fa-solid fa-right-from-bracket fa-2x"></i> PAYDOS
                                                </div>
                                                <p>
                                                    <mark class="p-2 rounded mb-2">
                                                        <b class="text-success me-2"><i class="fa-solid fa-plus"></i>(insert)</b> 
                                                        uretim_paydos_loglar
                                                    </mark>
                                                    <?php 
                                                        $sql = "SHOW FULL COLUMNS FROM uretim_paydos_loglar;";
                                                        $sth = $conn->prepare($sql);
                                                        $sth->execute();
                                                        $kolonlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <div class="accordion ms-3 mt-2" id="accordion-uretim_paydos_loglar">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOneuretim_paydos_loglar" aria-expanded="true" aria-controls="collapseOne">
                                                                    <i class="fa-solid fa-handshake me-2"></i> Üretim Paydos Tablosu
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOneuretim_paydos_loglar" class="accordion-collapse collapse" data-bs-parent="#accordion-uretim_paydos_loglar">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered border-primary table-striped border-2">
                                                                            <thead>
                                                                                <tr class="table-success">
                                                                                    <th>#</th>
                                                                                    <th>Field</th>
                                                                                    <th>Type</th>
                                                                                    <th>Collation</th>
                                                                                    <th>Null</th>
                                                                                    <th>Key</th>
                                                                                    <th>Default</th>
                                                                                    <th>Extra</th>
                                                                                    <th>Comment</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($kolonlar as $index => $kolon) { ?>
                                                                                    <tr>
                                                                                        <th><?php echo $index+1;?></th>
                                                                                        <th><?php echo $kolon['Field']?></th>
                                                                                        <td><?php echo $kolon['Type']?></td>
                                                                                        <td><?php echo $kolon['Collation']?></td>
                                                                                        <td><?php echo $kolon['Null']?></td>
                                                                                        <td><?php echo $kolon['Key']?></td>
                                                                                        <td><?php echo $kolon['Default']?></td>
                                                                                        <td><?php echo $kolon['Extra']?></td>
                                                                                        <td><?php echo $kolon['Comment']?></td>
                                                                                    </tr>
                                                                                <?php }?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </p>
                                                <p>
                                                    <mark class="p-2 rounded mb-2">
                                                        <b class="text-success me-2"><i class="fa-solid fa-plus"></i>(insert)</b>
                                                        uretilen_adetler
                                                    </mark>
                                                    <?php 
                                                        $sql = "SHOW FULL COLUMNS FROM uretilen_adetler;";
                                                        $sth = $conn->prepare($sql);
                                                        $sth->execute();
                                                        $kolonlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <div class="accordion ms-3 mt-2" id="accordion-uretilen_adetler">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOneuretilen_adetler" aria-expanded="true" aria-controls="collapseOne">
                                                                    <i class="fa-solid fa-handshake me-2"></i> Üretim Adetler Tablosu
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOneuretilen_adetler" class="accordion-collapse collapse" data-bs-parent="#accordion-uretilen_adetler">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered border-primary table-striped border-2">
                                                                            <thead>
                                                                                <tr class="table-success">
                                                                                    <th>#</th>
                                                                                    <th>Field</th>
                                                                                    <th>Type</th>
                                                                                    <th>Collation</th>
                                                                                    <th>Null</th>
                                                                                    <th>Key</th>
                                                                                    <th>Default</th>
                                                                                    <th>Extra</th>
                                                                                    <th>Comment</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($kolonlar as $index => $kolon) { ?>
                                                                                    <tr>
                                                                                        <th><?php echo $index+1;?></th>
                                                                                        <th><?php echo $kolon['Field']?></th>
                                                                                        <td><?php echo $kolon['Type']?></td>
                                                                                        <td><?php echo $kolon['Collation']?></td>
                                                                                        <td><?php echo $kolon['Null']?></td>
                                                                                        <td><?php echo $kolon['Key']?></td>
                                                                                        <td><?php echo $kolon['Default']?></td>
                                                                                        <td><?php echo $kolon['Extra']?></td>
                                                                                        <td><?php echo $kolon['Comment']?></td>
                                                                                    </tr>
                                                                                <?php }?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </p>
                                                <p>
                                                    <mark class="p-2 rounded mb-2">
                                                        <b class="text-warning me-2"><i class="fa-regular fa-pen-to-square"></i>(update)</b>
                                                        uretim_islem_tarihler
                                                    </mark>
                                                    <?php 
                                                        $sql = "SHOW FULL COLUMNS FROM uretim_islem_tarihler;";
                                                        $sth = $conn->prepare($sql);
                                                        $sth->execute();
                                                        $kolonlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <div class="accordion ms-3 mt-2" id="accordion-uretim_islem_tarihler">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOneuretim_islem_tarihler" aria-expanded="true" aria-controls="collapseOne">
                                                                    <i class="fa-solid fa-handshake me-2"></i> Üretim Adetler Tablosu
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOneuretim_islem_tarihler" class="accordion-collapse collapse" data-bs-parent="#accordion-uretim_islem_tarihler">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered border-primary table-striped border-2">
                                                                            <thead>
                                                                                <tr class="table-success">
                                                                                    <th>#</th>
                                                                                    <th>Field</th>
                                                                                    <th>Type</th>
                                                                                    <th>Collation</th>
                                                                                    <th>Null</th>
                                                                                    <th>Key</th>
                                                                                    <th>Default</th>
                                                                                    <th>Extra</th>
                                                                                    <th>Comment</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($kolonlar as $index => $kolon) { ?>
                                                                                    <tr>
                                                                                        <th><?php echo $index+1;?></th>
                                                                                        <th><?php echo $kolon['Field']?></th>
                                                                                        <td><?php echo $kolon['Type']?></td>
                                                                                        <td><?php echo $kolon['Collation']?></td>
                                                                                        <td><?php echo $kolon['Null']?></td>
                                                                                        <td><?php echo $kolon['Key']?></td>
                                                                                        <td><?php echo $kolon['Default']?></td>
                                                                                        <td><?php echo $kolon['Extra']?></td>
                                                                                        <td><?php echo $kolon['Comment']?></td>
                                                                                    </tr>
                                                                                <?php }?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </p>
                                                <p>
                                                    <mark class="p-2 rounded mb-2">
                                                        <b class="text-warning me-2"><i class="fa-regular fa-pen-to-square"></i>(update)</b>
                                                        planlama
                                                    </mark>
                                                </p>
                                                <p>
                                                    <mark class="p-2 rounded mb-2">
                                                        <b class="text-success me-2"><i class="fa-solid fa-plus"></i>(insert)</b>
                                                        stok_alt_depolar_kullanilanlar
                                                    </mark>
                                                    
                                                </p>
                                            </div>
                                        </li>
                                    </ol>
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
