// Client-side DataTables pagination: fetch once with a big limit, then page locally
function initLaporanDitugaskan() {
  const sel = '#tbl-ditugaskan';
  const el  = document.querySelector(sel);
  if (!el) return;

  const token      = 'MY_SUPER_SECRET_TOKEN';
  const pageLength = Number(el.dataset.limit || 10);     // rows per page (DataTables)
  const fetchLimit = Number(el.dataset.fetchLimit || 500); // how many rows to fetch once
  const userID = document.getElementById('sessionUser').value;
  const apiUrl = `https://app.faps.my.id/api/pengaduan/all?limit=${encodeURIComponent(fetchLimit)}&assigned_to=${userID}`;

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
        item.status === "diproses"
      );


      new DataTable(sel, {
        data,
        deferRender: true,
        paging: true,
        pageLength,
        lengthChange: true,
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
              <a href="#tindak_lanjut" data-ticket="${encodeURIComponent(ticket)}" class="btn btn-sm w-100 w-lg-0 bg-warning text-white tindak_lanjut mb-2" >TINDAK LANJUT</a>
              <a href="#detail" data-ticket="${encodeURIComponent(ticket)}" data-backload="laporan_ditugaskan" class="btn btn-sm w-100 w-lg-0 bg-primary text-white detail">DETAIL</a>
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
          <tr><td colspan="7" class="text-center text-danger py-4">
            Tidak ada laporan yang ditugaskan
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
  const backLoad = $(this).data('backload');

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
            <button class="btn btn-secondary" id="btn-back" onclick="loadComponent('${backLoad}')">← Kembali</button>
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




// Create Tindak Lanjut
$(document).on("click", 'a.tindak_lanjut[data-ticket]', function (e) {
  e.preventDefault();

  const ticketCode = $(this).data('ticket');
  const token = "MY_SUPER_SECRET_TOKEN";

  openModal($('#tindak-lanjut-modal'),$('#tindak-lanjut-title'));
  $('#kode_tiket_tindak_lanjut').text(`Kode Tiket : ${ticketCode}`);
  
  


  $(document).on('click', '#create_tindak_lanjut', function (e) {
    e.preventDefault();

    const $btn  = $(this);
    const petugasId = String($btn.data('id') || '').trim(); // from data-id attribute

    let fd = new FormData($("#tindak_lanjut_form")[0]);

    if (!ticketCode) {
      failed('Kode tiket tidak ditemukan.');
      return;
    }

    // UI state
    const originalText = $btn.text();
    $btn.prop('disabled', true).text('MENGIRIM…');
    if (typeof showLoader === 'function') showLoader();

    // --- Send POST request ---
    $.ajax({
      url: `https://app.faps.my.id/api/tindak_lanjut/ticket/${encodeURIComponent(ticketCode)}`,
      method: 'POST',
      processData: false,
      contentType: false,
      data: fd,
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}`},
      timeout: 15000
    })
    .done(function (res) {
      Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: "Berhasil membuat laporan tindak lanjut!",
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: "OK"
      }).then((result) => {
         closeModal($('#tindak-lanjut-modal'));
         setTimeout(() => {
           loadComponent("laporan_ditugaskan");
         }, 500);
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
});