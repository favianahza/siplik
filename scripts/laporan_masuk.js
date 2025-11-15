// // -- dataTable initLaporanMasuk --
// function initLaporanMasukTable() {
//   const tableSelector = "#tbl-pengaduan";
//   const tableEl = document.querySelector(tableSelector);
//   if (!tableEl) {
//     console.warn("⚠️ Table element not found for laporan_masuk!");
//     return;
//   }

//   const limit = Number(tableEl.dataset.limit || 10);
//   const apiUrl = `https://app.faps.my.id/api/pengaduan/all?limit=${encodeURIComponent(limit)}`;
//   const token = "MY_SUPER_SECRET_TOKEN";

//   // Destroy any previous instance to prevent duplication
//   if ($.fn.DataTable.isDataTable(tableSelector)) {
//     $(tableSelector).DataTable().destroy();
//   }

//   // Initialize new DataTable
//   new DataTable(tableSelector, {
//     processing: true,
//     paging: true,
//     pageLength: limit,
//     searching: true,
//     ajax: function (data, callback) {
//       fetch(apiUrl, {
//         method: "GET",
//         headers: {
//           Accept: "application/json",
//           Authorization: `Bearer ${token}`,
//         },
//       })
//         .then((res) => {
//           if (!res.ok) throw new Error(`HTTP ${res.status}`);
//           return res.json();
//         })
//         .then((json) => {
//           callback({ data: Array.isArray(json) ? json : [] });
//         })
//         .catch((err) => {
//           console.error("❌ Gagal memuat data:", err);
//           callback({ data: [] });
//         });
//     },

//     columns: [
//       {
//         data: "id",
//         render: function (data, type, row, meta) {
//         return meta.row + meta.settings._iDisplayStart + 1;
//         },
//         className: "text-center",
//       },
//       { data: "ticket_code" },
//       {
//         data: "created_at",
//         render: (value, type) => {
//             if (type === "display") return formatDMY(value);
//             return value || "";
//         },
//         className: "text-center"
//       },      
//       { data: "nama_kategori" },
//       { data: "nama_kecamatan" },
//       { data: "alamat_lengkap" },
//       {
//         data: "ticket_code",
//         orderable: false,
//         searchable: false,
//         render: (ticket, type, row) => `
//         <div class="d-flex flex-wrap justify-content-center">
//             <a href="#detail" data-ticket="${encodeURIComponent(ticket)}"class="btn btn-sm w-100 w-lg-0 mb-2 bg-primary text-white detail">DETAIL</a>
//             <a href="#alokasi" data-ticket="${encodeURIComponent(ticket)}" class="btn btn-sm w-100 w-lg-0 ${row.status === "diverifikasi" ? "bg-warning text-white alokasi" : "bg-danger disabled"}">ALOKASI PETUGAS</a>
//         </div>`
//       },
//     ],
//     language: {
//       emptyTable: "Belum ada data pengaduan.",
//       processing: "Memuat data…",
//     },
//   });
// }

