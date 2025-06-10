<?php
// C:\laragon\www\sidesi\edit_jurusan_proses.php

include_once 'koneksi.php';
$db = new database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tambahkan baris debugging ini sementara:
    error_log("POST data received: " . print_r($_POST, true));

    $kode_jurusan = trim($_POST['kode_jurusan'] ?? '');
    $nama_jurusan = trim($_POST['nama_jurusan'] ?? '');

    $errors = [];

    if (empty($kode_jurusan)) {
        $errors[] = "Kode Jurusan tidak ditemukan untuk diupdate. Harap hubungi administrator.";
    }

    if (empty($nama_jurusan)) {
        $errors[] = "Nama Jurusan wajib diisi.";
    } elseif (strlen($nama_jurusan) > 50) {
        $errors[] = "Nama Jurusan maksimal 50 karakter.";
    }

    if (!empty($errors)) {
        header("Location: jurusan.php?status=error&pesan=" . urlencode(implode("<br>", $errors)));
        exit();
    }

    $result = $db->update_jurusan($kode_jurusan, $kode_jurusan, $nama_jurusan);

    if ($result) {
        header("Location: jurusan.php?status=success&pesan=" . urlencode("Jurusan '{$nama_jurusan}' berhasil diperbarui!"));
        exit();
    } else {
        error_log("Failed to update jurusan. DB Error: " . $db->koneksi->error); 
        header("Location: jurusan.php?status=error&pesan=" . urlencode("Gagal memperbarui jurusan. Silakan coba lagi atau hubungi administrator."));
        exit();
    }
} else {
    header("Location: jurusan.php");
    exit();
}
?>