<?php
// C:\laragon\www\sidesi\hapus_jurusan_proses.php

include_once 'koneksi.php';
$db = new database();

if (isset($_GET['kode_jurusan'])) { // Pastikan parameter 'kode_jurusan' ada di URL
    $kode_jurusan = htmlspecialchars(trim($_GET['kode_jurusan'])); // Ambil dan bersihkan kode jurusan

    if (empty($kode_jurusan)) {
        header("Location: jurusan.php?status=error&pesan=" . urlencode("Kode Jurusan tidak valid untuk penghapusan."));
        exit();
    }

    $result = $db->hapus_jurusan($kode_jurusan);

    if ($result) {
        header("Location: jurusan.php?status=success&pesan=" . urlencode("Jurusan dengan kode '{$kode_jurusan}' berhasil dihapus!"));
        exit();
    } else {
        // Deteksi pesan error jika gagal karena foreign key (ada siswa terkait)
        if (strpos($db->koneksi->error, 'FOREIGN KEY constraint failed') !== false || strpos($db->koneksi->error, 'Cannot delete or update a parent row') !== false) {
             header("Location: jurusan.php?status=error&pesan=" . urlencode("Gagal menghapus jurusan '{$kode_jurusan}'. Terdapat siswa yang masih terdaftar di jurusan ini. Harap hapus atau pindahkan siswa terkait terlebih dahulu."));
        } else {
             // Pesan error umum jika ada masalah lain
             header("Location: jurusan.php?status=error&pesan=" . urlencode("Gagal menghapus jurusan '{$kode_jurusan}'. Error database: " . $db->koneksi->error));
        }
        exit();
    }
} else {
    // Jika tidak ada parameter 'kode_jurusan' di URL, redirect
    header("Location: jurusan.php");
    exit();
}
?>