// Client-side DataTables pagination: fetch once with a big limit, then page locally
function initLaporanMasukTable() {
  const sel = '#tbl-pengaduan';
  const el  = document.querySelector(sel);
  if (!el) return;

  const token      = 'MY_SUPER_SECRET_TOKEN';
  const pageLength = Number(el.dataset.limit || 10);     // rows per page (DataTables)
  const fetchLimit = Number(el.dataset.fetchLimit || 500); // how many rows to fetch once

  const apiUrl = `https://app.faps.my.id/api/pengaduan/all?limit=${encodeURIComponent(fetchLimit)}`;

  // Destroy previous instance if any
  if ($.fn.DataTable.isDataTable(sel)) $(sel).DataTable().destroy();

  // Show a lightweight loading row
  const tbody = el.querySelector('tbody');
  if (tbody) {
    tbody.innerHTML = `
      <tr><td colspan="6" class="text-center py-4">
        <div class="spinner-border text-success me-2" role="status" style="width:1.25rem;height:1.25rem;"></div>
        Memuat data…
      </td></tr>`;
  }

  // Fetch once
  fetch(apiUrl, {
    headers: { Accept: 'application/json', Authorization: `Bearer ${token}` }
  })
    .then(r => {
      if (!r.ok) throw new Error(`HTTP ${r.status}`);
      return r.json();
    })
    .then(rows => {
      let arr = Array.isArray(rows) ? rows : [];
      
      // Initialize DataTable with client-side pagination/search/sort
      const data = arr.filter(item => 
        item.status === "masuk" || item.status === "diverifikasi"
      );

      new DataTable(sel, {
        data,
        deferRender: true,
        paging: true,
        pageLength,
        lengthChange: true,   // user can choose 10/25/50/…
        searching: true,
        ordering: true,
        columns: [
          {
            data: "id",
            render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
            },
            className: "text-center",
          },
          { data: "ticket_code" },
          {
            data: "created_at",
            render: (value, type) => {
                if (type === "display") return formatDMY(value);
                return value || "";
            },
            className: "text-center"
          },      
          { data: "nama_kategori" },
          { data: "nama_kecamatan" },
          { data: "alamat_lengkap" },
          {
            data: "ticket_code",
            orderable: false,
            searchable: false,
            render: (ticket, type, row) => `
            <div class="d-flex flex-wrap justify-content-center">
                <a href="#detail" data-ticket="${encodeURIComponent(ticket)}"class="btn btn-sm w-100 w-lg-0 mb-2 bg-primary text-white detail">DETAIL</a>
                <a href="#alokasi" data-ticket="${encodeURIComponent(ticket)}" class="btn btn-sm w-100 w-lg-0 ${row.status === "diverifikasi" ? "bg-warning text-white alokasi" : "bg-danger disabled"}">ALOKASI PETUGAS</a>
            </div>`
          },
        ],
        language: {
          emptyTable: 'Belum ada data pengaduan.',
          processing: 'Memuat data…',
          paginate: { previous: '«', next: '»' }
        }
      });
    })
    .catch(err => {
      console.error('Gagal memuat data:', err);
      if (tbody) {
        tbody.innerHTML = `
          <tr><td colspan="6" class="text-center text-danger py-4">
            Gagal memuat data.
          </td></tr>`;
      }
    });
}


