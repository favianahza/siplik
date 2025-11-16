 <!-- Header / Navbar -->  
<header class="section">
    <div class="container">
        <nav class="navbar" aria-label="Navigasi utama">

            <div class="navbar-left">
                <a href="<?= BASE_URL . '/index' ?>" class="navbar-brand">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="14" cy="14" r="14" fill="#1CB957"/>
                        <path d="M19.5 10.5L12.5 17.5L8.5 13.5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>SIPLIK</span>
                </a>
            </div>

            <div class="navbar-right">
                <ul>
                    <li><a href="<?= BASE_URL . '#tentang_kami' ?>">Tentang Kami</a></li>
                    <li><a href="<?= BASE_URL . '#faq' ?>">FAQ</a></li>
                    
                    <?php if(!isset($_SESSION["logged_in"])): ?>
                        <li><a href="#daftar">Daftar</a></li>
                        <li><a href="#login">Login</a></li>
                    <?php else: ?>
                        <li><a href="<?= BASE_URL . '/dashboard' ?>">Dashboard</a></li>
                        <li><a href="#logout" id="logout">Logout</a></li>
                    <?php endif; ?>
                    <li><a href="<?= BASE_URL . '/lapor'?>">Buat Laporan</a></li>
                    <li><a href="#lacak" class="navbar-link-primary">Lacak Laporan</a></li>
                </ul>
            </div>
        </nav>
    </div>
</header>