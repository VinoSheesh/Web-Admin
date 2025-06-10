<?php
// Perbaikan edit_siswa_proses.php

include_once 'koneksi.php';
$db = new database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Log semua data POST yang diterima
    error_log("POST Data received: " . print_r($_POST, true));
    
    // Ambil semua data dari form edit
    $nisn = trim($_POST['nisn'] ?? ''); // Trim untuk menghilangkan spasi
    $nama = trim($_POST['nama'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
    $kode_jurusan = $_POST['kode_jurusan'] ?? null;
    $kelas = $_POST['kelas'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
    $tahun_masuk = $_POST['tahun_masuk'] ?? null;
    $id_agama = $_POST['id_agama'] ?? null;
    $no_hp = trim($_POST['no_hp'] ?? '');
    $status_siswa = $_POST['status_siswa'] ?? 'Aktif';

    // Debug: Log NISN yang akan dicari
    error_log("Processing edit for NISN: '{$nisn}'");

    // --- Validasi Sisi Server ---
    $errors = [];

    // Validasi NISN (Pastikan tidak kosong)
    if (empty($nisn)) {
        $errors[] = "NISN siswa tidak ditemukan untuk diupdate.";
        error_log("ERROR: NISN is empty");
    } elseif (!preg_match("/^[0-9]{5}$/", $nisn)) {
        $errors[] = "NISN harus 5 digit angka.";
        error_log("ERROR: NISN format invalid: '{$nisn}'");
    }

    // Validasi Nama
    if (empty($nama)) {
        $errors[] = "Nama wajib diisi.";
    } elseif (strlen($nama) > 50) {
        $errors[] = "Nama maksimal 50 karakter.";
    }

    // Validasi Jurusan
    if (empty($kode_jurusan)) {
        $errors[] = "Jurusan wajib dipilih.";
    }

    // Validasi Kelas
    $valid_kelas_options = ['X', 'XI', 'XII'];
    if (empty($kelas)) {
        $errors[] = "Kelas wajib dipilih.";
    } elseif (!in_array($kelas, $valid_kelas_options)) {
        $errors[] = "Kelas tidak valid. Pilih antara X, XI, atau XII.";
    }

    // Validasi Tanggal Lahir
    if (!empty($tanggal_lahir)) {
        $date_parts = explode('-', $tanggal_lahir);
        if (!(count($date_parts) === 3 && checkdate($date_parts[1], $date_parts[2], $date_parts[0]))) {
            $errors[] = "Format Tanggal Lahir tidak valid. Gunakan YYYY-MM-DD.";
        }
    }

    // Validasi Tahun Masuk
    if (!empty($tahun_masuk) && !preg_match("/^[0-9]{4}$/", $tahun_masuk)) {
        $errors[] = "Tahun Masuk harus 4 digit angka (YYYY).";
    }

    // Validasi Agama
    if (empty($id_agama)) {
        $errors[] = "Agama wajib dipilih.";
    }

    // Validasi Nomor HP
    if (!empty($no_hp)) {
        if (strlen($no_hp) > 15) {
            $errors[] = "Nomor HP maksimal 15 karakter.";
        }
        if (!preg_match("/^[0-9]+$/", $no_hp)) {
            $errors[] = "Nomor HP hanya boleh berisi angka.";
        }
    }

    // Validasi Status Siswa
    $valid_status_options = ['Aktif', 'Lulus', 'Keluar', 'Mutasi'];
    if (!empty($status_siswa) && !in_array($status_siswa, $valid_status_options)) {
        $errors[] = "Status Siswa tidak valid.";
    }

    // --- Akhir Validasi Sisi Server ---

    if (!empty($errors)) {
        error_log("Validation errors: " . implode(", ", $errors));
        header("Location: tables.php?status=error&pesan=" . urlencode(implode("<br>", $errors)));
        exit();
    }

    // DEBUG: Cek apakah siswa dengan NISN ini ada di database
    error_log("Checking if NISN exists: '{$nisn}'");
    $existing_siswa = $db->get_siswa_by_nisn($nisn);
    
    if (!$existing_siswa) {
        error_log("ERROR: Siswa dengan NISN '{$nisn}' tidak ditemukan di database");
        header("Location: tables.php?status=error&pesan=" . urlencode("Siswa dengan NISN {$nisn} tidak ditemukan di database."));
        exit();
    } else {
        error_log("SUCCESS: Siswa ditemukan - " . print_r($existing_siswa, true));
    }

    // Jika validasi lolos, panggil fungsi update_siswa
    error_log("Attempting to update siswa with NISN: '{$nisn}'");
    
    $result = $db->update_siswa(
        $nisn,              // Parameter 1: NISN (untuk WHERE clause)
        $nama,              // Parameter 2: nama
        $kode_jurusan,      // Parameter 3: kode_jurusan  
        $kelas,             // Parameter 4: kelas
        $alamat,            // Parameter 5: alamat
        $id_agama,          // Parameter 6: id_agama
        $jenis_kelamin,     // Parameter 7: jenis_kelamin
        $tanggal_lahir,     // Parameter 8: tanggal_lahir
        $tahun_masuk,       // Parameter 9: tahun_masuk
        $no_hp,             // Parameter 10: no_hp
        $status_siswa       // Parameter 11: status_siswa
    );

    if ($result) {
        error_log("SUCCESS: Data siswa NISN '{$nisn}' berhasil diperbarui");
        header("Location: tables.php?status=success&pesan=" . urlencode("Data siswa dengan NISN {$nisn} berhasil diperbarui!"));
        exit();
    } else {
        error_log("ERROR: Gagal memperbarui data siswa NISN '{$nisn}'");
        header("Location: tables.php?status=error&pesan=" . urlencode("Gagal memperbarui data siswa dengan NISN {$nisn}. Silakan coba lagi."));
        exit();
    }
} else {
    // Jika diakses langsung tanpa POST
    header("Location: tables.php");
    exit();
}
?>