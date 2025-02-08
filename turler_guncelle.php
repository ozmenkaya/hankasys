<?php 
    include "include/db.php";
    include "include/oturum_kontrol.php";

    $id     = intval($_GET['id']);
    $sth = $conn->prepare('SELECT * FROM turler WHERE id=:id AND firma_id = :firma_id');
    $sth->bindParam('id', $id);
    $sth->bindValue('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $tur = $sth->fetch(PDO::FETCH_ASSOC);

    if(empty($tur))
    {
        require_once "include/yetkisiz.php";
        die();
    }

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
                    <h5>Tür Güncelleme İşlemi</h5>
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
                    <form class="row g-3 needs-validation" action="turler_db_islem.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $tur['id']; ?>">
                        <div class="form-floating col-md-6">
                            <input type="text" class="form-control" name="tur" id="tur" value="<?php echo $tur['tur'];?>" required >
                            <label for="tur" class="form-label">Tur</label>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-warning mt-2" type="submit" name="tur_guncelle">
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
    </body>
</html>
