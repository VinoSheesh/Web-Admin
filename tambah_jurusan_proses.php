<?php
// C:\laragon\www\sidesi\tambah_jurusan_proses.php

include_once 'koneksi.php';
$db = new database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_jurusan = trim($_POST['nama_jurusan'] ?? '');

    $errors = [];

    // Validasi Nama Jurusan
    if (empty($nama_jurusan)) {
        $errors[] = "Nama Jurusan wajib diisi.";
    } elseif (strlen($nama_jurusan) > 50) {
        $errors[] = "Nama Jurusan maksimal 50 karakter.";
    }

    if (!empty($errors)) {
        header("Location: jurusan.php?status=error&pesan=" . urlencode(implode("<br>", $errors)));
        exit();
    }

    // --- LOGIKA UNTUK MENGHASILKAN KODE JURUSAN OTOMATIS ---
    $last_kode = $db->get_last_kode_jurusan();
    $new_kode = 1; // Default jika belum ada jurusan

    if ($last_kode !== null) {
        // Coba konversi kode terakhir ke angka
        if (is_numeric($last_kode)) {
            $new_kode = (int)$last_kode + 1;
        } else {
            // Jika kode terakhir bukan angka (misal: "IPA", "IPS"), Anda harus memutuskan logikanya di sini.
            // Untuk sementara, kita bisa menganggapnya sebagai error atau memberikan kode default.
            // Atau Anda perlu logika yang lebih kompleks untuk mengurai kode non-angka.
            // Contoh: jika "JUR001", Anda perlu ekstrak "001" dan inkremen.
            // Untuk saat ini, kita anggap kode_jurusan adalah angka murni.
            $errors[] = "Format Kode Jurusan terakhir tidak valid (bukan angka). Harap hubungi administrator.";
        }
    }

    if (!empty($errors)) {
        header("Location: jurusan.php?status=error&pesan=" . urlencode(implode("<br>", $errors)));
        exit();
    }

    // Pastikan kode_jurusan yang baru unik
    // Ini penting jika ada kemungkinan kode jurusan yang dihapus dan Anda ingin mengisi "lubang"
    // Tapi untuk auto-increment sederhana, ini mungkin tidak terlalu kritis jika Anda yakin tidak ada duplikasi
    $existing_jurusan = $db->get_jurusan_by_kode($new_kode); // Asumsi ada fungsi get_jurusan_by_kode
    while ($existing_jurusan) {
        $new_kode++;
        $existing_jurusan = $db->get_jurusan_by_kode($new_kode);
    }
    
    $kode_jurusan_otomatis = (string)$new_kode; // Konversi kembali ke string jika perlu

    // --- AKHIR LOGIKA OTOMATIS ---

    // Panggil fungsi tambah_jurusan dengan kode yang sudah otomatis
    $result = $db->add_jurusan($kode_jurusan_otomatis, $nama_jurusan);

    if ($result) {
        header("Location: jurusan.php?status=success&pesan=" . urlencode("Jurusan '{$nama_jurusan}' berhasil ditambahkan dengan Kode Jurusan: {$kode_jurusan_otomatis}"));
        exit();
    } else {
        error_log("Failed to add jurusan. DB Error: " . $db->koneksi->error);
        header("Location: jurusan.php?status=error&pesan=" . urlencode("Gagal menambahkan jurusan. Silakan coba lagi atau hubungi administrator."));
        exit();
    }

} else {
    header("Location: jurusan.php");
    exit();
}
?>