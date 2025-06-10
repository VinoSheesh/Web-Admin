<?php
// Pastikan session dimulai dan variabel session tersedia
if (!isset($_SESSION)) {
    session_start();
}

// Fallback jika session variables tidak ada
$display_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'User';
$display_username = isset($_SESSION['username']) ? $_SESSION['username'] : 'username';
$display_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
?>

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Sidebar Toggle (Topbar) -->
    <form class="form-inline">
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?php echo htmlspecialchars($display_name); ?> (<?php echo htmlspecialchars($display_role); ?>)
                </span>
                <img class="img-profile rounded-circle" src="img/undraw_profile.svg" alt="Profile">
            </a>
            
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <!-- User Info Header -->
                <div class="dropdown-item-text">
                    <div class="font-weight-bold"><?php echo htmlspecialchars($display_name); ?></div>
                    <small class="text-muted"><?php echo ucfirst(htmlspecialchars($display_role)); ?></small>
                </div>
                
                <div class="dropdown-divider"></div>

                <!-- Navigation Links -->
                <a class="dropdown-item" href="profil_saya.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profil Saya
                </a>
                
                <a class="dropdown-item" href="pengaturan_akun.php">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    Pengaturan Akun
                </a>
                
                <div class="dropdown-divider"></div>
                
                <!-- Logout -->
                <a class="dropdown-item" href="logout.php">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>