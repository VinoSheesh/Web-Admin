<?php
session_start();

include 'koneksi.php';

$db = new database();

$data_jurusan = $db->tampil_data_jurusan();

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

    <title>Data Jurusan - SMKN 6 Surakarta</title>

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
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Data Jurusan SMKN 6 Surakarta</h1>
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
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Jurusan
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'administrator'): ?>
                                    <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal"
                                        data-target="#tambahJurusanModal">
                                        + Tambah Jurusan
                                    </button>
                                <?php endif; ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
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

                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Jurusan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Loop untuk menampilkan data jurusan dari database
                                        if (!empty($data_jurusan)) { // Cek apakah ada data jurusan
                                            $no = 1; // Inisialisasi nomor urut
                                            foreach ($data_jurusan as $jurusan) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $no++; ?></td>
                                                    <td><?php echo htmlspecialchars($jurusan['nama_jurusan'] ?? ''); ?></td>
                                                    <td>
                                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'administrator'): ?>
                                                            <a href="#" class="btn btn-warning btn-sm edit-jurusan-btn"
                                                                data-toggle="modal" data-target="#editJurusanModal"
                                                                data-kode_jurusan="<?php echo htmlspecialchars($jurusan['kode_jurusan'] ?? ''); ?>"
                                                                data-nama_jurusan="<?php echo htmlspecialchars($jurusan['nama_jurusan'] ?? ''); ?>">
                                                                Edit
                                                            </a>
                                                            <a href="hapus_jurusan_proses.php?kode_jurusan=<?php echo htmlspecialchars($jurusan['kode_jurusan'] ?? ''); ?>"
                                                                class="btn btn-danger btn-sm delete-jurusan"
                                                                data-nama-jurusan="<?php echo htmlspecialchars($jurusan['nama_jurusan'] ?? ''); ?>">
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
                                            // Tampilkan pesan jika tidak ada data
                                            echo '<tr><td colspan="3" class="text-center">Tidak ada data jurusan.</td></tr>';
                                        }
                                        ?>
                                    </tbody>

                                </table>

                                <!-- Modal Tambah Jurusan -->
                                <div class="modal fade" id="tambahJurusanModal" tabindex="-1" role="dialog"
                                    aria-labelledby="tambahModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="tambahModalLabel">Tambah Data Jurusan</h5>
                                                <button class="close" type="button" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <form action="tambah_jurusan_proses.php" method="POST">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="nama_jurusan">Nama Jurusan</label>
                                                        <input type="text" class="form-control" id="nama_jurusan"
                                                            name="nama_jurusan" required maxlength="50">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" type="button"
                                                        data-dismiss="modal">Batal</button>
                                                    <button class="btn btn-primary" type="submit">Tambah
                                                        Jurusan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Edit Jurusan -->
                                <div class="modal fade" id="editJurusanModal" tabindex="-1" role="dialog"
                                    aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel">Edit Data Jurusan</h5>
                                                <button class="close" type="button" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <form action="edit_jurusan_proses.php" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" id="edit_kode_jurusan_hidden"
                                                        name="kode_jurusan">

                                                    <div class="form-group">
                                                        <label for="edit_nama_jurusan">Nama Jurusan</label>
                                                        <input type="text" class="form-control" id="edit_nama_jurusan"
                                                            name="nama_jurusan" required maxlength="50">
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "scrollX": true
            });

            // Script untuk mengisi data ke Modal Edit Jurusan
            $('#editJurusanModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Tombol yang memicu modal
                var kode_jurusan = button.data('kode_jurusan'); // Ambil nilai kode_jurusan dari atribut data-
                var nama_jurusan = button.data('nama_jurusan'); // Ambil nilai nama_jurusan

                var modal = $(this);
                // Ini baris yang paling penting: mengisi nilai kode_jurusan ke input hidden
                modal.find('#edit_kode_jurusan_hidden').val(kode_jurusan);
                // Ini mengisi nama jurusan ke input text
                modal.find('#edit_nama_jurusan').val(nama_jurusan);

                // Debugging - bisa dihapus setelah yakin berfungsi
                console.log('Kode Jurusan dari tombol:', kode_jurusan);
                console.log('Nilai input hidden setelah diisi:', modal.find('#edit_kode_jurusan_hidden').val());
            });

            // Handle form tambah jurusan
            $('#tambahJurusanModal form').on('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Tambah',
                    text: 'Yakin ingin menambah jurusan baru?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Tambah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });

            // Handle form edit jurusan
            $('#editJurusanModal form').on('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    text: 'Yakin ingin menyimpan perubahan?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });

            // Hapus handler onclick yang lama
            $('.delete-jurusan').off('click').on('click', function(e) {
                e.preventDefault();
                const href = $(this).attr('href');
                const namaJurusan = $(this).data('nama-jurusan');

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Yakin ingin menghapus jurusan ${namaJurusan}? Data yang terkait dengan jurusan ini juga akan terpengaruh.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            });

            // Tampilkan SweetAlert untuk pesan sukses/error dari URL
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const pesan = urlParams.get('pesan');

            if (status && pesan) {
                let icon = status === 'success' ? 'success' : 'error';
                Swal.fire({
                    icon: icon,
                    title: status === 'success' ? 'Berhasil!' : 'Gagal!',
                    text: decodeURIComponent(pesan),
                    showConfirmButton: false,
                    timer: 2000
                });
                
                // Hapus parameter dari URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
</body>

</html>