<?php 
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../config/database.php";
$kecamatan = Database::fetchAll("SELECT * from ref_kecamatan")
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SIPLIK — Sistem Informasi Pengaduan Lingkungan Kota</title>
  <meta name="description" content="SIPLIK membantu warga melaporkan dan memantau masalah lingkungan kota seperti sampah, banjir, polusi udara, dan penghijauan." />
  <link rel="stylesheet" href="styles/style.css">
  <link rel="stylesheet" href="styles/laporan.css">  
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
  <?php require_once $navbar; ?>

  <main>
    <h1 style="line-height: 1em">Laporan Pengaduan</h1>

    <form>
      <!-- Kategori Pengaduan -->
      <div class="form-group kategori">        
        <label for="kategori">Kategori Pengaduan</label>
        <div class="info-helper-container">
        <small class="info-helper">
          Pilih kategori yang sesuai
          <span class="info-icon" tabindex="0">ℹ️
            <span class="info-tooltip">
              <strong>Pemilihan kategori dapat merujuk pada informasi dibawah ini:</strong><br><br>
              <b>Pencemaran Udara</b> <br><small>Asap pembakaran sampah, emisi kendaraan, industri kecil menengah tanpa filter.</small><br><br>
              <b>Pencemaran Air</b> <br><small>Limbah cair rumah tangga atau usaha, air sumur berbau, sungai tercemar.</small><br><br>
              <b>Pencemaran Tanah / Limbah B3</b> <br><small>Tumpahan oli, limbah bengkel, limbah rumah sakit, sampah medis.</small><br><br>
              <b>Persampahan & Kebersihan Kota</b> <br><small>Penumpukan sampah, TPS liar, keterlambatan pengangkutan.</small><br><br>
              <b>Penghijauan & RTH</b> <br><small>Pohon tumbang, taman rusak, kurangnya pohon pelindung.</small><br><br>
              <b>Perusakan Lingkungan / Alih Fungsi Lahan</b> <br><small>Penebangan liar, pembangunan di kawasan lindung, pengerukan tanah.</small>
            </span>
          </span>
        </small>
        </div>
        <select id="kategori" name="kategori" required>
          <option value="" disabled selected hidden>Pilih Kategori Pengaduan</option>
          <option value="1">Pencemaran Udara</option>
          <option value="2">Pencemaran Air</option>
          <option value="3">Pencemaran Tanah / Limbah B3</option>
          <option value="4">Persampahan & Kebersihan Kota</option>
          <option value="5">Penghijauan & Ruang Terbuka Hijau (RTH)</option>
          <option value="6">Perusakan Lingkungan / Alih Fungsi Lahan</option>
        </select>
      </div>

      <!-- Lokasi -->
      <div class="form-group data">
        <label>Lokasi</label>

        <!-- Row: Kecamatan + Kelurahan (same line) -->
        <div class="lokasi-row">
          <div class="field">
            <small class="sub-label">Nama Kecamatan</small>
            <select name="id_kecamatan" required>
              <option value="" disabled selected hidden>Pilih Nama Kecamatan</option>
              <?php foreach($kecamatan as $record ): ?>
                <option value="<?= $record["id"]?>"><?= $record["nama"]?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <small class="sub-label">Nama Kelurahan</small>
            <select name="id_kelurahan" required>
            </select>
          </div>
        </div>
        <!-- Alamat Lengkap -->
        <div class="alamat-row">
          <small class="sub-label" style="text-align: center">Tulis alamat lengkap dengan jelas!</small>
          <input type="text" name="alamat_lengkap" placeholder="Contoh: Jl. Melati No. 12, RT 03 RW 05"
                 required />
        </div><br>

        <!-- Deskripsi Laporan (place ABOVE the Upload Bukti section) -->
        <div class="form-group">
          <label for="deskripsi_laporan">Deskripsi Laporan</label>
          <small class="sub-label">Tuliskan kronologi atau detail laporan secara jelas.</small>
          <textarea
            id="deskripsi_laporan"
            name="deskripsi_laporan"
            rows="5"
            placeholder="Jelaskan masalah lingkungan yang ingin Anda laporkan..."
            required
          ></textarea>
        </div><br>

        <!-- Tanggal Kejadian -->
        <div class="form-group">
          <label for="tanggal_kejadian">Tanggal Kejadian</label>
          <input type="date" id="tanggal_kejadian" class="tanggal_kejadian" name="tanggal_kejadian" placeholder="DD/MM/YYYY" required />
        </div><br>

        <!-- Upload Bukti -->
        <div class="form-group bukti_foto">
          <label for="bukti">Upload Bukti</label>
          <small class="sub-label" style="text-align: center">Masukan bukti foto sebagai pendukung laporan!</small>
          <input id="bukti" name="bukti[]" type="file" accept="image/*" multiple />
        </div><br>

        <!-- Data Kontak Pelapor -->
        <div class="form-group kontak_pelapor">
          <label>Data Kontak Pelapor</label>

          <div class="kontak-row">
            <div class="field">
              <small class="sub-label">Nama Pelapor</small>
              <input type="text" name="nama_pelapor" id="nama_pelapor" placeholder="Nama lengkap Anda" required>
            </div>

            <div class="field">
              <small class="sub-label">Email</small>
              <input type="email" name="email_pelapor" id="email_pelapor" placeholder="contoh@email.com" required>
            </div>

            <div class="field">
              <small class="sub-label">No. Telp</small>
              <input type="tel" name="telp_pelapor" id="telp_pelapor" placeholder="08xxxxxxxxxx" required
                    inputmode="numeric" pattern="[0-9]*"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>
          </div>

          <!-- Anonim checkbox -->
          <div class="custom-checkbox">
            <input type="checkbox" id="anonim" name="anonim">
            <label for="anonim" class="checkbox-label">Lapor sebagai Anonim</label>
          </div>

          <!-- Persetujuan checkbox -->
          <br>
          <small style="font-weight: bold">Saya menyatakan data yang saya sampaikan sudah benar dan valid.</small>
          <div class="custom-checkbox">
            <input type="checkbox" id="persetujuan" name="persetujuan">
            <label for="persetujuan" class="checkbox-label">Sudah benar dan valid</label>
          </div>

        </div>

        <div class="captcha">
          <div class="g-recaptcha" data-sitekey="6Ld5xvUrAAAAAK8WETwxt_rIOtV-NcqKSX1jOrK0"></div>
        </div>

      </div>

      <!-- Submit -->
      <button type="submit" class="submit-laporan">KIRIM LAPORAN</button>
    </form>
  </main>

  <?php 
    require_once $modal;
    require_once $footer;
    require_once $loader;
  ?>

</body>
<script type="text/javascript" src="scripts/utils.js"></script>
<script type="text/javascript" src="scripts/script.js"></script>
<script type="text/javascript" src="scripts/laporan.js"></script>
</html>
