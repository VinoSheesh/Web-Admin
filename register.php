<?php
session_start();
include_once 'koneksi.php';
$db = new database();

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$register_message = '';
$message_class = '';
$username = ''; // Inisialisasi variabel ini
$full_name = ''; // Inisialisasi variabel ini
$role = 'pengamat'; // Inisialisasi variabel $role dengan nilai default

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role = trim($_POST['role'] ?? 'pengamat');  // Default role 'pengamat'

    $errors = [];

    if (empty($username) || empty($password) || empty($confirm_password) || empty($full_name)) {
        $errors[] = "Semua kolom wajib diisi.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }

    // Cek apakah username sudah ada
    if ($db->get_user_by_username($username)) {
        $errors[] = "Username sudah digunakan. Silakan pilih username lain.";
    }

    // Validasi role (pastikan hanya 'administrator' atau 'pengamat')
    if (!in_array($role, ['administrator', 'pengamat'])) {
        $errors[] = "Role tidak valid.";
    }

    if (empty($errors)) {
        $result = $db->register_user($username, $password, $full_name, $role);
        if ($result) {
            $register_message = "Registrasi berhasil! Silakan login.";
            $message_class = "alert-success";
            // Opsional: Redirect ke halaman login setelah berhasil register
            // header("Location: login.php?status=success&message=" . urlencode($register_message));
            // exit();
        } else {
            $register_message = "Registrasi gagal. Silakan coba lagi.";
            $message_class = "alert-danger";
        }
    } else {
        $register_message = implode("<br>", $errors);
        $message_class = "alert-danger";
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
    <title>SB Admin 2 - Register</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Buat Akun Baru!</h1>
                            </div>
                            <?php if ($register_message): ?>
                                <div class="alert <?php echo $message_class; ?> text-center"><?php echo $register_message; ?></div>
                            <?php endif; ?>
                            <form class="user" method="POST" action="register.php">
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" id="username"
                                            name="username" placeholder="Username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" id="full_name"
                                            name="full_name" placeholder="Nama Lengkap" value="<?php echo htmlspecialchars($full_name ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user"
                                            id="password" name="password" placeholder="Password" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user"
                                            id="confirm_password" name="confirm_password" placeholder="Ulangi Password" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" id="role" name="role" required>
                                        <option value="pengamat" <?php echo ($role == 'pengamat') ? 'selected' : ''; ?>>Pengamat</option>
                                        <option value="administrator" <?php echo ($role == 'administrator') ? 'selected' : ''; ?>>Administrator</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Register Akun
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="login.php">Sudah punya akun? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>