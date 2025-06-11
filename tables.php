<?php
session_start();

include 'koneksi.php';

$db = new database();

$data_siswa = $db->tampil_data_siswa();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Tables</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include "sidebar.php" ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include "nav.php" ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid overflow-x-hidden">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Tabel Data Siswa SMKN 6 Surakarta</h1>
                    <?php
                    // Bagian ini untuk menampilkan notifikasi sukses/gagal
                    if (isset($_GET['status'])) {
                        $status = htmlspecialchars($_GET['status']); // success atau error
                        $pesan = htmlspecialchars($_GET['pesan'] ?? 'Terjadi kesalahan.'); // Pesan dari proses
                    
                        if ($status == 'success') {
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                            echo $pesan;
                            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                            echo '</div>';
                        } elseif ($status == 'error') {
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                            echo $pesan;
                            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                            echo '</div>';
                        }
                    }
                    ?>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Siswa
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'administrator'): ?>
                                    <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal"
                                        data-target="#tambahSiswaModal">
                                        + Tambah Siswa
                                    </button>
                                <?php endif; ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive ">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <style>
                                        /* Mencegah wrap pada semua sel data di dalam tabel DataTables */
                                        #dataTable td {
                                            white-space: nowrap;
                                        }

                                        /* Opsional: Mencegah wrap pada header kolom juga */
                                        #dataTable th {
                                            white-space: nowrap;
                                        }
                                    </style>
                                    <div class="table-responsive">
                                        <thead>
                                            <tr>
                                                <th>NISN</th>
                                                <th>Nama</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Jurusan</th>
                                                <th>Kelas</th>
                                                <th>Alamat</th>
                                                <th>Tanggal Lahir</th>
                                                <th>Tahun Masuk</th>
                                                <th>Agama</th>
                                                <th>No. HP</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!empty($data_siswa)) {
                                                foreach ($data_siswa as $siswa) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($siswa['nisn']); ?></td>
                                                        <td><?php echo htmlspecialchars($siswa['nama']); ?></td>
                                                        <td><?php echo htmlspecialchars($siswa['jenis_kelamin']); ?></td>
                                                        <td><?php echo htmlspecialchars($siswa['nama_jurusan']); ?></td>
                                                        <td><?php echo htmlspecialchars($siswa['kelas']); ?></td>
                                                        <td><?php echo htmlspecialchars($siswa['alamat']); ?></td>
                                                        <td><?php echo htmlspecialchars($siswa['tanggal_lahir']); ?></td>
                                                        <td><?php echo htmlspecialchars($siswa['tahun_masuk'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($siswa['nama_agama']); ?></td>
                                                        <td><?php echo htmlspecialchars($siswa['no_hp']); ?></td>
                                                        <td><?php echo htmlspecialchars($siswa['status_siswa']); ?></td>
                                                        <td>
                                                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'administrator'): ?>
                                                                <a href="#" class="btn btn-warning btn-sm edit-siswa-btn" 
                                                                   data-toggle="modal" 
                                                                   data-target="#editSiswaModal" 
                                                                   data-nisn="<?php echo $siswa['nisn']; ?>">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </a>
                                                                <a href="#" class="btn btn-danger btn-sm"
                                                                   onclick="confirmDelete('<?php echo htmlspecialchars($siswa['nisn']); ?>', '<?php echo htmlspecialchars($siswa['nama']); ?>')">
                                                                    <i class="fas fa-trash"></i> Hapus
                                                                </a>
                                                            <?php else: ?>
                                                                <span>-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="12" class="text-center">Tidak ada data siswa ditemukan.
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                </table>
                                <div class="modal fade" id="tambahSiswaModal" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Siswa Baru
                                                </h5>
                                                <button class="close" type="button" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <form action="tambah_siswa_proses.php" method="POST">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="nisn">NISN</label>
                                                                <input type="text" class="form-control" id="nisn"
                                                                    name="nisn" required minlength="5" maxlength="5"
                                                                    pattern="[0-9]{5}"
                                                                    title="NISN harus 5 digit angka.">
                                                                <small class="form-text text-muted">NISN harus 5 digit
                                                                    angka.</small>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="nama">Nama</label>
                                                                <input type="text" class="form-control" id="nama"
                                                                    name="nama" required maxlength="50">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="jenis_kelamin">Jenis Kelamin</label>
                                                                <select class="form-control" id="jenis_kelamin"
                                                                    name="jenis_kelamin">
                                                                    <option value="">Pilih Jenis Kelamin</option>
                                                                    <option value="L">Laki-laki</option>
                                                                    <option value="P">Perempuan</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="kode_jurusan">Jurusan</label>
                                                                <select class="form-control" id="kode_jurusan"
                                                                    name="kode_jurusan" required>
                                                                    <option value="">Pilih Jurusan</option>
                                                                    <?php
                                                                    // Ambil data jurusan dari database
                                                                    $data_jurusan = $db->tampil_data_jurusan();
                                                                    if (!empty($data_jurusan)) {
                                                                        foreach ($data_jurusan as $jurusan) {
                                                                            echo '<option value="' . htmlspecialchars($jurusan['kode_jurusan']) . '">' . htmlspecialchars($jurusan['nama_jurusan']) . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="kelas">Kelas</label>
                                                                <select class="form-control" id="kelas" name="kelas"
                                                                    required>
                                                                    <option value="">Pilih Kelas</option>
                                                                    <option value="X">X</option>
                                                                    <option value="XI">XI</option>
                                                                    <option value="XII">XII</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="alamat">Alamat</label>
                                                                <input type="text" class="form-control" id="alamat"
                                                                    name="alamat">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                                                <input type="date" class="form-control"
                                                                    id="tanggal_lahir" name="tanggal_lahir">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="tahun_masuk">Tahun Masuk</label>
                                                                <input type="text" class="form-control" id="tahun_masuk"
                                                                    name="tahun_masuk" placeholder="Contoh: 2023"
                                                                    pattern="[0-9]{4}" maxlength="4"
                                                                    title="Tahun harus 4 digit angka (YYYY).">
                                                                <small class="form-text text-muted">Hanya 4 digit angka
                                                                    (YYYY).</small>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="id_agama">Agama</label>
                                                                <select class="form-control" id="id_agama"
                                                                    name="id_agama" required>
                                                                    <option value="">Pilih Agama</option>
                                                                    <?php
                                                                    // Ambil data agama dari database
                                                                    $data_agama = $db->tampil_data_agama();
                                                                    if (!empty($data_agama)) {
                                                                        foreach ($data_agama as $agama) {
                                                                            echo '<option value="' . htmlspecialchars($agama['id_agama']) . '">' . htmlspecialchars($agama['nama_agama']) . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="no_hp">No. HP</label>
                                                                <input type="text" class="form-control" id="no_hp"
                                                                    name="no_hp" maxlength="15">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="status_siswa">Status Siswa</label>
                                                                <select class="form-control" id="status_siswa"
                                                                    name="status_siswa">
                                                                    <option value="Aktif">Aktif</option>
                                                                    <option value="Lulus">Lulus</option>
                                                                    <option value="Keluar">Keluar</option>
                                                                    <option value="Mutasi">Mutasi</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" type="button"
                                                        data-dismiss="modal">Batal</button>
                                                    <button class="btn btn-primary" type="submit">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="editSiswaModal" tabindex="-1" role="dialog"
                                    aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel">Edit Data Siswa</h5>
                                                <button class="close" type="button" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <form action="edit_siswa_proses.php" method="POST">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                    <label for="nisn">NISN</label>
                                                                <input type="text" class="form-control" id="nisn"
                                                                    name="nisn" readonly>
                                                                <small class="form-text text-muted">NISN tidak dapat
                                                                    diubah</small>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="edit_nama">Nama</label>
                                                                <input type="text" class="form-control" id="edit_nama"
                                                                    name="nama" required maxlength="50">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="edit_jenis_kelamin">Jenis Kelamin</label>
                                                                <select class="form-control" id="edit_jenis_kelamin"
                                                                    name="jenis_kelamin">
                                                                    <option value="">Pilih Jenis Kelamin</option>
                                                                    <option value="L">Laki-laki</option>
                                                                    <option value="P">Perempuan</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="edit_kode_jurusan">Jurusan</label>
                                                                <select class="form-control" id="edit_kode_jurusan"
                                                                    name="kode_jurusan" required>
                                                                    <option value="">Pilih Jurusan</option>
                                                                    <?php
                                                                    // Ambil data jurusan dari database
                                                                    $data_jurusan = $db->tampil_data_jurusan();
                                                                    if (!empty($data_jurusan)) {
                                                                        foreach ($data_jurusan as $jurusan) {
                                                                            echo '<option value="' . htmlspecialchars($jurusan['kode_jurusan']) . '">' . htmlspecialchars($jurusan['nama_jurusan']) . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="edit_kelas">Kelas</label>
                                                                <select class="form-control" id="edit_kelas"
                                                                    name="kelas" required>
                                                                    <option value="">Pilih Kelas</option>
                                                                    <option value="X">X</option>
                                                                    <option value="XI">XI</option>
                                                                    <option value="XII">XII</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="edit_alamat">Alamat</label>
                                                                <input type="text" class="form-control" id="edit_alamat"
                                                                    name="alamat">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="edit_tanggal_lahir">Tanggal Lahir</label>
                                                                <input type="date" class="form-control"
                                                                    id="edit_tanggal_lahir" name="tanggal_lahir">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="edit_tahun_masuk">Tahun Masuk</label>
                                                                <input type="text" class="form-control"
                                                                    id="edit_tahun_masuk" name="tahun_masuk"
                                                                    placeholder="Contoh: 2023" pattern="[0-9]{4}"
                                                                    maxlength="4"
                                                                    title="Tahun harus 4 digit angka (YYYY).">
                                                                <small class="form-text text-muted">Hanya 4 digit angka
                                                                    (YYYY).</small>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="edit_id_agama">Agama</label>
                                                                <select class="form-control" id="edit_id_agama"
                                                                    name="id_agama" required>
                                                                    <option value="">Pilih Agama</option>
                                                                    <?php
                                                                    // Ambil data agama dari database
                                                                    $data_agama = $db->tampil_data_agama();
                                                                    if (!empty($data_agama)) {
                                                                        foreach ($data_agama as $agama) {
                                                                            echo '<option value="' . htmlspecialchars($agama['id_agama']) . '">' . htmlspecialchars($agama['nama_agama']) . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="edit_no_hp">No. HP</label>
                                                                <input type="text" class="form-control" id="edit_no_hp"
                                                                    name="no_hp" maxlength="15">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="edit_status_siswa">Status Siswa</label>
                                                                <select class="form-control" id="edit_status_siswa"
                                                                    name="status_siswa">
                                                                    <option value="Aktif">Aktif</option>
                                                                    <option value="Lulus">Lulus</option>
                                                                    <option value="Keluar">Keluar</option>
                                                                    <option value="Mutasi">Mutasi</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" type="button"
                                                        data-dismiss="modal">Batal</button>
                                                    <button class="btn btn-primary" type="submit">Simpan
                                                        Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    </div>
    <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- Footer -->
    <footer class="sticky-footer bg-white">
        <div class="container my-auto">
            <div class="copyright text-center my-auto">
                <span>Copyright &copy; Your Website 2020</span>
            </div>
        </div>
    </footer>
    <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Cek apakah ada parameter status dan pesan di URL
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const pesan = urlParams.get('pesan');

            if (status && pesan) {
                if (status === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: decodeURIComponent(pesan),
                        confirmButtonColor: '#3085d6'
                    });
                } else if (status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        html: decodeURIComponent(pesan),
                        confirmButtonColor: '#3085d6'
                    });
                }
                // Hapus parameter dari URL tanpa reload
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });

        // Optional: Tambahkan validasi real-time untuk NISN
        function checkNISN(input) {
            const nisn = input.value;
            if (nisn.length === 5) { // Hanya cek jika panjang NISN sudah 5 digit
                fetch('check_nisn.php?nisn=' + nisn)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'NISN Sudah Terdaftar',
                                text: 'NISN ' + nisn + ' sudah digunakan oleh siswa lain.',
                                confirmButtonColor: '#3085d6'
                            });
                            input.value = ''; // Kosongkan input
                        }
                    });
            }
        }
    </script>

    <script>
        $(document).ready(function () {
            // Debug: Tambahkan logging untuk modal edit
            $('.edit-siswa-btn').on('click', function () {
                console.log('Edit button clicked');
                console.log('NISN:', $(this).data('nisn'));
                console.log('All data attributes:', $(this).data());
            });

            // Skrip untuk mengisi data ke Modal Edit Siswa
            $('#editSiswaModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Tombol yang memicu modal
                console.log('Modal opening with button data:', button.data());

                // Ambil data dari atribut data-* tombol
                var nisn = button.data('nisn');
                var nama = button.data('nama');
                var jenis_kelamin = button.data('jenis_kelamin');
                var kode_jurusan = button.data('kode_jurusan');
                var kelas = button.data('kelas');
                var alamat = button.data('alamat');
                var tanggal_lahir = button.data('tanggal_lahir');
                var tahun_masuk = button.data('tahun_masuk');
                var id_agama = button.data('id_agama');
                var no_hp = button.data('no_hp');
                var status_siswa = button.data('status_siswa');

                // Debug: Log data yang akan dimasukkan ke form
                console.log('Setting form values:');
                console.log('NISN:', nisn);
                console.log('Nama:', nama);
                console.log('Kode Jurusan:', kode_jurusan);
                console.log('ID Agama:', id_agama);

                // Isi data ke dalam field form modal
                var modal = $(this);
                modal.find('#edit_nisn').val(nisn);
                modal.find('#edit_nama').val(nama);
                modal.find('#edit_jenis_kelamin').val(jenis_kelamin);
                modal.find('#edit_kode_jurusan').val(kode_jurusan);
                modal.find('#edit_kelas').val(kelas);
                modal.find('#edit_alamat').val(alamat);
                modal.find('#edit_tanggal_lahir').val(tanggal_lahir);
                modal.find('#edit_tahun_masuk').val(tahun_masuk);
                modal.find('#edit_id_agama').val(id_agama);
                modal.find('#edit_no_hp').val(no_hp);
                modal.find('#edit_status_siswa').val(status_siswa);

                // Debug: Verify values are set
                console.log('Form values after setting:');
                console.log('NISN field value:', modal.find('#edit_nisn').val());
                console.log('Nama field value:', modal.find('#edit_nama').val());
            });
        });
    </script>
    <script>
