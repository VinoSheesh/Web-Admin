<?php

class database
{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "sekolah";
    private $port = 3306;

    public $koneksi;

    public function __construct()
    {
        $this->koneksi = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

        if ($this->koneksi->connect_error) {
            die("Koneksi gagal: " . $this->koneksi->connect_error);
        }
    }

    

    function sanitize_input($data) {
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data); // Mencegah XSS saat ditampilkan
    $data = mysqli_real_escape_string($koneksi, $data); // Mencegah SQL Injection
    return $data;
}

// Anda mungkin sudah punya fungsi untuk mendapatkan data user dari DB
function get_user_data_by_id($user_id) {
    global $koneksi;
    $query = "SELECT id, username, full_name, role FROM users WHERE id = " . (int)$user_id;
    $result = mysqli_query($koneksi, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}
    
    public function get_all_users(){
        // Menambahkan kolom 'password' jika Anda ingin menampilkannya (TIDAK DISARANKAN)
        $query = mysqli_query($this->koneksi, "SELECT id, username, full_name, role, created_at, password FROM users");
        $data = [];
        while($d = mysqli_fetch_array($query)){
            $data[] = $d;
        }
        return $data;
    }

    // Fungsi untuk menghapus user (akan digunakan oleh admin)
    public function delete_user($id){
        $stmt = mysqli_prepare($this->koneksi, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    // Fungsi untuk mengupdate user (akan digunakan oleh admin)
    public function update_user($id, $username, $full_name, $role){
        $stmt = mysqli_prepare($this->koneksi, "UPDATE users SET username = ?, full_name = ?, role = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $username, $full_name, $role, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    
    // Fungsi untuk mendapatkan data user berdasarkan ID (untuk form edit)
    public function get_user_by_id($id){
        $stmt = mysqli_prepare($this->koneksi, "SELECT id, username, full_name, role, created_at FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    public function register_user($username, $password, $full_name, $role = 'pengamat'){
        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($this->koneksi, "INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $username, $hashed_password, $full_name, $role);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    public function login_user($username, $password){
        $stmt = mysqli_prepare($this->koneksi, "SELECT id, username, password, full_name, role FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            // Verifikasi password yang diinput dengan hash yang tersimpan
            if (password_verify($password, $user['password'])) {
                mysqli_stmt_close($stmt);
                return $user; // Mengembalikan data user jika login berhasil
            }
        }
        mysqli_stmt_close($stmt);
        return false; // Login gagal
    }

    public function get_user_by_username($username){
        $stmt = mysqli_prepare($this->koneksi, "SELECT id, username, full_name, role FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

     public function get_total_users(){
        $query = mysqli_query($this->koneksi, "SELECT COUNT(*) AS total FROM users");
        $result = mysqli_fetch_assoc($query);
        return $result['total'];
    }

    public function get_statistik_jurusan()
    {
        // Query ini menghitung jumlah siswa untuk setiap jurusan.
        // LEFT JOIN memastikan jurusan yang belum punya siswa tetap muncul (dengan jumlah 0).
        $query = "SELECT j.nama_jurusan, COUNT(s.nisn) as jumlah_siswa
                  FROM jurusan j
                  LEFT JOIN siswa s ON j.kode_jurusan = s.kode_jurusan
                  GROUP BY j.nama_jurusan
                  ORDER BY j.nama_jurusan ASC"; // Urutkan berdasarkan nama jurusan agar konsisten

        $result = $this->koneksi->query($query); // Jalankan query
        $data = []; // Buat array kosong untuk menyimpan hasil
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Pastikan jumlah_siswa adalah integer
                $row['jumlah_siswa'] = (int) $row['jumlah_siswa'];
                $data[] = $row;
            }
        } else {
            // Jika ada error pada query, catat ke log PHP untuk debugging
            error_log("Error get_statistik_jurusan: " . $this->koneksi->error);
        }
        return $data; // Kembalikan array berisi data statistik jurusan
    }

    // FUNGSI UNTUK STATISTIK SISWA PER TAHUN MASUK (untuk Bar/Line Chart)
    // Akan mengembalikan data seperti:
    // [ { "tahun_masuk": "2020", "jumlah_siswa": 30 },
    //   { "tahun_masuk": "2021", "jumlah_siswa": 45 }, ... ]
    public function get_statistik_tahun_masuk()
    {
        // Query ini menghitung jumlah siswa per tahun masuk.
        // WHERE clause memastikan hanya tahun_masuk yang tidak kosong yang dihitung.
        $query = "SELECT tahun_masuk, COUNT(nisn) as jumlah_siswa
                  FROM siswa
                  WHERE tahun_masuk IS NOT NULL AND tahun_masuk != ''
                  GROUP BY tahun_masuk
                  ORDER BY tahun_masuk ASC"; // Urutkan berdasarkan tahun masuk

        $result = $this->koneksi->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        } else {
            error_log("Error get_statistik_tahun_masuk: " . $this->koneksi->error);
        }
        return $data; // Kembalikan array berisi data statistik tahun masuk
    }

    // (Opsional) FUNGSI UNTUK JUMLAH TOTAL SISWA (untuk kotak angka di dashboard)
    public function get_total_siswa()
    {
        $query = "SELECT COUNT(nisn) as total FROM siswa";
        $result = $this->koneksi->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['total'];
        }
        return 0; // Jika tidak ada siswa
    }

    // (Opsional) FUNGSI UNTUK JUMLAH TOTAL JURUSAN (untuk kotak angka di dashboard)
    public function get_total_jurusan()
    {
        $query = "SELECT COUNT(kode_jurusan) as total FROM jurusan";
        $result = $this->koneksi->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['total'];
        }
        return 0; // Jika tidak ada jurusan
    }

     public function get_total_agama(){
        $query = mysqli_query($this->koneksi, "SELECT COUNT(*) AS total FROM agama");
        $result = mysqli_fetch_assoc($query);
        return $result['total'];
    }

    // Fungsi untuk menampilkan data siswa (sesuai struktur baru)
    public function tampil_data_siswa()
    {
        $hasil = [];
        $query = "SELECT
                siswa.nisn,
                siswa.nama,
                siswa.jenis_kelamin,
                siswa.kode_jurusan,  -- TAMBAHKAN INI untuk modal edit
                siswa.kelas,
                siswa.alamat,
                siswa.tanggal_lahir,
                siswa.tahun_masuk,
                siswa.id_agama,      -- TAMBAHKAN INI untuk modal edit
                siswa.no_hp,
                siswa.status_siswa,
                jurusan.nama_jurusan,
                agama.nama_agama
              FROM siswa
              LEFT JOIN jurusan ON siswa.kode_jurusan = jurusan.kode_jurusan
              LEFT JOIN agama ON siswa.id_agama = agama.id_agama
              ORDER BY siswa.nama ASC";

        $data = $this->koneksi->query($query);

        if ($data) {
            while ($row = $data->fetch_assoc()) {
                $hasil[] = $row;
            }
        } else {
            echo "Error saat mengambil data siswa: " . $this->koneksi->error;
        }
        return $hasil;
    }

    public function tampil_data_agama()
    {
        $hasil = [];
        $query = "SELECT * FROM agama ORDER BY nama_agama ASC";
        $data = $this->koneksi->query($query);
        if ($data) {
            while ($row = $data->fetch_assoc()) {
                $hasil[] = $row;
            }
        } else {
            echo "Error saat mengambil data agama: " . $this->koneksi->error;
        }
        return $hasil;
    }

     public function get_all_agama(){
        $data = array();
        $query = mysqli_query($this->koneksi, "SELECT * FROM agama ORDER BY id_agama ASC");
        while ($row = mysqli_fetch_array($query)) {
            $data[] = $row;
        }
        return $data;
    }

    public function get_last_id_agama(){
        $query = "SELECT id_agama FROM agama ORDER BY CAST(id_agama AS UNSIGNED) DESC LIMIT 1";
        $result = mysqli_query($this->koneksi, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['id_agama'];
        } else {
            return null; // Tidak ada agama di database
        }
    }

    public function get_agama_by_id($id_agama){
        $stmt = mysqli_prepare($this->koneksi, "SELECT * FROM agama WHERE id_agama = ?");
        mysqli_stmt_bind_param($stmt, "s", $id_agama);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    public function add_agama($id_agama, $nama_agama){
        $stmt = mysqli_prepare($this->koneksi, "INSERT INTO agama (id_agama, nama_agama) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $id_agama, $nama_agama);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    public function update_agama($id_agama_lama, $id_agama_baru, $nama_agama){
        // Dalam kasus ini, id_agama_lama dan id_agama_baru akan sama karena ID tidak diubah
        $stmt = mysqli_prepare($this->koneksi, "UPDATE agama SET id_agama = ?, nama_agama = ? WHERE id_agama = ?");
        mysqli_stmt_bind_param($stmt, "sss", $id_agama_baru, $nama_agama, $id_agama_lama);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    public function delete_agama($id_agama){
        $stmt = mysqli_prepare($this->koneksi, "DELETE FROM agama WHERE id_agama = ?");
        mysqli_stmt_bind_param($stmt, "s", $id_agama);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    // FUNGSI TAMBAH SISWA - URUTAN PARAMETER SUDAH DIPERBAIKI
    // Parameter wajib di depan, opsional di belakang
    public function tambah_siswa(
        $nisn,              // Wajib
        $nama,              // Wajib
        $kode_jurusan,      // Wajib
        $kelas,             // Wajib
        $alamat,            // Wajib
        $id_agama,          // Wajib
        $jenis_kelamin = null, // Opsional
        $tanggal_lahir = null, // Opsional
        $tahun_masuk = null,   // Opsional
        $no_hp = null,      // Opsional
        $status_siswa = 'Aktif' // Opsional
    ) {
        $query = "INSERT INTO siswa (
                    nisn, nama, jenis_kelamin, kode_jurusan, kelas, alamat,
                    tanggal_lahir, tahun_masuk, id_agama, no_hp, status_siswa
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->koneksi->prepare($query);

        // Perhatikan urutan 's' untuk string, 'i' untuk integer
        // nisn(s), nama(s), jenis_kelamin(s), kode_jurusan(i), kelas(s), alamat(s),
        // tanggal_lahir(s), tahun_masuk(s), id_agama(i), no_hp(s), status_siswa(s)
        $stmt->bind_param(
            "sssissssiis",
            $nisn,
            $nama,
            $jenis_kelamin,
            $kode_jurusan,
            $kelas,
            $alamat,
            $tanggal_lahir,
            $tahun_masuk,
            $id_agama,
            $no_hp,
            $status_siswa
        );

        if (!$stmt->execute()) {
            echo "Error saat menambah siswa: " . $stmt->error;
            return false;
        }
        return true;
    }

    public function get_siswa_by_nisn($nisn) {
        error_log("Mencari siswa dengan NISN: " . $nisn); // Debug log
        
        $query = "SELECT 
            siswa.*,
            jurusan.nama_jurusan,
            agama.nama_agama
          FROM siswa
          LEFT JOIN jurusan ON siswa.kode_jurusan = jurusan.kode_jurusan
          LEFT JOIN agama ON siswa.id_agama = agama.id_agama
          WHERE siswa.nisn = ?";

    $stmt = $this->koneksi->prepare($query);
    if ($stmt === false) {
        error_log("Prepare failed: " . $this->koneksi->error);
        return false;
    }

    $stmt->bind_param("s", $nisn);
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    if ($data) {
        error_log("Data siswa ditemukan: " . print_r($data, true)); // Debug log
    } else {
        error_log("Data siswa tidak ditemukan untuk NISN: " . $nisn);
    }
    
    $stmt->close();
    return $data;
    }

    // FUNGSI EDIT SISWA - URUTAN PARAMETER SUDAH DIPERBAIKI
    // Parameter wajib di depan, opsional di belakang
    public function edit_siswa(
        $current_nisn,      // Wajib (NISN siswa yang akan diedit)
        $nisn_new,          // Wajib (NISN baru jika diubah)
        $nama,              // Wajib
        $kode_jurusan,      // Wajib
        $kelas,             // Wajib
        $alamat,            // Wajib
        $id_agama,          // Wajib
        $jenis_kelamin = null, // Opsional
        $tanggal_lahir = null, // Opsional
        $tahun_masuk = null,   // Opsional
        $no_hp = null,      // Opsional
        $status_siswa = 'Aktif' // Opsional
    ) {
        $query = "UPDATE siswa SET
                    nisn = ?,
                    nama = ?,
                    jenis_kelamin = ?,
                    kode_jurusan = ?,
                    kelas = ?,
                    alamat = ?,
                    tanggal_lahir = ?,
                    tahun_masuk = ?,
                    id_agama = ?,
                    no_hp = ?,
                    status_siswa = ?
                  WHERE nisn = ?";

        $stmt = $this->koneksi->prepare($query);

        // Urutan binding: (semua kolom yang di-SET) lalu (NISN untuk WHERE)
        // sssissssiis (untuk SET) + s (untuk WHERE nisn)
        $stmt->bind_param(
            "sssissssiis",
            $nisn_new,
            $nama,
            $jenis_kelamin,
            $kode_jurusan,
            $kelas,
            $alamat,
            $tanggal_lahir,
            $tahun_masuk,
            $id_agama,
            $no_hp,
            $status_siswa,
            $current_nisn
        );

        if (!$stmt->execute()) {
            echo "Error saat mengedit siswa: " . $stmt->error;
            return false;
        }
        return true;
    }

    public function update_siswa(
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
    ) {
        // Debug: Log data yang akan diupdate
        error_log("UPDATE SISWA - NISN: {$nisn}, Nama: {$nama}");

        // Cek apakah siswa dengan NISN ini ada
        $existing = $this->get_siswa_by_nisn($nisn);
        if (!$existing) {
            error_log("NISN {$nisn} tidak ditemukan di database saat update");
            return false;
        }

        $query = "UPDATE siswa SET
            nama = ?,
            kode_jurusan = ?,
            kelas = ?,
            alamat = ?,
            id_agama = ?,
            jenis_kelamin = ?,
            tanggal_lahir = ?,
            tahun_masuk = ?,
            no_hp = ?,
            status_siswa = ?
          WHERE nisn = ?";

        $stmt = $this->koneksi->prepare($query);

        if ($stmt === false) {
            error_log("Prepare failed update_siswa: " . $this->koneksi->error);
            return false;
        }

        // Bind parameter dengan urutan yang benar
        $stmt->bind_param(
            "ssssissssss",
            $nama,          // 1. nama (string)
            $kode_jurusan,  // 2. kode_jurusan (string) 
            $kelas,         // 3. kelas (string)
            $alamat,        // 4. alamat (string)
            $id_agama,      // 5. id_agama (integer)
            $jenis_kelamin, // 6. jenis_kelamin (string)
            $tanggal_lahir, // 7. tanggal_lahir (string)
            $tahun_masuk,   // 8. tahun_masuk (string)
            $no_hp,         // 9. no_hp (string)
            $status_siswa,  // 10. status_siswa (string)
            $nisn           // 11. nisn untuk WHERE clause (string)
        );

        if ($stmt->execute()) {
            $affected_rows = $stmt->affected_rows;
            $stmt->close();

            if ($affected_rows === 0) {
                error_log("No rows affected - NISN {$nisn} update failed");
                return false;
            }

            error_log("Successfully updated NISN {$nisn}");
            return true;
        } else {
            error_log("Execute failed update_siswa: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }


    public function hapus_siswa($nisn) {
        error_log("Executing delete query for NISN: " . $nisn);
        
        // Gunakan prepared statement
        $query = "DELETE FROM siswa WHERE nisn = ?";
        $stmt = $this->koneksi->prepare($query);
        
        if ($stmt === false) {
            error_log("Prepare failed: " . $this->koneksi->error);
            return false;
        }
        
        // Bind parameter
        $stmt->bind_param("s", $nisn);
        
        // Eksekusi query
        $success = $stmt->execute();
        
        if (!$success) {
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        // Cek affected rows
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        error_log("Affected rows: " . $affected);
        
        return $affected > 0;
    }

    public function __destruct()
    {
        if ($this->koneksi) {
            $this->koneksi->close();
        }
    }

    public function tampil_data_jurusan()
    {
        $data = []; // Inisialisasi array kosong untuk menampung hasil
        // Query untuk memilih semua kolom dari tabel 'jurusan', diurutkan berdasarkan nama jurusan
        $query = "SELECT * FROM jurusan ORDER BY nama_jurusan ASC";
        $result = $this->koneksi->query($query); // Jalankan query

        if ($result) { // Periksa apakah query berhasil dieksekusi
            while ($row = $result->fetch_assoc()) { // Ambil setiap baris hasil sebagai array asosiatif
                $data[] = $row; // Tambahkan baris ke array data
            }
        } else {
            // Catat error ke log jika query gagal (penting untuk debugging!)
            error_log("Error tampil_data_jurusan: " . $this->koneksi->error);
        }
        return $data; // Kembalikan array berisi semua data jurusan
    }

    // Menambahkan jurusan baru ke tabel 'jurusan'
    public function tambah_jurusan($kode_jurusan, $nama_jurusan)
    {
        // Gunakan prepared statement untuk keamanan (mencegah SQL Injection)
        $query = "INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES (?, ?)";
        $stmt = $this->koneksi->prepare($query);

        if ($stmt === false) {
            // Jika prepared statement gagal, catat error
            error_log("Prepare failed: (" . $this->koneksi->errno . ") " . $this->koneksi->error);
            return false;
        }

        // Bind parameter: "ss" berarti dua string
        $stmt->bind_param("ss", $kode_jurusan, $nama_jurusan);

        if ($stmt->execute()) { // Jalankan statement
            return true; // Berhasil menambahkan
        } else {
            // Catat error jika eksekusi gagal
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            return false; // Gagal menambahkan
        }
        $stmt->close(); // Tutup statement
    }

    public function add_jurusan($kode_jurusan, $nama_jurusan)
    {
        // Menggunakan prepared statement untuk mencegah SQL injection
        $stmt = mysqli_prepare($this->koneksi, "INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES (?, ?)");

        // 'ss' berarti dua parameter string
        mysqli_stmt_bind_param($stmt, "ss", $kode_jurusan, $nama_jurusan);

        // Eksekusi statement
        $result = mysqli_stmt_execute($stmt);

        // Tutup statement
        mysqli_stmt_close($stmt);

        return $result; // Mengembalikan true jika berhasil, false jika gagal
    }

    public function get_last_kode_jurusan()
    {
        $query = "SELECT kode_jurusan FROM jurusan ORDER BY CAST(kode_jurusan AS UNSIGNED) DESC LIMIT 1";
        $result = mysqli_query($this->koneksi, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['kode_jurusan'];
        } else {
            return null; // Tidak ada jurusan di database atau kode bukan angka
        }
    }

    // Mengambil satu data jurusan berdasarkan kode_jurusan (digunakan untuk mengisi form edit)
    public function get_jurusan_by_kode($kode_jurusan)
    {
        $query = "SELECT * FROM jurusan WHERE kode_jurusan = ?";
        $stmt = $this->koneksi->prepare($query);

        if ($stmt === false) {
            error_log("Prepare failed: (" . $this->koneksi->errno . ") " . $this->koneksi->error);
            return false;
        }

        $stmt->bind_param("s", $kode_jurusan); // "s" berarti string
        $stmt->execute();
        $result = $stmt->get_result(); // Dapatkan hasil query
        $data = $result->fetch_assoc(); // Ambil satu baris hasil
        $stmt->close();
        return $data; // Kembalikan data jurusan atau null jika tidak ditemukan
    }

    // Memperbarui data jurusan yang sudah ada
    // $kode_jurusan_lama: kode jurusan yang ingin diubah (untuk WHERE clause)
    // $kode_jurusan_baru: kode jurusan baru yang akan disimpan
    // $nama_jurusan: nama jurusan baru yang akan disimpan
    public function update_jurusan($kode_jurusan_lama, $kode_jurusan_baru, $nama_jurusan)
    {
        $query = "UPDATE jurusan SET kode_jurusan = ?, nama_jurusan = ? WHERE kode_jurusan = ?";
        $stmt = $this->koneksi->prepare($query);

        if ($stmt === false) {
            error_log("Prepare failed: (" . $this->koneksi->errno . ") " . $this->koneksi->error);
            return false;
        }

        // "sss" berarti tiga string
        $stmt->bind_param("sss", $kode_jurusan_baru, $nama_jurusan, $kode_jurusan_lama);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            return false;
        }
        $stmt->close();
    }

    public function hapus_jurusan($kode_jurusan)
    {

        $query = "DELETE FROM jurusan WHERE kode_jurusan = ?";
        $stmt = $this->koneksi->prepare($query);

        if ($stmt === false) {
            error_log("Prepare failed: (" . $this->koneksi->errno . ") " . $this->koneksi->error);
            return false;
        }

        $stmt->bind_param("s", $kode_jurusan);

        if ($stmt->execute()) {
            return true;
        } else {
            if (strpos($stmt->error, 'FOREIGN KEY constraint failed') !== false || strpos($stmt->error, 'Cannot delete or update a parent row') !== false) {
                error_log("Gagal menghapus jurusan karena masih ada siswa yang terdaftar di jurusan ini: " . $kode_jurusan);
                return false;
            }
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            return false;
        }
        $stmt->close();
    }

    public function update_user_profile($user_id, $full_name) {
    $stmt = mysqli_prepare($this->koneksi, "UPDATE users SET full_name = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $full_name, $user_id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

public function update_user_password($user_id, $new_password) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($this->koneksi, "UPDATE users SET password = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

public function verify_current_password($user_id, $current_password) {
    $stmt = mysqli_prepare($this->koneksi, "SELECT password FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$user) {
        return false;
    }
    
    return password_verify($current_password, $user['password']);
}

public function check_nisn_exists($nisn) {
    $stmt = mysqli_prepare($this->koneksi, "SELECT COUNT(*) as count FROM siswa WHERE nisn = ?");
    mysqli_stmt_bind_param($stmt, "s", $nisn);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $data['count'] > 0;
}
}
?>