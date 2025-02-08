<?php 
    include "include/db.php";
    $sth = $conn->prepare('SELECT makinalar.*, departmanlar.departman FROM makinalar 
    JOIN departmanlar ON makinalar.departman_id = departmanlar.id 
    WHERE makinalar.firma_id = :firma_id ORDER BY departmanlar.departman');
    $sth->bindParam('firma_id', $_SESSION['firma_id']);
    $sth->execute();
    $makinalar = $sth->fetchAll(PDO::FETCH_ASSOC);
    #echo "<pre>"; print_r($personeller); exit;
?>
<!doctype html>
<html lang="en">
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
                <h5>
                    <i class="fa-solid fa-gears"></i> Makinalar
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
                            <a href="makina_ekle.php"  class="btn btn-primary"
                                data-bs-toggle="tooltip"
                                data-bs-placement="bottom" 
                                data-bs-title="Makina Ekle"
                            > 
                                <i class="fa-solid fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="myTable" class="table table-hover table-striped">
                        <thead class="table-primary">
                        <tr>
                            <th>Sıra</th>
                            <th>Adı</th>
                            <th>Modeli</th>
                            <th>Seri No</th>
                            <th>Departmanı</th>
                            <th class="text-center">Durumu</th>
                            <th class="text-center">Makina Ayar Süresi</th>
                            <th class="text-end">İşlemler</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($makinalar as $key=> $makina){ ?>
                                <tr class="<?php echo $makina['makina_ayar_suresi_varmi'] == 'var' ? 'table-success':'table-danger';?>">
                                    <th class="table-primary"><?php echo $key + 1 ; ?></th>
                                    <td><?php echo $makina['makina_adi']; ?></td>
                                    <td><?php echo $makina['makina_modeli']; ?></td>
                                    <td><?php echo $makina['makina_seri_no']; ?></td>
                                    <td><?php echo $makina['departman']; ?></td>
                                    <th class="text-center">
                                        <?php if($makina['durumu'] == 'aktif'){ ?>
                                            <span class="badge bg-success">AKTİF</span>
                                        <?php }elseif($makina['durumu'] == 'bakımda'){?> 
                                            <span class="badge bg-warning">BAKIMDA</span>
                                        <?php }else{ ?>
                                            <span class="badge bg-danger">PASİF</span>
                                        <?php } ?>
                                    </th>
                                    <th class="text-center">
                                        <?php if($makina['makina_ayar_suresi_varmi'] == 'var'){ ?>
                                            <span class="badge bg-success">VAR</span>
                                        <?php }else{ ?>
                                            <span class="badge bg-danger">YOK</span>
                                        <?php } ?>
                                    </th>

                                    <td>
                                        <div class="d-flex justify-content-end"> 
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <a href="makina_guncelle.php?id=<?php echo $makina['id']; ?>" 
                                                    class="btn btn-warning"
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="bottom" 
                                                    data-bs-title="Güncelle"
                                                >
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </a>
                                                <?php if($makina['kullanildi_mi'] == 'hayır' && in_array($makina['durumu'], ['pasif'])){ ?>
                                                    <a href="makina_db_islem.php?islem=makina_sil&id=<?php echo $makina['id']; ?>" 
                                                        onClick="return confirm('Silmek İstediğinize Emin Misiniz?')"
                                                        class="btn btn-danger"
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="bottom" 
                                                        data-bs-title="Sil"
                                                    >
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                <?php }else{?> 
                                                    <a href="#" 
                                                        class="btn btn-danger disabled"
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="bottom" 
                                                        data-bs-title="Sil"
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
    </div>     
    <?php 
        include_once "include/scripts.php"; 
        include_once "include/uyari_session_oldur.php";
    ?>
  </body>
</html>

