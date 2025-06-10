// C:\laragon\www\sidesi\js\demo\chart-area-siswa-demo.js

// Pengaturan default font untuk Chart.js (dari template SB Admin 2)
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Fungsi format angka (biasanya sudah ada di file demo SB Admin 2)
function number_format(number, decimals, dec_point, thousands_sep) {
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

// --- Ambil dan Siapkan Data untuk Siswa Chart ---
// chartDataTahunMasuk adalah variabel global yang disuntikkan dari PHP di index.php
var labelsTahunMasuk = chartDataTahunMasuk.map(function(item) {
    return item.tahun_masuk; // Ambil tahun masuk sebagai label
});
var dataValuesTahunMasuk = chartDataTahunMasuk.map(function(item) {
    return item.jumlah_siswa; // Ambil jumlah siswa sebagai nilai data
});

// --- Inisialisasi Chart Siswa (Area/Line Chart) ---
var ctxArea = document.getElementById("myAreaChart"); // Dapatkan elemen canvas
var myAreaChart = new Chart(ctxArea, {
  type: 'line', // Tipe grafik garis (bisa diganti 'bar' untuk batang)
  data: {
    labels: labelsTahunMasuk, // Label sumbu X (tahun masuk)
    datasets: [{
      label: "Jumlah Siswa", // Label untuk dataset ini
      lineTension: 0.3, // Kehalusan garis
      backgroundColor: "rgba(78, 115, 223, 0.05)", // Warna area di bawah garis
      borderColor: "rgba(78, 115, 223, 1)", // Warna garis
      pointRadius: 3, // Ukuran titik
      pointBackgroundColor: "rgba(78, 115, 223, 1)",
      pointBorderColor: "rgba(78, 115, 223, 1)",
      pointHoverRadius: 3,
      pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
      pointHoverBorderColor: "rgba(78, 115, 223, 1)",
      pointHitRadius: 10,
      pointBorderWidth: 2,
      data: dataValuesTahunMasuk, // Data untuk sumbu Y (jumlah siswa)
    }],
  },
  options: {
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        time: {
          unit: 'year' // Unit waktu pada sumbu X
        },
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          maxTicksLimit: 7
        }
      }],
      yAxes: [{
        ticks: {
          min: 0, // Mulai dari 0
          maxTicksLimit: 5,
          padding: 10,
          callback: function(value, index, values) {
            return number_format(value) + ' Siswa'; // Format angka dan tambahkan 'Siswa'
          }
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: {
      display: false // Tidak perlu legend jika hanya satu dataset
    },
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      intersect: false,
      mode: 'index',
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + ': ' + number_format(tooltipItem.yLabel) + ' Siswa';
        }
      }
    }
  }
});