$(function () {
  // Modals
  const $loginModal    = $('#login-modal');
  const $registerModal = $('#register-modal');


  // Inputs for focusing
  const $loginUsername = $('#login-username');
  const $regFullname   = $('#reg-fullname');

  // Openers
  const $loginLinks    = $('a[href="#login"]');
  const $registerLinks = $('a[href="#daftar"], [data-open-register]');

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


});
