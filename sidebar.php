<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon">
            <img src="img/image.png" alt="" class="img-fluid" style="width: 30px;">
        </div>
        <div class="sidebar-brand-text mx-2">SIDISI SMKN 6</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link pt-2 pb-2" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <li class="nav-item">
        <a class="nav-link collapsed pt-2 pb-2" href="#" data-toggle="collapse" data-target="#collapsePages"
            aria-expanded="true" aria-controls="collapsePages">
            <i class="fas fa-fw fa-folder"></i>
            <span>Data</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="tables.php">Siswa</a>
                <a class="collapse-item" href="jurusan.php">Jurusan</a>
                <a class="collapse-item" href="agama.php">Agama</a>
            </div>
        </div>
    </li>
    
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'administrator'): ?>
        <li class="nav-item">
            <a class="nav-link pt-2 pb-2" href="users.php">
                <i class="fas fa-fw fa-users-cog"></i> <span>Manajemen Pengguna</span>
            </a>
        </li>
    <?php endif; ?>

    <!-- Nav Item - Tables -->

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Sidebar Message -->

</ul>