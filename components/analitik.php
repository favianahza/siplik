<div class="row" id="row-statistik-laporan">

  <!-- Total laporan masuk hari ini -->
  <div class="col-md-3 col-sm-6 mb-3">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body d-flex flex-column justify-content-between">
        <div>
          <div class="text-muted small"><h5><i class="fas fa-inbox text-primary"></i>  Laporan Masuk Hari Ini</h5></div>
          <div class="h3 fw-bold mb-0" id="stat-masuk-hari-ini">0</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Total laporan aktif -->
  <div class="col-md-3 col-sm-6 mb-3">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body d-flex flex-column justify-content-between">
        <div>
          <div class="text-muted small"></div>
          <div class="text-muted small"><h5><i class="fas fa-tasks text-warning"></i>  Laporan Aktif</h5></div>
          <div class="h3 fw-bold mb-0" id="stat-laporan-aktif">0</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Total laporan selesai bulan ini -->
  <div class="col-md-3 col-sm-6 mb-3">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body d-flex flex-column justify-content-between">
        <div>
          <div class="text-muted small"><h5><i class="fas fa-check-circle text-success"></i>  Laporan Selesai Bulan Ini</h5></div>
          <div class="h3 fw-bold mb-0" id="stat-selesai-bulan-ini">0</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Total laporan ditolak -->
  <div class="col-md-3 col-sm-6 mb-3">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body d-flex flex-column justify-content-between">
        <div>
          <div class="text-muted small"><h5><i class="fas fa-times-circle text-danger"></i>  Laporan Ditolak</h5></div>
          <div class="h3 fw-bold mb-0" id="stat-ditolak">0</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Laporan Masuk Harian -->
  <div class="col-md-6">
    <div class="card mb-4 h-100">
      <div class="card-header">
        <h3 class="card-title">Laporan Masuk Harian</h3>
      </div>
      <div class="card-body" style="height: 300px;">
        <canvas id="chartHarian"></canvas>
      </div>
    </div>
  </div>

  <!-- Laporan Masuk Bulanan -->
  <div class="col-md-6">
    <div class="card mb-4 h-100">
      <div class="card-header">
        <h3 class="card-title">Laporan Masuk Bulanan</h3>
      </div>
      <div class="card-body" style="height: 300px;">
        <canvas id="chartBulanan"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header">
        <h3 class="card-title">Laporan Berdasarkan Kategori</h3>
      </div>
      <div class="card-body" style="height: 320px;">
        <canvas id="chartKategori"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header">
        <h3 class="card-title">Laporan setiap Kecamatan</h3>
      </div>
      <div class="card-body" style="height: 380px;">
        <canvas id="chartKecamatan"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header">
        <h3 class="card-title">Jumlah laporan terbanyak berdasarkan kelurahan</h3>
      </div>
      <div class="card-body" style="height: 380px;">
        <canvas id="chartKelurahan"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <!-- Petugas dengan tugas aktif terbanyak -->
  <div class="col-md-6">
    <div class="card mb-4 h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0 flex-grow-1">Petugas dengan tugas aktif terbanyak</h3>
        <button type="button" class="btn btn-xs btn-outline-secondary" id="toggleAktifPetugas">
          Tampilkan Semua
        </button>
      </div>
      <div class="card-body" style="height: 320px;">
        <canvas id="chartPetugasAktif"></canvas>
      </div>
    </div>
  </div>

  <!-- Petugas dengan tugas selesai terbanyak -->
  <div class="col-md-6">
    <div class="card mb-4 h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0 flex-grow-1">Petugas dengan tugas selesai terbanyak</h3>
        <button type="button" class="btn btn-xs btn-outline-secondary m-0 align-self-end" id="toggleSelesaiPetugas">
          Tampilkan Semua
        </button>
      </div>
      <div class="card-body" style="height: 320px;">
        <canvas id="chartPetugasSelesai"></canvas>
      </div>
    </div>
  </div>
</div>


<script>
  $(function () {
    initStatistikLaporan();
    initCharts();
  });
</script>