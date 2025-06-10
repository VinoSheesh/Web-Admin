<?php
// C:\laragon\www\sidesi\edit_agama_proses.php

include_once 'koneksi.php';
$db = new database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_agama = trim($_POST['id_agama'] ?? ''); // ID Agama yang tidak berubah (dari input hidden)
    $nama_agama = trim($_POST['nama_agama'] ?? '');

    $errors = [];

    // Validasi ID Agama (hanya memastikan tidak kosong, karena ini identifier)
    if (empty($id_agama)) {
        $errors[] = "ID Agama tidak ditemukan untuk diupdate. Harap hubungi administrator.";
    }

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

    // Panggil fungsi update_agama. ID lama dan baru adalah SAMA karena ID tidak diubah.
    $result = $db->update_agama($id_agama, $id_agama, $nama_agama);

    if ($result) {
        header("Location: agama.php?status=success&pesan=" . urlencode("Agama '{$nama_agama}' berhasil diperbarui!"));
        exit();
    } else {
        error_log("Failed to update agama. DB Error: " . $db->koneksi->error);
        header("Location: agama.php?status=error&pesan=" . urlencode("Gagal memperbarui agama. Silakan coba lagi atau hubungi administrator."));
        exit();
    }
} else {
    header("Location: agama.php");
    exit();
}
?>