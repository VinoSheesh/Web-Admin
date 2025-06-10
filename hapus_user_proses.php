<?php
session_start();
include_once 'koneksi.php';
$db = new database();

// Cek hak akses: hanya administrator yang bisa menghapus user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrator') {
    header("Location: users.php?status=error&pesan=" . urlencode("Anda tidak memiliki izin untuk menghapus pengguna."));
    exit();
}

if (isset($_GET['id'])) {
    $id = trim($_GET['id']);

    // Mencegah user menghapus akunnya sendiri
    if ($_SESSION['user_id'] == $id) {
        header("Location: users.php?status=error&pesan=" . urlencode("Anda tidak bisa menghapus akun Anda sendiri."));
        exit();
    }

    $result = $db->delete_user($id);

    if ($result) {
        header("Location: users.php?status=success&pesan=" . urlencode("Pengguna berhasil dihapus."));
        exit();
    } else {
        header("Location: users.php?status=error&pesan=" . urlencode("Gagal menghapus pengguna."));
        exit();
    }
} else {
    header("Location: users.php?status=error&pesan=" . urlencode("ID pengguna tidak ditemukan."));
    exit();
}
?>