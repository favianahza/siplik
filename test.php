<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komponen Fitur SIPLIK</title>
    <style>
        /* --- Reset & Global --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica,
                Arial, sans-serif;
            background-color: #f8faff; /* Latar belakang sedikit abu-abu seperti di gambar */
            color: #4A5568; /* Teks abu-abu tua */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* --- Kontainer Utama --- */
        .features-section {
            width: 100%;
            max-width: 960px;
            padding: 60px 20px;
            text-align: center;
        }

        /* --- Header --- */
        .features-header h2 {
            font-size: 28px;
            font-weight: 600;
            color: #2D3748; /* Teks lebih gelap untuk judul */
            margin-bottom: 12px;
        }

        .features-header p {
            font-size: 16px;
            line-height: 1.6;
            max-width: 550px;
            margin: 0 auto;
            margin-bottom: 50px;
        }

        /* --- Konten (Diagram + Deskripsi) --- */
        .features-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* --- 1. Diagram Interaktif (Kiri) --- */
        .features-diagram {
            flex: 1;
            position: relative;
            max-width: 350px; /* Ukuran diagram */
            min-height: 350px; /* Jaga rasio */
            margin: 0 auto; /* Tengah di mobile */
        }

        .diagram-bg {
            /* Lingkaran latar belakang hijau muda */
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300px;
            height: 300px;
            background-color: #e6f7ec; /* Hijau sangat muda */
            border-radius: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
        }

        .diagram-item {
            position: absolute;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 14px;
            font-weight: 500;
            color: #555;
            width: 90px; /* Beri lebar agar teks tidak pecah */
            text-align: center;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #ffffff;
            border: 2px solid #e2e8f0; /* Abu-abu muda */
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .icon-circle svg {
            width: 24px;
            height: 24px;
            color: #a0aec0; /* Ikon abu-abu */
            transition: all 0.3s ease;
        }

        /* --- Posisi Item Diagram --- */
        #btn-lacak {
            top: 40px;
            left: 40px;
        }

        #btn-evaluasi {
            top: 40px;
            right: 40px;
        }

        #btn-laporkan {
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
        }

        /* --- Gaya Khusus Item "Laporkan" --- */
        #btn-laporkan .icon-circle {
            width: 80px;
            height: 80px;
            background-color: #38A169; /* Hijau solid */
            border: none;
        }

        #btn-laporkan .icon-circle svg {
            width: 32px;
            height: 32px;
            color: #ffffff; /* Ikon putih */
        }

        /* --- Gaya Status Aktif (Dipilih) --- */
        .diagram-item.active .icon-circle {
            border-color: #38A169; /* Border hijau */
            box-shadow: 0 0 12px rgba(56, 161, 105, 0.5); /* Bayangan hijau */
        }

        .diagram-item.active .icon-circle svg {
            color: #38A169; /* Ikon hijau */
        }

        /* Jaga agar ikon "Laporkan" tetap putih saat aktif */
        #btn-laporkan.active .icon-circle svg {
            color: #ffffff;
        }

        /* --- 2. Deskripsi Fitur (Kanan) --- */
        .features-description {
            flex: 1;
            text-align: left;
            padding-left: 50px;
        }

        .description-content {
            display: none; /* Sembunyikan semua deskripsi */
            animation: fadeIn 0.5s;
        }

        .description-content.active {
            display: block; /* Tampilkan hanya yang aktif */
        }

        .description-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background-color: #e6f7ec; /* Latar ikon hijau muda */
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 16px;
        }

        .description-icon svg {
            width: 20px;
            height: 20px;
            color: #38A169; /* Ikon hijau */
        }

        .description-content h3 {
            font-size: 20px;
            font-weight: 600;
            color: #2D3748;
            margin-bottom: 10px;
        }

        .description-content p {
            font-size: 16px;
            line-height: 1.7;
            color: #4A5568;
        }

        .description-content p.muted {
            font-size: 14px;
            color: #718096;
            margin-top: 10px;
        }

        /* --- Animasi FadeIn --- */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- Desain Responsif (Mobile) --- */
        @media (max-width: 768px) {
            .features-content {
                flex-direction: column;
            }

            .features-diagram {
                margin-bottom: 40px;
            }

            .features-description {
                padding-left: 0;
                text-align: center;
            }

            .description-icon {
                margin: 0 auto 16px; /* Ikon di tengah */
            }
        }
    </style>
