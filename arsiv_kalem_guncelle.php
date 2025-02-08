<?php 
    include "include/db.php";

    $id     = intval($_GET['id']);

    $sth = $conn->prepare('SELECT id, arsiv, departman_id FROM arsiv_kalemler WHERE id=:id AND firma_id = :firma_id');
    $sth->bindParam('id', $id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $arsiv_kalem = $sth->fetch(PDO::FETCH_ASSOC);

    if(empty($arsiv_kalem))
    {
        include "include/yetkisiz.php";
        exit;
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
                <div class="card-header">
                    <h5>
                        <i class="fa-regular fa-folder-open"></i>
                        Güncelleme İşlemi
                    </h5>
                </div>
                <div class="card-body">
                    <form class="row g-3 needs-validation" action="arsiv_kalem_db_islem.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $arsiv_kalem['id']; ?>">
                        <div class="form-floating col-md-6">
                            <input type="text" class="form-control" name="arsiv" id="arsiv" value="<?php echo $arsiv_kalem['arsiv'];?>" required >
                            <label for="arsiv" class="form-label">Arşiv</label>
                        </div>
                        <?php  
                            $sth = $conn->prepare('SELECT * FROM departmanlar WHERE firma_id = :firma_id');
                            $sth->bindParam('firma_id', $_SESSION['firma_id']);
                            $sth->execute();
                            $departmanlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="form-floating col-md-6">
                            <select class="form-select" id="departman_id" name="departman_id" required>
                                <option selected disabled value="">Seç..</option>
                                <?php foreach ($departmanlar as $departman) { ?>
                                    <option  value="<?php echo $departman['id']; ?>" <?php echo $departman['id'] == $arsiv_kalem['departman_id'] ? 'selected': '';?>>
                                        <?php echo $departman['departman']; ?>
                                    </option>
                                <?php }?>
                            </select>
                            <label for="departman_id" class="form-label">Arsiv</label>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <button class="btn btn-warning" type="submit" name="arsiv_kalem_guncelle">
                                <i class="fa-regular fa-pen-to-square"></i> GÜNCELLE
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <?php include "include/scripts.php" ?>
    </body>
</html>
