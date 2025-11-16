$(function () {
  // Modals
  const $loginModal    = $('#login-modal');
  const $registerModal = $('#register-modal');
  const $checkTicketModal = $('#check-laporan-modal')

  // Inputs for focusing
  const $loginUsername = $('#login-username');
  const $regFullname   = $('#reg-fullname');
  const $ticketCodeInput   = $('#ticket_code_input');

  // Openers
  const $loginLinks    = $('a[href="#login"]');
  const $registerLinks = $('a[href="#daftar"], [data-open-register]');
  const $checkLaporanLinks = $('a[href="#lacak"]');

  // Cross-modal links
  const $toRegisterInsideLogin = $('#login-modal a[href="#daftar"], #login-modal [data-open-register]');
  const $toLoginInsideRegister = $('#register-modal a[href="#login"], #register-modal [data-open-login]');

  function openModal($modal, $focusEl) {
    // Close both first to ensure single-open
    $loginModal.add($registerModal).removeClass('is-open').attr('aria-hidden', 'true');

    $modal.addClass('is-open').attr('aria-hidden', 'false');
    $('body').addClass('modal-open');

    // focus after paint
    setTimeout(() => { if ($focusEl && $focusEl.length) $focusEl.trigger('focus'); }, 0);
  }

  function closeModal($modal) {
    $modal.removeClass('is-open').attr('aria-hidden', 'true');

    // If none open, remove body flag
    if (!$loginModal.is('.is-open') && !$registerModal.is('.is-open')) {
      $('body').removeClass('modal-open');
    }
  }

  // Bind navbar openers
  $loginLinks.on('click', function (e) { e.preventDefault(); openModal($loginModal, $loginUsername); });
  $registerLinks.on('click', function (e) { e.preventDefault(); openModal($registerModal, $regFullname); });
  $checkLaporanLinks.on('click', function(e) {e.preventDefault(); openModal($checkTicketModal, $ticketCodeInput); });

  // Close handlers (overlay + close buttons) for both modals
  $('.modal').each(function () {
    const $modal   = $(this);
    const $overlay = $modal.find('.modal-overlay');
    const $closers = $modal.find('[data-close-modal]');

    $overlay.on('click', function () { closeModal($modal); });
    $closers.on('click', function () { closeModal($modal); });
  });

  // Cross-links: Login <-> Register
  $toRegisterInsideLogin.on('click', function (e) {
    e.preventDefault();
    closeModal($loginModal);
    openModal($registerModal, $regFullname);
  });
  $toLoginInsideRegister.on('click', function (e) {
    e.preventDefault();
    closeModal($registerModal);
    openModal($loginModal, $loginUsername);
  });

  // Esc to close whichever is open
  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') {
      if ($loginModal.is('.is-open')) closeModal($loginModal);
      if ($registerModal.is('.is-open')) closeModal($registerModal);
    }
  });

  // Basic password match validation for Register
  const $regForm = $('#register-form');
  const $pass    = $('#reg-password');
  const $pass2   = $('#reg-password-confirm');
  const $msg     = $('#reg-passmatch');

  function checkMatch() {
    const v1 = $pass.val();
    const v2 = $pass2.val();
    if (!v1 || !v2) { $msg.text(''); return true; }
    const ok = v1 === v2;
    $msg.text(ok ? '' : 'Password dan konfirmasi tidak cocok.');
    return ok;
  }


  $pass.on('input', checkMatch);
  $pass2.on('input', checkMatch);

  $regForm.on('submit', function (e) {
    if (!checkMatch()) { e.preventDefault(); $pass2.trigger('focus'); }
  });

  
  // Register function
  $(document).on('click', '#register', function (e) {
    e.preventDefault();

    const $btn  = $(this);
    const $form = $btn.closest('form');
    if (!$form.length) return;

    let isValid = true;
    $form.find('input, select, textarea').each(function () {
      const $el = $(this);
      if ($el.is(':disabled')) return;
      if (!$el.val() || $el.val().trim() === '') {
        isValid = false;
        $el.addClass('is-invalid');
      } else {
        $el.removeClass('is-invalid');
      }
    });
    if (!isValid) {
      failed("Isi seluruh form dengan lengkap!")
      return;
    }

    const v1 = $pass.val();
    const v2 = $pass2.val();
    if(v1 != v2){
      return failed("Konfirmasi password yang dimasukan tidak sama!")
    }
    

    const originalText = $btn.text();
    $btn.prop('disabled', true).text('MENDAFTAR…');
    showLoader();

    const fd = new FormData($form[0]);

    $.ajax({
      url: 'https://app.faps.my.id/api/users',
      method: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'Authorization': 'Bearer MY_SUPER_SECRET_TOKEN'},
      timeout: 30000
    })
    .done(function (res) {
      Swal.fire({
        title: "Berhasil mendaftar!",
        icon: "success",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "OK"
      }).then((result) => {
        if (result.isConfirmed) {
          location.reload()
        }
      });
    })
    .fail(function (jqXHR) {
      message = (jqXHR.responseJSON.message || `HTTP ${jqXHR.status || ''}`)
      failed(message);
    })
    .always(function () {
      hideLoader();
      $btn.prop('disabled', false).text(originalText || 'Buat Akun');
    });
  });    

  // --- Login user ---
  $(document).on('click', '#login', function (e) {
    e.preventDefault();

    const $btn  = $(this);
    const $form = $btn.closest('form');
    if (!$form.length) return;

    // --- Basic validation ---
    let isValid = true;
    $form.find('input, select, textarea').each(function () {
      const $el = $(this);
      if ($el.is(':disabled')) return;
      if (!$el.val() || $el.val().trim() === '') {
        isValid = false;
        $el.addClass('is-invalid');
      } else {
        $el.removeClass('is-invalid');
      }
    });

    if (!isValid) {
      alert('Mohon isi semua kolom sebelum login!');
      return;
    }

    // --- Loader + button state ---
    const originalText = $btn.text();
    $btn.prop('disabled', true).text('MEMPROSES…');
    showLoader();

    // --- Build FormData payload ---
    const fd = new FormData($form[0]);

    // --- Ajax POST to API ---
    $.ajax({
      url: 'https://app.faps.my.id/api/auth/login',
      method: 'POST',
      data: fd,
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
        window.location.href = 'https://app.faps.my.id/dashboard';
      }, 500);
    })
    .fail(function (jqXHR) {
      message = (jqXHR.responseJSON.message || `HTTP ${jqXHR.status || ''}`)
      failed(message);
      hideLoader();
    })
    .always(function () {
      $btn.prop('disabled', false).text(originalText || 'Login');
    });
  });



  // --- Check Ticket Code ---
  $(document).on('click', '#check', function (e) {
    e.preventDefault();

    const $btn  = $(this);
    let ticketCode = $("#ticket_code_input").val();
    let apiUrl = `https://app.faps.my.id/api/pengaduan/${ticketCode}`
    
    if (ticketCode === "") {
      failed("Kode tiket tidak boleh kosong!");
      return;
    }    

    // --- Loader + button state ---
    const originalText = $btn.text();
    $btn.prop('disabled', true).text('MEMPROSES…');
    showLoader();

    // --- Ajax POST to API ---
    $.ajax({
      url: apiUrl,
      method: 'GET',
      processData: false,
      contentType: false,
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'Authorization': 'Bearer MY_SUPER_SECRET_TOKEN'},
      xhrFields: { withCredentials: true },
      timeout: 30000
    })
    .done(function (res) {
      let apiUrl = `https://app.faps.my.id/api/tindak_lanjut/ticket/${ticketCode}`
      let status = res[0].status
      hideLoader();
      if(status == "diproses" || status == "diverifikasi" ){
        info("Pengaduan anda sedang diproses. Cek kembali nanti!")
        return 
      } else if (status == "masuk") {
        info("Pengaduan anda sedang diverifikasi. Cek kembali nanti!")
        return
      } else if (status == "ditolak") {
        failed("Pengaduan anda ditolak!", "Ditolak!")
        return
      } else {

        // Get tindak lanjut!
        closeModal($checkTicketModal);

        // Call to API about tindak_lanjut
        $.ajax({
            url: apiUrl,
            method: 'GET',
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer MY_SUPER_SECRET_TOKEN'},
            xhrFields: { withCredentials: true },
            timeout: 30000
        }).done(function(res){

        const tindak_lanjut = Array.isArray(res) ? res[0] : res;
        const images = Array.isArray(tindak_lanjut.fotos) ? tindak_lanjut.fotos : [];

        function getStatusBadge(status) {
          const map = {
            selesai: ['SELESAI', 'success'],
            masuk: ['DITERIMA', 'secondary'],
            diverifikasi: ['DIVERIFIKASI', 'primary'],
            diproses: ['DIPROSES', 'warning'],
            ditolak: ['DITOLAK', 'danger']
          };
          const [text, color] = map[status.toLowerCase()] || ['UNKNOWN', 'dark'];
          return `<span class="badge bg-${color} mt-1 fs-5 py-2 px-3">${text}</span>`;
        }

        const imgTindakLanjutList = images.length
          ? `
            <div class="d-flex justify-content-center flex-wrap gap-4">
              ${images.map(img => `
                <a data-fslightbox="lampiran" href="https://app.faps.my.id/assets/uploads/tindak_lanjut/${img}">
                  <img src="https://app.faps.my.id/assets/uploads/tindak_lanjut/${img}"
                      alt="Bukti Tindak Lanjut ${tindak_lanjut.ticket_code}"
                      class="img-fluid rounded shadow-sm"
                      style="max-width: 600px; height: auto;">
                </a>
              `).join('')}
            </div>
          `
          : `<em class="text-muted">Tidak ada lampiran.</em>`;

          const tindakLanjutHTML = ` 
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
            <section class="content-header py-3">
              <div class="container text-center">
                <h2 class="fw-bold mb-1">Detail Tindak Lanjut: ${tindak_lanjut.ticket_code}</h2>
                <div class="mt-2">${getStatusBadge(tindak_lanjut.status)}</div>
                <div class="small mt-2 fw-bold">Diselesaikan pada: ${tindak_lanjut.created_at}</div>
              </div>
            </section>

            <section class="content py-4">
              <div class="container">

                <div class="card shadow-sm mb-4">
                  <div class="card-body text-center">
                    <h4 class="fw-semibold mb-3">Catatan Tindak Lanjut</h4>
                    <p class="mb-0">${tindak_lanjut.catatan || '<em class="text-muted">Tidak ada catatan terkait tindak lanjut.</em>'}</p>
                  </div>
                </div>

                <div class="card shadow-sm mb-4">
                  <div class="card-body">
                    <h4 class="fw-semibold mb-3 text-center">Lampiran Pengaduan</h4>
                    ${imgTindakLanjutList}
                  </div>
                </div>

              </div>
            </section>
            <script src="https://cdn.jsdelivr.net/npm/fslightbox@3.7.4/index.min.js"></script>
          `;
          
        // showLoader();
        const $main = $('main');
        $main.html(`${tindakLanjutHTML}`);
        $main.css('background-color', '#f5f5f5');
        return          

        })

      }
    
    })
    .fail(function (jqXHR) {
      message = "Kode ticket yang dimasukan tidak ada!"
      failed(message);
      hideLoader();
    })
    .always(function () {
      $btn.prop('disabled', false).text(originalText);
    });
  });


});