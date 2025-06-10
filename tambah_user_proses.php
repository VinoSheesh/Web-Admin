<?php
session_start();
include_once 'koneksi.php';
$db = new database();

// Cek hak akses: hanya administrator yang bisa menambah user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrator') {
    header("Location: users.php?status=error&pesan=" . urlencode("Anda tidak memiliki izin untuk menambah pengguna."));
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role = trim($_POST['role'] ?? 'pengamat');

    $errors = [];

    if (empty($username) || empty($password) || empty($confirm_password) || empty($full_name) || empty($role)) {
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

    if (empty($errors)) {
        $result = $db->register_user($username, $password, $full_name, $role); // Menggunakan fungsi register_user yang sudah ada
        if ($result) {
            header("Location: users.php?status=success&pesan=" . urlencode("Pengguna berhasil ditambahkan."));
            exit();
        } else {
            header("Location: users.php?status=error&pesan=" . urlencode("Gagal menambahkan pengguna."));
            exit();
        }
    } else {
        header("Location: users.php?status=error&pesan=" . urlencode(implode(" ", $errors)));
        exit();
    }
} else {
    header("Location: users.php"); // Redirect jika diakses langsung tanpa POST
    exit();
}
?>