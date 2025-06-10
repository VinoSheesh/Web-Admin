<?php
// C:\laragon\www\sidesi\hapus_agama_proses.php

include_once 'koneksi.php';
$db = new database();

if (isset($_GET['id_agama'])) {
    $id_agama = htmlspecialchars(trim($_GET['id_agama']));

    if (empty($id_agama)) {
        header("Location: agama.php?status=error&pesan=" . urlencode("ID Agama tidak ditemukan untuk dihapus."));
        exit();
    }

    $result = $db->delete_agama($id_agama);

    if ($result) {
        header("Location: agama.php?status=success&pesan=" . urlencode("Agama dengan ID '{$id_agama}' berhasil dihapus!"));
        exit();
    } else {
        error_log("Failed to delete agama. DB Error: " . $db->koneksi->error);
        header("Location: agama.php?status=error&pesan=" . urlencode("Gagal menghapus agama. Silakan coba lagi atau hubungi administrator."));
        exit();
    }
} else {
    header("Location: agama.php?status=error&pesan=" . urlencode("Permintaan hapus tidak valid."));
    exit();
}
?>