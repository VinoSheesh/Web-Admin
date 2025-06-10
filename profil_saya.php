<?php
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include file koneksi database
require_once 'koneksi.php';
$db = new database();

// Ambil data user dari database
$user_data = $db->get_user_by_id($_SESSION['user_id']);

if (!$user_data) {
    header("Location: logout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Profil Saya</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include 'sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include 'nav.php'; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Profil Saya</h1>
                        <a href="pengaturan_akun.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-edit fa-sm text-white-50"></i> Edit Profil
                        </a>
                    </div>

                    <div class="row">
                        <!-- Profile Card -->
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Informasi Profil</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Aksi Profil:</div>
                                            <a class="dropdown-item" href="pengaturan_akun.php">
                                                <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i>
                                                Edit Profil
                                            </a>
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#changePasswordModal">
                                                <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i>
                                                Ganti Password
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 text-center mb-3">
                                            <img class="img-profile rounded-circle mb-2" 
                                                 src="img/undraw_profile.svg" 
                                                 alt="Profile Picture" 
                                                 style="width: 120px; height: 120px;">
                                            <h5 class="text-gray-900"><?php echo htmlspecialchars($user_data['full_name']); ?></h5>
                                            <span class="badge badge-<?php echo ($user_data['role'] === 'administrator') ? 'danger' : 'primary'; ?>">
                                                <?php echo ucfirst(htmlspecialchars($user_data['role'])); ?>
                                            </span>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="table-responsive">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <th width="30%">Username</th>
                                                        <td><?php echo htmlspecialchars($user_data['username']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Nama Lengkap</th>
                                                        <td><?php echo htmlspecialchars($user_data['full_name']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Role</th>
                                                        <td>
                                                            <span class="badge badge-<?php echo ($user_data['role'] === 'administrator') ? 'danger' : 'primary'; ?>">
                                                                <?php echo ucfirst(htmlspecialchars($user_data['role'])); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tanggal Bergabung</th>
                                                        <td>
                                                            <?php 
                                                            if(isset($user_data['created_at']) && !empty($user_data['created_at'])) {
                                                                echo date('d F Y', strtotime($user_data['created_at']));
                                                            } else {
                                                                echo 'Tidak tersedia';
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="btn-group" role="group" aria-label="Profile Actions">
                                                <a href="pengaturan_akun.php" class="btn btn-primary">
                                                    <i class="fas fa-edit"></i> Edit Profil
                                                </a>
                                                <a href="index.php" class="btn btn-secondary">
                                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                                </a>
                                                <?php if ($user_data['role'] === 'administrator'): ?>
                                                <a href="users.php" class="btn btn-info">
                                                    <i class="fas fa-users-cog"></i> Kelola User
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info Card -->
                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <a href="pengaturan_akun.php" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><i class="fas fa-cogs mr-2"></i>Pengaturan Akun</h6>
                                                <small><i class="fas fa-chevron-right"></i></small>
                                            </div>
                                            <p class="mb-1 small text-muted">Edit nama lengkap dan ganti password</p>
                                        </a>
                                        
                                        <a href="index.php" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</h6>
                                                <small><i class="fas fa-chevron-right"></i></small>
                                            </div>
                                            <p class="mb-1 small text-muted">Kembali ke halaman utama</p>
                                        </a>

                                        <?php if ($user_data['role'] === 'administrator'): ?>
                                        <a href="users.php" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><i class="fas fa-users-cog mr-2"></i>Kelola Pengguna</h6>
                                                <small><i class="fas fa-chevron-right"></i></small>
                                            </div>
                                            <p class="mb-1 small text-muted">Manajemen pengguna sistem</p>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- System Info Card -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Informasi Sistem</h6>
                                </div>
                                <div class="card-body">
                                    <div class="small">
                                        <div class="mb-2">
                                            <strong>Status Akun:</strong> 
                                            <span class="badge badge-success">Aktif</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Login Terakhir:</strong><br>
                                            <small class="text-muted"><?php echo date('d F Y, H:i'); ?></small>
                                        </div>
                                        <div class="mb-2">
                                            <strong>IP Address:</strong><br>
                                            <small class="text-muted"><?php echo $_SERVER['REMOTE_ADDR']; ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>