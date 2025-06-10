<?php
// C:\laragon\www\sidesi\tambah_siswa_proses.php

// Pastikan koneksi.php hanya di-load sekali
include_once 'koneksi.php';

// Buat objek database untuk berinteraksi dengan database
$db = new database();

// Cek apakah permintaan datang dari metode POST (yaitu, dari pengiriman form)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari $_POST superglobal
    // Gunakan operator Null Coalescing (??) untuk menangani kasus di mana field mungkin kosong
    // Ini penting untuk mencegah 'Undefined index' error dan untuk htmlspecialchars()
    $nisn          = $_POST['nisn'] ?? '';
    $nama          = $_POST['nama'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
    $kode_jurusan  = $_POST['kode_jurusan'] ?? null;
    $kelas         = $_POST['kelas'] ?? '';
    $alamat        = $_POST['alamat'] ?? null;
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
    $tahun_masuk   = $_POST['tahun_masuk'] ?? null;
    $id_agama      = $_POST['id_agama'] ?? null;
    $no_hp         = $_POST['no_hp'] ?? null;
    $status_siswa  = $_POST['status_siswa'] ?? 'Aktif';

    // --- Validasi Sisi Server yang Diperkuat ---
    $errors = []; // Array untuk menyimpan pesan error

    // Validasi NISN: Wajib, 5 digit angka
    if (empty($nisn)) {
        $errors[] = "NISN wajib diisi.";
    } elseif (!preg_match("/^[0-9]{5}$/", $nisn)) { // Memastikan tepat 5 digit angka
        $errors[] = "NISN harus 5 digit angka.";
    }

    if ($db->check_nisn_exists($nisn)) {
        $errors[] = "NISN $nisn sudah terdaftar dalam database!";
    }

    // Validasi Nama: Wajib, maks 50 karakter
    if (empty($nama)) {
        $errors[] = "Nama wajib diisi.";
    } elseif (strlen($nama) > 50) {
        $errors[] = "Nama maksimal 50 karakter.";
    }

    // Validasi Jenis Kelamin: Opsional
    // Hanya validasi jika diisi, dan harus 'L' atau 'P'
    if (!empty($jenis_kelamin) && !in_array($jenis_kelamin, ['L', 'P'])) {
        $errors[] = "Jenis Kelamin tidak valid. Pilih Laki-laki atau Perempuan.";
    }


    // Validasi Jurusan: Wajib dipilih
    if (empty($kode_jurusan)) {
        $errors[] = "Jurusan wajib dipilih.";
    }
    // Tambahan: Pastikan kode_jurusan yang dipilih ada di database (opsional tapi bagus)
    // $existing_jurusan = $db->get_jurusan_by_kode($kode_jurusan); // Anda perlu membuat fungsi ini di koneksi.php
    // if (!$existing_jurusan) {
    //     $errors[] = "Jurusan yang dipilih tidak valid.";
    // }


    // Validasi Kelas: Wajib dipilih, harus salah satu dari X, XI, XII
    $valid_kelas_options = ['X', 'XI', 'XII'];
    if (empty($kelas)) {
        $errors[] = "Kelas wajib dipilih.";
    } elseif (!in_array($kelas, $valid_kelas_options)) {
        $errors[] = "Kelas tidak valid. Pilih antara X, XI, atau XII.";
    }

    // Validasi Alamat: Opsional, bisa ditambahkan batasan panjang jika perlu
    // if (!empty($alamat) && strlen($alamat) > 255) {
    //     $errors[] = "Alamat terlalu panjang.";
    // }


    // Validasi Tanggal Lahir: Opsional, validasi format YYYY-MM-DD
    if (!empty($tanggal_lahir)) {
        $date_parts = explode('-', $tanggal_lahir);
        if (count($date_parts) === 3 && checkdate($date_parts[1], $date_parts[2], $date_parts[0])) {
            // Tanggal valid
        } else {
            $errors[] = "Format Tanggal Lahir tidak valid. Gunakan YYYY-MM-DD.";
        }
    }


    // Validasi Tahun Masuk: Opsional, 4 digit angka jika diisi
    if (!empty($tahun_masuk) && !preg_match("/^[0-9]{4}$/", $tahun_masuk)) {
        $errors[] = "Tahun Masuk harus 4 digit angka (YYYY).";
    }

    // Validasi Agama: Wajib dipilih
    if (empty($id_agama)) {
        $errors[] = "Agama wajib dipilih.";
    }
    // Tambahan: Pastikan id_agama yang dipilih ada di database (opsional tapi bagus)
    // $existing_agama = $db->get_agama_by_id($id_agama); // Anda perlu membuat fungsi ini di koneksi.php
    // if (!$existing_agama) {
    //     $errors[] = "Agama yang dipilih tidak valid.";
    // }


    // Validasi Nomor HP: Opsional, maks 15 karakter, hanya angka
    if (!empty($no_hp)) {
        if (strlen($no_hp) > 15) {
            $errors[] = "Nomor HP maksimal 15 karakter.";
        }
        if (!preg_match("/^[0-9]+$/", $no_hp)) { // Hanya angka
            $errors[] = "Nomor HP hanya boleh berisi angka.";
        }
    }


    // Validasi Status Siswa: Opsional, harus salah satu dari opsi yang valid
    $valid_status_options = ['Aktif', 'Lulus', 'Keluar', 'Mutasi'];
    if (!empty($status_siswa) && !in_array($status_siswa, $valid_status_options)) {
        $errors[] = "Status Siswa tidak valid.";
    }

    // --- Akhir Validasi Sisi Server ---

    if (!empty($errors)) {
        // Jika ada error, arahkan kembali dengan semua pesan error digabungkan
        header("Location: tables.php?status=error&pesan=" . urlencode(implode("<br>", $errors)));
        exit(); // Hentikan eksekusi skrip
    }

    // Jika semua validasi sisi server lolos, lanjutkan proses database
    $result = $db->tambah_siswa(
        $nisn,
        $nama,
        $kode_jurusan,
        $kelas,
        $alamat,
        $id_agama,
        $jenis_kelamin,
        $tanggal_lahir,
        $tahun_masuk,
        $no_hp,
        $status_siswa
    );

    // Periksa hasil operasi database
    if ($result) {
        // Jika penambahan data berhasil, arahkan kembali ke halaman data siswa dengan pesan sukses
        header("Location: tables.php?status=success&pesan=" . urlencode("Data siswa berhasil ditambahkan!"));
        exit();
    } else {
        // Jika ada kegagalan saat menambah data, arahkan kembali dengan pesan error
        // Penting: $db->koneksi->error akan memberikan pesan error dari MySQL, sangat membantu untuk debugging
        header("Location: tables.php?status=error&pesan=" . urlencode("Gagal menambahkan data siswa. Error database: " . $db->koneksi->error));
        exit();
    }
} else {
    // Jika file ini diakses langsung (bukan dari submit form POST), arahkan kembali ke halaman siswa
    header("Location: tables.php");
    exit();
}
?>