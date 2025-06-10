<?php
session_start();
include_once 'koneksi.php';
$db = new database();

// Cek hak akses: hanya administrator yang bisa mengedit user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrator') {
    header("Location: users.php?status=error&pesan=" . urlencode("Anda tidak memiliki izin untuk mengedit pengguna."));
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = trim($_POST['id'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role = trim($_POST['role'] ?? 'pengamat');

    if (empty($id) || empty($username) || empty($full_name) || empty($role)) {
        header("Location: users.php?status=error&pesan=" . urlencode("Semua kolom wajib diisi."));
        exit();
    }

    // Anda bisa tambahkan validasi lebih lanjut di sini, misalnya cek username unik (kecuali untuk user yang sedang diedit)

    $result = $db->update_user($id, $username, $full_name, $role);

    if ($result) {
        header("Location: users.php?status=success&pesan=" . urlencode("Pengguna berhasil diupdate."));
        exit();
    } else {
        header("Location: users.php?status=error&pesan=" . urlencode("Gagal mengupdate pengguna."));
        exit();
    }
} else {
    header("Location: users.php"); // Redirect jika diakses langsung tanpa POST
    exit();
}
?>