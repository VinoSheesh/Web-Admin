// C:\laragon\www\sidesi\js\demo\chart-pie-jurusan-demo.js

// Pengaturan default font untuk Chart.js (dari template SB Admin 2)
(Chart.defaults.global.defaultFontFamily = "Nunito"),
  '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = "#858796";

// --- Ambil dan Siapkan Data untuk Jurusan Chart ---
// chartDataJurusan adalah variabel global yang disuntikkan dari PHP di index.php
var labelsJurusan = chartDataJurusan.map(function (item) {
  return item.nama_jurusan;
});
var dataValuesJurusan = chartDataJurusan.map(function (item) {
  // Pastikan ini adalah angka
  return parseInt(item.jumlah_siswa); // atau parseFloat jika ada desimal
});

// Fungsi untuk menghasilkan warna acak yang berbeda jika Anda punya banyak jurusan
// Atau Anda bisa menggunakan array warna tetap yang sudah didefinisikan.
var fixedColors = [
  "#4e73df", // Primary (biru)
  "#1cc88a", // Success (hijau)
  "#36b9cc", // Info (biru muda)
  "#f6c23e", // Warning (kuning)
  "#e74a3b", // Danger (merah)
  "#858796", // Secondary (abu-abu)
  "#5a5c69", // Dark (hitam)
  "#B22222", // FireBrick
  "#DAA520", // Goldenrod
  "#6A5ACD", // SlateBlue
];

// Pilih warna sesuai jumlah jurusan yang ada
var backgroundColors = fixedColors.slice(0, labelsJurusan.length);
if (labelsJurusan.length > fixedColors.length) {
  // Jika jumlah jurusan lebih banyak dari warna yang disiapkan, gunakan warna dinamis
  function dynamicColors(num) {
    var colors = [];
    for (var i = 0; i < num; i++) {
      var r = Math.floor(Math.random() * 200); // Batasi range agar tidak terlalu cerah
      var g = Math.floor(Math.random() * 200);
      var b = Math.floor(Math.random() * 200);
      colors.push("rgba(" + r + "," + g + "," + b + ",0.8)");
    }
    return colors;
  }
  backgroundColors = dynamicColors(labelsJurusan.length);
}

// --- Inisialisasi Chart Jurusan (Doughnut Chart) ---
var ctx = document.getElementById("myPieChart");
var myPieChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: labelsJurusan,
    datasets: [{
      data: dataValuesJurusan, // Ini harus berisi angka
      backgroundColor: backgroundColors,
      hoverBackgroundColor: backgroundColors.map(color => color.replace('0.8)', '1)')),
      hoverBorderColor: "rgba(234, 236, 244, 1)",
    }],
  },
  options: {
    maintainAspectRatio: false,
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem, data) {
          var label = data.labels[tooltipItem.index];
          var value = data.datasets[0].data[tooltipItem.index]; // Ini adalah angka
          var total = data.datasets[0].data.reduce((a, b) => a + b, 0); // Penjumlahan angka

          var percentage = (total > 0) ? ((value / total) * 100).toFixed(1) + '%' : '0.0%';

          return label + ': ' + value + ' Siswa (' + percentage + ')';
        }
      }
    },
    legend: {
      display: false
    },
    cutoutPercentage: 80,
  },
});


// --- Membuat Legend Manual di HTML ---
// Karena legend bawaan Chart.js kadang kurang fleksibel, kita buat sendiri
var legendHtml = "";
labelsJurusan.forEach(function (label, index) {
  var color = backgroundColors[index];
  legendHtml +=
    '<span class="mr-2"><i class="fas fa-circle" style="color: ' +
    color +
    ';"></i> ' +
    label +
    "</span>";
});
// Masukkan legend ke elemen HTML dengan ID "legendJurusan"
document.getElementById("legendJurusan").innerHTML = legendHtml;
