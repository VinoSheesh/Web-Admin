<?php
session_start();

include_once 'koneksi.php';
$db = new database();

// Ambil semua data agama
$data_agama = $db->get_all_agama();

// Pesan status
$status_pesan = '';
$alert_class = '';
if (isset($_GET['status']) && isset($_GET['pesan'])) {
    $status = htmlspecialchars($_GET['status']);
    $pesan = htmlspecialchars($_GET['pesan']);
    if ($status == 'success') {
        $alert_class = 'alert-success';
    } else {
        $alert_class = 'alert-danger';
    }
    $status_pesan = $pesan;
}

// Tambahkan nomor urut pada data agama
if (!empty($data_agama)) {
    $no = 1;
    foreach ($data_agama as $key => $agama) {
        $data_agama[$key]['no'] = $no++;
    }
}

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
                    <h1 class="h3 mb-2 text-gray-800">Data Agama</h1>

                    <?php if ($status_pesan): ?>
                        <div class="alert <?= $alert_class ?> alert-dismissible fade show" role="alert">
                            <?= $status_pesan ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Agama
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'administrator'): ?>
                                    <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal"
                                        data-target="#tambahAgamaModal">
                                        + Tambah Agama
                                    </button>
                                <?php endif; ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Agama</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($data_agama)) {
                                            foreach ($data_agama as $agama) {
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($agama['no'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($agama['nama_agama'] ?? ''); ?></td>
                                                    <td>
                                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'administrator'): ?>
                                                            <a href="#" class="btn btn-warning btn-sm edit-agama-btn"
                                                                data-toggle="modal" data-target="#editAgamaModal"
                                                                data-id_agama="<?php echo htmlspecialchars($agama['id_agama'] ?? ''); ?>"
                                                                data-nama_agama="<?php echo htmlspecialchars($agama['nama_agama'] ?? ''); ?>">
                                                                Edit
                                                            </a>
                                                            <a href="hapus_agama_proses.php?id_agama=<?php echo htmlspecialchars($agama['id_agama'] ?? ''); ?>"
                                                               class="btn btn-danger btn-sm delete-agama"
                                                               data-nama-agama="<?php echo htmlspecialchars($agama['nama_agama'] ?? ''); ?>">
                                                                Hapus
                                                            </a>
                                                        <?php else: ?>
                                                            <span>-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='3' class='text-center'>Tidak ada data agama.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="tambahAgamaModal" tabindex="-1" role="dialog"
                    aria-labelledby="tambahModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="tambahModalLabel">Tambah Data Agama</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <form action="tambah_agama_proses.php" method="POST">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="nama_agama">Nama Agama</label>
                                        <input type="text" class="form-control" id="nama_agama" name="nama_agama"
                                            required maxlength="50">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                    <button class="btn btn-primary" type="submit">Tambah Agama</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="editAgamaModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Data Agama</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <form action="edit_agama_proses.php" method="POST">
                                <div class="modal-body">
                                    <input type="hidden" id="edit_id_agama_hidden" name="id_agama">

                                    <div class="form-group">
                                        <label for="edit_nama_agama">Nama Agama</label>
                                        <input type="text" class="form-control" id="edit_nama_agama" name="nama_agama"
                                            required maxlength="50">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                    <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                                </div>
                            </form>
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

    <!-- PERBAIKAN: Include script khusus untuk jurusan -->
    <script src="js/demo/datatables-agama-demo.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Handling form submit untuk tambah agama
        document.querySelector('#tambahAgamaModal form').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Tambah',
                text: 'Yakin ingin menambah data agama ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tambah',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // Handling form submit untuk edit agama
        document.querySelector('#editAgamaModal form').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: 'Yakin ingin menyimpan perubahan data agama ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // Ganti confirm standar dengan SweetAlert untuk hapus
        const deleteLinks = document.querySelectorAll('a[href^="hapus_agama_proses.php"]');
        deleteLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const agamaName = this.getAttribute('data-nama-agama');
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Yakin ingin menghapus agama ${agamaName}? Perhatian: Ini mungkin akan mempengaruhi data siswa yang terkait!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = this.href;
                    }
                });
            });
        });

        // Tampilkan SweetAlert untuk pesan sukses/error dari URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const pesan = urlParams.get('pesan');

            if (status && pesan) {
                let icon = 'success';
                if (status === 'error') {
                    icon = 'error';
                }
                
                Swal.fire({
                    icon: icon,
                    title: status === 'success' ? 'Berhasil!' : 'Gagal!',
                    text: decodeURIComponent(pesan),
                    timer: 3000,
                    showConfirmButton: false
                });

                // Hapus parameter dari URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>



</html></body></body>

</html>