// Get laporan in detail
$(document).on('click', 'a.detail[data-ticket]', function (e) {
  e.preventDefault();

  const ticketCode = $(this).data('ticket');
  const $main = $('main#content-wrapper');
  const token = 'MY_SUPER_SECRET_TOKEN';
  const apiUrl = `https://app.faps.my.id/api/pengaduan/${ticketCode}/?detailed=true`;

  $main.html(`
    <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
      <div class="spinner-border text-success mb-3" role="status"></div>
      <p>Sedang memuat detail laporan...</p>
    </div>
  `);

  $.ajax({
    url: apiUrl,
    method: 'GET',
    dataType: 'json',
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
    data: { ticket_code: ticketCode },
    timeout: 30000
  })
  .done(function (data) {
    const baseUrl = 'https://app.faps.my.id/assets/uploads/bukti/';
    const laporan = Array.isArray(data) ? data[0] : data;
    if (!laporan) throw new Error('Data laporan tidak ditemukan');

    const images = Array.isArray(laporan.images) ? laporan.images : [];
    const imgList = images.map(function (img) {
      return `
        <a data-fslightbox="lampiran" href="${baseUrl}${img}">
          <img src="${baseUrl}${img}" alt="Bukti Laporan ${laporan.ticket_code}"
               class="img-fluid rounded shadow-sm mb-2"
               style="max-width: 500px; display: inline; margin: 0 auto;">
        </a>
      `;
    }).join('');

    const lampiranHTML = `
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center">
          <h5 class="fw-semibold mb-3">Lampiran Bukti</h5>
          ${imgList || '<em class="text-muted">Tidak ada lampiran.</em>'}
        </div>
      </div>
    `;

    const formatDate = function (str) {
      if (!str) return '-';
      const d = new Date(String(str).replace(' ', 'T'));
      return d.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    };
    if(laporan.status == "masuk"){
        laporan.status = `<span class="badge bg-warning">BELUM DIVERIFIKASI</span>`;
        var verifyButton = `
        <a href="#tolak" data-ticket="${encodeURIComponent(ticketCode)}" class="btn btn-danger text-white tolak">Tolak</a>
        <a href="#terima" data-ticket="${encodeURIComponent(ticketCode)}" class="btn btn-success text-white terima">Verifikasi</a>
        `;
    } else if((laporan.status == "diverifikasi")){
        laporan.status = `<span class="badge bg-info">TELAH DIVERIFIKASI</span>`;
        var verifyButton = '';
    } else if((laporan.status == "ditolak")){
        laporan.status = `<span class="badge bg-danger">DITOLAK</span>`;
        var verifyButton = '';
    } else if((laporan.status == "diproses")){
        laporan.status = `<span class="badge bg-primary">DIPROSES</span>`;
        var verifyButton = '';
    } else {
        laporan.status = `<span class="badge bg-success">SELESAI</span>`;
        var verifyButton = '';
    }

    $main.html(`
      <section class="content-header py-3 border-bottom">
        <div class="container-fluid d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
          <h1 class="m-0 fw-bold">Detail Laporan: ${laporan.ticket_code}</h1>
          <div class="text-muted small text-md-end">
            Diperbarui pada: <strong>${formatDate(laporan.updated_at)}</strong>
          </div>
        </div>
      </section>

      <section class="content py-3">
        <div class="container-fluid">
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
              <h5 class="fw-semibold mb-3">Informasi Umum</h5>
              <table class="table table-sm table-borderless mb-0">
                <tbody>
                  <tr><th width="180">Kode Tiket</th><td>${laporan.ticket_code}</td></tr>
                  <tr><th>Kategori</th><td>${laporan.nama_kategori}</td></tr>
                  <tr><th>Kecamatan</th><td>${laporan.nama_kecamatan}</td></tr>
                  <tr><th>Kelurahan</th><td>${laporan.nama_kelurahan}</td></tr>
                  <tr><th>Alamat Lengkap</th><td>${laporan.alamat_lengkap}</td></tr>
                  <tr><th>Tanggal Masuk</th><td>${formatDate(laporan.created_at)}</td></tr>
                  <tr><th>Status</th><td>${laporan.status}</td></tr>
                </tbody>
              </table>
            </div>
          </div>

          ${lampiranHTML}

          <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
              <h5 class="fw-semibold mb-3">Kronologi / Deskripsi</h5>
              <p class="mb-0">${laporan.kronologi || '<em>Tidak ada keterangan.</em>'}</p>
            </div>
          </div>

          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
              <h5 class="fw-semibold mb-3">Pelapor</h5>
              <table class="table table-sm table-borderless mb-0">
                <tbody>
                  <tr><th width="180">Nama</th><td>${laporan.is_anonim ? '<em>Anonim</em>' : laporan.nama_pelapor}</td></tr>
                  <tr><th>Email</th><td>${laporan.is_anonim ? '-' : laporan.email_pelapor}</td></tr>
                  <tr><th>No. Telp</th><td>${laporan.is_anonim ? '-' : laporan.telp_pelapor}</td></tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="text-end">
            <button class="btn btn-secondary" id="btn-back" onclick="loadComponent('laporan_masuk')">← Kembali ke Daftar</button>
          ${verifyButton}
          </div>
        </div>
      </section>
      <script src="https://cdn.jsdelivr.net/npm/fslightbox@3.7.4/index.min.js"></script>
    `);
  })
  .fail(function (jqXHR, textStatus) {
    console.error('❌ Gagal mengambil detail laporan:', textStatus, jqXHR.status);
    $main.html(`
      <div class="text-center text-danger py-5">
        <p>Gagal memuat detail laporan. Silakan coba lagi.</p>
        <button class="btn btn-outline-secondary" id="btn-back">Kembali</button>
      </div>
    `);
  });
});

