<?php
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include file koneksi database dan buat instance
require_once 'koneksi.php';
$db = new database();

// Inisialisasi pesan
$message = '';
$message_type = '';

// Ambil data user saat ini untuk ditampilkan di form
$user_id = $_SESSION['user_id'];
$user_data = $db->get_user_by_id($user_id);

if (!$user_data) {
    header("Location: logout.php");
    exit();
}

// Tangani proses update jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Update Profile
    if (isset($_POST['update_profile'])) {
        $new_full_name = trim($_POST['full_name']);
        
        if (empty($new_full_name)) {
            $message = "Nama lengkap tidak boleh kosong.";
            $message_type = "danger";
        } else {
            if ($db->update_user($user_id, $user_data['username'], $new_full_name, $user_data['role'])) {
                $_SESSION['full_name'] = $new_full_name;
                $user_data['full_name'] = $new_full_name;
                $message = "Nama lengkap berhasil diperbarui!";
                $message_type = "success";
            } else {
                $message = "Gagal memperbarui nama lengkap.";
                $message_type = "danger";
            }
        }
    }

    // Change Password
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $message = "Semua field password harus diisi.";
            $message_type = "danger";
        } elseif ($new_password !== $confirm_password) {
            $message = "Password baru dan konfirmasi password tidak cocok.";
            $message_type = "danger";
        } elseif (strlen($new_password) < 6) {
            $message = "Password baru minimal 6 karakter.";
            $message_type = "danger";
        } else {
            // Verifikasi password saat ini
            if (!$db->verify_current_password($_SESSION['user_id'], $current_password)) {
                $message = "Password saat ini salah.";
                $message_type = "danger";
            } else {
                // Update password
                if ($db->update_user_password($_SESSION['user_id'], $new_password)) {
                    $message = "Password berhasil diubah!";
                    $message_type = "success";
                } else {
                    $message = "Gagal mengubah password.";
                    $message_type = "danger";
                }
            }
        }
    }
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

    <title>SB Admin 2 - Pengaturan Akun</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include "sidebar.php" ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include "nav.php" ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Pengaturan Akun</h1>
                        <a href="profil_saya.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Profil
                        </a>
                    </div>

                    <!-- Alert Messages -->
                    <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> mr-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Edit Profile Card -->
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Edit Nama Lengkap</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Aksi:</div>
                                            <a class="dropdown-item" href="profil_saya.php">
                                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                                Lihat Profil
                                            </a>
                                            <a class="dropdown-item" href="index.php">
                                                <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                                Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="">
                                        <input type="hidden" name="update_profile" value="1">
                                        
                                        <div class="form-group row">
                                            <label for="username" class="col-sm-3 col-form-label font-weight-bold">Username</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control-plaintext bg-light px-3 py-2 rounded" 
                                                       id="username" 
                                                       value="<?php echo htmlspecialchars($user_data['username']); ?>" 
                                                       readonly>
                                                <small class="form-text text-muted">Username tidak dapat diubah</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label for="role" class="col-sm-3 col-form-label font-weight-bold">Role</label>
                                            <div class="col-sm-9">
                                                <div class="pt-2">
                                                    <span class="badge badge-<?php echo ($user_data['role'] === 'administrator') ? 'danger' : 'primary'; ?> badge-lg">
                                                        <?php echo ucfirst(htmlspecialchars($user_data['role'])); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label for="full_name" class="col-sm-3 col-form-label font-weight-bold">Nama Lengkap</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                                       value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Update Profil
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Change Password Card -->
                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Ubah Password</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Aksi:</div>
                                            <a class="dropdown-item" href="profil_saya.php">
                                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                                Lihat Profil
                                            </a>
                                            <a class="dropdown-item" href="index.php">
                                                <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                                Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="">
                                        <input type="hidden" name="change_password" value="1">
                                        
                                        <div class="form-group">
                                            <label for="current_password">Password Saat Ini</label>
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="new_password">Password Baru</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm_password">Konfirmasi Password Baru</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-lock"></i> Ubah Password
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End of Main Content -->

                <!-- Footer -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright &copy; Your Website 2023</span>
                        </div>
                    </div>
                </footer>
                <!-- End of Footer -->

            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->

    </div>
    <!-- End of Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin keluar dari akun ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="logout.php">Ya, Keluar</a>
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