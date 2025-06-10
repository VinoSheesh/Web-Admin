// C:\laragon\www\sidesi\js\demo\datatables-jurusan-demo.js

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

      // Anda bisa menambahkan console.log untuk debugging
      console.log('Kode Jurusan dari tombol:', kode_jurusan);
      console.log('Nilai input hidden setelah diisi:', modal.find('#edit_kode_jurusan_hidden').val());
  });
});