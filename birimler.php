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
            <div class="card border-secondary border-2">
                <div class="card-header d-flex justify-content-between border-secondary">
                    <h5>
                        <i class="fa-solid fa-ruler-vertical"></i>
                        Birimler 
                    </h5>
                    <div>
                        <div class="d-md-flex justify-content-end"> 
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <a href="javascript:window.history.back();" 
                                    class="btn btn-secondary"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" 
                                    data-bs-title="Geri Dön"
                                >
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>
                                <?php //if(in_array(DEPARTAN_OLUSTUR, $_SESSION['sayfa_idler'])){ ?>
                                    <button type="button" class="btn btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#birim-ekle-modal"
                                        data-bs-placement="bottom" 
                                        data-bs-title="Ekle"
                                    >
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                <?php //} ?>
                            </div>
                        </div>
                    </div> 
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="myTable" class="table table-hover" >
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Birim</th>
                                    <th class="text-end">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $sth = $conn->prepare('SELECT * FROM birimler WHERE firma_id = :firma_id');
                                    $sth->bindParam('firma_id', $_SESSION['firma_id']);
                                    $sth->execute();
                                    $birimler = $sth->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <?php foreach ($birimler as $index => $birim) { ?>
                                    <tr>
                                        <th class="table-primary"><?php echo $index + 1; ?></th>
                                        <td><?php echo $birim['ad']; ?></td>
                                        <td>
                                            <div class="d-flex justify-content-end"> 
                                                <div class="btn-group" role="group">
                                                    <a href="birim_guncelle.php?id=<?php echo $birim['id']; ?>" 
                                                        class="btn btn-warning"
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="bottom" 
                                                        data-bs-title="Düzenle"
                                                    >
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Birim Ekle Modal -->
        <div class="modal fade" id="birim-ekle-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form  action="birim_db_islem.php" method="POST" id="birim-ekle-form" class="row g-3 needs-validation" >
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">
                                <i class="fa-solid fa-ruler-vertical"></i>
                                Birim Ekle
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="birim" id="birim" required >
                                <label for="birim" class="form-label">Birim</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" name="birim_ekle" id="birim-ekle-button">
                                <i class="fa-regular fa-square-plus"></i> KAYDET
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fa-regular fa-rectangle-xmark"></i> İPTAL
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php 
            include_once "include/scripts.php"; 
            include_once "include/uyari_session_oldur.php"; 
        ?>
        <script>
            $(function(){
                //formu gönderdiğinde buttonu pasif yapma
                $("#birim-ekle-form").submit(function(){
                    $("#birim-ekle-button").addClass('disabled');
                    return true;
                });

                //modal açıldığında focus yapma
                $('#birim-ekle-modal').on('shown.bs.modal', function () {
                    $('#birim').focus();
                });

            });                                        
        </script>
    </body>
</html>
