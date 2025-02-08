<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-2 me-0 px-3 fs-6" 
        href="<?php echo $_SESSION['yetki_id'] == URETIM_YETKI_ID ? 'makina_listesi.php':'index.php';?>"
    >
        <img src="dosyalar/logo/<?php echo $_SESSION['logo']; ?>"  class="rounded img-fluid" style="height:25px" alt="">
        <?php echo $_SESSION['firma_adi']; ?>
    </a>

    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" 
        data-bs-toggle="collapse" data-bs-target="#sidebarMenu" 
        aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
</header>