$(document).ready(function() {
    $('.edit-siswa-btn').on('click', function() {
        var nisn = $(this).data('nisn');
        console.log('NISN yang akan diedit:', nisn); // Debug log
        
        // Reset form dan pesan error sebelumnya
        $('#editSiswaModal form')[0].reset();
        
        // Ambil data siswa dengan AJAX
        $.ajax({
            url: 'get_siswa.php',
            type: 'GET',
            data: { nisn: nisn },
            dataType: 'json',
            success: function(data) {
                console.log('Data yang diterima:', data); // Debug log
                
                // Isi form dengan data yang diterima
                $('#editSiswaModal #nisn').val(data.nisn);
                $('#editSiswaModal #edit_nama').val(data.nama);
                $('#editSiswaModal #edit_jenis_kelamin').val(data.jenis_kelamin);
                $('#editSiswaModal #edit_kode_jurusan').val(data.kode_jurusan);
                $('#editSiswaModal #edit_kelas').val(data.kelas);
                $('#editSiswaModal #edit_alamat').val(data.alamat);
                $('#editSiswaModal #edit_tanggal_lahir').val(data.tanggal_lahir);
                $('#editSiswaModal #edit_tahun_masuk').val(data.tahun_masuk);
                $('#editSiswaModal #edit_id_agama').val(data.id_agama);
                $('#editSiswaModal #edit_no_hp').val(data.no_hp);
                $('#editSiswaModal #edit_status_siswa').val(data.status_siswa);
                
                // Tampilkan modal
                $('#editSiswaModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data siswa'
                });
            }
        });
    });
});
</script>
<script>
function confirmDelete(nisn, nama) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Yakin ingin menghapus data siswa ${nama}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `hapus_siswa_proses.php?nisn=${nisn}`;
        }
    });
    return false;
}
</script>
</body>

</html>