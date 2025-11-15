<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../config/database.php";
secureSessionStart();
if(!isset($_SESSION["logged_in"])){
  header("Location: ".BASE_URL);
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIPLIK — Dashboard</title>
  <!-- AdminLTE (Bootstrap 5) -->
  <link href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
  <!-- OverlayScrollbars (AdminLTE dependency) -->
  <link href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.1.1/styles/overlayscrollbars.min.css" rel="stylesheet">
  <!-- Google Font (same as landing page) -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Dashboard related -->
  <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
  <script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js"></script>
  <link href="styles/dashboard.css" rel="stylesheet">
  <link href="styles/modal.css" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
<input type="hidden" id="sessionUser" value="<?php echo $_SESSION['user_id']; ?>">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?= BASE_URL . '/index'?>" class="nav-link">Beranda</a>
      </li>
      <li>
        <a href="#change_password" class="nav-link">Ganti Password</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- User -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i> <span class="d-none d-md-inline"><?= $_SESSION["user_name"] ?></span>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="dashboard.php" class="brand-link">
      <span class="brand-text font-weight-bold">SIPLIK</span>
    </a>
    <br>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item mb-2">
            <a href="#ringkasan" class="nav-link active" data-load="ringkasan">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Ringkasan</p>
            </a>
          </li>
          <li class="nav-item has-treeview my-1">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-file"></i>
              <p>Laporan<i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview my-1" data-widge="treeview">
<?php if($_SESSION["user_role"] == "Petugas DLHK") : ?>
              <li class="nav-item"><a href="#laporan_masuk" class="nav-link text-white my-2" data-load="laporan_masuk"><i class="fa fa-arrow-circle-right nav-icon"></i><p>Laporan Masuk</p></a></li>
              <li class="nav-item"><a href="#laporan_diproses" class="nav-link text-white my-2" data-load="laporan_diproses"><i class="fa fa-info-circle nav-icon"></i><p>Laporan Diproses</p></a></li>
              <li class="nav-item"><a href="#arsip_laporan" class="nav-link text-white my-2" data-load="arsip_laporan"><i class="fa fa-archive nav-icon"></i><p>Arsip Laporan</p></a></li>
<?php else: ?>
              <li class="nav-item"><a href="#laporan_ditugaskan" class="nav-link text-white my-2" data-load="laporan_ditugaskan"><i class="fa fa-arrow-circle-right nav-icon"></i><p>Laporan Ditugaskan</p></a></li>
              <li class="nav-item"><a href="#laporan_diproses" class="nav-link text-white my-2" data-load="laporan_diproses"><i class="fa fa-info-circle nav-icon"></i><p>Laporan Diproses</p></a></li>
              <li class="nav-item"><a href="#arsip_laporan" class="nav-link text-white my-2" data-load="arsip_laporan"><i class="fa fa-archive nav-icon"></i><p>Arsip Laporan</p></a></li>  
<?php endif;?>
            </ul>
          </li>
          <!-- <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-map-marker-alt"></i><p>Peta Lokasi</p></a></li> -->
          <li class="nav-item my-1"><a href="#petugas" class="nav-link" data-load="list_petugas"><i class="nav-icon fas fa-users"></i><p> List Petugas</p></a></li>
          <li class="nav-item my-1"><a href="#" class="nav-link" data-load="analitik"><i class="nav-icon fas fa-chart-line"></i><p>Analitik</p></a></li>
          <li class="nav-item mt-2 my-1">
            <hr>
            <a href="logout" class="nav-link" style="border-radius:6px;" id="logout">
              <i class="nav-icon fas fa-sign-out-alt"></i><p>Keluar</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper -->
  <main class="content-wrapper p-3" id="content-wrapper">
  </main>

  <footer class="main-footer text-sm">
    <strong>&copy; <?php echo date('Y'); ?> SIPLIK.</strong> Dashboard powered by AdminLTE.
    <div class="float-right d-none d-sm-inline-block">v3.2 Template</div>
  </footer>

</div>

<?php 
  include $modal_dashboard;
  include $loader;
?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.1.1/browser/overlayscrollbars.browser.es6.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script type="text/javascript" src="scripts/utils.js"></script>
<script type="text/javascript" src="scripts/dashboard.js"></script>
<?php if($_SESSION["user_role"] == "Petugas DLHK") : ?>
<script type="text/javascript" src="scripts/laporan_masuk.js"></script>
<script type="text/javascript" src="scripts/laporan_diproses.js"></script>
<script type="text/javascript" src="scripts/arsip_laporan.js"></script>
<script type="text/javascript" src="scripts/list_petugas.js"></script>
<?php else: ?>
<script type="text/javascript" src="scripts/laporan_ditugaskan.js"></script>
<script type="text/javascript" src="scripts/arsip_laporan.js"></script>
<?php endif; ?>
</body>
</html>
