// C:\laragon\www\sidesi\js\demo\datatables-agama-demo.js

$(document).ready(function() {
  // Cegah re-inisialisasi DataTables
  if (!$.fn.DataTable.isDataTable('#dataTable')) {
    $('#dataTable').DataTable({
      "scrollX": true // Sesuaikan jika tabel Anda sangat lebar
    });
  }

  // Script untuk mengisi data ke Modal Edit Agama
  $('#editAgamaModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Tombol yang memicu modal
      // Ambil data dari atribut data-* tombol
      var id_agama = button.data('id_agama');
      var nama_agama = button.data('nama_agama');

      // Isi data ke dalam field form modal
      var modal = $(this);
      // Isi input hidden yang akan dikirim ke PHP
      modal.find('#edit_id_agama_hidden').val(id_agama);
      // Isi nama agama
      modal.find('#edit_nama_agama').val(nama_agama);

      // Anda bisa menambahkan console.log untuk debugging
      console.log('ID Agama dari tombol:', id_agama);
      console.log('Nama Agama dari tombol:', nama_agama);
      console.log('Nilai input hidden setelah diisi:', modal.find('#edit_id_agama_hidden').val());
  });
});