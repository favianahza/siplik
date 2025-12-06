<?php 
require_once __DIR__ . "/config/init.php";
secureSessionStart();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SIPLIK — Sistem Informasi Pengaduan Lingkungan Kota</title>
  <meta name="description" content="SIPLIK membantu warga melaporkan dan memantau masalah lingkungan kota seperti sampah, banjir, polusi udara, dan penghijauan." />
  <link rel="stylesheet" href="styles/style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
  <?php require_once $navbar; ?>
  <main>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h2>Laporkan Masalah Lingkungan Kota Anda dengan Mudah</h2>
            <p>SIPLIK adalah Sistem Informasi Pengaduan Lingkungan Kota yang memungkinkan Anda melaporkan masalah, melacak status, dan melihat evaluasi secara transparan.</p>
            
            <div class="hero-buttons">
                <a href="<?= BASE_URL . "/lapor" ?>" class="btn btn-primary">Buat Laporan Sekarang</a>
                <a href="<?= BASE_URL . "/evaluasi_publik" ?>" class="btn btn-secondary">Lihat Evaluasi</a>
            </div>
        </div>

        <div class="hero-image">
            <img src="<?= BASE_URL . "/assets/photo-1684827182328-374fa4b8088e.jpg"?>" alt="Pemandangan taman kota yang asri">
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">

            <header class="features-header">
                <h2>Kenapa Menggunakan SIPLIK?</h2>
                <p>Kami menyediakan platform yang efisien dan transparan untuk memudahkan warga dalam menyelesaikan masalah lingkungan perkotaan.</p>
            </header>

            <div class="features-content">
                <div class="features-diagram">
                    <div class="diagram-bg"></div>
                    
                    <button class="diagram-item active" id="btn-lacak">
                        <span class="icon-circle">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check w-8 h-8 text-green-500" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                        </span>
                        <span>Lacak</span>
                    </button>
                    
                    <button class="diagram-item" id="btn-evaluasi">
                        <span class="icon-circle">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-earth w-8 h-8 text-gray-400" aria-hidden="true"><path d="M21.54 15H17a2 2 0 0 0-2 2v4.54"></path><path d="M7 3.34V5a3 3 0 0 0 3 3a2 2 0 0 1 2 2c0 1.1.9 2 2 2a2 2 0 0 0 2-2c0-1.1.9-2 2-2h3.17"></path><path d="M11 21.95V18a2 2 0 0 0-2-2a2 2 0 0 1-2-2v-1a2 2 0 0 0-2-2H2.05"></path><circle cx="12" cy="12" r="10"></circle></svg>
                        </span>
                        <span>Evaluasi</span>
                    </button>
                    
                    <button class="diagram-item" id="btn-laporkan">
                        <span class="icon-circle">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check w-10 h-10 text-white" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                        </span>
                        <span>Laporkan</span>
                    </button>
                </div>

                <div class="features-description">

                    <div class="description-content active" id="content-lacak">
                        <div class="description-icon">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check w-8 h-8 text-green-500" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                        </div>
                        <h3>Lacak Status Laporan Anda</h3>
                        <p>Pantau progres laporan Anda secara real-time dari awal pengajuan, peninjauan, penanganan, hingga selesai. Transparansi penuh untuk Anda.</p>
                    </div>

                    <div class="description-content" id="content-evaluasi">
                        <div class="description-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-earth w-8 h-8 text-gray-400" aria-hidden="true"><path d="M21.54 15H17a2 2 0 0 0-2 2v4.54"></path><path d="M7 3.34V5a3 3 0 0 0 3 3a2 2 0 0 1 2 2c0 1.1.9 2 2 2a2 2 0 0 0 2-2c0-1.1.9-2 2-2h3.17"></path><path d="M11 21.95V18a2 2 0 0 0-2-2a2 2 0 0 1-2-2v-1a2 2 0 0 0-2-2H2.05"></path><circle cx="12" cy="12" r="10"></circle></svg>
                        </div>
                        <h3>Berikan Evaluasi & Masukan</h3>
                        <p>Setelah laporan Anda selesai ditangani, berikan penilaian dan masukan Anda. Ulasan Anda sangat berharga untuk meningkatkan kualitas layanan kami.</p>
                    </div>

                    <div class="description-content" id="content-laporkan">
                        <div class="description-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check w-10 h-10 text-white" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                        </div>
                        <h3>Pelaporan Cepat & Terstruktur</h3>
                        <p>Kami memuat dengan mudah lengkap dengan foto dan detail lokasi yang akurat untuk penanganan yang lebih cepat.</p>
                        <p class="muted">Klik tombol di sebelah untuk melihat fitur lainnya.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <div class="page-wrapper">
      <section class="planet-section">
          <header class="planet-section-header">
              <h2>Jaga Planet Kita Bersama</h2>
              <p>Setiap elemen alam memiliki peran vital. Mari bersama penjaganya menjaga laut, bumi, dan lingkungan hijau di sekitar kita.</p>
          </header>

          <div class="planet-cards-container">
              
              <div class="planet-card" data-card="laut" data-index="0">
                  <center>
                  <div class="icon-wrapper">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-waves w-6 h-6 text-blue-500" aria-hidden="true"><path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"></path><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"></path><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"></path></svg>
                  </div>
                  </center>
                  <h3>Jaga Laut Kita</h3>
                  <p>Laut adalah sumber kehidupan dan energi bumi. Menjaganya terhadap sampah, limbah plastik, sampai perkebun untuk ekosistem laut yang sehat.</p>
              </div>

              <div class="planet-card active" data-card="bumi" data-index="1">
                  <center>
                  <div class="icon-wrapper">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-earth w-6 h-6 text-yellow-500" aria-hidden="true"><path d="M21.54 15H17a2 2 0 0 0-2 2v4.54"></path><path d="M7 3.34V5a3 3 0 0 0 3 3a2 2 0 0 1 2 2c0 1.1.9 2 2 2a2 2 0 0 0 2-2c0-1.1.9-2 2-2h3.17"></path><path d="M11 21.95V18a2 2 0 0 0-2-2a2 2 0 0 1-2-2v-1a2 2 0 0 0-2-2H2.05"></path><circle cx="12" cy="12" r="10"></circle></svg>
                  </div>
                  </center>
                  <h3>Pelihara Bumi Pertiwi</h3>
                  <p>Tanah yang subur adalah dasar dari pangan dan kelangsungan alam hayati. Praktik pengolahan sampah dan pengurangan limbah industri membantu menjaga kesuburan bumi.</p>
              </div>

              <div class="planet-card" data-card="lingkungan" data-index="2">
                  <center>
                  <div class="icon-wrapper">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trees w-6 h-6 text-green-500" aria-hidden="true"><path d="M10 10v.2A3 3 0 0 1 8.9 16H5a3 3 0 0 1-1-5.8V10a3 3 0 0 1 6 0Z"></path><path d="M7 16v6"></path><path d="M13 19v3"></path><path d="M12 19h8.3a1 1 0 0 0 .7-1.7L18 14h.3a1 1 0 0 0 .7-1.7L16 9h.2a1 1 0 0 0 .8-1.7L13 3l-1.4 1.5"></path></svg>
                  </div>
                  </center>
                  <h3>Lestarikan Lingkungan</h3>
                  <p>Pohon dan area hijau adalah paru-paru kota. Menjaga penanamannya akan memastikan udara dan mengurangi rumah kaca untuk ekosistem hijau yang seimbang.</p>
              </div>
          </div>

          <div class="planet-pagination">
              <button class="dot" data-index="0" aria-label="Pilih Jaga Laut Kita"></button>
              <button class="dot active" data-index="1" aria-label="Pilih Pelihara Bumi Pertiwi"></button>
              <button class="dot" data-index="2" aria-label="Pilih Lestarikan Lingkungan"></button>
          </div>
      </section>
    </div>    
    
  </main>

  <?php 
    include $modal;
    include $footer;
    include $loader;
  ?>

</body>
<script type="text/javascript" src="scripts/utils.js"></script>
<script type="text/javascript" src="scripts/script.js"></script>
</html>
