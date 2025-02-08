<?php 
    include "include/db.php";
    include "include/oturum_kontrol.php";

    $id     = intval($_GET['id']);
    $sth    = $conn->prepare('SELECT * FROM siparis_form WHERE id=:id AND firma_id = :firma_id');
    $sth->bindParam('id', $id);
    $sth->bindValue('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $siparis_form = $sth->fetch(PDO::FETCH_ASSOC);

    if(empty($siparis_form))
    {
        require_once "include/yetkisiz.php";
        die();
    }

    $sth = $conn->prepare('SELECT * FROM turler WHERE firma_id = :firma_id');
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $turler = $sth->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
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
                    <h5>Sipariş Form Güncelleme İşlemi</h5>
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
                    <form class="row g-3 needs-validation" action="siparis_form_db_islem.php" 
                        method="POST" id="siparis-deger-guncelle-form" autocomplete="off">
                        <input type="hidden" name="id" value="<?php echo $siparis_form['id']; ?>">
                        <div class="form-floating col-md-12">
                            <input type="text" class="form-control" name="deger" id="deger" 
                                value="<?php echo $siparis_form['deger'];?>" required autofocus>
                            <label for="deger" class="form-label">Değer</label>
                        </div>

                        <ul class="list-group m-2">
                            <?php foreach ($turler as $key => $tur) { ?>          
                                <li class="list-group-item">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" name="tur_id[]" type="checkbox" role="switch" 
                                            id="tur-<?php echo $tur['id']; ?>" value="<?php echo $tur['id']; ?>"
                                            <?php echo in_array($tur['id'],json_decode($siparis_form['tur_idler'], true)) ? 'checked':'';?>
                                        >
                                        <label class="form-check-label" for="tur-<?php echo $tur['id']; ?>">
                                            <?php echo $tur['tur']; ?>
                                        </label>
                                    </div>
                                </li>
                            <?php }?>
                        </ul>

                        <div class="col-md-6">
                            <button class="btn btn-warning" type="submit" name="siparis_form_guncelle" id="siparis-deger-guncelle-button">
                                <i class="fa-regular fa-pen-to-square"></i> GÜNCELLE
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php 
            include "include/scripts.php";
            include "include/uyari_session_oldur.php";
        ?>
        <script>
            $(function(){
                $("#siparis-deger-guncelle-form").submit(function(){
                    $("#siparis-deger-guncelle-button").addClass('disabled');
                    return true;
                });
            });  
        </script>
    </body>
</html>
