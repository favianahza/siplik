<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../config/database.php";
secureSessionStart();
?>
<section class="content-header py-3 border-bottom">
  <div class="container-fluid d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
    <h1 class="m-0 fw-bold">Laporan yang Ditugaskan</h1>
    <div class="text-muted small text-md-end">
      <?php
        date_default_timezone_set('Asia/Jakarta');
        $now = new DateTime('now');
      ?>
      Latest update: <strong><?php echo $now->format('d/m/Y H:i'); ?> WIB</strong>
    </div>
  </div>
</section>

<section class="content py-3">
  <div class="container-fluid">

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="fw-semibold">Tabel List Laporan yang Ditugaskan kepada anda</div>
          <div class="small text-muted">Limit: <span id="dt-limit">10</span></div>
        </div>

        <div class="table-responsive">
          <table id="tbl-ditugaskan" class="table table-striped table-hover w-100" data-fetch-limit="1000">
            <thead class="table-light">
              <tr>
                <th style="width:60px">No.</th>
                <th>Kode Tiket</th>
                <th>Tanggal Masuk</th>
                <th>Kategori</th>                
                <th>Kecamatan</th>
                <th>Alamat</th>
                <th style="text-align: center;">Aksi</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

      </div>
    </div>

  </div>
</section>