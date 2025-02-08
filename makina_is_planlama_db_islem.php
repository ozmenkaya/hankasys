<?php 
require_once "include/db.php";
require_once "include/oturum_kontrol.php";

//makina iş sıralama
if(isset($_POST['islem']) && $_POST['islem'] == 'planlama_siralama'){
    $planlama_idler = $_POST['planlama_idler'];
    foreach ($planlama_idler as $index => $planlama_id) {
        $sql = "UPDATE planlama SET sira = :sira  WHERE id = :id;";
        $sth = $conn->prepare($sql);
        $sth->bindValue('sira', intval($index)+1);
        $sth->bindParam('id', $planlama_id);
        $durum = $sth->execute();
    }
    echo json_encode(['durum'=>true]); exit;
}