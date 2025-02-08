<?php 

$sayfa_adi = basename($_SERVER['PHP_SELF']); 
?>
<nav id="sidebarMenu" class="col-md-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky sidebar-sticky">
        <ul class="nav flex-column" style="font-size:16px">
            <li class="nav-item w-100">
                <a href="javascript:;" class="nav-link p-2 fw-bold text-wrap">
                    <span class="mt-2 text-start d-block text-decoration-underline 
                            badge bg-secondary p-2 fs-6 text-wrap border-start border-5 border-primary"
                    >
                        <i class="fa-regular fa-circle-user fs-5"></i>
                        Hoşgeldin,
                        <?php echo $_SESSION['ad'].' '.$_SESSION['soyad']; ?> <br>
                        <i class="fa-solid fa-tag mt-2"></i>
                        <?php echo yetkiAdi($_SESSION['yetki_id']); ?>
                    </span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo in_array($sayfa_adi, ['index.php']) ? 'rounded-start border-start border-primary border-5 ms-2  active fw-bold' :'';?>" 
                    href="index.php">
                    <i class="fa-solid fa-home fs-4"></i>
                    <span>Anasayfa</span>
                </a>
            </li>
            <?php if(in_array($_SESSION['yetki_id'], [SUPER_ADMIN_YETKI_ID,ADMIN_YETKI_ID] )){ ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['firma_ayarlar.php']) ? 'rounded-start border-start border-primary border-5 ms-2  active fw-bold' :'';?>" 
                        href="firma_ayarlar.php">
                        <i class="fa-solid fa-gear fs-4"></i>
                        Firma Ayarlar
                        <!--
                        <span class="badge text-bg-primary fs-6">
                            YENİ <i class="fa-solid fa-star-of-life"></i>
                        </span>
                        -->
                    </a>
                </li>
            <?php } ?>

            <li class="nav-item">
                <a class="nav-link <?php echo in_array($sayfa_adi, ['sifre_guncelle.php']) ? 'rounded-start border-start border-primary border-5 ms-2  active fw-bold' :'';?>" 
                    href="sifre_guncelle.php">
                    <i class="fa-solid fa-lock fs-4"></i>
                    Şifre Değiştir
                </a>
            </li>

            <?php if( in_array(MUSTERI_GOR, $_SESSION['sayfa_idler']) ){ ?>
                <li class="nav-item">
                    <a class="nav-link  
                        <?php echo in_array($sayfa_adi, ['musteriler.php','musteri_ekle.php','musteri_guncelle.php','siparis.php','siparis_ekle.php','siparis_gor.php']) ? 'rounded-start border-start border-primary border-5 ms-2  fw-bold active':'';?>"  
                        href="musteriler.php">
                        <i class="fa-solid fa-users fs-4"></i>
                        Müşteriler 
                    </a>
                </li>
            <?php }?>

            <?php if(in_array($_SESSION['yetki_id'], [SUPER_ADMIN_YETKI_ID,ADMIN_YETKI_ID] )){ ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['birimler.php']) ? 'rounded-start border-start border-primary border-5 ms-2  active fw-bold' :'';?>" 
                        href="birimler.php">
                        <i class="fa-solid fa-ruler-vertical fs-4"></i>
                        Birimler
                        
                    </a>
                </li>
            <?php } ?>

            
            
            <?php if( in_array(DEPARTMAN_GOR, $_SESSION['sayfa_idler'])){ ?>
                <li class="nav-item">
                    <a class="nav-link 
                        <?php echo in_array($sayfa_adi,['departman.php','departman_form.php','departman_form_guncelle.php','departman_planlama.php']) ? 'rounded-start border-start border-primary border-5 ms-2  fw-bold active':'';?>" href="departman.php">
                        <i class="fa-solid fa-building fs-4"></i> Departmanlar 
                    </a>
                </li>
            <?php }?>
            
            <?php if(in_array(PERSONEL_GOR, $_SESSION['sayfa_idler'])){ ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi,['personel.php','personel_ekle.php','personel_guncelle.php'] )? 'rounded-start border-start border-primary border-5 ms-2  fw-bold active':'';?>" href="personel.php">
                    <i class="fa-solid fa-users fs-4"></i>
                        Personeller 
                    </a>
                </li>
            <?php }?>
            
            <?php if(in_array(STOK_GOR, $_SESSION['sayfa_idler'])){ ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['stok_kalem.php','stok_kalem_guncelle.php','stok_alt_kalem_deger.php','stok_alt_kalem_deger_guncelle.php']) ? 'rounded-start border-start border-primary border-5 ms-2  fw-bold active':'';?>" href="stok_kalem.php">
                        <i class="fa-sharp fa-solid fa-layer-group fs-4"></i>
                        Stok Kalem
                    </a>
                </li>
            <?php } ?>
            
            <?php if(in_array(DEPO_GOR, $_SESSION['sayfa_idler'])){ ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['stok.php','stok_alt_depolar.php','stok_alt_kalem_guncelle.php']) ? 'rounded-start border-start border-primary border-5 ms-2  fw-bold active':'';?>" href="stok.php">
                        <i class="fa-sharp fa-solid fa-layer-group fs-4"></i>
                        Stok
                    </a>
                </li>
            <?php }?>
            
            <?php if(in_array(TUM_SIPARISLERI_GOR, $_SESSION['sayfa_idler'])){ ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['siparisler_onay.php']) ? 'rounded-start border-start border-primary border-5 ms-2  fw-bold active':'';?>" href="siparisler_onay.php" >
                        <i class="fa-solid fa-bag-shopping fs-4"></i>
                        Siparişler
                    </a>
                </li>
            <?php } ?>

            <?php if(in_array($_SESSION['yetki_id'], [SUPER_ADMIN_YETKI_ID,ADMIN_YETKI_ID] )){ ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['siparis_form.php']) ? 'rounded-start border-start border-primary border-5 ms-2 fw-bold active ':'';?>" href="siparis_form.php" >
                        <i class="fa-solid fa-bag-shopping fs-4"></i>
                        Sipariş Form
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['siparis_form_tipleri.php']) ? 'rounded-start border-start border-primary border-5 ms-2 fw-bold active ':'';?>" href="siparis_form_tipleri.php" >
                        <i class="fa-solid fa-bag-shopping fs-4"></i>
                        Sipariş Form Tipleri
                    </a>
                </li>
            <?php } ?>
                
            <?php if(in_array(SEKTOR_GOR, $_SESSION['sayfa_idler'])){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi,['sektor.php','sektor_guncelle.php'] )? 'rounded-start border-start border-primary border-5 ms-2 fw-bold active ':'';?>" href="sektor.php">
                        <i class="fa-solid fa-chart-simple fs-4"></i>
                        Sektorler 
                    </a>
                </li>
            <?php }?>

            <?php if(in_array($_SESSION['yetki_id'], [SUPER_ADMIN_YETKI_ID,ADMIN_YETKI_ID] )){ ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi,['turler.php','turler_guncelle.php'] )? 'rounded-start border-start border-primary border-5 ms-2 fw-bold active ':'';?>" href="turler.php">
                    <i class="fa-solid fa-chart-simple fs-4"></i>
                        Türler 
                    </a>
                </li>
            <?php } ?>

            <?php if(in_array(MAKINA_GOR, $_SESSION['sayfa_idler'])){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi,['makina.php','makina_guncelle.php','makina_ekle.php'] )? 'rounded-start border-start border-primary border-5 ms-2 fw-bold active ':'';?>" href="makina.php">
                        <i class="fa-solid fa-gears fs-4"></i>
                        Makinalar
                    </a>
                </li>
            <?php } ?>
            
            <?php if(in_array(TEDARIKCI_GOR, $_SESSION['sayfa_idler'])){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi,['tedarikci.php','tedarikci_guncelle.php','tedarikci_ekle.php']) ? 'rounded-start border-start border-primary border-5 ms-2 fw-bold active ' :'';?>" href="tedarikci.php">
                        <i class="fa-solid fa-parachute-box fs-4"></i>
                        Tedarikçi
                    </a>
                </li>
            <?php }?>
            
            <?php if(in_array(ARSIV_GOR, $_SESSION['sayfa_idler'])){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['arsiv_kalem.php','arsiv_alt.php','arsiv_alt_ekle.php','arsiv_kalem_guncelle.php','arsiv_alt_guncelle.php']) ? 'rounded-start border-start border-primary border-5 ms-2 fw-bold active ' :'';?>" href="arsiv_kalem.php">
                    <i class="fa-regular fa-folder-open fs-4"></i>
                        Arşiv
                    </a>
                </li>
            <?php }?>

            <?php if(in_array(PLANLAMA, $_SESSION['sayfa_idler'])){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['planlama.php','planla_siparis.php','planla_siparis_duzenle.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" href="planlama.php">
                        <i class="fa-solid fa-list-check fs-4"></i>
                        Planlama
                    </a>
                </li>
            <?php }?>

            <?php if( in_array(URETIM_KONTROL, $_SESSION['sayfa_idler']) ){ ?>
                <li class="nav-item">
                    <a class="nav-link  <?php echo in_array($sayfa_adi, ['uretim_kontrol.php']) ? 'rounded-start border-start border-primary border-5 ms-2 fw-bold active ':'';?>"  
                        href="uretim_kontrol.php">
                        <i class="fa-solid fa-list-check fs-4"></i>
                        Üretim Kontrol 
                    </a>
                </li>
            <?php }?>

            <?php if(in_array(MAKINA_IS_PLANI, $_SESSION['sayfa_idler'])){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['makina_is_planlama.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" href="makina_is_planlama.php">
                        <i class="fa-solid fa-list-check fs-4"></i>
                        Makina Planlama 
                    </a>
                </li>
            <?php }?>

            <!-- superadmin -->
            <?php if( $_SESSION['yetki_id'] == SUPER_ADMIN_YETKI_ID ){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['firma.php','firma_guncelle.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" href="firma.php">
                        <i class="fa-solid fa-building fs-4"></i>  
                        Firmalar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $sayfa_adi == 'superadmin_personel_ekle.php' ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" href="superadmin_personel_ekle.php">
                        <i class="fa-solid fa-user-plus fs-4"></i>
                        Firmalara Personel Ekle
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $sayfa_adi == 'super_admin_firma_kurma_yardim.php' ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" 
                        href="super_admin_firma_kurma_yardim.php">
                        <i class="fa-brands fa-hire-a-helper fs-4"></i>
                        Firma Kurulum Yardımı
                    </a>
                </li>
            <?php }?>

            <?php if( in_array($_SESSION['yetki_id'],[SUPER_ADMIN_YETKI_ID,ADMIN_YETKI_ID])){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['sayfa_yetkiler.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" href="sayfa_yetkiler.php">
                        <i class="fa-solid fa-plug-circle-check fs-4"></i>
                        Sayfa Yetkileri
                    </a>
                </li>
            <?php } ?>


            <?php if(in_array(FASON, $_SESSION['sayfa_idler'])){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['fason.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" href="fason.php">
                        <i class="fa-regular fa-object-group fs-4"></i>
                        Fason
                    </a>
                </li>
            <?php } ?>

            <?php if(in_array(RAPORLAR, $_SESSION['sayfa_idler'])){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['rapor_siparisler.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" href="rapor_siparisler.php">
                        <i class="fa-solid fa-flag-checkered fs-4"></i>
                        Raporlar
                    </a>
                </li>
            <?php } ?>

            <?php if(in_array(DEPO, $_SESSION['sayfa_idler'])){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['depo.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" href="depo.php">
                        <i class="fa-solid fa-box-open fs-4"></i>
                        Depo
                    </a>
                </li>
            <?php }?>

            <?php if(in_array($_SESSION['yetki_id'],[MAKINE_BAKIM_ID])){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['makina_bakim.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" href="makina_bakim.php">
                        <i class="fa-solid fa-gear fs-4"></i>
                        Makina Bakım
                    </a>
                </li>
            <?php }?>


            <li class="nav-item">
                <a class="nav-link <?php echo in_array($sayfa_adi, ['geri_bildirim.php', 'geri_bildirim_detay.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" 
                    href="geri_bildirim.php">
                    <i class="fa-regular fa-comment-dots fs-4"></i>
                    Geri Bildirim 
                    <span class="badge text-bg-warning fs-6">
                        <i class="fa-regular fa-eye-slash"></i>
                        (<?php echo gorunmeyen_geri_bildirim_sayisi(); ?>) 
                    </span>
                </a>
            </li>
            <?php if( $_SESSION['yetki_id'] == URETIM_YETKI_ID ){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['makina_listesi.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" 
                        href="makina_listesi.php">
                        <i class="fa-solid fa-gears fs-4"></i>
                        Makine Ekranı
                    </a>
                </li>
            <?php }?>

            <?php if( $_SESSION['yetki_id'] == SUPER_ADMIN_YETKI_ID ){  ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['_session.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" 
                        href="_session.php">
                        <i class="fa-solid fa-terminal fs-4"></i>
                        OTURUM BİLGİSİ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['_info.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" 
                        href="_info.php">
                        <i class="fa-solid fa-terminal fs-4"></i>
                        PHPİNFO
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['hata_loglari.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" 
                        href="hata_loglari.php">
                        <i class="fa-solid fa-bug fs-4"></i>
                        HATALAR
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($sayfa_adi, ['_log.php']) ? 'rounded-start border-start border-primary border-5 ms-2 active fw-bold' :'';?>" 
                        href="_kod_notlar.php">
                        <i class="fa-solid fa-laptop-code fs-4"></i>
                        KOD NOTLAR
                    </a>
                </li>
            <?php } ?>

            
            <li class="nav-item">
                <a class="nav-link link-danger fw-bold" href="login_kontrol.php?islem=cikis-yap">
                    <i class="fa-solid fa-arrow-right-from-bracket fs-4"></i>
                    Çıkış
                </a>
            </li>
        </ul>
    </div>
</nav>
<main class="ms-sm-auto col-md-10">
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3">
        
