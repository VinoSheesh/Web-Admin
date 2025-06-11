<?php
// C:\laragon\www\sidesi\hapus_siswa_proses.php

session_start();
include_once 'koneksi.php';

// Cek apakah user adalah administrator
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrator') {
    header("Location: tables.php?status=error&pesan=" . urlencode("Anda tidak memiliki akses untuk menghapus data."));
    exit();
}

// Buat objek database
$db = new database();

// Cek apakah ada parameter NISN di URL
if (isset($_GET['nisn'])) {
    $nisn = trim($_GET['nisn']);
    
    // Debug log
    error_log("Attempting to delete student with NISN: " . $nisn);
    
    // Cek apakah siswa ada
    $siswa = $db->get_siswa_by_nisn($nisn);
    if (!$siswa) {
        header("Location: tables.php?status=error&pesan=" . urlencode("Siswa dengan NISN {$nisn} tidak ditemukan."));
        exit();
    }

    // Lakukan penghapusan
    $result = $db->hapus_siswa($nisn);
    
    if ($result) {
        error_log("Successfully deleted student with NISN: " . $nisn);
        header("Location: tables.php?status=success&pesan=" . urlencode("Data siswa berhasil dihapus!"));
    } else {
        error_log("Failed to delete student with NISN: " . $nisn);
        header("Location: tables.php?status=error&pesan=" . urlencode("Gagal menghapus data siswa."));
    }
} else {
    header("Location: tables.php?status=error&pesan=" . urlencode("NISN tidak ditemukan."));
}

exit();