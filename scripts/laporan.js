$(function () {

  let today = new Date().toISOString().split("T")[0];
  $("#tanggal_kejadian").attr("max", today);

  // --- Anonim toggle ---
  const $anon = $('#anonim');
  const $fields = $(
    'input[name="nama_pelapor"], input[name="email_pelapor"], input[name="telp_pelapor"]'
  );

  $anon.on('change', function () {
    const isAnon = this.checked;

    if (isAnon) {
      $fields.val('-').prop('disabled', true).addClass('disabled-field');
    } else {
      $fields.val('').prop('disabled', false).removeClass('disabled-field');
    }
  });

  // --- Dependent dropdowns: Kecamatan -> Kelurahan ---
  const $kec = $('select[name="id_kecamatan"]');
  const $kel = $('select[name="id_kelurahan"]');

  if (!$kec.length || !$kel.length) return;

  function resetKelurahan(placeholder) {
    $kel.empty().append(
        $('<option>', {
          value: '',
          text: placeholder || 'Pilih Nama Kelurahan',
          selected: true,
          disabled: true,
        })
      );
  }

  resetKelurahan();

  $kec.on('change', function () {
    const kecamatanId = $(this).val();

    // Loading state
    $kel
      .prop('disabled', true).empty().append(
        $('<option>', { text: 'Memuat…', selected: true, disabled: true })
      );

    $.ajax({
      url: 'https://app.faps.my.id/api/kelurahan',
      method: 'GET',
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'Authorization': 'Bearer MY_SUPER_SECRET_TOKEN'},
      data: { kecamatan_id: kecamatanId }, // jQuery will serialize as query string
      timeout: 15000,
    })
      .done(function (data) {
        resetKelurahan('Pilih Nama Kelurahan');

        if (Array.isArray(data)) {
          $.each(data, function (_, item) {
            $kel.append(
              $('<option>', {
                value: String(item.id),
                text: item.nama,
              })
            );
          });
        }

        $kel.prop('disabled', false);
      })
      .fail(function (jqXHR, textStatus) {
        console.error('Gagal memuat kelurahan:', textStatus, jqXHR.status);
        resetKelurahan('Gagal memuat kelurahan');
        $kel.prop('disabled', true); // prevent wrong submission
      });
  });

  // -- Create Pengaduan --

  $(document).on('click', '.submit-laporan', function (e) {
    e.preventDefault();

    const $btn = $(this);
    const $form = $btn.closest('form');
    if (!$form.length) return;

    /// Basic validation
    let isValid = true;

    // Loop through all required fields except the 'anonim' checkbox
    $form.find('input, select, textarea').each(function () {
      const $el = $(this);

      // skip non-required or disabled elements
      if ($el.is('#anonim') || $el.is(':disabled')) return;

      // check for empty or null value
      if (!$el.val() || $el.val().trim() === '' ) {
        isValid = false;
        $el.addClass('is-invalid'); // optional Bootstrap red border
      } else {
        $el.removeClass('is-invalid');
      }
    });

    // Persetujuan
    if($("#persetujuan").prop("checked") === false) {
      isValid = false;
    }
        
    // stop submission if invalid
    if (!isValid) {
      failed("Isi seluruh form dengan lengkap!")
      return;
    }

    const captcha = grecaptcha.getResponse(); 
    const fd = new FormData($form[0]); // includes <input type="file" name="bukti">

    if($("#anonim").prop("checked") === false){
      fd.set("anonim", "off")
    } else {
      fd.set("anonim", "on")
    }

    showLoader()
    $.ajax({
      url: 'https://app.faps.my.id/api/pengaduan',
      method: 'POST',
      data: fd,
      processData: false,     // DO NOT serialize FormData
      contentType: false,     // let browser set multipart boundary
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'Authorization': 'Bearer MY_SUPER_SECRET_TOKEN'},
      timeout: 30000
    })
    .done(function (res) {
      const tiket = res.ticket;
      // Swal.fire({
      //   title: "Laporan berhasil dibuat!",
      //   html: `<h2 id="tiket">Kode Tiket : ${tiket}</h2>`,
      //   icon: "success",
      //   footer: 'Simpan Kode Tiket untuk mengetahui progress laporan anda!',
      //   confirmButtonColor: "#3085d6",
      //   confirmButtonText: "OK"
      // }).then((result) => {
      //   if (result.isConfirmed) {
      //     Swal.fire({
      //       text: "Ditunggu!",
      //       icon: "info"
      //     });
      //   }
      // });
      Swal.fire({
        title: "Laporan berhasil dibuat!",
        html: `KODE TIKET: <b id="ticket-id">${tiket}</b>`,
        icon: "success",
        footer: `
          <center>
          <small>Pastikan simpan Kode Tiket yang muncuk untuk memantau progress laporan!</small>
          <button id="copy-btn" style=" background: none; border: none; color: #3085d6; cursor: pointer; font-size: 20px;">
            📋Salin Kode Tiket
          </button>
          </center>
        `,
        confirmButtonColor: "#3085d6",
        confirmButtonText: "OK",
        didRender: () => {
          const copyBtn = document.getElementById("copy-btn");
          copyBtn?.addEventListener("click", async () => {
            try {
              await navigator.clipboard.writeText(tiket);
            } catch (err) {
              Swal.showValidationMessage("❌ Gagal menyalin Ticket ID");
            }
          });
        }
      }).then((result) => {
        if (result.isConfirmed) {
          location.reload()
        }
      });
    })
    .fail(function (jqXHR) {
      message = 'Gagal membuat laporan! ' + (jqXHR.responseJSON.errors || `HTTP ${jqXHR.status || ''}`)
      failed(message);
    }).always(function (res) {
      console.log(res)
      hideLoader();
      $btn.prop('disabled', false).text('KIRIM LAPORAN');
    });

    // const payload = {};
    // $.each($form.serializeArray(), function (_, f) {
    //   payload[f.name] = f.value;
    // });

    // const originalText = $btn.text();
    // // $btn.prop('disabled', true).text('MENGIRIM…');
    // console.log(payload);

    // $.ajax({
    //   url: 'https://app.faps.my.id/api/pengaduan',
    //   method: 'POST',
    //   data: JSON.stringify(payload),
    //   contentType: 'application/json',
    //   dataType: 'json',
    //   headers: { 'Accept': 'application/json' },
    // })
    //   .done(function (res) {
    //     const tiket = res?.kode || res?.kode_tiket || '';
    //     alert(tiket ? `Laporan terkirim! Kode tiket: ${tiket}` : 'Laporan terkirim!');
    //     $form[0].reset();
    //   })
    //   .fail(function (jqXHR) {
    //     alert('Gagal mengirim laporan: ' + (jqXHR.responseJSON?.message || 'Terjadi kesalahan.'));
    //   })
    //   .always(function () {
    //     $btn.prop('disabled', false).text(originalText);
    //   });
  });


});
