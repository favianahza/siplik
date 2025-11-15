<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../config/database.php";
secureSessionStart();
$sqlMasuk = "SELECT COUNT(ticket_code) AS num FROM pengaduan WHERE DATE(created_at) = CURDATE() AND (status = 'masuk' OR status = 'diproses')";
$sqlSelesai = "SELECT COUNT(ticket_code) AS num FROM pengaduan WHERE (DATE(created_at) = CURDATE() OR DATE(updated_at) = CURDATE()) AND status = 'selesai'";
$sqlUniqueEmail = "SELECT COUNT(DISTINCT email_pelapor) AS unique_emails FROM pengaduan WHERE DATE(created_at) = CURDATE() AND (is_anonim = 0 OR is_anonim IS NULL) AND email_pelapor IS NOT NULL AND email_pelapor != ''";
$sqlLatest = "  SELECT * FROM pengaduan ORDER BY id DESC LIMIT 1";
$stats["today_incoming"] = Database::fetch($sqlMasuk)["num"] ?? 0;
$stats['today_resolved'] = Database::fetch($sqlSelesai)["num"] ?? 0;
$stats["today_unique"] = Database::fetch($sqlUniqueEmail)["unique_emails"] ?? 0;
$stats["latest"] = Database::run("SELECT * FROM view_pengaduan_detailed ORDER BY id DESC LIMIT 1")->fetch();
?>
<section class="content-header py-3 border-bottom">
  <div class="container-fluid d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
    <h1 class="m-0 fw-bold">Dashboard Pengaduan Lingkungan DLHK</h1>
    <div class="text-muted small text-md-end">
      <div>Anda masuk sebagai <strong><?php echo htmlspecialchars($_SESSION["user_role"] ?? "—"); ?></strong></div>
      <?php date_default_timezone_set('Asia/Jakarta'); $now = new DateTime('now'); ?>
      <div><?php echo $now->format('d/m/Y H:i'); ?> WIB</div>
    </div>
  </div>
</section>

<section class="content py-4">
  <div class="container-fluid">
    <div class="row g-3">

      <!-- Card: Laporan Masuk Hari Ini -->
      <div class="col-12 col-sm-6 col-lg-4">
        <div class="card shadow-sm h-100 border-0">
          <div class="card-body d-flex align-items-center align-content-center">
            <div class="me-3">
              <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10"
                  style="width:3.2rem;height:3.2rem; margin: .75rem;">
              <i class="fa-solid fa-inbox"></i>
              </span>
            </div>
            <div>
              <div class="text-muted">  Laporan Masuk hari Ini</div>
              <div class="h3 m-0 fw-bold"><?php echo (int)($stats['today_incoming'] ?? 0); ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: Laporan Selesai Hari Ini -->
      <div class="col-12 col-sm-6 col-lg-4">
        <div class="card shadow-sm h-100 border-0">
          <div class="card-body d-flex align-items-center">
            <div class="me-3">
              <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10"
                  style="width:3.2rem;height:3.2rem; margin: .75rem;">
              <i class="fa-solid fa-circle-check"></i>
              </span>
            </div>
            <div>
              <div class="text-muted">Laporan Selesai Hari Ini</div>
              <div class="h3 m-0 fw-bold"><?php echo (int)($stats['today_resolved'] ?? 0); ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: Jumlah Pelapor Unik -->
      <div class="col-12 col-sm-6 col-lg-4">
        <div class="card shadow-sm h-100 border-0">
          <div class="card-body d-flex align-items-center">
            <div class="me-3">
              <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-info bg-opacity-10"
                  style="width:3.2rem;height:3.2rem; margin: .75rem;">
              <i class="fa-solid fa-users"></i>
              </span>
            </div>
            <div>
              <div class="text-muted">Jumlah Pelapor Baru Hari Ini</div>
              <div class="h3 m-0 fw-bold"><?php echo (int)($stats['today_unique'] ?? 0); ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Additional Content Below Cards -->
    <div class="row mt-4">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white border-bottom-0 fw-bold">
            <h3>Ringkasan Aktivitas</h3>
          </div>
          <div class="card-body pt-0">
            <p class="text-muted mb-0">
              Sistem pengaduan ini membantu Dinas Lingkungan Hidup dan Kebersihan (DLHK) dalam memantau,
              menindaklanjuti, dan menyelesaikan laporan masyarakat terkait permasalahan lingkungan.
            </p><br>
            <p>Kode tiket pengaduan terkini: <b><?= $stats["latest"]["ticket_code"]; ?></b></p>
            <ul class="text-muted">
                <li>Kecamatan: <b><?= $stats["latest"]["nama_kecamatan"];  ?></b></li>
                <li>Kelurahan: <b><?= $stats["latest"]["nama_kelurahan"];  ?></b></li>
                <li>Lokasi: <b><?= $stats["latest"]["alamat_lengkap"];  ?></b></li>
                <li>Kategori: <b><?= $stats["latest"]["nama_kategori"];  ?></b></li>
                <li>Petugas yang menangani: <b><?= $stats["latest"]["assigned_to"] ?? "Belum ada"; ?></b></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white border-bottom-0 fw-bold">
            <h3>Tips Penggunaan</h3>
          </div>
          <div class="card-body small text-muted pt-0">
            <ul class="mb-0 ps-3">
              <li>Gunakan menu <strong>Laporan</strong> untuk memantau laporan masuk, laporan yang sedang diproses dan arsip laporan.</li>
              <li>Mengalokasikan petugas lapangan dapat dilakukan melalui menu <strong>Penugasan</strong></li>
              <li>Periksa tren dan statistik laporan melalui menu <strong>Analitik</strong></li>
              <li>Buka <strong>Analitik</strong> untuk memantau statistik dan tren laporan.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
