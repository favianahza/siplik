const $changePassModal = $('#change-pass-modal');
const $oldPassword   = $('#old-password');
const $changePassLinks    = $('a[href="#change_password"]');
const $changeForm = $('#change-form');
const $change_pass = $('#change-password');
const $change_pass_confirm = $('#change-password-confirm');
const $change_pass_match     = $('#change-passmatch');

function checkMatchOnChangePassword() {
  const v1 = $change_pass.val();
  const v2 = $change_pass_confirm.val();
  if (!v1 || !v2) { $change_pass_match.text(''); return true; }
  const ok = v1 === v2;
  $change_pass_match.text(ok ? '' : 'Password dan konfirmasi password baru tidak cocok!');
  return ok;
}

$change_pass.on('input', checkMatchOnChangePassword);
$change_pass_confirm.on('input', checkMatchOnChangePassword);

$changeForm.on('submit', function (e) {
  if (!checkMatchOnChangePassword()) { e.preventDefault(); $change_pass_confirm.trigger('focus'); }
});

$changePassLinks.on('click', function (e) { e.preventDefault(); openModal($changePassModal, $oldPassword); });

// Close handlers (overlay + close buttons) for both modals
$('.modal').each(function () {
  const $modal   = $(this);
  const $overlay = $modal.find('.modal-overlay');
  const $closers = $modal.find('[data-close-modal]');

  $overlay.on('click', function () { closeModal($modal); });
  $closers.on('click', function () { closeModal($modal); });
});


// Load component for Dashboard
function loadComponent(componentName) {
  if (!componentName) return;

  const target = $("#content-wrapper");
  const filePath = `components/${componentName}.php`;

  // Show loading indicator
  target.fadeTo(150, 0.3).html('<p style="text-align:center;">Memuat konten...</p>');

  // Load the component
  $.ajax({
    url: filePath,
    method: "GET",
    cache: false,
    success: function (data) {
      target.stop(true, true).fadeTo(150, 1).html(data);
      console.log(`✅ Loaded: ${filePath}`);
      if (componentName === "laporan_masuk") {
        console.log("📊 Initializing Laporan Masuk DataTable...");
        initLaporanMasukTable(); // call the specific function
      } else if (componentName === "arsip_laporan"){
        console.log("📊 Initializing Arsip Laporan DataTable...");
        initArsipLaporanTable(); // call the specific function
      } else if (componentName === "laporan_diproses"){
        console.log("📊 Initializing Laporan Proses DataTable...");
        initArsipLaporanProses(); // call the specific function
      } else if (componentName === "list_petugas"){
        initPetugasTable();
      } else if (componentName === "laporan_ditugaskan") {
        initLaporanDitugaskan();
      }
      
    },
    error: function (xhr, status, err) {
      target.html(
        `<p style="color:red; text-align:center;">Gagal memuat ${componentName}.php<br>${err}</p>`
      );
      console.error(`❌ Error loading ${filePath}:`, err);
    }
  });
}


// Component Event
$(document).on("click", "[data-load]", function (e) {
  e.preventDefault();
  showLoader();
  const component = $(this).data("load");
  loadComponent(component);
  hideLoader();
});

$(document).on("click", ".nav-link", function () {
  sessionStorage.setItem("activeNav", $(this).attr("href"));
});

// Handle nav-link active state (only one active at a time)
$(document).on("click", ".nav-link", function (e) {
  const $this = $(this);

  // Prevent default if it's a dropdown toggle (href="#")
  const isTreeview = $this.closest(".has-treeview").length > 0;
  if ($this.attr("href") === "#") e.preventDefault();

  // Remove all active classes first
  $(".nav-link").removeClass("active");

  // Add active class to the clicked link
  $this.addClass("active");

  // Optional: if link inside treeview, mark its parent as open
  if (isTreeview) {
    $this.closest(".has-treeview").find("> .nav-link").addClass("active");
  }
});


// Modal Related

function openModal($modal, $focusEl) {

  $modal.addClass('is-open').attr('aria-hidden', 'false');
  $('body').addClass('modal-open');

  // focus after paint
  setTimeout(() => { if ($focusEl && $focusEl.length) $focusEl.trigger('focus'); }, 0);
}

function closeModal($modal) {
  $modal.removeClass('is-open').attr('aria-hidden', 'true');
}

// --- Change password (by user ID) ---
$(document).on('click', '#ganti', function (e) {
  e.preventDefault();

  const $btn  = $(this);
  const $form = $btn.closest('form');
  if (!$form.length) return;

  const userId = String($btn.data('id') || '').trim();   // ← use data-id

  // Inputs
  const $old  = $form.find('input[name="old_password"]');
  const $new  = $form.find('input[name="new_password"]');
  const $conf = $form.find('input[name="new_password_confirm"]');

  // Validation
  let ok = true;
  [$old, $new, $conf].forEach($el => {
    if (!$el.val() || !$el.val().trim()) { $el.addClass('is-invalid'); ok = false; }
    else { $el.removeClass('is-invalid'); }
  });
  if (!userId) { failed('User ID tidak ditemukan.'); return; }
  if (!ok) { failed('Mohon isi semua kolom.'); return; }
  if ($new.val() !== $conf.val()) { $conf.addClass('is-invalid'); failed('Konfirmasi password baru tidak cocok.'); return; }

  // UI state
  const originalText = $btn.text();
  $btn.prop('disabled', true).text('MEMPROSES…');
  if (typeof showLoader === 'function') showLoader();

  // FormData payload
  const fd = new FormData();
  fd.append('old_password', $old.val());
  fd.append('new_password', $new.val());
  fd.append('new_password_confirm', $conf.val());


  // POST request
  $.ajax({
    url: 'https://app.faps.my.id/api/users/' + encodeURIComponent(userId), // ← use ID in URL
    method: 'POST',
    data: fd,
    processData: false,
    contentType: false,
    dataType: 'json',
    headers: { 'Accept': 'application/json', 'Authorization': 'Bearer MY_SUPER_SECRET_TOKEN'},
    timeout: 30000
  })
  .done(function () {
    success('Password berhasil diubah.');
    $old.val(''); $new.val(''); $conf.val('');
    setTimeout(() => {
      location.reload();
    }, 1000);    
  })
  .fail(function (jqXHR) {
    const msg = jqXHR?.responseJSON?.message || 'Gagal mengubah password.';
    failed(msg);
  })
  .always(function () {
    if (typeof hideLoader === 'function') hideLoader();
    $btn.prop('disabled', false).text(originalText || 'Ganti Password');
  });
});


$(document).ready(function () {
  // Auto-load Ringkasan content on first dashboard load
  if ($("#content-wrapper").length) {
    setTimeout(() => {
      loadComponent("ringkasan");
    }, 200);
  }
});

