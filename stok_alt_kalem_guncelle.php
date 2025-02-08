<?php 
    require_once "include/db.php";
    require_once "include/oturum_kontrol.php";

    $id         = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $stok_id    = isset($_GET['stok_id']) ? intval($_GET['stok_id']) : 0;

    $sth = $conn->prepare('SELECT id, veri FROM stok_alt_kalemler WHERE id =:id AND stok_id =:stok_id AND firma_id = :firma_id');
    $sth->bindParam('id', $id);
    $sth->bindParam('stok_id', $stok_id);
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $stok_alt_kalem = $sth->fetch(PDO::FETCH_ASSOC);

    if(empty($stok_alt_kalem))
    {
        require_once "include/yetkisiz.php";
        die();
    }

    $sth = $conn->prepare('SELECT ad, kolon_tipi FROM stok_alt_kalem_degerler WHERE  stok_id =:stok_id');
    $sth->bindParam('stok_id', $stok_id);
    $sth->execute();
    $stok_alt_kalem_degerler = $sth->fetchAll(PDO::FETCH_ASSOC);

    
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
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h5>Stok Alt Kalem Güncelle</h5>
                </div>
                <div class="card-body">
                    <form class="row g-3 needs-validation" action="stok_alt_kalem_db_islem.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $stok_alt_kalem['id']; ?>">
                        <input type="hidden" name="stok_id" value="<?php echo $stok_id; ?>">
                        <?php $veriler = json_decode($stok_alt_kalem['veri']); ?>
                        <?php foreach ($stok_alt_kalem_degerler as $stok_alt_kalem) { ?>
                            <?php 
                                $stok_alt_kalem_deger = "";
                                foreach ($veriler as $key => $veri) {
                                    if($key == $stok_alt_kalem['ad'])
                                    {
                                        $stok_alt_kalem_deger = $veri;
                                        break;
                                    }
                                }    
                            ?>
                            <div class="form-floating col-md-2">
                                <input type="<?php echo $stok_alt_kalem['kolon_tipi']?>" class="form-control" 
                                name="alt_stok_kalem_ad[<?php echo $stok_alt_kalem['ad']; ?>]" 
                                    id="<?php echo $key; ?>" required  value="<?php echo $stok_alt_kalem_deger; ?>">
                                <label for="<?php echo $key; ?>" class="form-label"><?php echo $stok_alt_kalem['ad']; ?></label>
                            </div>
                            
                        <?php }?>

                        <?php if(!empty($stok_alt_kalem_degerler )){ ?>
                            <div class="form-floating col-md-2 mt-1">
                                <button type="submit" class="btn btn-warning btn-lg mt-3" 
                                name="stok_alt_kalem_guncelle">
                                    <i class="fa-regular fa-pen-to-square"></i> GÜNCELLE
                                </button>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
        <?php include_once "include/scripts.php"; ?>
        <?php require_once "include/uyari_session_oldur.php"; ?>
    </body>
</html>
