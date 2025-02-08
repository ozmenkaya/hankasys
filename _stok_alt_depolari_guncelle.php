<?php 

include "include/db.php";

$sql = 'SELECT id FROM `stok_alt_depolar`';
$sth = $conn->prepare($sql);
$sth->execute();
$stok_alt_depolar = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach ($stok_alt_depolar as $key => $stok_alt_depo) {
    $sql = 'SELECT SUM(fire_miktari) AS fire_miktari,  SUM(tuketim_miktari) AS tuketim_miktari
            FROM stok_alt_depolar_kullanilanlar WHERE stok_alt_depo_id = :stok_alt_depo_id';
    $sth = $conn->prepare($sql);
    $sth->bindParam("stok_alt_depo_id", $stok_alt_depo['id']);
    $sth->execute();
    $toplam_kullanilan = $sth->fetch(PDO::FETCH_ASSOC); 
    $fire_miktari = $toplam_kullanilan['fire_miktari'] ? $toplam_kullanilan['fire_miktari'] : 0;
    $tuketim_miktari = $toplam_kullanilan['tuketim_miktari'] ? $toplam_kullanilan['tuketim_miktari'] : 0;
    $toplam_kullanilan = $fire_miktari + $tuketim_miktari;
    
    $sql = "UPDATE stok_alt_depolar SET kullanilan_adet = :kullanilan_adet  WHERE id = :id;";
    $sth = $conn->prepare($sql);
    $sth->bindParam('kullanilan_adet', $toplam_kullanilan);
    $sth->bindParam('id', $stok_alt_depo['id']);
    $durum = $sth->execute();
    echo $stok_alt_depo['id'].'=>'.$toplam_kullanilan."<br>";
}