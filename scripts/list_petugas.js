// Client-side DataTables pagination: fetch once with a big limit, then page locally
function initPetugasTable() {
  const sel = '#tbl-petugas';
  const el  = document.querySelector(sel);
  if (!el) return;

  const token      = 'MY_SUPER_SECRET_TOKEN';
  const pageLength = Number(el.dataset.limit || 10);     // rows per page (DataTables)
  const fetchLimit = Number(el.dataset.fetchLimit || 500); // how many rows to fetch once

  const apiUrl = `https://app.faps.my.id/api/users/petugas`;

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
      const data = arr

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
            data: "user_id",
            render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
            },
            className: "text-center",
          },
          { data: "nama_petugas" },
          { data: "email_petugas" },
          { data: "total_pengaduan" },
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