<?php 
    include "include/db.php";
    include "include/oturum_kontrol.php";

    $sth = $conn->prepare('SELECT arsiv_kalemler.*, departmanlar.departman FROM arsiv_kalemler 
        JOIN departmanlar  ON arsiv_kalemler.departman_id = departmanlar.id
        WHERE arsiv_kalemler.firma_id = :firma_id');
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $arsiv_kalemler = $sth->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="tr">
<head>
    <?php require_once "include/head.php";?>
    <title>Hanka Sys SAAS</title>
</head>
<body>
    <?php 
        require_once "include/header.php";
        require_once "include/sol_menu.php";
    ?>
    
    <div class="container-fluid">
        <div class="card border-secondary">
            <div class="card-header d-flex justify-content-between">
                <h5>
                    <i class="fa-regular fa-folder-open"></i> ARŞİV KALEMLER
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
                            <a href="arsiv_kalem_db_islem.php?islem=arsiv_kalem_csv" 
                                class="btn btn-success"
                                data-bs-toggle="tooltip" 
                                data-bs-placement="bottom" 
                                data-bs-title="Excel"
                            >
                                <i class="fa-regular fa-file-excel"></i>
                            </a>
                            <?php if(in_array(ARSIV_OLUSTUR, $_SESSION['sayfa_idler'])){ ?>
                                <button type="button" class="btn btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#arsiv-kalem-ekle-modal"
                                    data-bs-placement="bottom" 
                                    data-bs-title="Arsiv Ekle"
                                >
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            <?php } ?>
                        </div>
                    </div>
                </div>	
            </div>
            <div class="card-body">
                <table id="myTable" class="table table-hover" >
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Arşiv</th>
                            <th>Departman</th>
                            <th class="text-end">Arşiv Sayısı</th>
                            <th class="text-end">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($arsiv_kalemler as $index => $arsiv_kalem){ ?>
                            <?php 
                                $sql = 'SELECT COUNT(*) AS arsiv_alt_sayisi FROM `arsiv_altlar` WHERE arsiv_id = :arsiv_id;';
                                $sth = $conn->prepare($sql);
                                $sth->bindParam('arsiv_id', $arsiv_kalem['id']);
                                $sth->execute();
                                $arsiv_alt_varmi = $sth->fetch(PDO::FETCH_ASSOC);
                            ?>
                            <tr>
                                <th class="table-primary"><?php echo $index + 1; ?></th>
                                <th><?php echo $arsiv_kalem['arsiv']; ?></th>
                                <td><?php echo $arsiv_kalem['departman']; ?></td>
                                <th class="text-end">
                                    <?php echo $arsiv_alt_varmi['arsiv_alt_sayisi']; ?> Adet
                                </th>
                                <td>
                                    <div class="d-md-flex justify-content-end"> 
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <?php if(in_array(ARSIV_GOR, $_SESSION['sayfa_idler'])){ ?>
                                                <a href="arsiv_alt.php?arsiv_id=<?php echo $arsiv_kalem['id']; ?>" 
                                                    class="btn btn-secondary"
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="bottom" 
                                                    data-bs-title="Arşiv Listesi"
                                                >
                                                    <i class="fa-solid fa-table-list"></i>
                                                </a>
                                                <?php } ?>
                                            <?php if(in_array(ARSIV_DUZENLE, $_SESSION['sayfa_idler'])){ ?>
                                                <a href="arsiv_kalem_guncelle.php?id=<?php echo $arsiv_kalem['id']; ?>" 
                                                    class="btn btn-warning"
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="bottom" 
                                                    data-bs-title="Güncelle"
                                                >
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </a>
                                            <?php } ?>
                                            
                                            <?php if(in_array(ARSIV_SIL, $_SESSION['sayfa_idler']) && !$arsiv_alt_varmi['arsiv_alt_sayisi']){ ?>
                                                <a href="arsiv_kalem_db_islem.php?islem=arsiv_kalem_sil&id=<?php echo $arsiv_kalem['id']; ?>" 
                                                    onClick="return confirm('Silmek İstediğinize Emin Misiniz?')"  
                                                    class="btn btn-danger"
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="bottom" 
                                                    data-bs-title="Sil"
                                                >
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </a>
                                            <?php }else{?> 
                                                <a href="javascript:;" 
                                                    class="btn btn-danger"
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="bottom" 
                                                    data-bs-html="true"
                                                    data-bs-title="<b class='text-danger'>Alt Arşiv Olduğun İçin Silinemez veya Silme İzniniz Yoktur!</b>"
                                                >
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td> 
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
      <!-- Stok Kalem Ekle -->
    <div class="modal fade" id="arsiv-kalem-ekle-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form  action="arsiv_kalem_db_islem.php" method="POST" id="arsiv-kalem-ekle-form">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel"> Ekle</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating p-1">
                            <input type="text" class="form-control" name="arsiv" id="arsiv" required >
                            <label for="arsiv" class="form-label">Arşiv</label>
                        </div>
                        <?php  
                            $sth = $conn->prepare('SELECT * FROM departmanlar WHERE firma_id = :firma_id');
                            $sth->bindParam('firma_id', $_SESSION['firma_id']);
                            $sth->execute();
                            $departmanlar = $sth->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="form-floating p-1">
                            <select class="form-select" id="departman_id" name="departman_id" required>
                                <option selected disabled value="">Seç..</option>
                                <?php foreach ($departmanlar as $departman) { ?>
                                    <option  value="<?php echo $departman['id']; ?>">
                                        <?php echo $departman['departman']; ?>
                                    </option>
                                <?php }?>
                            </select>
                            <label for="departman_id" class="form-label">Departman</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" name="arsiv_kalem_ekle" id="arsiv-kalem-ekle-button">
                            <i class="fa-regular fa-square-plus"></i> KAYDET
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            İPTAL <i class="fa-regular fa-rectangle-xmark"></i>
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
            //formu gönderdiğinde buttonu pasif yapma
            $("#arsiv-kalem-ekle-form").submit(function(){
                $("#arsiv-kalem-ekle-button").addClass('disabled');
                return true;
            });

            //modal açıldığında focus yapma
            $('#arsiv-kalem-ekle-modal').on('shown.bs.modal', function () {
                $('#arsiv').focus();
            });
        });
    </script>
    </body>
</html>

