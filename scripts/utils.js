// -- Loading Screen Spinner --

function showLoader(){ $('#loader-overlay').fadeIn(120); }
function hideLoader(){ $('#loader-overlay').fadeOut(120); }

// -- SweetAlert2 --
function failed(text, title="Gagal!"){
    return Swal.fire({
        icon: "error",
        title: title,
        text: text,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: "OK"
    });
}

// -- helper to format "YYYY-MM-DD HH:mm:ss" -> "DD-MM-YYYY" --
function formatDMY(input) {
  if (!input) return '';
  // supports "2025-10-26 02:58:07" or ISO
  const [datePart] = String(input).split(' ');
  const [y, m, d] = datePart.split('-');
  if (!y || !m || !d) return input;
  return `${d}-${m}-${y}`;
}


function success(text, title="Berhasil!"){
    return Swal.fire({
        icon: "success",
        title: title,
        text: text,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: "OK"
    });
}

function info(text, title="Informasi"){
    return Swal.fire({
        icon: "info",
        title: title,
        text: text,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: "OK"
    });
}

// -- Logout --
$(document).on('click', '#logout', function (e) {
    e.preventDefault();

    // --- Ajax POST to API ---
    $.ajax({
      url: 'https://app.faps.my.id/api/auth/logout',
      method: 'POST',
      processData: false,
      contentType: false,
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'Authorization': 'Bearer MY_SUPER_SECRET_TOKEN'},
      xhrFields: { withCredentials: true },
      timeout: 30000
    })
    .done(function (res) {
      showLoader();
      setTimeout(function () {
        window.location.href = 'https://app.faps.my.id';
      }, 500);
    })
    .fail(function (jqXHR) {
      message = (jqXHR.responseJSON.message || `HTTP ${jqXHR.status || ''}`)
      failed(message);
      hideLoader();
    })
    .always(function () {
        console.log("Logout!");
    });
});

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

document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.planet-card');
    const dots = document.querySelectorAll('.dot');

    // Fungsi untuk mengatur status aktif
    function setActive(index) {
        // Hapus 'active' dari semua kartu dan titik
        cards.forEach(card => card.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        // Tambahkan 'active' ke kartu dan titik yang sesuai
        const activeCard = document.querySelector(`.planet-card[data-index="${index}"]`);
        const activeDot = document.querySelector(`.dot[data-index="${index}"]`);

        if (activeCard) activeCard.classList.add('active');
        if (activeDot) activeDot.classList.add('active');
    }

    // Tambahkan event listener ke setiap titik
    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            const index = dot.getAttribute('data-index');
            setActive(index);
        });
    });

    // (Opsional) Tambahkan event listener ke setiap kartu
    cards.forEach(card => {
        card.addEventListener('click', () => {
            const index = card.getAttribute('data-index');
            setActive(index);
        });
    });

    // Atur status aktif awal (default ke index 1 sesuai gambar)
    setActive(1); 
});