</head>
<body>

    <svg width="0" height="0" style="display:none;">
        <symbol id="icon-check" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </symbol>
        <symbol id="icon-eval" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9 4.804A7.968 7.968 0 0010 4.5c1.152 0 2.26.234 3.27.692L14 7.5l-.999 2.001.001.001c.02.04.038.08.056.12l.001.002.002.003c.015.027.028.055.042.082l.002.003.001.002c.038.07.07.142.102.215l.002.003c.029.066.056.133.08.201l.002.004c.02.056.037.113.052.17l.001.004c.012.046.02.093.027.14l.001.003c.005.034.008.068.01.103l.001.002c.001.01.001.02.002.03l.001.003v.003c0 .01.001.02.001.03v.002c0 .017 0 .033.001.05v.002c0 .017 0 .033-.001.05l-.001.003c-.001.016-.002.033-.003.049l-.001.003c-.003.02-.007.04-.01.06l-.001.003c-.004.02-.009.04-.013.06l-.001.003c-.005.02-.01.04-.016.06l-.001.003c-.006.02-.012.04-.019.059l-.001.003c-.007.02-.015.04-.022.06l-.001.002c-.008.02-.017.04-.025.06l-.001.002c-.009.02-.018.04-.028.06l-.001.002c-.01.02-.02.04-.03.06l-.001.002c-.01.02-.02.04-.03.058l-.001.003c-.011.02-.022.04-.034.06l-.001.002c-.011.02-.023.04-.035.06l-.001.002c-.012.02-.025.04-.037.058l-.001.002c-.013.02-.026.04-.039.058l-.001.002c-.013.02-.027.04-.04.058l-.001.002c-.014.02-.028.04-.042.058l-.001.002c-.014.02-.029.04-.044.057l-.001.002c-.015.02-.03.04-.046.057l-.001.002c-.016.02-.032.04-.048.057l-.001.002c-.016.02-.033.04-.05.056l-.001.002c-.017.02-.034.04-.052.056l-.001.002c-.018.02-.036.038-.054.056l-.001.002c-.018.02-.037.038-.056.055l-.001.002c-.019.02-.038.038-.058.055l-.001.002c-.02.02-.039.037-.059.054l-.001.002c-.02.02-.04.037-.06.054l-.001.002c-.02.02-.04.037-.06.054l-.001.002c-.02.02-.04.036-.06.053l-.001.002c-.02.02-.04.036-.06.053l-.001.002c-.02.02-.04.036-.06.052l-.001.002c-.02.02-.04.035-.06.052l-.001.002c-.02.02-.04.035-.06.05l-.001.002c-.02.02-.04.035-.06.05l-.001.002c-.02.02-.04.034-.06.05l-.001.002c-.02.02-.04.034-.06.05l-.001.002c-.02.02-.04.033-.06.048l-.001.002c-.02.02-.04.033-.06.048l-.001.002c-.02.02-.04.033-.06.047l-.001.002c-.02.02-.04.032-.06.047l-.001.002c-.02.02-.04.032-.06.046l-.001.002c-.02.02-.04.032-.06.046l-.001.002c-.02.02-.04.032-.06.046l-.001.002c-.02.02-.04.03-.06.045l-.001.002c-.02.01-.04.03-.06.044l-.001.002c-.02.01-.04.03-.06.043l-.001.002c-.02.01-.04.03-.06.043l-.001.002c-.02.01-.04.03-.06.042l-.001.002c-.02.01-.04.03-.06.042l-.001.002c-.02.01-.04.03-.06.04l-.001.002c-.02.01-.04.03-.06.04l-.001.002c-.02.01-.04.03-.06.038l-.001.002c-.02.01-.04.03-.06.038l-.001.002c-.02.01-.04.03-.06.036l-.001.002c-.02.01-.04.03-.06.036l-.001.002c-.02.01-.04.03-.06.034l-.001.002c-.02.01-.04.03-.06.033l-.001.002c-.02.01-.04.03-.06.032l-.001.002c-.02.01-.04.03-.06.03l-.001.002c-.02.01-.04.02-.06.03l-.001.002c-.02.01-.04.02-.06.028l-.001.002c-.02.01-.04.02-.06.027l-.001.002c-.02.01-.04.02-.06.026l-.001.002c-.02.01-.04.02-.06.025l-.001.002c-.02.01-.04.02-.06.024l-.001.002c-.02.01-.04.02-.06.023l-.001.002c-.02.01-.04.02-.06.022l-.001.002c-.02.01-.04.02-.06.02l-.001.002c-.02.01-.04.02-.06.02l-.001.002c-.04.01-.09.02-.13.028l-.002.001c-.04.01-.09.02-.13.027l-.002.001c-.04.01-.09.02-.13.026l-.002.001c-.04.01-.09.02-.13.024l-.002.001c-.04.01-.09.02-.13.022l-.002.001c-.04.01-.09.02-.13.02l-.002.001c-.04.01-.09.01-.13.018l-.002.001c-.04.01-.09.01-.13.017l-.002.001c-.04.01-.09.01-.13.015l-.002.001c-.04.01-.09.01-.13.013l-.002.001c-.04.01-.09.01-.13.012l-.002.001c-.04.01-.09.01-.13.01l-.002.001c-.04.01-.09.01-.13.008l-.002.001c-.04.01-.09.01-.13.007l-.002.001c-.04,0-.09.01-.13.005l-.002.001c-.04,0-.09.01-.13.004l-.002.001c-.04,0-.09.001-.13,0l-.002.001c-.04,0-.09,0-.13-.002l-.002.001c-.04,0-.09,0-.13-.004l-.002.001c-.04,0-.09,0-.13-.005l-.002.001c-.04,0-.09-.01-.13-.007l-.002.001c-.04,0-.09-.01-.13-.008l-.002.001c-.04,0-.09-.01-.13-.01l-.002.001c-.04,0-.09-.01-.13-.012l-.002.001c-.04,0-.09-.01-.13-.013l-.002.001c-.04,0-.09-.01-.13-.015l-.002.001c-.04,0-.09-.01-.13-.017l-.002.001c-.04,0-.09-.01-.13-.018l-.002.001c-.04,0-.09-.01-.13-.02l-.002.001c-.04,0-.09-.02-.13-.022l-.002.001c-.04,0-.09-.02-.13-.024l-.002.001c-.04,0-.09-.02-.13-.026l-.002.001c-.04,0-.09-.02-.13-.027l-.002.001c-.04,0-.09-.02-.13-.028l-.002.001c-.02,0-.04-.02-.06-.02l-.001.002c-.02,0-.04-.02-.06-.02l-.001.002c-.02,0-.04-.02-.06-.022l-.001.002c-.02,0-.04-.02-.06-.023l-.001.002c-.02,0-.04-.02-.06-.024l-.001.002c-.02,0-.04-.02-.06-.025l-.001.002c-.02,0-.04-.02-.06-.026l-.001.002c-.02,0-.04-.02-.06-.027l-.001.002c-.02,0-.04-.02-.06-.028l-.001.002c-.02,0-.04-.02-.06-.03l-.001.002c-.02,0-.04-.02-.06-.03l-.001.002c-.02,0-.04-.03-.06-.032l-.001.002c-.02,0-.04-.03-.06-.033l-.001.002c-.02,0-.04-.03-.06-.034l-.001.002c-.02,0-.04-.03-.06-.036l-.001.002c-.02,0-.04-.03-.06-.036l-.001.002c-.02,0-.04-.03-.06-.038l-.001.002c-.02,0-.04-.03-.06-.038l-.001.002c-.02,0-.04-.03-.06-.04l-.001.002c-.02,0-.04-.03-.06-.04l-.001.002c-.02-.01-.04-.03-.06-.042l-.001.002c-.02-.01-.04-.03-.06-.042l-.001.002c-.02-.01-.04-.03-.06-.043l-.001.002c-.02-.01-.04-.03-.06-.043l-.001.002c-.02-.01-.04-.03-.06-.044l-.001.002c-.02-.01-.04-.03-.06-.045l-.001.002c-.02-.01-.04-.03-.06-.046l-.001.002c-.02-.01-.04-.03-.06-.046l-.001.002c-.02-.01-.04-.03-.06-.046l-.001.002c-.02-.01-.04-.03-.06-.047l-.001.002c-.02-.01-.04-.03-.06-.047l-.001.002c-.02-.01-.04-.03-.06-.048l-.001.002c-.02-.01-.04-.03-.06-.048l-.001.002c-.02-.01-.04-.03-.06-.05l-.001.002c-.02-.01-.04-.03-.06-.05l-.001.002c-.02-.01-.04-.03-.06-.05l-.001.002c-.02-.01-.04-.03-.06-.05l-.001.002c-.02-.01-.04-.03-.06-.052l-.001.002c-.02-.01-.04-.04-.06-.052l-.001.002c-.02-.01-.04-.04-.06-.053l-.001.002c-.02-.01-.04-.04-.06-.053l-.001.002c-.02-.01-.04-.04-.06-.054l-.001.002c-.02-.01-.04-.04-.06-.054l-.001.002c-.02-.01-.04-.04-.059-.054l-.001.002c-.02-.01-.039-.04-.058-.055l-.001.002c-.019-.01-.038-.04-.056-.055l-.001.002c-.018-.01-.037-.04-.056-.055l-.001.002c-.018-.01-.036-.04-.054-.056l-.001.002c-.018-.01-.036-.04-.052-.056l-.001.002c-.017-.01-.034-.04-.05-.056l-.001.002c-.016-.01-.033-.04-.048-.057l-.001.002c-.016-.01-.032-.04-.046-.057l-.001.002c-.015-.01-.03-.04-.044-.057l-.001.002c-.014-.01-.029-.04-.042-.058l-.001.002c-.014-.01-.028-.04-.04-.058l-.001.002c-.013-.01-.027-.04-.039-.058l-.001.002c-.013-.01-.026-.04-.037-.058l-.001.002c-.012-.01-.025-.04-.035-.06l-.001.002c-.011-.01-.023-.04-.034-.06l-.001.002c-.011-.01-.022-.04-.03-.06l-.001.003c-.01-.02-.02-.04-.03-.058l-.001.003c-.01-.02-.02-.04-.028-.06l-.001.003c-.009-.02-.018-.04-.025-.06l-.001.003c-.008-.02-.017-.04-.022-.06l-.001.003c-.007-.02-.015-.04-.019-.059l-.001.003c-.006-.02-.012-.04-.016-.06l-.001.003c-.005-.02-.01-.04-.013-.06l-.001.003c-.004-.02-.009-.04-.01-.06l-.001.003c-.003-.02-.007-.04-.008-.06l-.001.003c-.002-.016-.003-.033-.003-.049l-.001.003c0-.017-.001-.033-.001-.05v-.002c0-.017 0-.033.001-.05v-.002c0-.01 0-.02.001-.03v-.003c0-.01.001-.02.002-.03l.001-.003c.001-.01.001-.02.001-.03l.001-.002c.002-.035.005-.07.01-.103l.001-.003c.007-.047.015-.094.027-.14l.001-.004c.015-.057.032-.114.052-.17l.001-.004c.f024-.068.05-.135.08-.201l.002-.003c.032-.073.064-.145.102-.215l.002-.003c.014-.027.027-.055.042-.082l.002-.003c.015-.027.029-.055.043-.082l.001-.003c.018-.04.036-.08.056-.12l.001-.002L6 7.5l.73-.692A7.968 7.968 0 0010 4.5c.712 0 1.407.092 2.064.264l.002.001L12 5.5l-1 2-1-2z" clip-rule="evenodd" />
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM2 10a8 8 0 1116 0 8 8 0 01-16 0z" clip-rule="evenodd" />
        </symbol>
    </svg>


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
                            <svg><use href="#icon-check"></use></svg>
                        </span>
                        <span>Lacak</span>
                    </button>
                    
                    <button class="diagram-item" id="btn-evaluasi">
                        <span class="icon-circle">
                            <svg><use href="#icon-eval"></use></svg>
                        </span>
                        <span>Evaluasi</span>
                    </button>
                    
                    <button class="diagram-item" id="btn-laporkan">
                        <span class="icon-circle">
                            <svg><use href="#icon-check"></use></svg>
                        </span>
                        <span>Laporkan</span>
                    </button>
                </div>

                <div class="features-description">

                    <div class="description-content active" id="content-lacak">
                        <div class="description-icon">
                            <svg><use href="#icon-check"></use></svg>
                        </div>
                        <h3>Lacak Status Laporan Anda</h3>
                        <p>Pantau progres laporan Anda secara real-time dari awal pengajuan, peninjauan, penanganan, hingga selesai. Transparansi penuh untuk Anda.</p>
                    </div>

                    <div class="description-content" id="content-evaluasi">
                        <div class="description-icon">
                            <svg><use href="#icon-eval"></use></svg>
                        </div>
                        <h3>Berikan Evaluasi & Masukan</h3>
                        <p>Setelah laporan Anda selesai ditangani, berikan penilaian dan masukan Anda. Ulasan Anda sangat berharga untuk meningkatkan kualitas layanan kami.</p>
                    </div>

                    <div class="description-content" id="content-laporkan">
                        <div class="description-icon">
                            <svg><use href="#icon-check"></use></svg>
                        </div>
                        <h3>Pelaporan Cepat & Terstruktur</h3>
                        <p>Kami memuat dengan mudah lengkap dengan foto dan detail lokasi yang akurat untuk penanganan yang lebih cepat.</p>
                        <p class="muted">Klik tombol di sebelah untuk melihat fitur lainnya.</p>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Ambil semua tombol dan semua konten deskripsi
            const featureButtons = document.querySelectorAll('.diagram-item');
            const featureContents = document.querySelectorAll('.description-content');

            // Tambahkan event listener untuk setiap tombol
            featureButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Dapatkan ID target dari tombol yang diklik
                    // (misal: 'btn-lacak' -> 'lacak')
                    const targetId = button.id.split('-')[1];
                    const targetContent = document.getElementById(`content-${targetId}`);

                    // 1. Hapus kelas 'active' dari semua tombol
                    featureButtons.forEach(btn => {
                        btn.classList.remove('active');
                    });

                    // 2. Tambahkan kelas 'active' ke tombol yang baru diklik
                    button.classList.add('active');

                    // 3. Hapus kelas 'active' dari semua konten deskripsi
                    featureContents.forEach(content => {
                        content.classList.remove('active');
                    });

                    // 4. Tambahkan kelas 'active' ke konten yang sesuai
                    if (targetContent) {
                        targetContent.classList.add('active');
                    }
                });
            });
        });
    </script>

</body>
</html>