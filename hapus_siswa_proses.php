<?php
// C:\laragon\www\sidesi\hapus_siswa_proses.php

// Pastikan koneksi.php hanya di-load sekali
include_once 'koneksi.php';

// Buat objek database
$db = new database();

// Cek apakah ada parameter NISN di URL
if (isset($_GET['nisn'])) {
    $nisn = htmlspecialchars($_GET['nisn']);

    // Panggil fungsi hapus_siswa dari kelas database
    $result = $db->hapus_siswa($nisn);

    if ($result) {
        // Jika berhasil, arahkan kembali ke halaman data siswa dengan pesan sukses
        header("Location: tables.php?status=success&pesan=" . urlencode("Data siswa dengan NISN {$nisn} berhasil dihapus!"));
        exit();
    } else {
        // Jika gagal, arahkan kembali dengan pesan error
        header("Location: tables.php?status=error&pesan=" . urlencode("Gagal menghapus data siswa dengan NISN {$nisn}. Error: " . $db->koneksi->error));
        exit();
    }
} else {
    // Jika tidak ada NISN yang diberikan, arahkan kembali ke halaman siswa dengan pesan error
    header("Location: tables.php?status=error&pesan=" . urlencode("NISN tidak ditemukan untuk penghapusan."));
    exit();
}
?>