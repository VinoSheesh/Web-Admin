<?php
// C:\laragon\www\sidesi\tambah_agama_proses.php

include_once 'koneksi.php';
$db = new database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_agama = trim($_POST['nama_agama'] ?? '');

    $errors = [];

    // Validasi Nama Agama
    if (empty($nama_agama)) {
        $errors[] = "Nama Agama wajib diisi.";
    } elseif (strlen($nama_agama) > 50) {
        $errors[] = "Nama Agama maksimal 50 karakter.";
    }

    if (!empty($errors)) {
        header("Location: agama.php?status=error&pesan=" . urlencode(implode("<br>", $errors)));
        exit();
    }

    // --- LOGIKA UNTUK MENGHASILKAN ID AGAMA OTOMATIS ---
    $last_id = $db->get_last_id_agama();
    $new_id = 1; // Default jika belum ada agama

    if ($last_id !== null) {
        if (is_numeric($last_id)) {
            $new_id = (int)$last_id + 1;
        } else {
            $errors[] = "Format ID Agama terakhir tidak valid (bukan angka). Harap hubungi administrator.";
        }
    }

    if (!empty($errors)) {
        header("Location: agama.php?status=error&pesan=" . urlencode(implode("<br>", $errors)));
        exit();
    }

    // Pastikan ID Agama yang baru unik (pencegahan jika ada ID yang dihapus)
    $existing_agama = $db->get_agama_by_id((string)$new_id);
    while ($existing_agama) {
        $new_id++;
        $existing_agama = $db->get_agama_by_id((string)$new_id);
    }
    
    $id_agama_otomatis = (string)$new_id; // Konversi kembali ke string

    // --- AKHIR LOGIKA OTOMATIS ---

    // Panggil fungsi add_agama dengan ID yang sudah otomatis
    $result = $db->add_agama($id_agama_otomatis, $nama_agama);

    if ($result) {
        header("Location: agama.php?status=success&pesan=" . urlencode("Agama '{$nama_agama}' berhasil ditambahkan dengan ID: {$id_agama_otomatis}"));
        exit();
    } else {
        error_log("Failed to add agama. DB Error: " . $db->koneksi->error);
        header("Location: agama.php?status=error&pesan=" . urlencode("Gagal menambahkan agama. Silakan coba lagi atau hubungi administrator."));
        exit();
    }

} else {
    header("Location: agama.php");
    exit();
}
?>