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
                        <i class="fa-brands fa-hire-a-helper"></i> Süper Admin Firma Kurma İşlemi Yardım Dökümantasyon
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <strong>1- </strong>Firma Ekle
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <a href="https://panel.hankasys.com/firma.php" class="btn btn-success btn-sm">
                                        Firma Ekle
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    <strong>2- </strong>Firmaya Admin Ekle
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <a href="superadmin_personel_ekle.php" class="btn btn-success btn-sm">
                                        Admin Ekle
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    <strong>3- </strong>Departman Ekle
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <a href="departman.php" class="btn btn-success btn-sm">
                                        Departman Ekle
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDort" aria-expanded="false" aria-controls="collapseDort">
                                    <strong>4- </strong>Personel Ekle
                                </button>
                            </h2>
                            <div id="collapseDort" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <a href="personel_ekle.php" class="btn btn-success btn-sm">
                                        Personel Ekle
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBes" aria-expanded="false" aria-controls="collapseBes">
                                    <strong>5- </strong>Departman Ekle
                                </button>
                            </h2>
                            <div id="collapseBes" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <a href="makina_ekle.php" class="btn btn-success btn-sm">
                                        Departman Ekle
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAlti" aria-expanded="false" aria-controls="collapseAlti">
                                    <strong>6- </strong>Stok Kalem Ekle
                                </button>
                            </h2>
                            <div id="collapseAlti" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <a href="stok_kalem.php" class="btn btn-success btn-sm">
                                        Stok Kalem Ekle
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseYedi" aria-expanded="false" aria-controls="collapseYedi">
                                    <strong>7- </strong>Stok Kalem Değer Ekle
                                </button>
                            </h2>
                            <div id="collapseYedi" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDokuz" aria-expanded="false" aria-controls="collapseDokuz">
                                    <strong>8- </strong>Tedarikçi Ekle
                                </button>
                            </h2>
                            <div id="collapseDokuz" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <a href="tedarikci.php" class="btn btn-success btn-sm">
                                        Tedarikçi Ekle
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSekiz" aria-expanded="false" aria-controls="collapseSekiz">
                                    <strong>9- </strong>Stok Ekle
                                </button>
                            </h2>
                            <div id="collapseSekiz" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <a href="stok.php" class="btn btn-success btn-sm">
                                        Stok Ekle
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSekiz" aria-expanded="false" aria-controls="collapseSekiz">
                                    <strong>10- </strong>Sektör Ekle
                                </button>
                            </h2>
                            <div id="collapseSekiz" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <a href="sektor.php" class="btn btn-success btn-sm">
                                        Sektör Ekle
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOnBir" aria-expanded="false" aria-controls="collapseOnBir">
                                    <strong>11- </strong>Tür Ekle
                                </button>
                            </h2>
                            <div id="collapseOnBir" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <a href="turler.php" class="btn btn-success btn-sm">
                                        Tür Ekle
                                    </a>
                                </div>
                            </div>
                        </div>


                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOnIki" aria-expanded="false" aria-controls="collapseOnIki">
                                    <strong>12- </strong>Sipariş Form Tipleri
                                </button>
                            </h2>
                            <div id="collapseOnIki" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <a href="siparis_form_tipleri.php" class="btn btn-success btn-sm">
                                        Sipariş Form Tipleri Aktif Et
                                    </a>
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
