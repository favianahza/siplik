// Arsip Laporan Table
function initArsipLaporanTable() {
  const sel = '#tbl-arsip';
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
        item.status === "ditolak" || item.status === "selesai"
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
            data: 'status',
            render: function (data, type, row) {
              if (data === 'ditolak') {
                return '<p class="btn btn-sm btn-danger text-white mb-0">DITOLAK</p>';
              } else {
                return '<p class="btn btn-sm btn-success text-white mb-0">SELESAI</p>';
              }
            }
          },
          {
            data: "ticket_code",
            orderable: false,
            searchable: false,
            render: (ticket, type, row) => {
                if (row.status == 'ditolak') {
                  return `<div class="d-flex flex-wrap justify-content-center"><a href="#detail" data-ticket="${encodeURIComponent(ticket)}" data-backload="arsip_laporan" class="btn btn-sm w-100 w-lg-0 mb-2 bg-primary text-white detail">DETAIL</a></div>`
                } else {
                  return `<div class="d-flex flex-wrap justify-content-center"><a href="#detail_selesai" data-ticket="${encodeURIComponent(ticket)}" data-backload="arsip_laporan" class="btn btn-sm w-100 w-lg-0 mb-2 bg-primary text-white detail_selesai">DETAIL</a></div>`
                }
            }
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