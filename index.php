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
            <div class="card mb-2 border-secondary border-2">
                <div class="card-header d-flex justify-content-between border-secondary">
                    <h5>
                        <i class="fa-solid fa-house"></i> Anasayfa
                    </h5>
                    <div>
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
                <div class="card-body">
                    <div class="alert alert-danger fw-bold d-md-flex justify-content-between" role="alert">
                        1- Şifrenizi ilk defa alıyorsanız güvenliğiniz için güçlü bir şifre vererek değiştiriniz.!!
                        <a href="https://panel.hankasys.com/sifre_guncelle.php" class="btn btn-success btn-sm fw-bold">
                            <i class="fa-solid fa-lock"></i> Şifre Değiştir
                        </a>
                    </div>

                    <h5>Raporlar Gelecek</h5>
                </div>
            </div>

            <div class="card border-secondary border-2">
                <div class="card-header d-flex justify-content-between border-secondary">
                    <h5>
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Giriş Logları
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="myTable" class="table table-hover" >
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th class="text-center">İp</th>
                                    <th>Tarayıcı</th>
                                    <th>Tarayıcı Versiyon</th>
                                    <th>İşletim Sistemi</th>
                                    <th>Tarih</th>
                                    <th class="text-end">Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $sql = 'SELECT * FROM giris_log WHERE email = :email ORDER BY id DESC';
                                    $sth = $conn->prepare($sql);
                                    $sth->bindParam('email', $_SESSION['email']);
                                    $sth->execute();
                                    $girisler = $sth->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <?php foreach ($girisler as $index => $giris) { ?>
                                    <?php 
                                        $tarayici = json_decode($giris['tarayici'], true);
                                    ?>
                                    <tr class="<?php echo $giris['durum'] =='basarılı' ? 'table-success':'table-danger'; ?>">
                                        <th class="table-primary"><?php echo $index + 1; ?></th>
                                        <td class="text-center">
                                            <span class="badge text-bg-secondary p-2 fw-bold fs-6">
                                                <i class="fa-solid fa-wifi"></i> <?php echo $giris['ip']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if(isset($tarayici['name']) && $tarayici['name'] == 'Mozilla Firefox' ){ ?>
                                                <i class="fa-brands fa-firefox"></i>
                                            <?php }else if(isset($tarayici['name']) && $tarayici['name'] == 'Google Chrome'){?> 
                                                <i class="fa-brands fa-chrome"></i>    
                                            <?php }else if(isset($tarayici['name']) && $tarayici['name'] == 'Edge'){?> 
                                                <i class="fa-brands fa-edge"></i>    
                                            <?php } ?>
                                            <?php echo isset($tarayici['name']) ? $tarayici['name'] : '-' ?>
                                        </td>
                                        <td><?php echo isset($tarayici['version']) ? $tarayici['version'] : '-' ?></td>
                                        <td>
                                            <?php if(isset($tarayici['platform']) && $tarayici['platform'] == 'Windows' ){ ?>
                                                <i class="fa-brands fa-windows"></i>
                                            <?php }else if(isset($tarayici['platform']) && $tarayici['platform'] == 'Linux'){?> 
                                                <i class="fa-brands fa-linux"></i>
                                            <?php }else if(isset($tarayici['platform']) && $tarayici['platform'] == 'Mac OS'){?> 
                                                <i class="fa-brands fa-apple"></i>
                                            <?php }else if(isset($tarayici['platform']) && $tarayici['platform'] == 'Android'){ ?>
                                                <i class="fa-brands fa-android"></i>
                                            <?php } ?>
                                            <?php echo isset($tarayici['platform']) ? $tarayici['platform'] : '-' ?>
                                        </td>
                                        <td><?php echo date('d-m-Y H:i:s',strtotime($giris['tarih'])); ?></td>
                                        <td class="text-end">
                                            <?php  if($giris['durum'] =='basarılı'){?> 
                                                <span class="badge text-bg-success">BAŞARILI</span>
                                            <?php }else{?> 
                                                <span class="badge text-bg-danger">BAŞARISIZ</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="row py-2 sticky-bottom bg-body-secondary shadow rounded mt-2">
                <div class="col-2">
                    <div class="bg-primary text-white text-center breaking-caret py-1 rounded fw-bold">
                        <i class="fa-regular fa-newspaper"></i>
                        <span class="d-none d-md-inline-block">YENİ ÖZELLİKLER</span>
                    </div>
                </div>

                <div class="col-10">
                    <div class="breaking-box pt-2 pb-1">
                        <!--marque-->
                        <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseleave="this.start();">
                            <a class="h6 fw-normal" href="birim.php">
                                <span class="position-relative mx-2 badge bg-primary rounded">
                                    <i class="fa-solid fa-ruler-vertical"></i> BİRİM EKLEME
                                </span> 
                                Firma Ait Birim Ekleme Özelliği(Tıklayınız)
                            </a>
                            <a class="h6 fw-normal" href="makina.php">
                                <span class="position-relative mx-2 badge bg-primary rounded">
                                    <i class="fa-solid fa-gears"></i> MAKİNA ÜRETİM
                                </span> 
                                Makina Üretimde Ayar Süresi Olsun Olmasını Ayarlabilirsiniz. (Tıklayınız)
                            </a>
                            <a class="h6" href="geri_bildirim.php">
                                <span class="position-relative mx-2 badge bg-primary rounded">
                                    <i class="fa-solid fa-bug"></i> HATA
                                </span> 
                                Hataları Bularak Bize Bildirebilirsiniz. Geliştirmemize Yardımcı Olunuz. (Tıklayınız)
                            </a>
                        </marquee>
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
