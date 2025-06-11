<?php
session_start(); // Pastikan ini baris pertama

// Cek apakah user sudah login DAN memiliki role 'administrator'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrator') {
    header("Location: index.php?status=error&pesan=" . urlencode("Anda tidak memiliki izin untuk mengakses halaman ini."));
    exit();
}

include 'koneksi.php';
$db = new database();

$data_users = $db->get_all_users();

$status_message = '';
$message_class = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $status_message = $_GET['pesan'] ?? 'Operasi berhasil.';
        $message_class = 'alert-success';
    } elseif ($_GET['status'] == 'error') {
        $status_message = $_GET['pesan'] ?? 'Terjadi kesalahan.';
        $message_class = 'alert-danger';
    }
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

                    <h1 class="h3 mb-2 text-gray-800">Manajemen Pengguna</h1>
                    <p class="mb-4">Halaman ini berisi daftar semua pengguna terdaftar dan informasi terkait.</p>

                    <?php if ($status_message): ?>
                        <div class="alert <?php echo $message_class; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($status_message); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengguna
                                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal"
                                    data-target="#tambahUserModal">
                                    + Tambah Pengguna
                                </button>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr> 
                                            <th>No</th>
                                            <th>Username</th>
                                            <th>Nama Lengkap</th>
                                            <th>Role</th>
                                            <th>Terdaftar Sejak</th>
                                            <th>Password</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1; // Initialize counter
                                        if (!empty($data_users)) {
                                            foreach ($data_users as $user) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $no++; ?></td>
                                                    <td><?php echo htmlspecialchars($user['username'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($user['full_name'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($user['role'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($user['created_at'] ?? ''); ?></td>
                                                    <td>******</td>
                                                    <td>
                                                        <a href="#" class="btn btn-warning btn-sm edit-user-btn"
                                                            data-toggle="modal" 
                                                            data-target="#editUserModal"
                                                            data-id="<?php echo htmlspecialchars($user['id'] ?? ''); ?>"
                                                            data-username="<?php echo htmlspecialchars($user['username'] ?? ''); ?>"
                                                            data-full_name="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>"
                                                            data-role="<?php echo htmlspecialchars($user['role'] ?? ''); ?>">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a href="hapus_user_proses.php?id=<?php echo htmlspecialchars($user['id'] ?? ''); ?>"
                                                            class="btn btn-danger btn-sm delete-user"
                                                            data-username="<?php echo htmlspecialchars($user['username'] ?? ''); ?>"
                                                            data-fullname="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="tambahUserModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Tambah Pengguna Baru</h5>
                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <form action="tambah_user_proses.php" method="POST">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm_password">Ulangi Password</label>
                                            <input type="password" class="form-control" id="confirm_password"
                                                name="confirm_password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="full_name">Nama Lengkap</label>
                                            <input type="text" class="form-control" id="full_name" name="full_name"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="role">Role</label>
                                            <select class="form-control" id="role" name="role" required>
                                                <option value="pengamat">Pengamat</option>
                                                <option value="administrator">Administrator</option>
                                            </select>
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

                    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Pengguna</h5>
                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <form action="edit_user_proses.php" method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" id="edit_user_id_hidden" name="id">
                                        <div class="form-group">
                                            <label for="edit_username">Username</label>
                                            <input type="text" class="form-control" id="edit_username" name="username"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_full_name">Nama Lengkap</label>
                                            <input type="text" class="form-control" id="edit_full_name" name="full_name"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_role">Role</label>
                                            <select class="form-control" id="edit_role" name="role" required>
                                                <option value="pengamat">Pengamat</option>
                                                <option value="administrator">Administrator</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" type="button"
                                            data-dismiss="modal">Batal</button>
                                        <button class="btn btn-primary" type="submit">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

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
        <script>// C:\laragon\www\sidesi\js\demo\datatables-users-demo.js (atau tambahkan ke datatables-demo.js)

            $(document).ready(function () {
                // Inisialisasi DataTables untuk tabel pengguna
                if (!$.fn.DataTable.isDataTable('#dataTable')) {
                    $('#dataTable').DataTable({
                        "scrollX": true
                    });
                }

                // Script untuk mengisi data ke Modal Edit User
                $('#editUserModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget); // Tombol yang mengklik modal
                    var id = button.data('id');
                    var username = button.data('username');
                    var full_name = button.data('full_name');
                    var role = button.data('role');

                    var modal = $(this);
                    modal.find('#edit_user_id_hidden').val(id);
                    modal.find('#edit_username').val(username);
                    modal.find('#edit_full_name').val(full_name);
                    modal.find('#edit_role').val(role);
                });
            });</script>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        $(document).ready(function() {
            // Handle tambah user form submit
            $('#tambahUserModal form').on('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Tambah',
                    text: 'Yakin ingin menambah pengguna baru?',
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

            // Handle edit user form submit
            $('#editUserModal form').on('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    text: 'Yakin ingin menyimpan perubahan data pengguna?',
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

            // Handle delete user
            $('.delete-user').on('click', function(e) {
                e.preventDefault();
                const href = $(this).attr('href');
                const username = $(this).data('username');
                const fullname = $(this).data('fullname');

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Yakin ingin menghapus pengguna ${fullname} (${username})?`,
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

            // Display SweetAlert for success/error messages from URL
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
                
                // Remove parameters from URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
        </script>
</body>

</html>