// Verify & accept specific laporan
$(document).on("click", 'a.terima[data-ticket]', function (e) {
  e.preventDefault();

  const ticketCode = $(this).data("ticket");
  const token = "MY_SUPER_SECRET_TOKEN";
  const apiUrl = `https://app.faps.my.id/api/pengaduan/${encodeURIComponent(ticketCode)}?verify=true`;

  Swal.fire({
    title: "Yakin ingin diterima?",
    text: "Pastikan laporan telah diverifikasi dengan benar!",
    icon: "question",
    confirmButtonColor: "#0b6919ff",
    showCloseButton: true,
    confirmButtonText: "Terima"
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
          url: apiUrl,
          method: 'PATCH',
          data: JSON.stringify([{"status": "diverifikasi"}]),
          contentType: 'application/json',
          dataType: 'json',
          headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}`},
          timeout: 30000
      })
      .done(function(data){
        success("Laporan berhasil diterima!");
        setTimeout(() => {
          loadComponent("laporan_masuk");
        }, 1000);        
      })
      .fail(function(jqXHR){
        message = 'Verifikasi gagal!' + (jqXHR.responseJSON.errors || `HTTP ${jqXHR.status || ''}`)
        failed(message);        
      });
    }
  }); 
});

// Verify & reject specific laporan
$(document).on("click", 'a.tolak[data-ticket]', function (e) {
  e.preventDefault();

  const ticketCode = $(this).data("ticket");
  const token = "MY_SUPER_SECRET_TOKEN";
  const apiUrl = `https://app.faps.my.id/api/pengaduan/${encodeURIComponent(ticketCode)}?reject=true`;

  Swal.fire({
    title: "Yakin ingin ditolak?",
    text: "Pastikan laporan telah diverifikasi dengan benar!",
    icon: "question",
    confirmButtonColor: "#c40303ff",
    showCloseButton: true,
    confirmButtonText: "Tolak",
  }).then((result) => {
     if (result.isConfirmed) {
      $.ajax({
          url: apiUrl,
          method: 'PATCH',
          data: JSON.stringify([{"status": "ditolak"}]),
          contentType: 'application/json',
          dataType: 'json',
          headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}`},
          timeout: 30000
      })
      .done(function(data){
        success("Laporan berhasil ditolak!");
        setTimeout(() => {
          loadComponent("laporan_masuk");
        }, 1000);        
      })
      .fail(function(jqXHR){
        message = 'Verifikasi gagal!' + (jqXHR.responseJSON.errors || `HTTP ${jqXHR.status || ''}`)
        failed(message);        
      });
    }  
  })
});

// Allocate petugas to specific laporan
$(document).on("click", 'a.alokasi[data-ticket]', function (e) {
  e.preventDefault();

  const ticketCode = $(this).data("ticket");
  const token = "MY_SUPER_SECRET_TOKEN";

  openModal($('#assign-petugas-modal'),$('#assign-petugas-title'));
  $('#kode_tiket_title').text(`Kode Tiket : ${ticketCode}`);
  // --- Assign Petugas ---
  $(document).on('click', '#assign_petugas', function (e) {
    e.preventDefault();

    const $btn  = $(this);
    const userId = String($btn.data('id') || '').trim(); // from data-id attribute

    if (!ticketCode) {
      failed('Kode tiket tidak ditemukan.');
      return;
    }

    // Get selected radio button value
    const selectedPetugas = $('input[name="petugas"]:checked').val();
    if (!selectedPetugas) {
      failed('Silakan pilih petugas terlebih dahulu!');
      return;
    }

    // UI state
    const originalText = $btn.text();
    $btn.prop('disabled', true).text('MENGIRIM…');
    if (typeof showLoader === 'function') showLoader();

    console.log(selectedPetugas)

    // --- Send PATCH request ---
    $.ajax({
      url: `https://app.faps.my.id/api/pengaduan/${encodeURIComponent(ticketCode)}?petugas=${encodeURIComponent(selectedPetugas)}`,
      method: 'PATCH',
      processData: false,
      contentType: false,
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}`},
      timeout: 30000
    })
    .done(function (res) {
      Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: "Berhasil mengalokasikan petugas!",
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: "OK"
      }).then((result) => {
         closeModal('#assign-petugas-modal');
         setTimeout(() => {
           loadComponent("laporan_masuk");
         }, 1000);
      });      
      
    })
    .fail(function (jqXHR) {
      const msg = jqXHR?.status;
      failed(msg);
    })
    .always(function () {
      if (typeof hideLoader === 'function') hideLoader();
      $btn.prop('disabled', false).text(originalText || 'Simpan Alokasi');
    });
  });

//   $.ajax({
//       url: 'https://app.faps.my.id/api/pengaduan',
//       method: 'POST',
//       data: fd,
//       processData: false,     // DO NOT serialize FormData
//       contentType: false,     // let browser set multipart boundary
//       dataType: 'json',
//       headers: { 'Accept': 'application/json', 'Authorization': 'Bearer MY_SUPER_SECRET_TOKEN'},
//       timeout: 30000
//   })  
});
