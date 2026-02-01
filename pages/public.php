<?php 
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../config/database.php";
secureSessionStart();
$total_laporan = Database::fetchAll("SELECT COUNT(*) AS total_laporan, SUM(status = 'selesai') AS total_selesai, SUM(status IN ('masuk','diverifikasi','diproses')) AS total_dalam_proses, SUM(status = 'ditolak') AS total_ditolak FROM pengaduan;")[0];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SIPLIK — Sistem Informasi Pengaduan Lingkungan Kota</title>
  <meta name="description" content="SIPLIK membantu warga melaporkan dan memantau masalah lingkungan kota seperti sampah, banjir, polusi udara, dan penghijauan." />
  <link rel="stylesheet" href="styles/style.css">
  <link rel="stylesheet" href="styles/public.css">  
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
  <?php require_once $navbar; ?>

  <main>
    <h1 style="line-height: 1em; text-align: center;">INFORMASI PUBLIK</h1>
    <br>

    <div class="info-cards">
      <div class="info-card info-primary">
        <div class="info-title">Total Laporan</div>
        <div class="info-value"><?= $total_laporan["total_laporan"] ?></div>
      </div>

      <div class="info-card info-success">
        <div class="info-title">Total Laporan yang Selesai</div>
        <div class="info-value"><?= $total_laporan["total_selesai"] ?></div>
      </div>

      <div class="info-card info-warning">
        <div class="info-title">Total Laporan dalam Proses</div>
        <div class="info-value"><?= $total_laporan["total_dalam_proses"] ?></div>
      </div>

      <div class="info-card info-danger">
        <div class="info-title">Total Laporan yang Ditolak</div>
        <div class="info-value"><?= $total_laporan["total_ditolak"] ?></div>
      </div>
    </div>

    <br>
    <h3 class="chart-title">Laporan yang masuk setiap Minggu</h3>
    <canvas id="chartLaporanMingguan" height="20" width="100%"></canvas>
    <br>

    <br>
    <h3 class="chart-title">Laporan yang selesai setiap Minggu</h3>
    <canvas id="chartLaporanSelesaiMingguan" height="20" width="100%"></canvas>
    <br>    

    <br>
    <h3 class="chart-title">Laporan yang masuk setiap Bulan</h3>
    <canvas id="chartLaporanBulanan" height="20" width="100%"></canvas>
    <br>

    <br>
    <h3 class="chart-title">Laporan yang selesai setiap Bulan</h3>
    <canvas id="chartLaporanSelesaiBulanan" height="20" width="100%"></canvas>
    <br>

    <!-- <div class="wrapper_pie">
      <canvas id="chartLaporanStatus"></canvas>
    </div>
    
    <div class="wrapper_doughnut">
      <canvas id="chartTipeLaporan"></canvas>
    </div> -->

    <div class="dashboard-grid">
      <div class="chart-card">
        <canvas id="chartLaporanStatus"></canvas>
      </div>
      <div class="chart-card">
        <canvas id="chartTipeLaporan"></canvas>
      </div>
    </div>

    <br>
    <h3 class="chart-title">Jumlah Laporan berdasarkan Kecamatan</h3>
    <canvas id="chartPengaduanKecamatan" height="20" width="100%"></canvas>
    <br>

    <br>
    <h3 class="chart-title">Jumlah Laporan berdasarkan Kelurahan</h3>
    <canvas id="chartPengaduanKelurahan" height="20" width="100%"></canvas>
    <br>    


  </main>


  <?php 
    require_once $modal;
    require_once $footer;
    require_once $loader;
  ?>

</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript" src="scripts/utils.js"></script>
<script type="text/javascript" src="scripts/script.js"></script>
<script type="text/javascript" src="scripts/public.js"></script>
</html>
